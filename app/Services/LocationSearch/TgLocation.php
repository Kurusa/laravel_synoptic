<?php

namespace App\Services\LocationSearch;

use App\Models\City;
use App\Services\WeatherService\WeatherManager;

class TgLocation
{

    static function getSearchResult($location): array
    {
        $weatherManager = new WeatherManager();
        $weatherData = $weatherManager->getWeatherByCoo([
            'longitude' => $location->getLongitude(),
            'latitude'  => $location->getLatitude(),
        ]);

        if (isset($weatherData['id'])) {
            return [[City::where('city_id', $weatherData['id'])->first()->full_title]];
        }

        return [];
    }

}
