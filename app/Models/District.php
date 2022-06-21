<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{
    Model,
    Relations\HasMany,
};

class District extends Model {

    protected $table = 'districts';

    protected $fillable = [
        'title',
        'selected_title',
    ];

    public $timestamps = false;

    public function cities(): HasMany
    {
        return $this->hasMany(City::class)->orderBy('weight', 'desc');
    }

}
