<?php

class LongPoll {
    private string $server, $key;
    private string $ts;
    private Curler $curler;

    public function __construct(Curler $curler) {
        $this->curler = $curler;
        $this->getLongPolling();
    }

    public function getLongPolling(): void {
        $data = $this->curler->vk_request("groups.getLongPollServer", ["group_id" => Config::getGroupId()]);
        if (!$data)
            exit("Failed to get longpolling data...\n");
        $this->server = $data["server"];
        $this->key = $data["key"];
        $this->ts = $data["ts"];
    }

    public function update() {
        $params = [
            "key" => $this->key,
            "ts" => $this->ts,
            "wait" => 25
        ];
        $updates = $this->curler->curl_get($this->server, $params);
        if (isset($updates["failed"])) {
            $this->getLongPolling();
            return $this->update();
        }
        $this->ts = $updates["ts"];
        return $updates["updates"];
    }
}
