<?php

namespace App\Http\Controllers\Sales\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\LoginLog;
use App\Models\ClientLoginLog;

class AuthController extends Controller
{
    /**
     * Create a new authentication controller instance.
     *
     * @return void
    */
    public function __construct()
    {
        //$this->middleware('guest:admin', ['except' => 'logout']);
    }

    /**
     * Function: Load Sales Users Login View
     *
     * @return   view    
    */
    public function showLoginForm()
    {        
        return view('sales.auth.login');
    }

    /**
     * Function: Authenticate Sales User with necessary validation
     *
     * @param   string  $request  
     * @return  view    
    */
    public function salesAuth(Request $request) {
        
        ##login by username
        ## Validate the form data
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        $customMessages = [
            'required' => 'The :attribute field is required.'
        ];

        $this->validate($request, $rules, $customMessages);
         
        ## Attempt to log the user in
        if (auth()->guard('sales_user')->attempt(['username' => $request->input('username'), 'password' => $request->input('password'), 'user_type' => '2'])) {
            
            ## IP Authentication 
            $is_valid_ip = checkValidIP($request->input('username'));
            // $is_tot_login_attempt_valid = checkTotLoginAttempt($request->input('username'));
            if($is_valid_ip > 0){
                
                updateLoginCount($request->input('username'));
                #if login sucessfull save user data in session
                saveUserSessionData(Auth::guard('sales_user')->user()->id);
                ## if successful, then redirect to their intended location

                session(['user_type_id' => Auth::guard('sales_user')->user()->user_type ? Auth::guard('sales_user')->user()->user_type : '']);
                session(['user_type_name' => Auth::guard('sales_user')->user()->user_type ? Config::get('constants.user_type.'.Auth::guard('sales_user')->user()->user_type) : '' ]);
                session(['sales_structure_id' => Auth::guard('sales_user')->user()->sales_structure_id ? Auth::guard('sales_user')->user()->sales_structure_id : '']);

                return redirect()->route('sales.dashboard');
            }else{
                session()->flash('message', "Unauthorized Access from IP !! Please contact administrator for more information.");
            }
        }else{
             session()->flash('message', "These credentials do not match our records.");
        }
    
        ## if unsuccessful, then redirect back to the login with the form data
        //session()->flash('message', "These credentials do not match our records.");
        Session()->flash('alert-class', 'alert-danger'); 
        return redirect()->back()->withInput($request->only('username'));
    }

    /**
     * Function: Sales User Logout
     *
     * @return redirect
    */
    public function logout(Request $request) {
        
        $id = session('sess_id_log');
        if (Auth::guard('sales_user')->check() && isset($id) && $id != "") {
            $dDate = date_getSystemDateTime();
            $update_array = array(
                'logout_date' => $dDate,
            );
            $update = LoginLog::updateLoginLog($id,$update_array);
            auth()->guard('sales_user')->logout();
            return redirect()->route('sales.login');
        } else {
            if (!empty(session()->all())) {
                session()->flush();
            }
            return redirect()->route('sales.login');
        }
    }
}
