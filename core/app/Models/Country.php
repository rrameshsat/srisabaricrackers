<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name','iso_code','status'];
    public $timestamps = true;

    public function states()
    {
        return $this->hasMany(State::class);
    }
}
