<?php

use App\Http\Controllers\Telegram\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'hello';
});

Route::post('/'. env('TELEGRAM_BOT_TOKEN') .'/webhook', [WebhookController::class, 'handle']);
