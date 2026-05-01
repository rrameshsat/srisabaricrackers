@extends('master.front')

@section('title')
    {{__('Register')}}
@endsection

@section('content')
<!-- Page Title-->
<div class="page-title">
    <div class="container">
        <div class="column">
            <ul class="breadcrumbs">
                <li><a href="{{route('front.index')}}">{{__('Home')}}</a></li>
                <li class="separator"></li>
                <li>{{__('Register')}}</li>
            </ul>
        </div>
    </div>
</div>

<!-- Page Content-->
<div class="container padding-bottom-3x mb-1">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7 col-xl-6">
            <div class="card register-card">
                <div class="card-body p-4">
                    <h4 class="text-center mb-4">{{__('Create Account')}}</h4>
                    
                    <form action="{{route('user.register.submit')}}" method="POST">
                        @csrf
                        
                        <!-- Account Info Section -->
                        <div class="form-section mb-4">
                            <h6 class="form-section-title">{{__('Account Information')}}</h6>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-fn">{{__('First Name')}} *</label>
                                        <input class="form-control" type="text" name="first_name" id="reg-fn" value="{{old('first_name')}}" required>
                                        @error('first_name')
                                        <p class="text-danger small">{{$message}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-ln">{{__('Last Name')}} *</label>
                                        <input class="form-control" type="text" name="last_name" id="reg-ln" value="{{old('last_name')}}" required>
                                        @error('last_name')
                                        <p class="text-danger small">{{$message}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-email">{{__('Email')}} *</label>
                                        <input class="form-control" type="email" name="email" id="reg-email" value="{{old('email')}}" required>
                                        @error('email')
                                        <p class="text-danger small">{{$message}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-phone">{{__('Phone')}} *</label>
                                        <input class="form-control" type="text" name="phone" id="reg-phone" value="{{old('phone')}}" required>
                                        @error('phone')
                                        <p class="text-danger small">{{$message}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-pass">{{__('Password')}} *</label>
                                        <input class="form-control" type="password" name="password" id="reg-pass" required>
                                        @error('password')
                                        <p class="text-danger small">{{$message}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-pass-confirm">{{__('Confirm Password')}} *</label>
                                        <input class="form-control" type="password" name="password_confirmation" id="reg-pass-confirm" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section mb-4">
                            <div class="alert alert-info mb-0">
                                <p class="mb-1">{{__('You can add your billing and shipping addresses after registration.')}}</p>
                                <a href="{{ route('user.address') }}" class="btn btn-sm btn-outline-primary">{{__('Add Address Now')}}</a>
                            </div>
                        </div>

                        <!-- Honeypot -->
                        <input type="text" name="honeypot" id="honeypot" value="" style="display:none;">

                        <!-- reCAPTCHA -->
                        @if ($setting->recaptcha == 1)
                        <div class="mb-4">
                            {!! NoCaptcha::renderJs() !!}
                            {!! NoCaptcha::display() !!}
                            @if ($errors->has('g-recaptcha-response'))
                            <p class="text-danger small">{{__($errors->first('g-recaptcha-response'))}}</p>
                            @endif
                        </div>
                        @endif

                        <!-- Submit -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a class="text-base-color small" href="{{ route('user.login') }}">{{ __('Already have an account? Login') }}</a>
                            <button class="btn btn-primary" type="submit">{{__('Register')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.register-card {
    border: 1px solid #e5e5e5;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.form-section {
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #eee;
}
.form-section:last-of-type {
    border-bottom: none;
    padding-bottom: 0;
}
.form-section-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.register-card .form-group {
    margin-bottom: 0;
}
.register-card .form-control {
    padding: 0.65rem 0.85rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.register-card .form-control:focus {
    border-color: #4a90d9;
    box-shadow: 0 0 0 3px rgba(74, 144, 217, 0.1);
}
.register-card .btn-primary {
    padding: 0.7rem 1.5rem;
    border-radius: 6px;
    font-weight: 500;
}
.register-card .text-danger {
    margin-top: 0.25rem;
}
@media (max-width: 576px) {
    .register-card .card-body {
        padding: 1.5rem;
    }
}
</style>

<script>
</script>
@endsection