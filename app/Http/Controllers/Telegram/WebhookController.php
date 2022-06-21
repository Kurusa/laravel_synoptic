<?php

namespace App\Http\Controllers\Telegram;

use App\Models\User;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Update;

class WebhookController
{
    public function handle(): void
    {
        $client = new Client(getenv('TELEGRAM_BOT_TOKEN'));

        $client->on(function (Update $update) {
            if ($update->getCallbackQuery()) {
                $action = \json_decode($update->getCallbackQuery()->getData(), true)['a'];

                if (isset(config('telegram.callback')[$action])) {
                    $handlerClassName = config('telegram.callback')[$action];
                }
            } elseif ($update->getMessage()->getText()) {
                $text = $update->getMessage()->getText();
                if (str_starts_with($text, '/')) {
                    if (isset(config('telegram.slash')[$text])) {
                        $handlerClassName = config('telegram.slash')[$text];
                    }
                }

                if ($key = $this->processKeyboardCommand($text)) {
                    if (isset(config('telegram.keyboard')[$key])) {
                        $handlerClassName = config('telegram.keyboard')[$key];
                    }
                }
            }

            if (!isset($handlerClassName)) {
                $user = User::where('chat_id', $update->getMessage()->getFrom()->getId())->first();
                if (isset(config('telegram.status')[$user->status])) {
                    $handlerClassName = config('telegram.status')[$user->status];
                }
            }

            (new ($handlerClassName ?? MainMenu::class) (new \App\Utils\Update($update)))->handle();
        }, function (Update $update) {
            return $update->getMessage() !== null || $update->getCallbackQuery() !== null;
        });

//        $client->on(function (Update $update) {
//            (new InlineQueryHandler(new \App\Utils\Update($update)))->handle();
//            return true;
//        }, function (Update $update) {
//            return $update->getInlineQuery() !== null;
//        });

        $client->run();
    }

    protected function processKeyboardCommand(string $text): ?string
    {
        $translations = \array_flip(config('texts'));
        if (isset($translations[$text])) {
            return $translations[$text];
        }

        return null;
    }
}
