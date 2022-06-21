<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{
    Model,
    Relations\HasMany,
};

class User extends Model {

    protected $table = 'users';

    protected $fillable = [
        'is_blocked',
        'user_name',
        'first_name',
        'chat_id',
        'status',
    ];

    public function setStatus(string $status)
    {
        $this->update(['status' => $status]);
        $this->refresh();
    }

    public function checkStatus(string $status): bool
    {
        return $this->status === $status;
    }

    public function cities(): HasMany
    {
        return $this->hasMany(UserCity::class);
    }

    public function draftCityEntity()
    {
        return $this->cities()->where('city_id')->first();
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

}
