<?php

namespace App\Http\Controllers\Telegram\Setting\Location;

use App\Http\Controllers\Telegram\BaseCommand;
use App\Models\City;
use App\Services\Status\UserStatus;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class CityName extends BaseCommand
{

    function processCommand($param = null)
    {
        if ($this->user->checkStatus(UserStatus::SETTINGS_CITY_NAME)) {
            $suitableCities = City::where('title', 'like', '%' . $this->update->getMessage()->getText() . '%')->get();
            if ($suitableCities->count()) {
                foreach ($suitableCities as $city) {
                    $buttons[] = [$city->full_title];
                }
            }

            if ($buttons) {
                $this->user->setStatus(UserStatus::SETTINGS_LOCATION_SELECTING);
                $this->selectingLocationFlow($buttons);
            } else {
                $this->notifyAboutCantFindCity();
            }
        } else {
            $this->user->setStatus(UserStatus::SETTINGS_CITY_NAME);

            $this->getBot()->sendMessageWithKeyboard(
                $this->text['request_to_write_city'],
                new ReplyKeyboardMarkup([
                    [$this->text['back']]
                ], false, true,
                )
            );
        }
    }

}
