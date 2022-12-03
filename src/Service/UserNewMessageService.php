<?php

namespace FinnAdvisor\Service;

use Exception;
use FinnAdvisor\Exceptions\AbstractFinnException;
use FinnAdvisor\Model\MessageTypeRegex;
use FinnAdvisor\Model\NewMessage;
use FinnAdvisor\VK\VKBotApiClient;
use Logger;

class UserNewMessageService
{
    private UserResponseService $responseService;
    private VKBotApiClient $apiClient;
    private Logger $logger;

    public function __construct(UserResponseService $responseService, VKBotApiClient $apiClient)
    {
        $this->responseService = $responseService;
        $this->apiClient = $apiClient;
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function processMessage(NewMessage $message): void
    {
        try {
            $this->routeMessage($message);
        } catch (AbstractFinnException $e) {
            $this->apiClient->sendMessage($e->getMessage(), $message->getPeerId());
        } catch (Exception $e) {
            $text = $message->getText();
            $userId = $message->getPeerId();

            $this->logger->error("Unexpected exception during processing the message `$text` from $userId", $e);
            $this->apiClient->sendMessage($this->responseService->serverError(), $userId);
        }
    }

    private function routeMessage(NewMessage $message): void
    {
        $beautifiedText = $this->beautifyMessageText($message->getText());
        if (preg_match(MessageTypeRegex::LIST_CATEGORIES, $beautifiedText)) {
            $response = $this->responseService->allCategories($message->getPeerId());
        } elseif (preg_match(MessageTypeRegex::NEW_CATEGORY, $beautifiedText, $matches)) {
            $response = $this->responseService->addCategory($message->getPeerId(), $matches[1]);
        } elseif (preg_match(MessageTypeRegex::REMOVE_CATEGORY, $beautifiedText, $matches)) {
            $response = $this->responseService->removeCategory($message->getPeerId(), $matches[1]);
        } elseif (preg_match(MessageTypeRegex::HELP, $beautifiedText)) {
            $response = $this->responseService->help();
        } else {
            $response = $this->responseService->unknown();
        }
        if ($response != null) {
            $this->apiClient->sendMessage($response, $message->getPeerId());
        }
    }

    private function beautifyMessageText(string $text): string
    {
        return strtolower(str_replace("\n", "", trim($text)));
    }
}
