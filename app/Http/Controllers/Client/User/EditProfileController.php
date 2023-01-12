<?php
namespace App\Http\Controllers\Client\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\Clients;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use Validator;
use Config;
use App;
use Image;
use Hash;
use Intervention\Image\File;

class EditProfileController extends Controller {

    public function __construct(){
        $this->destinationPath = Config::get('path.client_path');
        $this->AWSdestinationPath = Config::get('path.AWS_COUNTRY');
        $this->size_array = Config::get('constants.client_image_size');
        $this->img_max_size = Config::get('constants.IMG_MAX_SIZE');
        $this->img_ext_array = Config::get('constants.image_ext_array');
    }

    ## Load view for Client Edit Profile
    public function editProfile() {
        
        $id = Auth::guard('client')->user()->id;
        $data = Clients::getClientsDataFromId($id);

        ## Check Image is Exist or not
        $image = (isset($data[0]['image']) && $data[0]['image'] !='' )? $data[0]['image'] : '';
         
        $checkImgArr = array();
        if($image != '' )
        {
            $checkImgArr = checkImageExistWithSetting('',$this->destinationPath,'client','','_',$image,count($this->size_array)); 
        }
        
        return view('client/user/edit_profile')->with(['data' => $data, 'id' => $id, 'checkImgArr' => $checkImgArr]);
    }

