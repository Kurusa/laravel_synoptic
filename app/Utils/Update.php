<?php

namespace App\Utils;

use TelegramBot\Api\Types\User;

class Update extends \TelegramBot\Api\Types\Update
{

    private array $decodedCallbackQueryData = [];

    public function __construct(\TelegramBot\Api\Types\Update $update)
    {
        if ($update->getCallbackQuery()) {
            parent::setCallbackQuery($update->getCallbackQuery());
        }

        if ($update->getMessage()) {
            parent::setMessage($update->getMessage());
        }
    }

    public function getBotUser(): User
    {
        if ($this->getCallbackQuery()) {
            $user = $this->getCallbackQuery()->getFrom();
        } elseif ($this->getMessage()) {
            $user = $this->getMessage()->getFrom();
        } elseif ($this->getInlineQuery()) {
            $user = $this->getInlineQuery()->getFrom();
        } else {
            throw new \Exception('cant get telegram user data');
        }

        return $user;
    }

    private function getDecodedCallbackQueryData(): array
    {
        if ($this->getCallbackQuery() && !$this->decodedCallbackQueryData) {
            $this->decodedCallbackQueryData = json_decode($this->getCallbackQuery()->getData(), true);
        }

        return $this->decodedCallbackQueryData;
    }

    public function getCallbackQueryByKey(string $key): string
    {
        return isset($this->getDecodedCallbackQueryData()[$key]) ? $this->getDecodedCallbackQueryData()[$key] : '';
    }

}
