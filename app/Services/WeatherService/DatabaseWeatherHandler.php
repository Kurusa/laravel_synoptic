<?php

namespace App\Services\WeatherService;

use Carbon\Carbon;
use CurrentWeather;

class DatabaseWeatherHandler
{

    const CACHE_TIME = [
        WeatherManager::WEATHER_MODE_WEEKLY       => 90,
        WeatherManager::WEATHER_MODE_COO          => 90,
        WeatherManager::WEATHER_MODE_CURRENT      => 10,
        WeatherManager::WEATHER_MODE_INLINE_QUERY => 10,
    ];

    const OWM_FUNCTION = [
        WeatherManager::WEATHER_MODE_COO          => 'WEATHER',
        WeatherManager::WEATHER_MODE_WEEKLY       => 'FORECAST',
        WeatherManager::WEATHER_MODE_CURRENT      => 'WEATHER',
        WeatherManager::WEATHER_MODE_INLINE_QUERY => 'WEATHER',
    ];

    const OWM_RESOURCE = [
        WeatherManager::WEATHER_MODE_CURRENT      => CurrentWeather::class,
    ];

    public function getWeather(
        int $cityId,
        string $mode,
        $date = null,
    ): array
    {
        $date = $date ?: Carbon::today();
        $owmApi = new OwmApiService();

        $owmData = $owmApi->call(static::OWM_FUNCTION[$mode], [
            'id' => $cityId,
        ]);

        $sinoptikCallback = null;
        if ($mode !== WeatherManager::WEATHER_MODE_INLINE_QUERY) {
            $sinoptikCallback = json_encode(ParserSinoptik::parse($cityId, $date->timestamp ?: time()), true);
        }

        (self::OWM_RESOURCE[$mode])
        return (self::OWM_RESOURCE[$mode])::toArray([
            $owmData,
            $sinoptikCallback,
        ]);
    }

    public function getWeatherByCoo(array $coo, string $mode): array
    {
        $owmApi = new OwmApiService();

        return $owmApi->call(strtolower(static::OWM_FUNCTION[$mode]), [
            'lat' => $coo['latitude'],
            'lon' => $coo['longitude']
        ]);
    }

}
