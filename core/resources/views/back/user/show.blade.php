@extends('master.back')

@section('content')

<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class=" mb-0  pl-3"><b>{{ __('Customers Details') }}</b> </h3>
                <a class="btn btn-primary btn-sm" href="{{route('back.user.index')}}"><i class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
            </div>
        </div>
    </div>

	<!-- Form -->
	<div class="row">

		<div class="col-xl-12 col-lg-12 col-md-12">
        <form action="{{route('back.user.update',$user->id)}}" method="POST">
            @csrf
            @method('PUT')
            @include('alerts.alerts')
			<div class="card">

					<!-- Nested Row within Card Body -->
                    <div class="card-body">
                        <div class="gd-responsive-table">
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th>{{ __("First Name") }}</th>
                                    <td> <input type="text" name="first_name" class="form-control" id="text"
                                     value="{{$user->first_name}}" ></td>
                                </tr>
                                <tr>
                                    <th>{{ __("Last Name") }}</th>
                                    <td><input type="text" name="last_name" class="form-control" id="text"
                                     value="{{$user->last_name}}" ></td>
                                </tr>
                                <tr>
                                    <th>{{ __("Email Address") }}</th>
                                    <td><input type="text" name="email" class="form-control" id="text"
                                         value="{{$user->email}}" ></td>
                                </tr>
                                <tr>
                                    <th>{{ __("Phone Number") }}</th>
                                    <td><input type="text" name="phone" class="form-control" id="text"
                                         value="{{$user->phone}}" ></td>
                                </tr>
                                <input type="hidden" name="user_id" id="" value="{{$user->id}}">
                                <tr>
                                    <th>{{ __("Password") }}</th>
                                    <td><input type="password" name="password" class="form-control" id="text"
                                        placeholder="{{ __('Password') }}" value="" ></td>
                                </tr>

                                <tr>
                                    <th>{{ __("Total Orders") }}</th>
                                    <td>{{count($user->orders)}}</td>
                                </tr>
                                <tr>
                                    <th>{{ __("Joined") }}</th>
                                    <td>{{$user->created_at->diffForHumans()}}</td>
                                </tr>



                        </table>
                        <div class="mt-4 p-3 border rounded">
                            <h5>{{ __('Billing Address (Admin)') }}</h5>
                            <div class="form-row">
                                <div class="col-md-6 mb-2">
                                    <label>{{ __('Address 1') }}</label>
                                    <input type="text" class="form-control" name="bill_address1" value="{{$user->bill_address1}}">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>{{ __('Address 2') }}</label>
                                    <input type="text" class="form-control" name="bill_address2" value="{{$user->bill_address2}}">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6 mb-2">
                                    <label>{{ __('Company') }}</label>
                                    <input type="text" class="form-control" name="bill_company" value="{{$user->bill_company}}">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>{{ __('City') }}</label>
                                    <input type="text" class="form-control" name="bill_city" value="{{$user->bill_city}}">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6 mb-2">
                                    <label>{{ __('City') }}</label>
                                    <input type="text" class="form-control" name="bill_city" value="{{$user->bill_city}}">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>{{ __('Zip') }}</label>
                                    <input type="text" class="form-control" name="bill_zip" value="{{$user->bill_zip}}">
                                </div>
                            </div>
                            <div class="form-row align-items-end">
                                <div class="col-md-6 mb-2">
                                    <label>{{ __('Country') }}</label>
                                    <select class="form-control" name="bill_country" id="admin-bill-country">
                                        <option value="">{{ __('Choose Country') }}</option>
                                        @foreach (DB::table('countries')->get() as $country)
                                        <option value="{{ $country->name }}" data-country-id="{{ $country->id }}" {{ $user->bill_country == $country->name ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2" id="admin-bill-state-block" style="display:none;">
                                    <label>{{ __('Billing State') }}</label>
                                    <select class="form-control" name="bill_state_id" id="admin-bill-state">
                                        <option value="">{{ __('Select State') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2"></div>
                        <div class="mt-4 p-3 border rounded">
                            <h5>{{ __('Shipping Address (Admin)') }}</h5>
                            <div class="form-row">
                                <div class="col-md-6 mb-2">
                                    <label>{{ __('Address 1') }}</label>
                                    <input type="text" class="form-control" name="ship_address1" value="{{$user->ship_address1}}">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>{{ __('Address 2') }}</label>
                                    <input type="text" class="form-control" name="ship_address2" value="{{$user->ship_address2}}">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6 mb-2">
                                    <label>{{ __('Company') }}</label>
                                    <input type="text" class="form-control" name="ship_company" value="{{$user->ship_company}}">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>{{ __('City') }}</label>
                                    <input type="text" class="form-control" name="ship_city" value="{{$user->ship_city}}">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>{{ __('Zip') }}</label>
                                    <input type="text" class="form-control" name="ship_zip" value="{{$user->ship_zip}}">
                                </div>
                            </div>
                            <div class="form-row align-items-end">
                                <div class="col-md-6 mb-2">
                                    <label>{{ __('Country') }}</label>
                                    <select class="form-control" name="ship_country" id="admin-ship-country">
                                        <option value="">{{ __('Choose Country') }}</option>
                                        @foreach (DB::table('countries')->get() as $country)
                                        <option value="{{ $country->name }}" data-country-id="{{ $country->id }}" {{ $user->ship_country == $country->name ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2" id="admin-ship-state-block" style="display:none;">
                                    <label>{{ __('State') }}</label>
                                    <select class="form-control" name="ship_state_id" id="admin-ship-state">
                                        <option value="">{{ __('Select State') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">{{ __('Submit') }}</button>
                        <script>
                        document.addEventListener('DOMContentLoaded', function(){
                            var basePath = window.location.pathname.replace(/^\//, '').split('/')[0];
                            var apiUrl = basePath ? '/' + basePath : '';
                            
                            function loadStates(countryId, stateSel, blockSel, selectedId){
                                if(!countryId) { 
                                    blockSel.style.display = 'none'; 
                                    stateSel.innerHTML = '<option value="">Select State</option>'; 
                                    return; 
                                }
                                
                                var xhr = new XMLHttpRequest();
                                xhr.open('GET', apiUrl + '/country-states/' + countryId, true);
                                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                                xhr.onreadystatechange = function() {
                                    if (xhr.readyState === 4 && xhr.status === 200) {
                                        var states = JSON.parse(xhr.responseText);
                                        stateSel.innerHTML = '<option value="">Select State</option>';
                                        for(var i = 0; i < states.length; i++) {
                                            var sel = (selectedId && selectedId == states[i].id) ? 'selected' : '';
                                            var option = document.createElement('option');
                                            option.value = states[i].id;
                                            option.textContent = states[i].name;
                                            if(sel) option.selected = true;
                                            stateSel.appendChild(option);
                                        }
                                        blockSel.style.display = 'block';
                                    }
                                };
                                xhr.send();
                            }
                            
                            // Billing
                            var billCountry = document.getElementById('admin-bill-country');
                            var billState = document.getElementById('admin-bill-state');
                            var billBlock = document.getElementById('admin-bill-state-block');
                            var selectedBill = {{ $user->bill_state_id ? $user->bill_state_id : 'null' }};
                            var bid = billCountry.options[billCountry.selectedIndex].getAttribute('data-country-id');
                            if (bid) { loadStates(bid, billState, billBlock, selectedBill); }
                            billCountry.addEventListener('change', function(){ 
                                var id = this.options[this.selectedIndex].getAttribute('data-country-id'); 
                                loadStates(id, billState, billBlock, selectedBill); 
                                billState.value = '';
                            });

                            // Shipping
                            var shipCountry = document.getElementById('admin-ship-country');
                            var shipState = document.getElementById('admin-ship-state');
                            var shipBlock = document.getElementById('admin-ship-state-block');
                            var selectedShip = {{ $user->ship_state_id ? $user->ship_state_id : 'null' }};
                            var sid = shipCountry.options[shipCountry.selectedIndex].getAttribute('data-country-id');
                            if (sid) { loadStates(sid, shipState, shipBlock, selectedShip); }
                            shipCountry.addEventListener('change', function(){ 
                                var id = this.options[this.selectedIndex].getAttribute('data-country-id'); 
                                loadStates(id, shipState, shipBlock, selectedShip); 
                            });
                        });
                        </script>
                        </div>
                    </div>
			</div>
        </form>
		</div>

	</div>

</div>

@endsection
