<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpiConfig extends Model
{
    use HasFactory;
    protected $fillable = ['enabled', 'merchant_id', 'endpoint'];
    protected $casts = [
        'enabled' => 'boolean',
    ];
}
