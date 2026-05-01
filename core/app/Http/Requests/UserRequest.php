<?php

namespace App\Http\Requests;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $id = Auth::check() ? ',' . Auth::user()->id : '';
        $setting = Setting::first();
        $password = Auth::check() ? '' : 'required|';
        $check = Auth::check() ? 'nullable|min:6|max:16' : "min:6|max:16|confirmed";

        $recaptcha = $setting->recaptcha == 1 && !Auth::check() ? 'required|captcha' : 'nullable';

        return [
            'g-recaptcha-response' => $recaptcha,
            'first_name' => $password.'|max:255',
            'photo'      => [
            'mimes:jpeg,jpg,png,svg', 
                function ($attribute, $value, $fail) {
                    $allowedExtensions = ['jpeg', 'jpg', 'png', 'svg'];
                    if ($value->isValid() && $value->isFile()) {
                        if (!in_array($value->getMimeType(), ['image/jpeg', 'image/jpg', 'image/png', 'image/svg+xml'])) {
                            return $fail("The $attribute must be a valid image jpeg, jpg, png, svg.");
                        }
                        $extension = strtolower($value->getClientOriginalExtension());
                        if (!in_array($extension, $allowedExtensions)) {
                            return $fail("The $attribute must be a valid image jpeg, jpg, png, svg.");
                        }
                    } else {
                        return $fail("The $attribute must be a valid image jpeg, jpg, png, svg.");
                    }
                },
                'max:2048'
            ],
            'last_name'  => 'required|max:255',
            'phone'      => 'required|max:255',
            'bill_address1' => 'required|max:100',
            'bill_address2' => 'nullable|max:100',
            'bill_zip' => 'nullable|max:100',
            'bill_city' => 'required|max:100',
            'bill_country' => 'required|max:100',
            'bill_state_id' => [
                function ($attribute, $value, $fail) {
                    $india = \DB::table('countries')->where('name','India')->first();
                    if ($india) {
                        $stateCount = \DB::table('states')->where('country_id', (int)$india->id)->count();
                        if ($stateCount > 0 && empty($value)) {
                            $fail('The Billing State field is required.');
                        }
                        if (!empty($value)) {
                            $exists = \DB::table('states')->where('id', $value)->where('country_id', $india->id)->exists();
                            if (!$exists) $fail('The selected Billing State is invalid.');
                        }
                    }
                }
            ],
            // Phase 3: bill_state_id is the exclusive billing state field.
            // No separate billing state_id rule (Phase 3)
            // Shipping fields on registration
            'ship_address1' => 'required|max:100',
            'ship_address2' => 'nullable|max:100',
            'ship_zip' => 'nullable|max:100',
            'ship_city' => 'required|max:100',
            'ship_country' => 'required|max:100',
            // ship_state_id will be validated in a conditional rule if India has states
            'ship_state_id' => [
                function ($attribute, $value, $fail) {
                    $india = \DB::table('countries')->where('name','India')->first();
                    if ($india) {
                        $stateCount = \DB::table('states')->where('country_id',$india->id)->count();
                        if ($stateCount > 0 && empty($value)) {
                            $fail('The Shipping State field is required.');
                        }
                        if (!empty($value)) {
                            $exists = \DB::table('states')->where('id',$value)->where('country_id',$india->id)->exists();
                            if (!$exists) $fail('The selected Shipping State is invalid.');
                        }
                    }
                }
            ],
            'bill_state_id' => [
                function ($attribute, $value, $fail) {
                    $india = \DB::table('countries')->where('name','India')->first();
                    if ($india) {
                        $count = \DB::table('states')->where('country_id',$india->id)->count();
                        if ($count > 0 && empty($value)) {
                            $fail('The Billing State field is required.');
                        }
                        if (!empty($value)) {
                            $exists = \DB::table('states')->where('id',$value)->where('country_id',$india->id)->exists();
                            if (!$exists) $fail('The selected Billing State is invalid.');
                        }
                    }
                }
            ],
            'ship_address1' => 'required|max:100',
            'ship_address2' => 'nullable|max:100',
            'ship_zip' => 'nullable|max:100',
            'ship_city' => 'required|max:100',
            'ship_country' => 'required|max:100',
            'ship_state_id' => 'nullable',
            'email'      => Auth::guard('admin') ? 'required|email': 'required|email|unique:users,email'. $id,
            'password'   => $password.$check,
            'password_confirmation'   => $password,
            'honeypot'   => 'max:0',
        ];

    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'g-recaptcha-response.required' => __('Please verify that you are not a robot.'),
            'first_name.required' => __('First Name is required.'),
            'last_name.required' => __('Last Name field is required.'),
            'country.required' => __('Country is required.'),
            'city.required' => __('City is required.'),
            'address.required' => __('Address is required.'),
            'zip.required' => __('Zip Code is required.'),
            'phone.required' => __('Phone Number is required.'),
            'email.required' => __('Email field is required.'),
            'email.email'   => __('The email must be a valid email address.'),
            'password.required'    => __('Password field is required.'),
            'g-recaptcha-response.required' => __('Please verify that you are not a robot.'),
            'g-recaptcha-response.captcha' => __('Captcha error! try again later or contact site admin.'),
            'honeypot.max' => __('Please verify that you are not a robot.'),
        ];
    }
}
