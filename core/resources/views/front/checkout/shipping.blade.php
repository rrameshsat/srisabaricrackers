@extends('master.front')

@section('title')
    {{ __('Shipping') }}
@endsection
@section('content')
    <!-- Page Title-->
    <div class="page-title">
        <div class="container">
            <div class="column">
                <ul class="breadcrumbs">
                    <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a> </li>
                    <li class="separator"></li>
                    <li>{{ __('Shipping address') }}</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- Page Content-->
    <div class="container padding-bottom-3x mb-1  checkut-page">
        <div class="row">
            <!-- Shipping Adress-->
            <div class="col-xl-9 col-lg-8">
                <div class="steps flex-sm-nowrap mb-5">
                    <a class="step" href="{{ route('front.checkout.billing') }}">
                        <h4 class="step-title">1. {{ __('Billing Address') }}:</h4>
                    </a>
                    <a class="step active" href="javascript:;">
                        <h4 class="step-title">2. {{ __('Shipping Address') }}:</h4>
                    </a>
                    <a class="step" href="{{ route('front.checkout.payment') }}">
                        <h4 class="step-title">3. {{ __('Review and pay') }}</h4>
                    </a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6>{{ __('Shipping Address') }}</h6>

                        <form id="checkoutShipping" action="{{ route('front.checkout.shipping.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout-fn">{{ __('First Name') }}*</label>
                                        <input class="form-control {{ $errors->has('ship_first_name') ? 'requireInput' : '' }}" name="ship_first_name" type="text" id="checkout-fn"
                                            value="{{ isset($user) ? $user->first_name : '' }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout-ln">{{ __('Last Name') }}*</label>
                                        <input class="form-control {{ $errors->has('ship_last_name') ? 'requireInput' : '' }}" name="ship_last_name" type="text" id="checkout-ln"
                                            value="{{ isset($user) ? $user->last_name : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout-email">{{ __('E-mail Address') }}*</label>
                                        <input class="form-control {{ $errors->has('ship_email') ? 'requireInput' : '' }}" name="ship_email" type="email" id="checkout-email"
                                            value="{{ isset($user) ? $user->email : '' }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout-phone">{{ __('Phone Number') }}*</label>
                                        <input class="form-control {{ $errors->has('ship_phone') ? 'requireInput' : '' }}" name="ship_phone" type="text" id="checkout-phone"
                                            value="{{ isset($user) ? $user->phone : '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="checkout-address1">{{ __('Address') }} *</label>
                                        <input class="form-control {{ $errors->has('ship_address1') ? 'requireInput' : '' }}" name="ship_address1"  type="text"
                                            id="checkout-address1" value="{{ isset($user) ? $user->ship_address1 : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout-zip">{{ __('Zip Code') }} *</label>
                                        <input class="form-control {{ $errors->has('ship_zip') ? 'requireInput' : '' }}" name="ship_zip" type="text" id="checkout-zip"
                                            value="{{ isset($user) ? $user->ship_zip : '' }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout-city">{{ __('City') }} *</label>
                                        <input class="form-control {{ $errors->has('ship_city') ? 'requireInput' : '' }}" name="ship_city" type="text"
                                            id="checkout-city" value="{{ isset($user) ? $user->ship_city : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="checkout-country">{{ __('Country') }}</label>
                                        <select class="form-control" name="ship_country"  id="shipping-country">
                                            <option selected>{{ __('Choose Country') }}</option>
                                            @foreach (DB::table('countries')->get() as $country)
                                                <option value="{{ $country->name }}" data-country-id="{{ $country->id }}"
                                                    {{ isset($user) && $user->ship_country == $country->name ? 'selected' : '' }}>
                                                    {{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12" id="shipping-state-block" style="display:none;">
                                    <div class="form-group">
                                        <label for="shipping-state">{{ __('State (include tax) : Select Shipping State') }}</label>
                                        <select class="form-control" name="ship_state_id" id="shipping-state">
                                            <option value="">{{ __('Select Shipping State') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="d-flex justify-content-between paddin-top-1x mt-4">
                                <a class="btn btn-primary btn-sm" href="{{ route('front.cart') }}"><span
                                        class="hidden-xs-down"><i class="icon-arrow-left"></i>
                                        {{ __('Back To Cart') }}</span>
                                </a><button class="btn btn-primary  btn-sm" type="submit"><span
                                        class="hidden-xs-down">{{ __('Continue') }}</span><i
                                        class="icon-arrow-right"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Sidebar  -->
            <div class="col-xl-3 col-lg-4">
                @include('includes.checkout_sitebar', $cart)
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function($){
        function loadStatesForCountry($countrySel, $stateGroupSel, $stateSel) {
            var $selectedOption = $($countrySel).find('option:selected');
            var countryId = $selectedOption.data('country-id');
            if (!countryId) {
                $stateGroupSel && $stateGroupSel.hide();
                $stateSel && $stateSel.html('<option value="">{{ __('Select State') }}</option>');
                return;
            }
            $.ajax({
                url: '/country-states/' + countryId,
                method: 'GET',
                success: function(data){
                    var $state = $(stateSel);
                    $state.empty();
                    $state.append('<option value="">{{ __('Select State') }}</option>');
                    $.each(data, function(i, s){
                        var selected = '';
                        if (typeof window.userStateId !== 'undefined' && window.userStateId == s.id) {
                            selected = 'selected';
                        }
                        $state.append('<option value="' + s.id + '" ' + selected + '>' + s.name + '</option>');
                    });
                    $stateGroupSel && $stateGroupSel.show();
                },
                error: function(){
                    $stateGroupSel && $stateGroupSel.hide();
                }
            });
        }

        $(function(){
            // Initialize for shipping
            var $countryShipping = $('#shipping-country');
            window.userStateId = {{ isset($user) ? $user->state_id : 'null' }};
            loadStatesForCountry($countryShipping, $('#shipping-state-block'), $('#shipping-state'));
            $countryShipping.change(function(){ loadStatesForCountry(this, $('#shipping-state-block'), $('#shipping-state')); });
            if ($countryShipping.find('option[value="India"]').length > 0 && $countryShipping.val() === '') {
                $countryShipping.val('India').trigger('change');
            }
        });
    })(jQuery);
</script>
@endpush
