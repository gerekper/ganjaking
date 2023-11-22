<?php

namespace DynamicOOOS\TelegramBot\Api\Types;

use DynamicOOOS\TelegramBot\Api\Collection\Collection;
use DynamicOOOS\TelegramBot\Api\TypeInterface;
final class ArrayOfBotCommand extends Collection implements TypeInterface
{
    public static function fromResponse($data)
    {
        $arrayOfBotCommand = new self();
        foreach ($data as $botCommand) {
            $arrayOfBotCommand->addItem(BotCommand::fromResponse($botCommand));
        }
        return $arrayOfBotCommand;
    }
}
