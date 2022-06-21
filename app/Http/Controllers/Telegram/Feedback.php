<?php

namespace App\Http\Controllers\Telegram;

use App\Services\Status\UserStatus;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class Feedback extends BaseCommand
{
    function processCommand($param = false)
    {
        if ($this->user->checkStatus(UserStatus::FEEDBACK)) {
            $this->user->feedbacks()->create([
                'text' => $this->update->getMessage()->getText(),
            ]);

            $this->getBot()->sendText($this->text['message_sent']);
            $this->triggerCommand(MainMenu::class);
        } else {
            $this->user->setStatus(UserStatus::FEEDBACK);

            $this->getBot()->sendMessageWithKeyboard(
                $this->text['pre_send_feedback'],
                new ReplyKeyboardMarkup(
                    [[$this->text['back']]],
                    false, true,
                )
            );
        }
    }
}
