<?php

namespace FinnAdvisor\VK;

use Logger;
use VK\CallbackApi\VKCallbackApiHandler;

class VKBotCallbackApiHandler extends VKCallbackApiHandler
{
    private VKBotApiClient $client;
    private Logger $logger;

    public function __construct(VKBotApiClient $client)
    {
        $this->client = $client;
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function messageNew($group_id, $secret, $object)
    {
        $peerId = $object['message']['peer_id'];
        $this->logger->info("New message from $peerId");
        $user = $this->client->getUser($peerId);
        $this->client->sendMessage("Привет, " . $user->getFirstName() . "!", $peerId);
    }
}
