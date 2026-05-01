<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['title', 'slug', 'details','pos','meta_keywords','meta_descriptions', 'show_in_menu'];
    public $timestamps = false;
}