    ## Perform required action and return response to ajax request
    public function saveProfile(Request $request) {

        $result_arr = $image = array();
        $post = $request->All();
        
        if (isset($post["groupActionName"]) && isset($post["customActionType"]) && $post["customActionType"] == "group_action" && $post['groupActionName'] == "Update") {
            $validator = $this->validateClientEdit($post);
            if ($validator->fails()) {
                $validate_msg = setValidationErrorMessageForPopup($validator);
                $result_arr[0]['isError'] = 1;
                $result_arr[0]['msg'] = $validate_msg;
            }else{
                if($request->has('image')){
                    $image = $request->file('image');
                }
                ## Update Records
                $update = $this->updateClientProfile($post, $image);
                if (!empty($update) && isset($update['isError']) && isset($update['msg'])) {
                    $result_arr[0]['isError'] = $update['isError'];
                    $result_arr[0]['msg'] = $update['msg'];
                } else {
                    $result_arr[0]['isError'] = 0;
                    $result_arr[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
                }
            }
        }else{
            $result_arr[0]['isError'] = 1;
            $result_arr[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
        }  
        return Response::json($result_arr);      
    }

    ## Save Profile Data
    private function updateClientProfile($data = array(), $files = array()) {
       
        ## Variable Declaration
        $id = (isset($data['id']) ? $data['id'] : "");
        $image = "";
        #change password
        if(isset($data['changePasswordChk']) && $data['changePasswordChk'] =='1'){
            $current_password = (isset($data['current_password']) ? $data['current_password'] : "");

            $password = (isset($data['password']) ? $data['password'] : "");
            $data1 = Clients::getClientsDataFromId($id);
            ## check cureent password
            if(!empty($data1) && isset($data1[0]['password']) && Hash::check($current_password, $data1[0]['password'])){
            }
            else{
                
                $result['isError'] = 1;
                $result['msg'] = Config::get('messages.msg.MSG_CURRENT_PASS_NOT_MATCH');
                return $result; exit();
            }
        }

        if (!empty($files)) {   
            $fileOriname    = $files->getClientOriginalName();
            $image          = time().'_'.gen_remove_spacial_char($fileOriname);
            $fileExt        = $files->getClientOriginalExtension();
            $fileSize       = $files->getSize();
            $fileRealPath   = $files->getRealPath();
            if (in_array($fileExt, $this->img_ext_array)){
                if ($fileSize > $this->img_max_size || $fileSize == 0){
                    $result['isError'] = 1;
                    $result['msg'] = Config::get('messages.msg.MSG_IMG_INVALID_SIZE'). '<br/> Image size must be upto '.$this->img_max_size_MB.' MB';
                    return $result;
                    exit;
                }
                else {
                   
                    ## Store image in multiple size in folder
                   //if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
                     //  storeImageinAWS($fileRealPath, $this->AWSdestinationPath, $image, $this->size_array);
                //   }else{
                       storeImageinFolder($fileRealPath, $this->destinationPath, $image, $this->size_array);
                  // }
                   
                    ## Remove old image from folder
                    if (isset($data["image_old"]) && $data["image_old"] != "" && $data["image_old"] != NULL) {
                        ## Delete image from s3
                        //if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
                      //      deleteImageFromAWS($data["image_old"], $this->AWSdestinationPath, $this->size_array);
                      //  }else{ //## Delete image from local storage
                            deleteImageFromFolder($data["image_old"], $this->destinationPath, $this->size_array);
                      //  }                        
                    }
                }
            } else {
                $result['isError'] = 1;
                $result['msg'] = Config::get('messages.msg.MSG_IMG_INVALID_EXTENSION');
                return $result;
                exit;
            }
        }else{
            if(isset($data["image_old"]) && $data["image_old"] != ""){
                $image = $data["image_old"];
            }
        }

         if(isset($data['changePasswordChk']) && $data['changePasswordChk'] =='1'){
            $update_array = array(
                'client_name' => (isset($data['client_name']) ? $data['client_name'] : ""),
                'email' => (isset($data['email']) ? $data['email'] : ""),
                'username' => (isset($data['username']) ? $data['username'] : ""),
                'password' => Hash::make($password),
                'mobile_number' => (isset($data['mobile_number']) ? $data['mobile_number'] : ""),
                'image' => $image,
            );
        }else{
            $update_array = array(
                'client_name' => (isset($data['client_name']) ? $data['client_name'] : ""),
                'email' => (isset($data['email']) ? $data['email'] : ""),
                'username' => (isset($data['username']) ? $data['username'] : ""),
                'mobile_number' => (isset($data['mobile_number']) ? $data['mobile_number'] : ""),
                'image' => $image,
            );
        }    
        $update = Clients::updateClients($id, $update_array);

        if (isset($update)) {
             ## Start Add Activity
            $reference_id = (isset($id) ? $id : "");
            $module_id = Config::get('constants.module_id.client');
            $log_text = "Client " . $data['username'] . " edited";

            $activity_arr = array("id" => Auth::guard('client')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => '', "log_text" => $log_text, "req_data" => $data);
            usr_addActivityLog($activity_arr);
            $result['isError'] = 0;
            $result['msg'] = Config::get('messages.msg.MSG_UPDATE');
            
        } else {
            $result['isError'] = 1;
            $result['msg'] = Config::get('messages.msg.MSG_UPDATE_ERROR');
        }
        return $result;
    }

    ## Validation to Edit Client Record
    private function validateClientEdit($data = array()) {
        $rules = array(
            'client_name' => 'required|min:3|max:100',
            'email' => 'required|email|max:100|unique:clients,email,' . $data['id'] . ',id', 
            'current_password' => 'nullable|required_if:changePasswordChk,1',              
            'password' => 'nullable|required_if:changePasswordChk,1|min:'.config('constants.PASSWORD_MIN').'|max:'.config('constants.PASSWORD_MAX').'|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{6,20}/|required_with:password_confirmation|confirmed',
            'password_confirmation' => 'nullable|required_if:changePasswordChk,1',
            'mobile_number' => 'required|max:10|regex:/^(?=.*[0-9])[- +()0-9]+$/|unique:clients,mobile_number,'. $data['id'] .',id'
        );

        $messages = array(
            'client_name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Name'),
            'client_name.min' => sprintf(Config::get('messages.validation_msg.minlength'), '3', 'Name'),
            'client_name.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '100', 'Name'),
            'email.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Email'),
            'email.email' => sprintf(Config::get('messages.validation_msg.email_field'), 'Email'),
            'email.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '100', 'Email'),
            'email.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Email'),
            'current_password.required_if' => sprintf(Config::get('messages.validation_msg.required_field'), 'Current Password'),
            'password.required_if' => sprintf(Config::get('messages.validation_msg.required_field'), 'Password'),
            'password.min' => sprintf(Config::get('messages.validation_msg.minlength'), config('constants.PASSWORD_MIN'), 'Password'),
            'password.max' => sprintf(Config::get('messages.validation_msg.maxlength'), config('constants.PASSWORD_MAX'), 'Password'),
            'password.regex' => sprintf(Config::get('messages.validation_msg.regex_password'), 'Password'),
            'password.confirmed' =>  sprintf(Config::get('messages.validation_msg.password_confirmed')),
            'mobile_number.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Mobile Number'),
            'mobile_number.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '10', 'Mobile Number'),
            'mobile_number.regex' => sprintf(Config::get('messages.validation_msg.regex'), 'Mobile Number'),
            'mobile_number.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Mobile Number'),
        );
        return Validator::make($data, $rules, $messages);
    }

    /**
    * FUnction Remove Image
    * @param $request 
    * @return boolean
    */
    public function removeimage(Request $request) {
        $data = $request->all();
         
        $response = array(); 
        $id = $data['id'];
        if (!empty($id)) { 
            $data = Clients::getClientsDataFromId($id);

            #image delete from table            
            $update_array = array("image"=>"");
              
            Clients::updateClients($id, $update_array);

            #Image delete from folder
            $image_name = (isset($data[0]['image'])?$data[0]['image']:"");
            deleteImageFromFolder($image_name, $this->destinationPath, $this->size_array);
             
            $response['msg'] = "Image deleted successfully";
            $response['isError'] = 0;
        }else{
            $response['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
            $response['isError'] = 1;
        } 
        $response = json_encode($response);
        return $response;
    }
}