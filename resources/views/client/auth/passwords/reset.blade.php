@extends('layouts.admin')
@section('content')
<section class="flexbox-container">
    <div class="col-12 d-flex align-items-center justify-content-center border-radius-10">
        <div class="col-lg-3 col-md-8 col-10 box-shadow-2 p-0 custom_login border-radius-10">
            <div class="card border-grey border-lighten-3 m-0 border-radius-10">
                <div class="card-header border-0 border-radius-10">
                    <div class="card-title text-center">
                      <img src="{{asset('/images/logo/logo.png')}}" alt="
                       {{ Config::get('settings.SITE_NAME.default')}}" title="
                       {{ Config::get('settings.SITE_NAME.default')}}" > 
                    </div>
                    <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2"><span>Reset Password</span></h6>
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
                    <form class="form-horizontal" method="POST" action="{{ route('client.password.request') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="token" value="{{ $token }}">
						<fieldset class="form-group position-relative has-icon-left">
							<input type="email" class="form-control input-lg" id="vEmail" placeholder="Email Address" name="vEmail" value="{{ (isset($vEmail) && $vEmail != '')?$vEmail:old('vEmail') }}" autofocus>
							<div class="form-control-position">
								<i class="ft-mail"></i>
							</div>
						</fieldset>
						<fieldset class="form-group position-relative has-icon-left">
							<input type="password" class="form-control input-lg" id="password" placeholder="Password" name="password" value="" autofocus>
							<div class="form-control-position">
								<i class="ft-lock"></i>
							</div>
						</fieldset>
						<fieldset class="form-group position-relative has-icon-left">
							<input type="password" class="form-control input-lg" id="password" placeholder="Confirm Password" name="password_confirmation" value="" autofocus>
							<div class="form-control-position">
								<i class="ft-lock"></i>
							</div>
						</fieldset>
						<button type="submit" class="btn custom_login_btn btn-block btn-lg"><i class="ft-unlock"></i> Reset Password</button>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection