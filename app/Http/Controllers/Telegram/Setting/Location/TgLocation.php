<?php

namespace App\Http\Controllers\Telegram\Setting\Location;

use App\Http\Controllers\Telegram\BaseCommand;
use App\Services\Status\UserStatus;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class TgLocation extends BaseCommand
{

    function processCommand($param = null)
    {
        if ($this->user->checkStatus(UserStatus::SETTINGS_LOCATION_WAITING)) {
            $buttons = \App\Services\LocationSearch\TgLocation::getSearchResult($this->update->getMessage()->getLocation());
            if ($buttons) {
                $this->user->update([
                    'status' => UserStatus::SETTINGS_LOCATION_SELECTING,
                ]);

                $this->selectingLocationFlow($buttons);
            } else {
                $this->notifyAboutCantFindCity();
            }
        } else {
            $this->user->setStatus(UserStatus::SETTINGS_LOCATION_WAITING);

            $this->getBot()->sendMessageWithKeyboard(
                $this->text['send_your_location'],
                new ReplyKeyboardMarkup([
                    [[
                        'text' => $this->text['click'],
                        'request_location' => true,
                    ]],
                    [$this->text['back']]
                ], false, true),
            );
        }
    }

}
