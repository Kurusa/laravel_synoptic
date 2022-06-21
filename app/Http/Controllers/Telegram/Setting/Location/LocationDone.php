<?php

namespace App\Http\Controllers\Telegram\Setting\Location;

use App\Http\Controllers\Telegram\BaseCommand;
use App\Http\Controllers\Telegram\MainMenu;
use App\Models\City;
use Illuminate\Database\QueryException;

class LocationDone extends BaseCommand
{

    function processCommand($param = null)
    {
        if ($this->update->getCallbackQuery()) {
            $districtId = $this->update->getCallbackQueryByKey('d_id');
            $cityId = $this->update->getCallbackQueryByKey('id');

            try {
                $this->user->cities()->create([
                    'district_id' => $districtId,
                    'city_id'     => $cityId,
                ]);
            } catch (QueryException $e) {
                return $this->getBot()->sendText($this->text['you_already_have_this_city']);
            }

            $this->getBot()->deleteMessage(
                $this->user->chat_id,
                $this->update->getCallbackQuery()->getMessage()->getMessageId(),
            );
        } else {
            // Here are two cases. First is when city is selected from list,
            // Second is when user select specified city
            $exploded = explode(',', $this->update->getMessage()->getText());

            $city = City::where('title', $exploded[0] ?: $this->update->getMessage()->getText())->first();
            if (!$city) {
                return $this->notifyAboutCantFindCity();
            }

            if ($this->user->draftCityEntity()) {
                $this->user->draftCityEntity()->update([
                    'city_id' => $city->id,
                ]);
            } else {
                try {
                    $this->user->cities()->create([
                        'district_id' => $city->district->id,
                        'city_id'     => $city->id,
                    ]);
                } catch (QueryException $e) {
                    return $this->getBot()->sendText($this->text['you_already_have_this_city']);
                }
            }
        }

        $this->getBot()->sendText($this->text['saved_your_city']);
        $this->triggerCommand(MainMenu::class);
    }

}
