<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    Pivot,
};

class UserCity extends Pivot {

    protected $table = 'user_cities';

    protected $fillable = [
        'user_id',
        'district_id',
        'city_id',
    ];

    public $timestamps = false;

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
