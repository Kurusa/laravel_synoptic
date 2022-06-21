<?php

namespace App\Http\Controllers\Telegram;

use App\Utils\Api;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use App\Utils\Update;

class BasicMessages
{
    private $bot;

    protected $botUser;

    public function __construct(Update $update)
    {
        $this->update = $update;
        $this->botUser = $update->getBotUser();
    }

    public function getBot(): Api
    {
        if (!$this->bot) {
            $this->bot = new Api(env('TELEGRAM_BOT_TOKEN'));
            $this->bot->setChatId($this->botUser->getId());
        }

        return $this->bot;
    }

    function notifyAboutNewUser(): void
    {
        $this->getBot()->sendMessage(
            375036391,
            '<b>Новий користувач:</b> @' . $this->botUser->getUsername() . ', ' . $this->botUser->getFirstName() ?: '',
            'html',
        );
    }

    function notifyAboutCantFindCity(): void
    {
        $this->getBot()->sendText($this->text['cant_find_city']);
    }

    function selectingLocationFlow(array $buttons): void
    {
        $buttons[] = [$this->text['back']];

        $this->getBot()->sendMessageWithKeyboard(
            $this->text['did_you_mean_this_city'],
            new ReplyKeyboardMarkup($buttons, false, true),
        );
    }
}
