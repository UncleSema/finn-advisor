<?php

namespace FinnAdvisor\Operation;

use FinnAdvisor\Exceptions\UserWrongMessageException;
use FinnAdvisor\Model\Operation;
use FinnAdvisor\VK\VKBotApiClient;
use Logger;

class OperationService
{
    private OperationRepository $operationRepository;
    private VKBotApiClient $vkApi;
    private Logger $logger;

    public function __construct($operationRepository, $vkApi)
    {
        $this->operationRepository = $operationRepository;
        $this->vkApi = $vkApi;
        $this->logger = Logger::getLogger("OperationService");
    }

    public function processNewOperation($msg): void {

    }

    private function parseOperation($msg): Operation {
        $tokens = explode(" ", $msg);
        if (count($tokens) == 0) {
            throw new UserWrongMessageException();
        }


        return new Operation();
    }
}
