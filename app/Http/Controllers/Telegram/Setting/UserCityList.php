<?php

namespace App\Http\Controllers\Telegram\Setting;

use App\Http\Controllers\Telegram\BaseCommand;
use App\Models\City;
use App\Services\Status\UserStatus;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class UserCityList extends BaseCommand
{

    function processCommand($param = null)
    {
        if ($this->user->checkStatus(UserStatus::USER_CITY_LIST)) {
            $exploded = explode(',', $this->update->getMessage()->getText());
            $city = City::where('title', $exploded[0])->first();
            if ($city) {
                $this->user->cities()->where('city_id', $city->id)->delete();
                $this->triggerCommand(CityMenu::class);
            }
        } else {
            $this->user->setStatus(UserStatus::USER_CITY_LIST);

            foreach ($this->user->cities as $city) {
                $cities[] = [$city->city->full_title];
            }
            $cities[] = [$this->text['back']];

            $this->getBot()->sendMessageWithKeyboard(
                $this->text['my_cities_info'],
                new ReplyKeyboardMarkup($cities, false, true)
            );
        }
    }

}
