@extends('layouts.app')
@section('content')
<div class="content-wrapper">
     <div class="content-body">
        <section class="flexbox-container">
            <!-- <div class="col-sm-3 col-md-12 m-2">&nbsp;</div> -->
            <div class="col-12 d-flex align-items-center justify-content-center border-radius-10">
                <div class="col-lg-3 col-md-8 col-10 box-shadow-2 p-0 custom_login border-radius-10">
                    <div class="card border-grey border-lighten-3 m-0 border-radius-10">
                        <div class="card-header border-0 border-radius-10">
                            <div class="card-title text-center">
                                <img src="{{asset('/images/logo/logo.png')}}" alt="LevelNext" title="" >
                            </div>
                            <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2"><span>Login To Admin Panel</span></h6>
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
                                        {{$errors->first()}}  
                                    </div>
                                @endif
                                @if(Session::has('message') && Session::has('alert-class'))
                                    <div class="alert {{ Session::get('alert-class', 'alert-info') }}">
                                        {{ Session::get('message') }}
                                    </div>
                                @endif
                                <form class="form-horizontal" action="{{ route('admin.auth') }}" method="POST" novalidate>
                                    {{ csrf_field() }}
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control input-lg" id="username" placeholder="Your UserName" tabindex="1" data-validation-required-message="Please enter your username." name="username" value="{{ old('username') }}" required autofocus>
                                        <div class="form-control-position">
                                            <i class="ft-user"></i>
                                        </div>
                                        <div class="help-block font-small-3"></div>
                                    </fieldset>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="password" class="form-control input-lg" id="password" name="password" placeholder="Enter Password" tabindex="2" data-validation-required-message="Please enter valid passwords." required autocomplete="current-password">
                                        <div class="form-control-position">
                                            <i class="la la-key"></i>
                                        </div>
                                        <div class="help-block font-small-3"></div>
                                    </fieldset>
                                    <div class="form-group row">
                                        <div class="col-md-12 col-12 text-center text-md-right">   
                                            <a href="{{route('password.resets')}}" class="card-link">Forgot Password?</a>
                                        </div>
                                    </div>
                                    <button type="submit" class="custom_login_btn btn btn-block btn-lg"><i class="ft-unlock"></i> Login</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

