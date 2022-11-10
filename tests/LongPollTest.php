<?php

use PHPUnit\Framework\TestCase;

require 'www/Config.php';
require 'www/network/Curler.php';
require 'www/network/LongPoll.php';

class LongPollTest extends TestCase {
    private const VK_BOT_TOKEN = "long-poll-test-token";
    private const VK_GROUP_ID = "long-poll-test-group-id";

    public function setUp(): void {
        putenv("VK_BOT_TOKEN=" . self::VK_BOT_TOKEN);
        putenv("VK_GROUP_ID=" . self::VK_GROUP_ID);
    }

    public function testLongPoll(): void {
        $curler = $this->createStub(Curler::class);
        $server = "stub-server";
        $key = "stub-key";
        $ts = "stub-ts";

        $updates = ["type" => "wall_post_new", "event_id" => "1", "v" => "1.0"];

        $curler->method('vk_request')
            ->willReturn(["key" => $key, "server" => $server, "ts" => $ts]);

        $curler->method('curl_get')
            ->willReturn(['ts' => 'stub-ts-1', 'updates' => $updates]);

        $longPoll = new LongPoll($curler);
        $result = $longPoll->update();

        self::assertSame($updates, $result);
    }
}