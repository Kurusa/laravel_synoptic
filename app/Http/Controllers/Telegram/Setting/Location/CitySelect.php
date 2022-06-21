<?php

namespace App\Http\Controllers\Telegram\Setting\Location;

use App\Http\Controllers\Telegram\BaseCommand;
use App\Models\District;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class CitySelect extends BaseCommand
{

    function processCommand($param = null)
    {
        if ($this->update->getMessage()) {
            $district = District::where('title', $this->update->getMessage()->getText())->first();
        } else {
            $districtId = $this->update->getCallbackQueryByKey('d_id');
            $district = District::find($districtId);
        }

        $offset = $this->update->getCallbackQueryByKey('off') ?: 0;
        $limit = $district->cities->skip($offset)->count() > 30 ? 30 : $district->cities->skip($offset)->count();

        $keyboard = [];
        $buttons = [];
        foreach ($district->cities->skip($offset)->take($limit) as $key => $city) {
            $buttons[] = [
                'text' => $city->title,
                'callback_data' => json_encode([
                    'a'    => 'select_city',
                    'id'   => $city->id,
                    'd_id' => $district->id,
                ])
            ];
            if ($key % 2 == 0) {
                $keyboard[] = $buttons;
                $buttons = [];
            }
        }

        if ($district->cities->skip($offset)->count() > 30) {
            $keyboard[] = [[
                'text' => $this->text['next'],
                'callback_data' => json_encode([
                    'a'    => 'next_cities',
                    'off'  => $offset + 30,
                    'd_id' => $district->id,
                ])
            ]];
        }
        if ($offset > 0) {
            $keyboard[] = [[
                'text' => $this->text['prev'],
                'callback_data' => json_encode([
                    'a'    => 'prev_cities',
                    'off'  => $offset - 30,
                    'd_id' => $district->id,
                ])
            ]];
        }

        if ($this->update->getCallbackQuery()) {
            $this->getBot()->editMessageText(
                $this->user->chat_id,
                $this->update->getCallbackQuery()->getMessage()->getMessageId(),
                $this->text['select_city'],
                'html', true,
                new InlineKeyboardMarkup($keyboard),
            );
        } else {
            $this->getBot()->sendMessageWithKeyboard(
                $this->text['select_city'],
                new InlineKeyboardMarkup($keyboard),
            );
        }
    }

}
