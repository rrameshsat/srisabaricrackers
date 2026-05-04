@extends('layouts.app')

@section('content')
<div class="container">
  <h1>UPI Config</h1>
  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  <form action="{{ route('upi.config.update') }}" method="POST">
    @csrf
    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="enabled" id="enabled" {{ isset($config) && $config->enabled ? 'checked' : '' }}>
      <label class="form-check-label" for="enabled">Enable UPI</label>
    </div>
    <div class="mb-3">
      <label for="merchant_id" class="form-label">Merchant ID</label>
      <input type="text" class="form-control" id="merchant_id" name="merchant_id" value="{{ $config->merchant_id ?? '' }}">
    </div>
    <div class="mb-3">
      <label for="endpoint" class="form-label">Endpoint</label>
      <input type="text" class="form-control" id="endpoint" name="endpoint" value="{{ $config->endpoint ?? '' }}">
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
  </form>
  <hr>
  <p class="text-muted">This is a minimal Phase 1 config page to unblock environment and enable isolated testing.</p>
</div>
@endsection
