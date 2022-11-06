<?php

class Config {
    const API = 5.101;

    public static function getToken() {
        return getenv("VK_BOT_TOKEN");
    }

    public static function getGroupId() {
        return getenv("VK_GROUP_ID");
    }
}
