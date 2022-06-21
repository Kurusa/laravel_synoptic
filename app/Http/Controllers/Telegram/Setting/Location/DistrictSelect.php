<?php

namespace App\Http\Controllers\Telegram\Setting\Location;

use App\Http\Controllers\Telegram\BaseCommand;
use App\Models\District;
use App\Services\Status\UserStatus;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class DistrictSelect extends BaseCommand
{

    function processCommand($param = null)
    {
        $this->user->setStatus(UserStatus::SETTINGS_DISTRICT_SELECT);

        $districts = District::all();
        foreach ($districts as $district) {
            $buttons[] = [$district->title];
        }
        $buttons[] = [$this->text['back']];

        $this->getBot()->sendMessageWithKeyboard(
            $this->text['select_district'],
            new ReplyKeyboardMarkup($buttons, false, true),
        );
    }

}
