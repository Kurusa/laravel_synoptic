<?php

namespace App\Http\Controllers\Telegram\Weather;

use App\Services\WeatherService\{
    Templates\Current,
    WeatherManager,
};
use App\Models\City;
use App\Services\LocationSearch\TgLocation;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use App\Http\Controllers\Telegram\BaseCommand;

class CurrentLess extends BaseCommand
{

    function processCommand($param = null)
    {
        if ($this->update->getMessage()->getText()) {
            $exploded = explode(',', $this->update->getMessage()->getText());

            $city = City::where('title', $exploded[0] ?: $this->update->getMessage()->getText())->first();
            $weatherManager = new WeatherManager();
        return
            $this->getBot()->sendText(json_encode($weatherManager->getCurrentWeather($city->city_id)));

            $this->getBot()->sendMessageWithKeyboard(
                Current::getTemplate($weatherManager->getCurrentWeather($city->city_id)),
                new InlineKeyboardMarkup([
                    [[
                        'text'          => $this->text['generate_image'],
                        'callback_data' => json_encode([
                            'a'      => 'generate_current_image',
                            'cityId' => $param,
                        ])
                    ]]
                ]),
            );
        } elseif ($this->update->getMessage()->getLocation()) {
            $buttons = TgLocation::getSearchResult($this->update->getMessage()->getLocation());
            if ($buttons) {
                $this->selectingLocationFlow($buttons);
            } else {
                $this->notifyAboutCantFindCity();
            }
        }
    }

}
