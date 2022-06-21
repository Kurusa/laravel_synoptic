<?php

namespace App\Http\Controllers\Telegram;

use App\Services\Status\UserStatus;
use App\Models\User;
use App\Utils\Update;

abstract class BaseCommand extends BasicMessages
{
    protected User $user;

    protected $botUser;

    protected array $text;

    protected Update $update;

    function handle($param = null)
    {
        $this->text = config('texts');

        $this->user = User::where('chat_id', $this->botUser->getId())->firstOr(function () {
            $this->user = User::create([
                'chat_id'    => $this->botUser->getId(),
                'user_name'  => $this->botUser->getUsername(),
                'first_name' => $this->botUser->getFirstName(),
                'status'     => UserStatus::NEW,
            ]);

            $this->notifyAboutNewUser();
        });

        $this->processCommand($param);
    }

    function triggerCommand($class, $param = null)
    {
        (new $class($this->update))->handle($param);
    }

    abstract function processCommand($param = null);
}
