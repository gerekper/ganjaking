<?php

namespace ACP\Migrate;

use AC\Message;

trait MessageTrait
{

    protected function set_message(string $message, string $type = null)
    {
        if (null === $type) {
            $type = Message::ERROR;
        }

        $notice = new Message\Notice($message);
        $notice->set_type($type)
               ->register();
    }

}