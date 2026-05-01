<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
        'photo',
        'email_token',
        'ship_address1',
        'ship_address2',
        'ship_zip',
        'ship_city',
        'ship_country',
        'ship_company',
        'bill_address1',
        'bill_address2',
        'bill_zip',
        'bill_city',
        'bill_country',
        'bill_company',
        'bill_state_id',
        'state_id',
        'ship_state_id',
        'email_verify'

    ];


    protected $hidden = [
        'password'
    ];

    public function state()
    {
        // Deprecated: kept for backward compatibility if any code still uses it for primary state
        return $this->belongsTo('App\Models\State')->withDefault();
    }

    public function shippingState()
    {
        return $this->belongsTo('App\Models\State', 'ship_state_id')->withDefault();
    }

    public function billingState()
    {
        return $this->belongsTo('App\Models\State', 'bill_state_id')->withDefault();
    }

    public function products()
    {
        return $this->hasMany('App\Models\Item','vendor_id')->orderby('id','desc');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

    public function wishlists()
    {
        return $this->hasMany('App\Models\Wishlist');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\Review');
    }

    public function notifications()
    {
        return $this->hasMany('App\Models\Notification');
    }

    public function socialProviders()
    {
        return $this->hasMany('App\Models\SocialProvider');
    }

    public function withdraws()
    {
        return $this->hasMany('App\Models\Withdraw','vendor_id')->orderby('id','desc');
    }

    public function displayName()
    {
        return $this->first_name.' '.$this->last_name;
    }


    public function seller()
    {
        return $this->hasOne('App\Models\Seller');
    }


    public function wishlistCount()
    {
        return $this->wishlists()->whereHas('item', function($query) {
                    $query->where('status', '=', 1);
                })->count();
    }

}
