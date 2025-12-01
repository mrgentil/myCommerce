@extends('admin.layouts.login')

@section('css')
<style>
html, body {
    height: 100%;
    margin: 0;
    background-color: #f4f7f6;
    color: #333333;
    justify-content: center;
    align-items: center;
}

.container-wrapper {
    width: 100%;
    max-width: 550px;
    padding: 20px;
}

.register-container {
    width: 100%;
    background-color: #ffffff;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
</style>
@endsection

@section('content')
<div class="container-wrapper">
    <div class="register-container">
        <div class="text-center mb-3">
            <h1 class="fw-bold">{{ cms_translate('auth.velstore') }}</h1>
        </div>
        <h2 class="text-center mb-4">{{ __('Vendor Registration') }}</h2>

        @if(session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('vendor.register.submit') }}" autocomplete="off">
            @csrf
            
            <h5 class="mb-3 text-muted">{{ __('Personal Information') }}</h5>
            
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('Full Name') }} *</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" id="name" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">{{ __('Email') }} *</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" id="email" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">{{ __('Phone') }}</label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" id="phone">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">{{ __('Password') }} *</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }} *</label>
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                </div>
            </div>

            <hr class="my-4">
            <h5 class="mb-3 text-muted">{{ __('Shop Information') }}</h5>

            <div class="mb-3">
                <label for="shop_name" class="form-label">{{ __('Shop Name') }} *</label>
                <input type="text" class="form-control @error('shop_name') is-invalid @enderror" name="shop_name" value="{{ old('shop_name') }}" id="shop_name" required>
                @error('shop_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="shop_description" class="form-label">{{ __('Shop Description') }}</label>
                <textarea class="form-control @error('shop_description') is-invalid @enderror" name="shop_description" id="shop_description" rows="3">{{ old('shop_description') }}</textarea>
                @error('shop_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="alert alert-info small">
                <i class="bi bi-info-circle"></i> {{ __('Your account will be reviewed by our team. You will receive an email once approved.') }}
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">{{ __('Register as Vendor') }}</button>
                <a href="{{ route('vendor.login') }}" class="btn btn-outline-secondary">{{ __('Already have an account? Login') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
