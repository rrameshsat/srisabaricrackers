<?php

namespace App\Http/Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Return states for a given country as JSON.
     */
    public function states(Country $country)
    {
        // Eager load only id & name for dropdowns
        $states = $country->states()->orderBy('name')->get(['id', 'name']);
        return response()->json($states);
    }
}
