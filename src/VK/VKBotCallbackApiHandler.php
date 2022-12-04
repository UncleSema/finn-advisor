<?php

namespace FinnAdvisor\VK;

use Exception;
use FinnAdvisor\Caching\RedisClient;
use FinnAdvisor\Model\NewMessage;
use FinnAdvisor\Service\NewMessageRouter;
use Logger;
use VK\CallbackApi\VKCallbackApiHandler;

class VKBotCallbackApiHandler extends VKCallbackApiHandler
{
    private NewMessageRouter $messageRouter;
    private RedisClient $redisClient;
    private Logger $logger;

    public function __construct(NewMessageRouter $messageRouter, RedisClient $redisClient)
    {
        $this->messageRouter = $messageRouter;
        $this->redisClient = $redisClient;
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function messageNew(int $group_id, string|null $secret, array $object)
    {
        $message = new NewMessage(
            $object["message"]["id"],
            $object["message"]["date"],
            $object["message"]["peer_id"],
            $object["message"]["text"]
        );
        if ($this->isMessageAlreadyProcessed($message)) {
            return;
        }
        $peer_id = $message->getPeerId();
        $this->logger->debug("got new message, peer_id: $peer_id");
        try {
            $this->messageRouter->processMessage($message);
        } catch (Exception $e) {
            $this->logger->error("Cannot process new message", $e);
        }
    }

    private function isMessageAlreadyProcessed(NewMessage $message): bool
    {
        $id = $message->getPeerId() . "#" . $message->getId();
        return $this->redisClient->getsetMessageId($id) != null;
    }
}
