<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Telegram\Setting\CityMenu;
use App\Services\Status\UserStatus;

class Back extends BaseCommand
{
    function processCommand($param = null)
    {
        switch ($this->user->status) {
            case UserStatus::SETTINGS_CITY_NAME:
            case UserStatus::SETTINGS_DISTRICT_SELECT:
            case UserStatus::SETTINGS_LOCATION_WAITING:
            case UserStatus::SETTINGS_LOCATION_SELECTING:
                $this->triggerCommand(Setting\Location\SelectLocationType::class);
                break;
            case UserStatus::USER_CITY_LIST:
            case UserStatus::SETTINGS_LOCATION_TYPE_SELECT:
                $this->triggerCommand(CityMenu::class);
                break;
            case UserStatus::CITY_MENU:
            case UserStatus::DONE:
            case UserStatus::FEEDBACK:
            case UserStatus::FORECAST_CITY_SELECT:
            case UserStatus::SETTINGS:
            case UserStatus::CURRENT_CITY_SELECT:
                $this->triggerCommand(MainMenu::class);
                break;
        }
    }
}
