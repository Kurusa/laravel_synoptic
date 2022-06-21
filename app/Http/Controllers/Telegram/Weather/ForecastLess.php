<?php

namespace App\Http\Controllers\Telegram\Weather;

use App\Http\Controllers\Telegram\BaseCommand;
use App\Models\City;
use App\Services\LocationSearch\TgLocation;
use App\Services\WeatherService\Templates\Forecast;
use App\Services\WeatherService\WeatherManager;
use Carbon\Carbon;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class ForecastLess extends BaseCommand
{

    function processCommand($param = null)
    {
        if ($this->update->getMessage()->getText()) {
            $exploded = explode(',', $this->update->getMessage()->getText());

            $cityId = City::where('title', $exploded[0] ?: $this->update->getMessage()->getText())->first()->city_id;

            $callbackData = null;

            if ($this->update->getCallbackQuery()) {
                $nextDay = $this->update->getCallbackQueryByKey('day');
                $cityId = $callbackData['id'];
            } else {
                $nextDay = date('H') > 12 ? 1 : 0;
            }

            $text = $this->getWeatherMessage($cityId, $nextDay);
            if ($text) {
                $buttons = [];

                if ($nextDay === 0) {
                    $arrow[] = [
                        'text' => $this->text['next'],
                        'callback_data' => json_encode([
                            'a' => 'weather_next_less',
                            'day' => $nextDay + 1,
                            'id' => $cityId,
                        ])
                    ];
                } elseif ($nextDay <= 4) {
                    $arrow[] = [
                        'text' => $this->text['prev'],
                        'callback_data' => json_encode([
                            'a'   => 'weather_prev_less',
                            'day' => $nextDay - 1,
                            'id'  => $cityId,
                        ])
                    ];
                } elseif ($nextDay === 1) {
                    $arrow[] = [
                        'text' => $this->text['next'],
                        'callback_data' => json_encode([
                            'a'   => 'weather_next_less',
                            'day' => $nextDay + 1,
                            'id'  => $cityId,
                        ])
                    ];
                }

                $buttons[] = [[
                    'text' => $this->text['moreInfo'],
                    'callback_data' => json_encode([
                        'a'   => 'weather_more',
                        'id'  => $cityId,
                        'day' => $nextDay,
                    ])
                ]];
                $buttons[] = $arrow;

                if ($callbackData) {
                    $this->getBot()->editMessageText(
                        $this->user->chat_id,
                        $this->update->getCallbackQuery()->getMessage()->getMessageId(),
                        $text,
                        'html', true,
                        new InlineKeyboardMarkup($buttons)
                    );
                } else {
                    $this->getBot()->sendMessageWithKeyboard(
                        $text,
                        new InlineKeyboardMarkup($buttons),
                        $this->update->getMessage()->getMessageId()
                    );
                }
            }
        } elseif ($this->update->getMessage()->getLocation()) {
            $buttons = TgLocation::getSearchResult($this->update->getMessage()->getLocation());
            if ($buttons) {
                $this->selectingLocationFlow($buttons);
            } else {
                $this->notifyAboutCantFindCity();
            }
        }
    }

    private function getWeatherMessage(int $cityId, $day = 0)
    {
        $weatherManager = new WeatherManager();
        $weatherCache = $weatherManager->getWeeklyWeather($cityId, Carbon::now()->addDays($day)->startOfDay());

        $template = new Forecast();
        $template
            ->setStartDate(Carbon::now()->addDays($day)->startOfDay()->timestamp)
            ->setEndDate(Carbon::now()->addDays($day)->endOfDay()->timestamp);
        return $template->getTemplate($weatherCache);
    }

}
