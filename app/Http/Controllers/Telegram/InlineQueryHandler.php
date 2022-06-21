<?php

namespace App\Http\Controllers\Telegram;

use App\Services\LocationSearch\CityName;
use App\Services\WeatherService\WeatherManager;
use TelegramBot\Api\Types\Inline\QueryResult\Article;

class InlineQueryHandler extends BaseCommand
{

    function processCommand($param = null)
    {
        $weatherManager = new WeatherManager();

        $search = new CityName();
        $search->setTitle($this->update->getInlineQuery()->getQuery());

        $desc = '';
        $inputList = [];
        $replyList = [];
        $cities = array_slice($search->getCityButtons(), 0, 45);
        foreach ($cities as $key => $city) {
            $city_name = explode(', ', $city[0])[0];
            $city_data = \App\Models\City::where('title', $city_name)->get();

            if (sizeof($cities) <= 5) {
                $weatherData = $weatherManager->getInlineQueryWeather($city_data[0]->city_id);
                $weatherData = json_decode($weatherData['owm_callback'], true);
                $icons = $this->getProperty($weatherData['weather'][0]['id'])['icon'];
                $rand_key = array_rand($icons, 1);
                $desc = ($icons[$rand_key] ?: '') . ' ' . round($weatherData['main']['temp']) . '° ' . $weatherData['weather'][0]['description'];
            }
            $inputList[] = $city[0];
            $replyList[] = new Article(
                $key,
                $city[0],
                $desc
            );
        }

        $this->getBot()->answerInlineQuery($inputList, $this->update->getInlineQuery()->getId(), $replyList, 1);
    }

    protected function getProperty(int $weatherId): array
    {
        $weatherProperties = include(__DIR__ . '/../config/weather_properties.php');
        return $weatherProperties[$weatherId];
    }

}
