<?php

namespace App\Services\WeatherService\Templates;

use App\Models\WeatherCache;
use App\Utils\Twig;

class Current extends BaseTemplate
{

    public static function getTemplate(WeatherCache $weatherCache): string
    {
        $sinoptikData = json_decode($weatherCache['sinoptik_callback'], true);
        $weatherData = json_decode($weatherCache['owm_callback'], true);

        $result = self::buildOneDayData($weatherData);

        $result['city']   = $weatherCache['city']->title;
        $result['detail'] = $sinoptikData['detail'];

        return Twig::getInstance()->load('current.twig')->render([
            'data' => $result,
        ]);
    }

}
