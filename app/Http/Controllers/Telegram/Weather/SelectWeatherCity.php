<?php

namespace App\Http\Controllers\Telegram\Weather;

use App\Http\Controllers\Telegram\BaseCommand;
use App\Services\Status\UserStatus;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class SelectWeatherCity extends BaseCommand
{

    function processCommand($param = null)
    {
        $this->user->update([
            'status' => $this->update->getMessage()->getText() == $this->text['current_weather'] ? UserStatus::CURRENT_CITY_SELECT : UserStatus::FORECAST_CITY_SELECT,
        ]);

        foreach ($this->user->cities as $city) {
            $buttons[] = [$city->city->full_title];
        }

        $buttons[] = [[
            'text' => $this->text['send_location_type'],
            'request_location' => true,
        ]];
        $buttons[] = [$this->text['back']];

        $this->getBot()->sendMessageWithKeyboard(
            $this->text['select_city_from_list'],
            new ReplyKeyboardMarkup($buttons, false, true),
        );
    }

}
