<?php

namespace App\Services\WeatherService\Templates;

use App\Models\WeatherCache;
use App\Utils\Twig;

class Forecast extends BaseTemplate
{

    public function getTemplate(
        WeatherCache $weatherCache,
        bool $fullWeather = false,
    ): string {
        $week_array = [
            'Monday' => 'Пн',
            'Tuesday' => 'Вт',
            'Wednesday' => 'Ср',
            'Thursday' => 'Чт',
            'Friday' => 'Пт',
            'Saturday' => 'Сб',
            'Sunday' => 'Нд',
        ];
        $month_array = [
            1 => 'січня',
            2 => 'лютого',
            3 => 'березня',
            4 => 'квітня',
            5 => 'травня',
            6 => 'червня',
            7 => 'липня',
            8 => 'серпня',
            9 => 'вересеня',
            10 => 'жовтеня',
            11 => 'листопада',
            12 => 'груденя',
        ];

        $sinoptikData = json_decode($weatherCache['sinoptik_callback'], true);
        $weatherData = json_decode($weatherCache['owm_callback'], true);

        $result['sinoptik'] = $sinoptikData;
        $result['city']    = $weatherCache['city']->title;
        $result['sunset']  = date('H:i', $weatherData['city']['sunset']);
        $result['sunrise'] = date('H:i', $weatherData['city']['sunrise']);
        $result['date']    = $week_array[date('l', $this->startDate)] . ', ' . date('d', $this->startDate) . ' ' . $month_array[date('n', $this->startDate)];

        $temperatures = [];
        foreach ($weatherData['list'] as $key => $weatherItem) {
            if ($weatherItem['dt'] >= $this->startDate && $weatherItem['dt'] <= $this->endDate) {
                $temperatures[] = round($weatherItem['main']['temp']);
                
                $result['data'][$key] = $this->buildOneDayData($weatherItem);
                
                $result['data'][$key]['date'] = date('H:i', $weatherItem['dt']);
                $result['data'][$key]['temp'] = round($weatherItem['main']['temp']);
                $result['data'][$key]['desc'] = $weatherItem['weather'][0]['description'];

                $result['data'][$key]['wind_speed'] = round($weatherItem['wind']['speed']);
                $result['data'][$key]['clouds'] = $weatherItem['clouds']['all'];
                $result['data'][$key]['pressure'] = $weatherItem['main']['pressure'];
            }
        }
        $result['min'] = min($temperatures);
        $result['max'] = max($temperatures);

        foreach ($result['data'] as $key => $item) {
            foreach ($sinoptikData as $sinoptik) {
                if (isset($sinoptik['time'])) {
                    if ($sinoptik['time'] == $item['date']) {
                        $result['data'][$key]['desc'] = $sinoptik['desc'];
                    }
                }
            }
        }

        return Twig::getInstance()->load('day_forecast.twig')->render([
            'weather'      => $result,
            'full_weather' => $fullWeather,
        ]);
    }

}
