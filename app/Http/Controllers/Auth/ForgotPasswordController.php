<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Str;
use Password;
use Validator;
use Redirect;
use Response;
use Input;
use Mail;
use Config;
use DB;
use App\Models\Admin;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;
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
    /**
     * Function :  Create a new controller instance.
     *
     * @param   void
     * @return  void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    /**
    * Function: Display the form to request a password reset link.
    *
    * @param    void
    * @return   template
    */
    public function showLinkRequestForm() {
        return view('admin.auth.passwords.email');
    } 
    /**
     * Function :  Send a reset link to the given user.
     *
     * @param  $request
     * @return Redirect
     */
    public function sendResetLinkEmail(Request $request){
        ## Get Post Data
        $post = $request->all();

        ## Check Validation
        $rules = [
            'vEmail' => 'required|email',
        ];
        $messages = [
            'vEmail.required' => "Please enter email address",
            'vEmail.email' => "We can't find a user with that e-mail address.",
        ];

        $validator = Validator::make($post, $rules, $messages);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }
        $vEmail = (isset($post['vEmail'])?$post['vEmail']:"");
        //echo "<pre>";print_r($vEmail);
        ## Check request email is exist or not in admin table
        //DB::enableQueryLog();
        $data = Admin::checkEmailExist($vEmail);
        //$quries = DB::getQueryLog();
        //echo "<pre>";print_r($quries);exit;
        if(!empty($data)){

            ## Generate token to reset password
            $token = Str::random(64);
            
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
               // echo "<pre>"; print_r($mail);exit();
                $mailData['site_logo']   = asset('images/logo/').'/logo.png';
                $sentMail = Mail::send('admin.email.reset_password', $mailData, function ($message) use($mail) {
                    $message->from(Config::get('settings.ADMIN_EMAIL'), Config::get('settings.EMAIL_FROM_NAME'));
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