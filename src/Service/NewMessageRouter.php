<?php

namespace FinnAdvisor\Service;

use Exception;
use FinnAdvisor\Model\NewMessage;
use FinnAdvisor\Service\Metrics\MetricsService;
use FinnAdvisor\VK\VKBotApiClient;
use Logger;
use Throwable;

class NewMessageRouter
{
    private UserResponseService $responseService;
    private VKBotApiClient $apiClient;
    private MetricsService $metricsService;
    private Logger $logger;

    public function __construct(
        UserResponseService $responseService,
        VKBotApiClient $apiClient,
        MetricsService $metricsService
    ) {
        $this->responseService = $responseService;
        $this->apiClient = $apiClient;
        $this->metricsService = $metricsService;
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function processMessage(NewMessage $message): void
    {
        $text = $message->getText();
        $userId = $message->getPeerId();
        try {
            $this->routeMessage($message);
        } catch (Exception $e) {
            $this->logger->error("Unexpected exception during processing the message `$text` from $userId", $e);
            $this->apiClient->sendMessage($this->responseService->serverError($userId));
        } catch (Throwable $e) {
            $this->logger->error("Unexpected throwable during processing the message `$text` from $userId:\n" . $e);
            $this->apiClient->sendMessage($this->responseService->serverError($userId));
        }
    }

    private function routeMessage(NewMessage $message): void
    {
        $text = $message->getText();
        $peerId = $message->getPeerId();
        $matches = [];
        if ($this->parseAllCategories($text, $matches)) {
            $this->metricsService->newMessage("all_categories");
            $response = $this->responseService
                ->allCategories($peerId);
        } elseif ($this->parseAddCategory($text, $matches)) {
            $this->metricsService->newMessage("add_category");
            $response = $this->responseService
                ->addCategory($peerId, $matches[1]);
        } elseif ($this->parseRemoveCategory($text, $matches)) {
            $this->metricsService->newMessage("remove_category");
            $response = $this->responseService
                ->removeCategory($peerId, $matches[1]);
        } elseif ($this->parseHelp($text, $matches)) {
            $this->metricsService->newMessage("help");
            $response = $this->responseService->help($peerId);
        } elseif ($this->parseAddOperation($text, $matches)) {
            $this->metricsService->newMessage("add_operation");
            $response = $this->responseService
                ->addOperation($peerId, $matches[1], $matches[2], $matches[4]);
        } elseif ($this->parseRemoveOperation($text, $matches)) {
            $this->metricsService->newMessage("remove_operation");
            $response = $this->responseService
                ->removeOperation($peerId);
        } elseif ($this->parseRemoveOperationByCategory($text, $matches)) {
            $this->metricsService->newMessage("remove_operation_by_category");
            $response = $this->responseService
                ->removeOperationByCategory($peerId, $matches[1]);
        } elseif ($this->parseStatement($text, $matches)) {
            $this->metricsService->newMessage("statement");
            $response = $this->responseService
                ->statement($peerId);
        } elseif ($this->parseAllOperations($text, $matches)) {
            $this->metricsService->newMessage("all_operations");
            $response = $this->responseService
                ->allOperations($peerId);
        } elseif ($this->parseOperationsByCategory($text, $matches)) {
            $this->metricsService->newMessage("all_operations_by_category");
            $response = $this->responseService
                ->operationsByCategory($peerId, $matches[1]);
        } else {
            $this->metricsService->newMessage("unknown");
            $response = $this->responseService->unknown($peerId);
        }
        $this->apiClient->sendMessage($response);
    }

    private function parseAllCategories(string $text, array &$matches): bool
    {
        return $this->regex("категории", $text, $matches);
    }

    private function parseAddCategory(string $text, array &$matches): bool
    {
        return $this->regex("\+\s+(\S+)", $text, $matches);
    }

    private function parseRemoveCategory(string $text, array &$matches): bool
    {
        return $this->regex("-\s+(\S+)", $text, $matches);
    }

    private function parseHelp(string $text, array &$matches): bool
    {
        return $this->regex("помощь", $text, $matches) ||
            $this->regex("начать", $text, $matches);
    }

    private function parseAddOperation(string $text, array &$matches): bool
    {
        return $this->regex("\+\s+(\d+)\s+(\S+)\s*(\s(\S.*))?", $text, $matches);
    }

    private function parseRemoveOperation(string $text, array &$matches): bool
    {
        return $this->regex("убери", $text, $matches);
    }

    private function parseRemoveOperationByCategory(string $text, array &$matches): bool
    {
        return $this->regex("убери\s+(\S+)", $text, $matches);
    }

    private function parseStatement(string $text, array &$matches): bool
    {
        return $this->regex("отч[е|ё]т", $text, $matches);
    }

    private function parseAllOperations(string $text, array &$matches): bool
    {
        return $this->regex("операции", $text, $matches);
    }

    private function parseOperationsByCategory(string $text, array &$matches): bool
    {
        return $this->regex("операции\s+(\S+)", $text, $matches);
    }

    private function regex(string $regex, string $text, array &$matches): bool
    {
        return preg_match("/^\s*$regex\s*$/ui", $text, $matches, PREG_UNMATCHED_AS_NULL);
    }
}
