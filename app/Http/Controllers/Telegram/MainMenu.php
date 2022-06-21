<?php

namespace App\Http\Controllers\Telegram;

use App\Services\Status\UserStatus;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class MainMenu extends BaseCommand
{
    function processCommand($param = false)
    {
        $this->user->setStatus(UserStatus::MAIN_MENU);

        $this->getBot()->sendMessageWithKeyboard(
            $this->text['main_menu'],
            new ReplyKeyboardMarkup([
                [$this->text['forecast']],
                [$this->text['current_weather']],
                [$this->text['feedback'], $this->text['change_city']],
            ], false, true),
        );
    }
}
