<?php

namespace App\Utils;

use TelegramBot\Api\{
    BotApi,
    Types\Message,
};

class Api extends BotApi
{

    private int $chatId;

    public function __construct($token, $trackerToken = null)
    {
        parent::__construct($token, $trackerToken);
    }

    public function setChatId(int $chatId)
    {
        $this->chatId = $chatId;
    }

    public function sendMessageWithKeyboard(
        string $text,
               $keyboard,
        int    $chatId = null,
        int    $replyToMessageId = null,
    ): Message {
        return parent::sendMessage(
            $this->chatId ?: $chatId,
            $text,
            'HTML',
            true,
            $replyToMessageId,
            $keyboard,
        );
    }

    public function sendText(
        string $text,
    ): Message {
        return parent::sendMessage(
            $this->chatId,
            $text,
        );
    }

}
