<?php

namespace App\Services\WeatherService\Templates;

use App\Models\WeatherCache;

abstract class BaseTemplate
{

    protected int $startDate;
    protected int $endDate;

    abstract static function getTemplate(WeatherCache $weatherCache);

    protected static function buildOneDayData(array $data): array
    {
        $icons = self::getProperty($data['weather'][0]['id'])['icon'];

        return [
            'date'       => date('h:i'),
            'weather_id' => $data['weather'][0]['id'],
            'icon'       => $icons[array_rand($icons)] ?: '',
            'temp'       => round($data['main']['temp']),
            'desc'       => $data['weather'][0]['description'],
            'wind_speed' => $data['wind']['speed'],
            'pressure'   => $data['main']['pressure'],
            'humidity'   => $data['main']['humidity'],
            'clouds'     => $data['clouds']['all'],
            'sunset'     => date('H:i', $data['sys']['sunset']),
            'sunrise'    => date('H:i', $data['sys']['sunrise']),
        ];
    }

    private static function getProperty(int $weatherId): array
    {
        $weatherProperties = include(__DIR__ . '/../../../config/weather_properties.php');
        return $weatherProperties[$weatherId];
    }

    public function setEndDate(int $endDate): BaseTemplate
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function setStartDate(int $startDate): BaseTemplate
    {
        $this->startDate = $startDate;
        return $this;
    }

}
