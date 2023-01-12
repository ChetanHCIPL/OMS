<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Password;
use Validator;
use Redirect;
use Illuminate\Support\Facades\Input;
use Mail;
use Config;
use DB;
use App\Models\Admin;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm() {

        return view('admin/auth/passwords/email');
    }
 
    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Redirect
     */
    public function sendResetLinkEmail(Request $request){
        ## Get Post Data
       $post = $request->All();
        ## Check Validation
        $rules = [
            'vEmail' => 'required|email',
        ];
        $messages = [
            'vEmail.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Email'),
            'vEmail.email' => sprintf(Config::get('messages.validation_msg.email_field'), 'Email'),
        ];

        $validator = Validator::make($post, $rules, $messages);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $vEmail = (isset($post['vEmail'])?$post['vEmail']:"");

        ## Check request email is exist or not in admin table
        $data = Admin::checkEmailExist($vEmail);
        if(!empty($data)){

            ## Generate token to reset password
            $token = str_random(64);
            
            ## Delete existing record with same email 
            $delete = DB::table('password_resets')->where('email', $vEmail)->delete();

            ## Insert in password reset
            $insert_array = array(
                    'email'      => $vEmail,
                    'token'      => $token,
                    'created_at' => date_getSystemDateTime(),
            );
            $insert = DB::table('password_resets')->insert($insert_array);
            
            if($insert == 1){

                ## Send Mail
                $vFirstName = isset($data[0]['first_name'])?$data[0]['first_name']:"";
                $vLastName  = isset($data[0]['last_name'])?$data[0]['last_name']:"";
                $mail = $mailData = array();
                $mail['to_email'] = $vEmail;
                $mailData['token']  = $token;
                $mailData['name']   = $vFirstName." ".$vLastName;
                $mailData['site_logo']   = asset('/images/').'/logo/logo.png';
               
                $sentMail = Mail::send('admin/email/reset_password', $mailData, function ($message) use($mail) {
                    $message->from(Config::get('settings.ADMIN_EMAIL'), Config::get('settings.SITE_NAME.default'));
                    $message->to($mail['to_email']);
                    $message->subject('Reset password');
                });
				
                return Redirect::back()->with('status', trans(Password::RESET_LINK_SENT));
            }else{
                return Redirect::back()->withInput()->with('status', 'Something went wrong');
            }
        }else{
            ## Requested email is not exist in admin Table
            return Redirect::back()->withInput()->withErrors(['vEmail' => trans(Password::INVALID_USER)]);
        }
    }
}