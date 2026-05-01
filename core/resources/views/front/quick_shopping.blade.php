@extends('master.front')

@section('title')
    {{__('Quick Shopping')}}
@endsection

@section('meta')
<meta name="keywords" content="{{$setting->meta_keywords}}">
<meta name="description" content="{{$setting->meta_description}}">
@endsection

@section('content')
<div class="page-title">
    <div class="container">
      <div class="row">
          <div class="col-lg-12">
            <ul class="breadcrumbs">
                <li><a href="{{route('front.index')}}">{{__('Home')}}</a>
                </li>
                <li class="separator"></li>
                <li><a href="{{route('front.quickshopping')}}">{{__('Quick Shopping')}}</a>
                </li>
              </ul>
          </div>
      </div>
    </div>
  </div>
  <!-- Page Content-->

    <div class="deal-of-day-section pb-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h2 class="h3">{{ __('Quick Shopping') }}</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <p>Quick Shopping page is under construction.</p>
                </div>
            </div>
        </div>
    </div>

@endsection