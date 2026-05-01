@extends('master.front')
@section('title')
    {{__('Address')}}
@endsection
@section('content')

    <!-- Page Title-->
<div class="page-title">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul class="breadcrumbs">
                    <li><a href="{{route('front.index')}}">{{__('Home')}}</a> </li>
                    <li class="separator"></li>
                    <li>{{__('Shipping - Billing Address')}}</li>
                 </ul>
            </div>
        </div>
    </div>
 </div>
 <!-- Page Content-->
 <div class="container padding-bottom-3x mb-1">
    <div class="row">
       @include('includes.user_sitebar')
       <div class="col-lg-8">
          <div class="card">
              <div class="card-body">
                <div class="padding-top-2x mt-2 hidden-lg-up"></div>
                <h5>{{__('Billing Address')}}</h5>
                <form id="billingForm" class="row" action="{{route('user.billing.submit')}}" method="POST">
                  @csrf
                   <div class="col-md-6">
                      <div class="form-group">
                         <label for="billing-address1">{{__('Address 1')}} *</label>
                         <input class="form-control" type="text" name="bill_address1" id="billing-address1" value="{{$user->bill_address1}}">
                      @error('bill_address1')
                      <p class="text-danger">{{$message}}</p>
                      @endif
                        </div>
                   </div>
                   <div class="col-md-6">
                      <div class="form-group">
                         <label for="billing-address2">{{__('Address 2')}}</label>
                         <input class="form-control" type="text" name="bill_address2" value="{{$user->bill_address2}}" id="billing-address2">
                         @error('bill_address2')
                         <p class="text-danger">{{$message}}</p>
                         @endif
                        </div>
                   </div>
                   <div class="col-md-6">
                      <div class="form-group">
                         <label for="billing-zip">{{__('Zip Code')}}</label>
                         <input class="form-control" type="text" name="bill_zip" id="billing-zip" value="{{$user->bill_zip}}">
                         @error('bill_zip')
                         <p class="text-danger">{{$message}}</p>
                         @endif
                        </div>
                   </div>
                   <div class="col-md-6">
                      <div class="form-group">
                         <label for="billing-company">{{__('City')}} *</label>
                         <input class="form-control" type="text" name="bill_city" id="billing-city" value="{{$user->bill_city}}">
                         @error('bill_city')
                         <p class="text-danger">{{$message}}</p>
                         @endif
                        </div>
                   </div>
                   <div class="col-md-6">
                      <div class="form-group">
                         <label for="billing-company">{{__('Company')}}</label>
                         <input class="form-control" type="text" name="bill_company" id="billing-company" value="{{$user->bill_company}}">
                         @error('bill_company')
                         <p class="text-danger">{{$message}}</p>
                         @endif
                        </div>
                   </div>
                    <div class="col-md-6">
                       <div class="form-group">
                          <label for="billing-country">{{__('Country')}}</label>
