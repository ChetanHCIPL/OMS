<?php
namespace App\Http\Controllers\Sales\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\Admin;
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
        $this->destinationPath = Config::get('path.user_path');
        $this->AWSdestinationPath = Config::get('path.AWS_ADMIN_USER');
        $this->size_array = Config::get('constants.user_image_size');
        $this->img_max_size = Config::get('constants.IMG_MAX_SIZE');
        $this->img_max_size_MB = @(Config::get('constants.IMG_MAX_SIZE')*0.000001);
        $this->img_ext_array = Config::get('constants.image_ext_array');
    }

    ## Load view for Edit Profile
    public function editProfile() {
        
        $admin_id = Auth::guard('sales_user')->user()->id;
        $data    = Admin::getAdminData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array(), $admin_id = $admin_id)->toArray();
        ## Check Image is Exist or not
        $image = (isset($data[0]->image) && $data[0]->image !='' )? $data[0]->image : '';
         
        $checkImgArr = array();
       if($image != '' )
        {
            /*$checkImgArr['img_url'] =  url('/imagesuser/').'/1_'.$image;
            $checkImgArr['fancy_box_url'] = url('/images/user/').'/2_'.$image;*/
            $checkImgArr = checkImageExistWithSetting('',$this->destinationPath,'user','','_',$image,count($this->size_array)); 
        }
        
        return view('sales/user/edit_profile')->with(['data' => $data, 'admin_id' => $admin_id, 'checkImgArr' => $checkImgArr]);
    }

    ## Perform required action and return response to ajax request
    public function saveProfile(Request $request) {

        $result_arr = $image = array();
        $post = $request->All();
        
        if (isset($post["groupActionName"]) && isset($post["customActionType"]) && $post["customActionType"] == "group_action" && $post['groupActionName'] == "Update") {
            $validator = $this->validateEdit($post);
            if ($validator->fails()) {
                $validate_msg = setValidationErrorMessageForPopup($validator);
                $result_arr[0]['isError'] = 1;
                $result_arr[0]['msg'] = $validate_msg;
            }else{
                if($request->has('image')){
                    $image = $request->file('image');
                }
                ## Update Records
                $update = $this->updateRecord($post, $image);
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
    private function updateRecord($data = array(), $files = array()) {
       
        ## Variable Declaration
        $admin_id = (isset($data['admin_id']) ? $data['admin_id'] : "");
        $image = "";
        #change password
        if(isset($data['changePasswordChk']) && $data['changePasswordChk'] =='1'){
            $current_password = (isset($data['current_password']) ? $data['current_password'] : "");
            $password = (isset($data['password']) ? $data['password'] : "");
            $data1 = Admin::getUserData($admin_id);
            ## check cureent password
            if(!empty($data1) && isset($data1[0]->password) && Hash::check($current_password, $data1[0]->password)){
            }else{
                
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
                }else {
                   
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
                'first_name' => (isset($data['first_name']) ? $data['first_name'] : ""),
                'last_name' => (isset($data['last_name']) ? $data['last_name'] : ""),
                'email' => (isset($data['email']) ? $data['email'] : ""),
                'username' => (isset($data['username']) ? $data['username'] : ""),
                'password' => bcrypt($password),
                'mobile' => (isset($data['mobile']) ? $data['mobile'] : ""),
                'image' => $image,
            );
        }else{
            $update_array = array(
                'first_name' => (isset($data['first_name']) ? $data['first_name'] : ""),
                'last_name' => (isset($data['last_name']) ? $data['last_name'] : ""),
                'email' => (isset($data['email']) ? $data['email'] : ""),
                'username' => (isset($data['username']) ? $data['username'] : ""),
                'mobile' => (isset($data['mobile']) ? $data['mobile'] : ""),
                'image' => $image,
            );
        }    
        $update = Admin::updateUser($admin_id,$update_array);

        if (isset($update)) {
			 ## Start Add Activity
            $reference_id = (isset($admin_id) ? $admin_id : "");
            $module_id = Config::get('constants.module_id.user');
            $log_text = "Sales User " . $data['username'] . " edited";

            $activity_arr = array("admin_id" => Auth::guard('sales_user')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => '', "log_text" => $log_text, "req_data" => $data);
            usr_addActivityLog($activity_arr);
            $result['isError'] = 0;
            $result['msg'] = Config::get('messages.msg.MSG_UPDATE');
            
        } else {
            $result['isError'] = 1;
            $result['msg'] = Config::get('messages.msg.MSG_UPDATE_ERROR');
        }
        return $result;
    }

    ## Validation to Edit Record
    private function validateEdit($data = array()) {
        $rules = array(
            'first_name' => 'required|min:3|max:50',
            'last_name' => 'required|min:3|max:50',
            'email' => 'required|email|max:100|unique:admin,email,' . $data['admin_id'] . ',id', 
            'current_password' => 'nullable|required_if:changePasswordChk,1',              
            'password' => 'nullable|required_if:changePasswordChk,1|min:'.config('constants.PASSWORD_MIN').'|max:'.config('constants.PASSWORD_MAX').'|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{6,20}/|required_with:password_confirmation|confirmed',
            'password_confirmation' => 'nullable|required_if:changePasswordChk,1',
            'mobile' => 'required|max:10|regex:/^(?=.*[0-9])[- +()0-9]+$/|unique:admin,mobile,'. $data['admin_id'] .',id',
        );

        $messages = array(
            'first_name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'First Name'),
            'first_name.min' => sprintf(Config::get('messages.validation_msg.minlength'), '3', 'First Name'),
            'first_name.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '50', 'First Name'),
            'last_name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Last Name'),
            'last_name.min' => sprintf(Config::get('messages.validation_msg.minlength'), '3', 'Last Name'),
            'last_name.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '50', 'Last Name'),
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
            'mobile.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Mobile Number'),
            'mobile.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '10', 'Mobile Number'),
            'mobile.regex' => sprintf(Config::get('messages.validation_msg.regex'), 'Mobile Number'),
            'mobile.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Mobile Number'),
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
            $userData = Admin::getUser($id); 

            #image delete from table            
            $datas = array("image"=>"");
              
            Admin::updateUser($id,$datas); 

            #Image delete from folder
            $image_name = (isset($userData[0]['image'])?$userData[0]['image']:"");
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