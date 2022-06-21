<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{
    Model,
    Relations\BelongsTo,
};

class City extends Model {

    protected $table = 'cities';

    protected $fillable = [
        'district_id',
        'city_id',
        'title',
        'weight',
    ];

    public $with = ['district'];

    public $timestamps = false;

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function getFullTitleAttribute(): string
    {
        return $this->title . ', ' . $this->district->title . ' область';
    }

}
