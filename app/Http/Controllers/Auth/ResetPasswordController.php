<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Session;
use Config;
use Input;
use Redirect;
use Validator;
use Hash;
use DB;
use App\Models\Admin;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    // protected $redirectTo = '/';

    /**
     * Function: Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    /**
     * Function: Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param   array  $request
     * @param   $token
     * @return  template
     */
    public function showResetForm(Request $request, $token = null) {
        return view('admin.auth.passwords.reset')
            ->with(['token' => $token, 'vEmail' => $request->vEmail]);
    }
    /**
     * Function: Reset the given user's password.
     * @param  array  $request
     * @return Redirect
     */
    public function reset(Request $request){
        ## Get Post Data
        $post = $request->all();
        
        ## Check Validation
        $rules = [
            'token'     => 'required',
            'vEmail'    => 'required|email',
            'password'  => 'required|confirmed|min:6|max:20|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{6,20}/',
        ];

        $messages = [
            'token.required'        => sprintf(Config::get('messages.validation_msg.required_field'), 'Token'),
            'vEmail.required'       => sprintf(Config::get('messages.validation_msg.required_field'), 'Email'),
            'vEmail.email'          => sprintf(Config::get('messages.validation_msg.email_field'), 'Email'),
            'password.required'     => sprintf(Config::get('messages.validation_msg.required_field'), 'Password'),
            'password.confirmed'    => Config::get('messages.validation_msg.confirmed'),
            'password.min'          => sprintf(Config::get('messages.validation_msg.minlength'), '6', 'Password'),
            'password.max'          => sprintf(Config::get('messages.validation_msg.maxlength'), '20', 'Password'),
            'password.regex'        => sprintf(Config::get('messages.validation_msg.regex_password'), 'Password'),
        ];

        $validator = Validator::make($post, $rules, $messages);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $vEmail      = isset($post['vEmail'])?trim($post['vEmail']):"";
        $token       = isset($post['token'])?trim($post['token']):"";
        $password    = isset($post['password'])?trim($post['password']):"";

        ## Check email exist with token in password_resets table
        $data = DB::table('password_resets')->where('email', $vEmail)->where('token', $token)->select('created_at')->get()->toArray();
        if(!empty($data)){
            //$expiration_duration = Config::get('settings.PASSWORD_EXPIRATION_TIME');
            $expiration_duration = "30";
            $created_at = (isset($data[0]->created_at)?$data[0]->created_at:"");
            $expired_at = strtotime('+'.$expiration_duration.' minutes', strtotime($created_at));
            $current_time = strtotime(date_getSystemDateTime());
            ## Check Expiration Time for Password Reset Link
            if($current_time <= $expired_at){
                ## Update Password
                $update_array = array(
                    'password'  => bcrypt($password),
                );
                $update = Admin::where('email', $vEmail)->update($update_array);
                return redirect()->route('admin.login')->with('status', trans('passwords.reset'));
            }
        }
        ## Admin email is not exist in password_resets table or token expired
        return Redirect::back()->withInput()->withErrors(['' => trans('passwords.token')]);
    }
}