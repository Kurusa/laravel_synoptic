<?php

// TODO: rewrite service

namespace App\Services\WeatherService;

use App\Models\City;
use Goutte\Client;

class ParserSinoptik
{

    public static function parse(int $cityId, int $date): array
    {
        $city = City::where('city_id', $cityId)->first();

        $result = [];
        $url = 'https://ua.sinoptik.ua/погода-';
        $url .= str_replace(' ', '-', mb_strtolower($city->title)) . '/';
        $url .= date('Y-m-d', $date);

        $client = new Client();
        $crawler = $client->request('GET', $url);

        for ($i = 0; $i <= 7; $i++) {
            // 0:00, 2:00...
            $time = str_replace(' ', '', trim($crawler->filter('.gray.time > td')->getNode($i)->textContent));
            if ($time) {
                if (strlen($time) == 4) {
                    $result[$i]['time'] = '0';
                }

                $result[$i]['time'] .= $time;
                $result[$i]['desc'] = mb_strtolower($crawler->filter('.img.weatherIcoS > td')->eq($i)->first()->filter('.weatherIco')->attr('title'));
                $result[$i]['humidity'] = $crawler->filter('tr')->eq(6)->filter('td')->eq($i)->text();
                $result[$i]['rains'] = $crawler->filter('tr')->eq(8)->filter('td')->eq($i)->text();
            }
        }
        $result['description'] = $crawler->filter('.weatherIco')->getNode(1)->getAttribute('title');
        $result['detail'] = $crawler->filter('.wDescription > .rSide')->first()->text();

        return $result;
    }

}
