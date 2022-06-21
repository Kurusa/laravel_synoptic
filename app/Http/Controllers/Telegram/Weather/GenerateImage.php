<?php

namespace App\Http\Controllers\Telegram\Weather;

use App\Http\Controllers\Telegram\BaseCommand;
use App\Services\WeatherService\WeatherManager;

class GenerateImage extends BaseCommand
{

    function processCommand($param = null)
    {
        $city_id = json_decode($this->update->getCallbackQuery()->getData(), true)['city_id'];
        $city = \App\Models\City::where('id', $city_id)->get();
        $weatherManager = new WeatherManager();
        $weatherData = $weatherManager->getCurrentWeather($city[0]->city_id);

        $weatherData = json_decode($weatherData['owm_callback'], true);

        $image_maker = new \App\Services\ImageMakers\ImageMakerBlack();
        $image_maker->setDefaultFontfile($image_maker::MONTSERRAT_LIGHT);
        $image_maker->setImage();
        $image_maker->setWeatherData([
            'cityName' => $city[0]->title,
            'weatherDesc' => $weatherData['weather'][0]['description'],
            'currentTemp' => round($weatherData['main']['temp']),
            'weatherId' => $weatherData['weather'][0]['id'],
            'sunrise' => $weatherData['sys']['sunrise'],
            'sunset' => $weatherData['sys']['sunset'],
            'windSpeed' => $weatherData['wind']['speed'],
            'clouds' => $weatherData['clouds']['all'],
        ]);
        $image_maker->constructImage();
        $this->getBot()->sendPhoto($this->user->chat_id, new \CURLFile($image_maker->getImage()), null, $this->update->getCallbackQuery()->getMessage()->getMessageId());
    }

}
