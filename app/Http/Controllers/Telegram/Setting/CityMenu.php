<?php

namespace App\Http\Controllers\Telegram\Setting;

use App\Http\Controllers\Telegram\BaseCommand;
use App\Services\Status\UserStatus;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class CityMenu extends BaseCommand
{

    function processCommand($param = null)
    {
        $this->user->setStatus(UserStatus::CITY_MENU);

        $this->getBot()->sendMessageWithKeyboard(
            $this->text['city_list_info'],
            new ReplyKeyboardMarkup([
                [$this->text['add_city'], $this->text['my_cities']],
                [$this->text['back']]
            ], false, true)
        );
    }

}
