<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = ['title', 'logo', 'photo','link','details','home_page','text_color','title_color','text_position'];
    public $timestamps = false;
}