<select class="form-control" name="bill_country" id="billing-country">
                           <option value="" data-country-id="">{{__('Choose Country')}}</option>
                           @foreach (DB::table('countries')->get() as $country)
                           <option value="{{$country->name}}" data-country-id="{{ $country->id }}" {{$user->bill_country == $country->name ? 'selected' :''}} >{{$country->name}}</option>
                           @endforeach
                           </select>
                      @error('bill_country')
                       <p class="text-danger">{{$message}}</p>
                       @endif
                       </div>
                    </div>
                    <div class="col-md-6" id="billing-state-block" style="display:none;">
                       <div class="form-group">
                          <label for="billing-state">{{ __('State') }}</label>
                          <select class="form-control" name="bill_state_id" id="billing-state">
                             <option value="">{{ __('Select State') }}</option>
                          </select>
                       </div>
                    </div>
                   <div class="col-12 ">
                      <div class="text-right">
                         <button class="btn btn-primary margin-bottom-none  btn-sm" type="submit"><span>{{__('Update Address')}}</span></button>
                      </div>
                   </div>
                </form>
                <script>
                    document.addEventListener('DOMContentLoaded', function(){
                        // Set CSRF token for all AJAX requests
                        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        
                        var basePath = window.location.pathname.replace(/^\//, '').split('/')[0];
                        var apiUrl = basePath ? '/' + basePath : '';
                        
                        // Billing address
                        var billCountry = document.getElementById('billing-country');
                        var billState = document.getElementById('billing-state');
                        var billStateBlock = document.getElementById('billing-state-block');
                        
                        function loadBillingStates(countryId) {
                            if(!countryId) {
                                if(billStateBlock) billStateBlock.style.display = 'none';
                                return;
                            }
                            
                            var xhr = new XMLHttpRequest();
                            xhr.open('GET', apiUrl + '/country-states/' + countryId, true);
                            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                            xhr.onreadystatechange = function() {
                                if (xhr.readyState === 4 && xhr.status === 200) {
                                    var states = JSON.parse(xhr.responseText);
                                    billState.innerHTML = '<option value="">Select State</option>';
                                    for(var i = 0; i < states.length; i++) {
                                        var selected = '';
                                        @if(isset($user) && $user->bill_state_id)
                                        if ({{ $user->bill_state_id }} == states[i].id) selected = 'selected';
                                        @endif
                                        var option = document.createElement('option');
                                        option.value = states[i].id;
                                        option.textContent = states[i].name;
                                        if(selected) option.selected = true;
                                        billState.appendChild(option);
                                    }
                                    if(billStateBlock) billStateBlock.style.display = 'block';
                                }
                            };
                            xhr.send();
                        }
                        
                        var billCid = billCountry.options[billCountry.selectedIndex].getAttribute('data-country-id');
                        if(billCid){ loadBillingStates(billCid); }
                        
                        billCountry.addEventListener('change', function(){
                            var id = this.options[this.selectedIndex].getAttribute('data-country-id');
                            loadBillingStates(id);
                        });
                        
                        // Shipping address
                        var shipCountry = document.getElementById('shipping-country');
                        var shipState = document.getElementById('shipping-state');
                        
                        function loadShippingStates(countryId) {
                            if(!countryId) {
                                if(shipState) shipState.style.display = 'none';
                                return;
                            }
                            
                            var xhr = new XMLHttpRequest();
                            xhr.open('GET', apiUrl + '/country-states/' + countryId, true);
                            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                            xhr.onreadystatechange = function() {
                                if (xhr.readyState === 4 && xhr.status === 200) {
                                    var states = JSON.parse(xhr.responseText);
                                    shipState.innerHTML = '<option value="">Select Shipping State</option>';
                                    for(var i = 0; i < states.length; i++) {
                                        var selected = '';
                                        @if(isset($user) && $user->ship_state_id)
                                        if ({{ $user->ship_state_id }} == states[i].id) selected = 'selected';
                                        @endif
                                        var option = document.createElement('option');
                                        option.value = states[i].id;
                                        option.textContent = states[i].name;
                                        if(selected) option.selected = true;
                                        shipState.appendChild(option);
                                    }
                                    if(shipState) shipState.style.display = 'block';
                                }
                            };
                            xhr.send();
                        }
                        
                        var shipCid = shipCountry.options[shipCountry.selectedIndex].getAttribute('data-country-id');
                        if(shipCid){ loadShippingStates(shipCid); }
                        
                        shipCountry.addEventListener('change', function(){
                            var id = this.options[this.selectedIndex].getAttribute('data-country-id');
                            loadShippingStates(id);
                        });
                        
                        // Same as billing address checkbox
                        var sameAsBilling = document.getElementById('same_as_billing');
                        var shipFields = document.querySelectorAll('#shippingForm input, #shippingForm select');
                        
                        // Enable fields before form submit
                        document.getElementById('shippingForm').addEventListener('submit', function(){
                            shipFields.forEach(function(field){
                                field.disabled = false;
                            });
                        });
                        
                        sameAsBilling.addEventListener('change', function(){
                            if (this.checked) {
                                // Copy billing to shipping
                                document.getElementById('shipping-address1').value = document.getElementById('billing-address1').value;
                                document.getElementById('shipping-address2').value = document.getElementById('billing-address2').value;
                                document.getElementById('shipping-zip').value = document.getElementById('billing-zip').value;
                                document.getElementById('shippingcity').value = document.getElementById('billing-city').value;
                                document.getElementById('shipping-company').value = document.getElementById('billing-company').value;
                                
                                // Copy country
                                var billCountryVal = billCountry.value;
                                for (var k = 0; k < shipCountry.options.length; k++) {
                                    if (shipCountry.options[k].value === billCountryVal) {
                                        shipCountry.selectedIndex = k;
                                        break;
                                    }
                                }
                                
                                // Load states and copy
                                var billCid = billCountry.options[billCountry.selectedIndex].getAttribute('data-country-id');
                                if (billCid) {
                                    loadShippingStates(billCid);
                                    setTimeout(function(){
                                        document.getElementById('shipping-state').value = document.getElementById('billing-state').value;
                                    }, 500);
                                }
                                
                                // Disable shipping fields
                                shipFields.forEach(function(field){
                                    field.disabled = true;
                                });
                            } else {
                                // Enable shipping fields
                                shipFields.forEach(function(field){
                                    field.disabled = false;
                                });
                            }
                        });
                    });
                </script>
                <div class="padding-top-2x mt-2 hidden-lg-up"></div>
                <br>
                <h5>{{__('Shipping Address')}}</h5>
                <div class="custom-control custom-checkbox mb-3">
                    <input class="custom-control-input" type="checkbox" id="same_as_billing" name="same_as_billing">
                    <label class="custom-control-label" for="same_as_billing">{{ __('Same as billing address') }}</label>
                </div>
                <form id="shippingForm" class="row" action="{{route('user.shipping.submit')}}" method="POST">
                  @csrf
                   <div class="col-md-6">
                      <div class="form-group">
                         <label for="shipping-address1">{{__('Address 1')}} *</label>
                         <input class="form-control" name="ship_address1" value="{{$user->ship_address1}}" type="text" id="shipping-address1">
                         @error('ship_address1')
                         <p class="text-danger">{{$message}}</p>
                         @endif
                        </div>
                   </div>
                   <div class="col-md-6">
                      <div class="form-group">
                         <label for="shipping-address2">{{__('Address 2')}} </label>
                         <input class="form-control" value="{{$user->ship_address2}}" name="ship_address2" type="text" id="shipping-address2">
                         @error('ship_address2')
                         <p class="text-danger">{{$message}}</p>
                         @endif
                        </div>
                   </div>
                   <div class="col-md-6">
                      <div class="form-group">
                         <label for="shipping-zip">{{__('Zip Code')}}</label>
                         <input class="form-control" type="text" value="{{$user->ship_zip}}" name="ship_zip" id="shipping-zip">
                         @error('ship_zip')
                         <p class="text-danger">{{$message}}</p>
                         @endif
                        </div>
                   </div>
                   <div class="col-md-6">
                      <div class="form-group">
                         <label for="shipping-company">{{__('City')}} *</label>
                         <input class="form-control" type="text" name="ship_city" id="shippingcity" value="{{$user->ship_city}}">
                         @error('ship_city')
                         <p class="text-danger">{{$message}}</p>
                         @endif
                        </div>
                   </div>
                   <div class="col-md-6">
                      <div class="form-group">
                         <label for="shipping-company">{{__('Company')}}</label>
                         <input class="form-control" type="text" name="ship_company" id="shipping-company" value="{{$user->ship_company}}">
                         @error('ship_company')
                         <p class="text-danger">{{$message}}</p>
                         @endif
                        </div>
                   </div>
                   @if (DB::table('states')->count() > 0)
                    <div class="col-md-6">
                      <div class="form-group">
                         <label for="ship_state_id">{{__('State')}} <small>({{__('include tax')}})</small> </label>
                         <select class="form-control" name="ship_state_id" id="shipping-state">
                          <option value="" selected>{{__('Select Shipping State')}}</option>
                         </select>
                     @error('ship_state_id')
                      <p class="text-danger">{{$message}}</p>
                      @endif
                    </div>
                   </div>
                    @endif
             
                   <div class="{{DB::table('states')->count() > 0  ? 'col-md-12' : 'col-md-6'}} ">
                      <div class="form-group">
                         <label for="shipping-country">{{__('Country')}}</label>
<select class="form-control" name="ship_country" id="shipping-country">
                             <option value="">{{__('Choose Country')}}</option>
                             @foreach (DB::table('countries')->get() as $country)
                             <option value="{{$country->name}}" data-country-id="{{ $country->id }}" {{$user->ship_country == $country->name ? 'selected' :''}} >{{$country->name}}</option>
                             @endforeach
                          </select>
                         @error('ship_country')
                         <p class="text-danger">{{$message}}</p>
                         @endif
                      </div>
                   </div>
                    <div class="col-12 ">
                      <div class="text-right">
                         <button class="btn btn-primary margin-bottom-none btn-sm" type="submit"><span>{{__('Update Address')}}</span></button>
                      </div>
                   </div>
                </form>
              </div>
          </div>
</div>
    </div>
  </div>

<style>
.address-card {
    border: 1px solid #e5e5e5;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.address-card .card-body {
    padding: 1.5rem;
}
.address-section {
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #eee;
    margin-bottom: 1.5rem;
}
.address-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}
.address-section h5 {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 1.25rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.address-card .form-group {
    margin-bottom: 0;
}
.address-card .form-control {
    padding: 0.65rem 0.85rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.address-card .form-control:focus {
    border-color: #4a90d9;
    box-shadow: 0 0 0 3px rgba(74, 144, 217, 0.1);
}
.address-card .btn-primary {
    padding: 0.7rem 1.5rem;
    border-radius: 6px;
    font-weight: 500;
}
.address-card .text-danger {
    font-size: 0.875rem;
    margin-top: 0.25rem;
}
</style>
@endsection
