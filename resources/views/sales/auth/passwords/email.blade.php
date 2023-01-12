@extends('layouts.admin')
@section('content')
<section class="flexbox-container">
    <div class="col-12 d-flex align-items-center justify-content-center border-radius-10">
        <div class="col-lg-3 col-md-8 col-10 box-shadow-2 p-0 custom_login border-radius-10">
            <div class="card border-grey border-lighten-3 m-0 border-radius-10">
                <div class="card-header border-0 border-radius-10">
                    <div class="card-title text-center">
                        <img src="{{asset('/images/logo/logo.png')}}" alt="{{ Config::get('settings.SITE_NAME.default')}}" title="{{ Config::get('settings.SITE_NAME.default')}}" > 
                    </div>
                    <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2"><span>Forgot Password</span></h6>
                </div>
                <div class="card-content border-radius-10 pt-0">
                    <div class="card-body p-0">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger">
                                {{$errors->first()}}</h4>
                            </div>
                        @endif
                    <form class="form-horizontal" method="POST" action="{{ route('sales.password.email') }}">
                        {{ csrf_field() }}
                        <fieldset class="form-group position-relative has-icon-left">
                            <input type="email" class="form-control input-lg" id="vEmail" placeholder="Email Address" tabindex="1" data-validation-required-message="Please enter your email address." name="vEmail" value="{{ old('vEmail') }}" required autofocus>
                            <div class="form-control-position">
                                <i class="ft-mail"></i>
                            </div>
                            <div class="help-block font-small-3"></div>
                        </fieldset>
                        <div class="form-group row">
                            <div class="col-md-12 col-12 text-center text-md-right"><a href="{{route('sales.login')}}" class="card-link">Back to login?</a></div>
                        </div>
                        <button type="submit" class="custom_login_btn btn btn-block btn-lg"><i class="ft-unlock"></i> Send Password Reset Link</button>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection