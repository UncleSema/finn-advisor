<?php

require 'Config.php';
require 'network/Utils.php';
require 'network/LongPoll.php';
require 'utils/Functions.php';

$lp = new LongPoll();

while (true) {
    foreach($lp->update() as $update) {
        if ($update['type'] == 'message_new') {

            $peer_id = $update['object']['message']['peer_id'];
            $user_id = $update['object']['message']['from_id'];
            $message = $update['object']['message']['text'];
            $message = mb_strtolower($message, 'UTF-8');

            if (preg_match("/^(test)$/", $message)) {
                message_send("Test complete.", $peer_id);
            } else {
                message_send("This command does exists.", $peer_id);
            }
        }
    }
}
