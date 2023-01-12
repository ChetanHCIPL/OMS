<?php

namespace App\Http\Controllers\Client\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
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
     * Function: Load Client Login View
     *
     * @return   view    
     */
    public function showLoginForm()
    {        
        return view('client.auth.login');
    }

    /**
     * Function: Authenticate Client with necessary validation
     *
     * @param   string  $request  
     * @return  view    
     */
    public function clientsAuth(Request $request) {

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
        if (auth()->guard('client')->attempt(['username' => $request->input('username'), 'password' => $request->input('password')])) {
            ## IP Authentication 
            /*$is_valid_ip = checkValidIP($request->input('username'));
            // $is_tot_login_attempt_valid = checkTotLoginAttempt($request->input('username'));
            if($is_valid_ip > 0){*/

                saveClientSessionData(Auth::guard('client')->user()->id, $request->input('username'));
                ## if successful, then redirect to their intended location
                return redirect()->route('client.dashboard');
            /*}else{
                session()->flash('message', "Unauthorized Access from IP !! Please contact administrator for more information.");
            }*/
        }else{
            session()->flash('message', "These credentials do not match our records.");
        }
    
        ## if unsuccessful, then redirect back to the login with the form data
        //session()->flash('message', "These credentials do not match our records.");
        Session()->flash('alert-class', 'alert-danger'); 
        return redirect()->back()->withInput($request->only('username'));
    }

    /**
     * Function: Client Logout
     *
     * @return redirect
     */
     public function logout(Request $request) {
        
        $id = session('sess_client_id_log');
        if (Auth::guard('client')->check() && isset($id) && $id != "") {
            $dDate = date_getSystemDateTime();
            $update_array = array(
                'logout_date' => $dDate,
            );
            $update = ClientLoginLog::updateClientLoginLog($id,$update_array);
            auth()->guard('client')->logout();
            return redirect()->route('client.login');
        } else {
            if (!empty(session()->all())) {
                session()->flush();
            }
            return redirect()->route('client.login');
        }
    }
}
