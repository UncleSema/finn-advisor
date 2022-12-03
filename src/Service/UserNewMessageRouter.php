<?php

namespace FinnAdvisor\Service;

use FinnAdvisor\Model\NewMessage;
use FinnAdvisor\VK\VKBotApiClient;

class UserNewMessageRouter
{
    private UserResponseService $responseService;
    private VKBotApiClient $apiClient;

    public function __construct(UserResponseService $responseService, VKBotApiClient $apiClient)
    {
        $this->responseService = $responseService;
        $this->apiClient = $apiClient;
    }

    public function processMessage(NewMessage $message): void
    {
        $text = $this->beautifyMessageText($message->getText());
        if ($text == "категории") {
            $response = $this->responseService->allCategories($message->getPeerId());
        } elseif ($text == "помощь") {
            $response = $this->responseService->help();
        } elseif ($text == "убери") {
            $response = $this->responseService->remove();
        } elseif ($text == "отчёт" || $text == "отчет") {
            $response = $this->responseService->statement();
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
