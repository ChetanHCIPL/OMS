<?php

return [
    'SITE_URL' => 'http://192.168.32.160/ideal_oms/public/en',
    'ADMIN_PANEL_PREFIX' => 'admin',
    'SALES_PANEL_PREFIX' => 'sales_user',
    'CLIENT_PANEL_PREFIX' => 'client',
    'LN_PANEL_PREFIX' => 'admin',
    'DATE_PICKER_FORMAT' => 'd-M-Y',
    'DATETIME_PICKER_FORMAT' => 'd-m-Y',
    'CUSTOM_DATETIME_PICKER_FORMAT' => 'Y-m-d',
    'CUSTOM_TIME_PICKER_FORMAT' => 'h:mm a',
    // 'CURRENT_TIMEZONE' => 'Asia/Riyadh',  
    'TIME_SUFFIX_MSG' => 'min',
    'SITE_TITLE' => 'OMS',
    'TIMEZONE_GMT' => '+3',
    'ACTIVE_SELLER_ID' => '3',
    'APPROVE_DOCUMENT_SELLER' => '2',
    'APPROVE_AUCTION' => '4',
    'switch_on_color' => 'primary',
    'switch_off_color' => 'default',
    'PASSWORD_MIN' => '8',
    'PASSWORD_MAX' => '20',
    'PASSWORD_FORMAT' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]/',
    'ECOMMERCE_URL' => 'http://192.168.32.160/ideal_oms/public/',
    'OTP_EXPIRATION_TIME' => 5,// in Minutes
    'MAX_NO_OF_CUST_IDENTIFICATION_DOC' => 5, // Max No. of Customer Identification Documents
    'MAX_LEN_OF_API_INPUT_DATA' => 300, // API input (question/answer/) limit
    'QUESTION_ANSWER_EDIT_TIME' =>60, // Min, 1 Hours (60 min)
    'api_progress_call_execution_time' => 5, // In Seconds
    'image_ext_array' => array(
        'jpeg', 'png', 'jpg', 'gif'
    ),
    'document_ext_array' => array(
        'doc', 'docx','pdf' 
    ),
    'video_ext_array' => array(
        'mp4' 
    ),
    'audio_ext_array' => array(
        'mp3' 
    ),
    'media_ext_array' => array(
         'pdf','jpeg', 'png', 'jpg', 'doc', 'docx','ppt','xls','xlsx','txt'
    ),
    'SITE_IMAGES_STORAGE_AWS' => 'AWSS3',
    'SITE_IMAGES_STORAGE_LOCAL' => 'LOCAL',
    'IMG_MAX_SIZE' => '5242880',
    'DOC_MAX_SIZE' => '5242880', // 5,242,880 5242880
    'FILE_MAX_SIZE' => '5242880', // 5,242,880 5242880
    'CLOSED_ENVELOPE_FILE_MAX_SIZE' => '10485760', // (In Binary Bytes) 10 MB 
    'CLOSED_ENVELOPE_MAX_FILE_LIMIT' => '3', // Allow user to upload max 3 files for Closed Envelope
    'cust_identification_file_max_size' => '5242880', // (In Binary Bytes) 5 MB 
    'cust_identification_file_max_size_formsg' => '5 MB', // 5 MB 
    'aws_bucket_array' => array(),  //AWS BUCKET ARRAY
    'user_image_size' => array(
        array("W" => 80, "H" => 80),
        array("W" => "", "H" => "")
    ),
    'client_image_size' => array(
        array("W" => 80, "H" => 80),
        array("W" => "", "H" => "")
    ),
    'product_image_size' => array(
        array("W" => 80, "H" => 80),
        array("W" => "", "H" => "")
    ),
    'country_image_size' => array(
        array("W" => 80, "H" => 80),
        array("W" => "", "H" => "")
    ),
    'status_color' => array(
        '' => 'default',
        'Active' => 'success',
        'Sucess' => 'success',
        'Incomplete' => 'default',
        'Inactive' => 'danger',
        'Expired' => 'danger',
        'End'       => 'info',
        'Running' => 'primary',
        'Inactive' => 'danger',
        'Pending' => 'warning',
        'Upgraded' => 'info',
        'Cancelled' => 'warning',
        'Open' => 'success',
        'Closed' => 'primary',
        'InProcess' => 'danger',
        'Progress' => 'primary',
        'Failed' => 'danger',
        'Paused' => 'default',
        'Unverified' => 'warning',
        'Verified' => 'primary',
        'Archived' => 'warning',
        'Disabled' => 'primary',
        'Approved' => 'success', 
        'Rejected' => 'danger',
        'OnHold' => 'danger',
        'Blocked' => 'danger',
        'Success' => 'success',
        'Successful' => 'success',
        'Fail' => 'danger',
        'Yes' => 'success',
        'No' => 'danger',
        'Cancel' => 'danger',
        'Upgrade' => 'success',
        'Draft' =>'info',
        'unsuccessful' => 'danger',
        'Unsuccessful' => 'danger',
        'Regular' => 'info',
        'Mentor' => 'success',
        'Effect 1' => 'info',
        'Effect 2' => 'success',
        'Regular Free' => 'info',
        'Regular Paid' => 'success',
        'Trial Free'   => 'primary', 
        'Sent'      => 'success',
        'Inprocess'      => 'primary',
    ),
    'email_template_section' => array(
        "1" => "General", 
        "2" => "Client",
        "3" => "Admin",
        "4" => "Sales",
        "5" => "Dealer"
    ),
    'sms_template_section' => array(
        "1" => "General", 
        "2" => "Client",
        "3" => "Admin",
        "4" => "Sales",
        "5" => "Dealer"
    ),
     'notification_template_section' => array(
        "1" => "General", 
        "2" => "Member",
        "3" => "Admin"
    ),

      'whatsapp_template_section' => array(
        "1" => "General", 
        "2" => "Client",
        "3" => "Admin",
        "4" => "Sales",
        "5" => "Dealer"
    ),
     

    'label_type' => array(
        "1" => "Label",
        "2" => "Message",
        "3" => "Text",
        "4" => "Mobile App Label",
       // "4" => "Link",
        //"5" => "js",
    ),
    'status_array' => array(
        "1" => "Active",
        "2" => "Inactive",
        "3" => "Draft",
    ),
    'setting_config_type' => array (
        // "1" => array (
        //     "name" =>"Site Setup",
        //     "sub_config" => array (
        //         "1" => "General",
        //     )
        // ),
        "2" => array(
            "name" => "Site Display", 
            "sub_config" => array(
                '1' => "General", 
            )
        ),
        // "3" => array (
        //     "name" => "Site Contact", 
        //     "sub_config" => array(
        //         '1' => "General", 
        //     )
        // ),
        "6" => array (
            "name" => "SMS Setup", 
            "sub_config" => array(
                '1' => "General", 
            )
        ),
        "4" => array (
            "name" => "Admin", 
            "sub_config" => array(
                '1' => "General", 
            )
        ),
        "5" => array (
            "name" => "Application", 
            "sub_config" => array(
                '1' => "General", 
            )
        ),
        "7" => array (
            "name" => "App Contact", 
            "sub_config" => array(
                '1' => "General", 
            )
        ),
    ),
    'setting_display_type' => array(
        "1" => "text",
        "2" => "selectbox",
        "3" => "textarea",
        "4" => "checkbox",
    ),
    'email_template_mime' => array(
        "1" => "Html",
        "2" => "Text",
    ),
    'contact_us_question_type' => array(
       '1'=>'Issue', 
       '2'=>'Query', 
       '3'=>'Suggestion', 
       '4'=>'Feedback'
    ),
    'email_template' => array(
        "SMS" => array(
                    "SITE_NAME"   => "Website Name",
                    "ADMIN_PATH"  => "Admin Panel Path",
                    "USER_NAME"   => "Login User name",
                    "ADMIN_URL"   => "Admin Panel URL",
                    "PASSWORD"    => "Login Password",
                    "MAIL_FOOTER" => "Mail Footer"
                ),
        "ForgotPassword" => array(
                    "NAME"   => "User Name",
                    "ADMIN_EMAIL"  => "Admin Eamil Id",
                    "USER_NAME"   => "Login User name",
                    "ADMIN_URL"   => "Admin Panel URL",
                    "PASSWORD"    => "Login Password",
                    "MAIL_FOOTER" => "Mail Footer"
                ),
        "LoginViaMobileOPT" => array(
                    "NAME"        => "User Name",
                    "SITE_NAME"   => "Website Name",
                    "OTP_NUMBER"  =>  "OTP Number",
                    "ADMIN_EMAIL" => "Admin Email Id",
                    "MAIL_FOOTER" => "Mail Footer"
                ),
        "PasswordChanged" => array(
                    "NAME"        => "User Name",
                    "ADMIN_EMAIL"  => "Admin Email Id",
                    "USER_NAME"   => "Login User name",
                    "ADMIN_URL"   => "Admin Panel URL",
                    "PASSWORD"    => "Login Password",
                    "MAIL_FOOTER" => "Mail Footer"
                ),
        "RegistrationRequestReceived" => array(
                    "NAME"        => "User Name",
                    "SITE_NAME"   => "Website Name",
                    "ADMIN_EMAIL"  => "Admin Email Id",
                    "MAIL_FOOTER" => "Mail Footer"
                ),
        "RegistrationSuccessfully" => array(
                    "NAME"        => "User Name",
                    "SITE_NAME"   => "Website Name",
                    "ADMIN_EMAIL"  => "Admin Email Id",
                    "USER_NAME"   => "Login User name",
                    "ADMIN_URL"   => "Admin Panel URL",
                    "PASSWORD"    => "Login Password",
                    "MAIL_FOOTER" => "Mail Footer"
                ),
        
         
        "Registration" => array(
                    "SITE_NAME"   => "Website Name",
                    "ADMIN_EMAIL"  => "Admin Email Id",
                    "USER_NAME"   => "Login User name",
                    "ADMIN_URL"   => "Admin Panel URL",
                    "PASSWORD"    => "Login Password",
                    "MAIL_FOOTER" => "Mail Footer"
                )
    ),
    'module_id' => array(
        "user"=>7,
        "roles"=>8,
        "country"=>10,
        "state"=>11,
        "Districts"=>12, 
        "zone"=>123, 
        "email_template"=>13,
        "sms_template" => 42,
        "notification_template" => 71,
        "whatsapp_template" => 124,
        "ip" => 69,
        "boards" => 96,
        "medium" => 97,
        "product_head" => 98,
        "segment" => 101,
        "products" => 100,
        "semester" => 102,
        "series" => 103,
        "section" => 104,
        "print_job_vendor" => 105,
        "binding_job_vendor" => 106,
        "user_discount_category" => 107,
        "grade" => 108,
        "taluka" => 109,
        "orders" => 110,
        "accountyear" => 111,
        "designation" => 112,
        "auditstockreason" => 113,
        "paymentterms" => 114,
        "salesusermgmt" => 115,
        "salesusers" => 116,
        "client" => 117,
        "transporter" => 122,
        "contactus" => 126,
        'documents' => 127,
    ),
    'member_template_section' => array(
        "1" => "General",
        "2" => "Member",
        "3" => "Admin",
        "4" => "Products",
    ),
      'access_module' => array(
        "admin.user"              => "Users",
        "admin.userajaxlist"      =>  "Users",
        "user/data"               =>  "Users",
        "user"                    =>  "Users",
        "admin.usersave"          =>  "Users",
        "admin.user.muldelete"    =>  "Users",
        "admin.user.updatestatus" =>  "Users",
        "admin.user.removeimage"  =>  "Users",
        "sales.usersave"          => "Users",
        "salesuser.removeimage"   => "Users",
        "salesuser"               => "Users",
        "sales.saleslist"         => "Users",
        /*"
        "sales.user"              => "SalesUsers",
        "sales.userajaxlist"      => "SalesUsers",
        "salesuser.muldelete"     => "SalesUsers",
        "salesuser.updatestatus"  => "SalesUsers",*/

        "access-role/grid"        => "Roles",
        "access-role/data"        => "Roles",
        "access-role"             => "Roles",

        "ip"                      => "Ip",
        "ip/list"                 => "Ip",
        "ip/data"                 => "Ip",

        "access-group/save"  => "Roles",
        "access-group/role"  => "Roles",
        "access-group/role-view"  => "Roles",

        "country/grid"    => "Country",
        "country/data"    => "Country",
        "country"         => "Country",
        "admin/master/countrylist"=>"Country",
        "admin/master/districtslist"=>"Country",
        "admin/master/talukalist"=>"Country",

        "state/grid"      => "States",
        "state/data"      => "States",
        "state"           => "States",

        "zone/grid"      => "Zone",
        "zone/data"      => "Zone",
        "zone"           => "Zone",
        "zone/zoneajax"  => "Zone",

        "districts/grid"       => "Districts",
        "districts/data"       => "Districts",
        "districts"            => "Districts",
        "admin/master/districts/districtslist"=>"Districts",
        
        "taluka/grid"       => "Taluka",
        "taluka/data"       => "Taluka",
        "taluka"            => "Taluka",
        "taluka/talukalist" => "Taluka",
        
        "products/grid"    => "Products",
        "products/data"    => "Products",
        "products"         => "Products",
        
        "kit/grid"    => "Kit",
        "kit/data"    => "Kit",
        "kit"         => "Kit",
        
        
        "board/grid"                 => "Boards",
        "board/list"                 => "Boards",
        "board/data"                 => "Boards",


        "medium/grid"                => "Medium",
        "medium/list"                => "Medium",
        "medium/data"                => "Medium",
        "admin/master/mediumlist"     =>"Medium",


        "producthead/grid"          => "ProductHead",
        "producthead/data"          => "ProductHead",
        "producthead"               => "ProductHead",

        "transporter/grid"          => "Transporter",
        "transporter/data"          => "Transporter",
        "transporter"               => "Transporter",

        "segment/grid"    => "Segment",
        "segment/data"    => "Segment",
        "segment"         => "Segment",
        
        "semester/grid"                 => "Semester",
        "semester/list"                 => "Semester",
        "semester/data"                 => "Semester",
        
        
        "client/addresslist"    => "Client",
        "client/addressDetail"  => "Client",
        "client/clientContacts" => "Client",
        "client/grid"           => "Client",
        "client/data"           => "Client",
        "client"                => "Client",
        
        "series/grid"                 => "Series",
        "series/list"                 => "Series",
        "series/data"                 => "Series",

        "section/grid"                 => "Section",
        "section/list"                 => "Section",
        "section/data"                 => "Section",

        "orders"                       => "Orders",
        "orders/option"                => "Orders",
        "orders/filterProducts"         => "Orders",
        "orders/productsData"           => "Orders",
        "orders/list"                   => "Orders",
        "orders/data"                   => "Orders",
        "orders/challan"                => "Orders",
        "order/challan"                => "Orders",
        "orders/createChallan"          => "Orders",
        "orders/godown"                 => "Orders",

        "print_job_vendor/grid"                  => "PrintJobVendor",
        "print_job_vendor/list"                  => "PrintJobVendor",
        "print_job_vendor/data"                  => "PrintJobVendor",

        "binding_job_vendor/grid"                  => "BindingJobVendor",
        "binding_job_vendor/list"                  => "BindingJobVendor",
        "binding_job_vendor/data"                  => "BindingJobVendor",

        "user_discount_category/grid"                 => "UserDiscountCategory",
        "user_discount_category/list"                 => "UserDiscountCategory",
        "user_discount_category/data"                 => "UserDiscountCategory",

        "grade/grid"                 => "Grade",
        "grade/list"                 => "Grade",
        "grade/data"                 => "Grade",

        
        "accountyear/grid"                 => "AccountYear",
        "accountyear/list"                 => "AccountYear",
        "accountyear/data"                 => "AccountYear",

        "email-template/grid"  => "EmailTemplate",
        "email-template/data"  => "EmailTemplate",
        "email-template"       => "EmailTemplate",

        "activity-log"              => "ActivityLog",
        "get-activity-log"          => "ActivityLog",
        "get-activity-log-detail"   => "ActivityLog",

        "login-history/grid"  => "LoginLog",
        "login-history/data"  => "LoginLog",

        "admin/master/statelist"=>"States",

        "admin/master/zonelist"=>"Zone",

        "setting" =>"Setting",
        "setting/data" =>"Setting",
        
        "sms-template/grid"  => "SMSTemplate",
        "sms-template/data"  => "SMSTemplate",
        "sms-template"       => "SMSTemplate",

        "whatsapp-template/grid"  => "WhatsappTemplate",
        "whatsapp-template/data"  => "WhatsappTemplate",
        "whatsapp-template"       => "WhatsappTemplate",

        "notification-template/grid"  => "NotificationTemplate",
        "notification-template/data"  => "NotificationTemplate",
        "notification-template"       => "NotificationTemplate",

        "designation/grid" => "Designation",
        "designation/list" => "Designation",
        "designation/data" => "Designation",

        "auditstockreason/grid" => "AuditStockReason",
        "auditstockreason/list" => "AuditStockReason",
        "auditstockreason/data" => "AuditStockReason",

        "paymentterms/grid" => "PaymentTerms",
        "paymentterms/list" => "PaymentTerms",
        "paymentterms/data" => "PaymentTerms",

        "documents/grid" => "Documents",
        "documents/list" => "Documents",
        "documents/data" => "Documents",

        "contact-us/grid" => 'ContactUs',
        "contact-us/data" => 'ContactUs',
        "contact-mail-logs/data" => 'ContactUs',
        "contact-us" => 'ContactUs',
       
    ),
       
       
    'admin_access_general'=>array(
        "1"=>'AAA',
        "2"=>'Approve_Document_of_Seller',
        "3"=>'Activate_Seller',
        "4"=>'Approve_Auction',
    ),
    
    'HTTP_STATUS_MSG' => array(
        100 => 'Continue',  
        101 => 'Switching Protocols',  
        200 => 'OK',
        201 => 'Created',  
        202 => 'Accepted',  
        203 => 'Non-Authoritative Information',  
        204 => 'No Content',  
        205 => 'Reset Content',  
        206 => 'Partial Content',  
        300 => 'Multiple Choices',  
        301 => 'Moved Permanently',  
        302 => 'Found',  
        303 => 'See Other',  
        304 => 'Not Modified',  
        305 => 'Use Proxy',  
        306 => '(Unused)',  
        307 => 'Temporary Redirect',  
        400 => 'Bad Request',  
        401 => 'Unauthorized',  
        402 => 'Payment Required',  
        403 => 'Forbidden',  
        404 => 'Not Found',  
        405 => 'Method Not Allowed',  
        406 => 'Not Acceptable',  
        407 => 'Proxy Authentication Required',  
        408 => 'Request Timeout',  
        409 => 'Conflict',  
        410 => 'Gone',  
        411 => 'Length Required',  
        412 => 'Precondition Failed',  
        413 => 'Request Entity Too Large',  
        414 => 'Request-URI Too Long',  
        415 => 'Unsupported Media Type',  
        416 => 'Requested Range Not Satisfiable',  
        417 => 'Expectation Failed', 
        422 => 'Unprocessable Entity', 
        500 => 'Internal Server Error',  
        501 => 'Not Implemented',  
        502 => 'Bad Gateway',  
        503 => 'Service Unavailable',  
        504 => 'Gateway Timeout',  
        505 => 'HTTP Version Not Supported'
    ),
    'HTTP_MSG_STATUS' => array(
        'OK' => 200,
        'CREATED' => 201,
        'BAD_REQUEST' => 400,
        'UNAUTHORIZED' => 401,
        'FORBIDDEN' => 403,
        'NOT_FOUND' => 404,
        'METHOD_NOT_FOUND' => 405,
        'UNPROCESSABLE_ENTITY' => 422,
        'INTERNAL_SERVER_ERROR' => 500,
        'SERVICE_UNAVAILABLE' => 503,
        'GATEWAY_TIMEOUT' => 504
    ),

    'CRON_AVAILABLE_SCHEDULES' => array(
    1=>array("schedule"=>"every_minute", "schedule_title"=>"Every Minute", "schedule_description"=>"Run the task every minute", "method"=>"everyMinute","schedule_data"=>array()),
    2=>array("schedule"=>"every_five_minutes", "schedule_title"=>"Every Five Minutes", "schedule_description"=>"Run the task every five minutes", "method"=>"everyFiveMinutes","schedule_data"=>array()),
    3=>array("schedule"=>"every_ten_minutes", "schedule_title"=>"Every Ten Minutes", "schedule_description"=>"Run the task every ten minutes", "method"=>"everyTenMinutes","schedule_data"=>array()),
    4=>array("schedule"=>"every_fifteen_minutes", "schedule_title"=>"Every Fifteen Minutes", "schedule_description"=>"Run the task every fifteen minutes", "method"=>"everyFifteenMinutes","schedule_data"=>array()),
    5=>array("schedule"=>"every_thirty_minutes", "schedule_title"=>"Every Thirty Minutes", "schedule_description"=>"Run the task every Thirty minutes", "method"=>"everyThirtyMinutes","schedule_data"=>array()),
    6=>array("schedule"=>"hourly", "schedule_title"=>"Hourly", "schedule_description"=>"Run the task every hour", "method"=>"hourly","schedule_data"=>array()),
    7=>array("schedule"=>"hourly_at", "schedule_title"=>"Hourly At", "schedule_description"=>"Run the task every hour at added mins past the hour", "method"=>"hourlyAt","schedule_data"=>array("minutes")),
    8=>array("schedule"=>"daily", "schedule_title"=>"Daily", "schedule_description"=>"Run the task every day at midnight", "method"=>"daily","schedule_data"=>array()),
    9=>array("schedule"=>"daily_at", "schedule_title"=>"Daily At", "schedule_description"=>"Run the task every day at Added Hours", "method"=>"dailyAt","schedule_data"=>array("hours")),
    10=>array("schedule"=>"twice_daily", "schedule_title"=>"Twice Daily", "schedule_description"=>"Run the task daily at 1:00 & 13:00", "method"=>"twiceDaily","schedule_data"=>array()),
    11=>array("schedule"=>"weekly", "schedule_title"=>"Weekly", "schedule_description"=>"Run the task every week", "method"=>"weekly","schedule_data"=>array()),
    12=>array("schedule"=>"weekly_on", "schedule_title"=>"Weekly On", "schedule_description"=>"Run the task every week on Tuesday at 8:00 If(week_day=1 and hours=8:00)", "method"=>"weeklyOn","schedule_data"=>array("week_day","hours")),
    13=>array("schedule"=>"monthly", "schedule_title"=>"Monthly", "schedule_description"=>"Run the task every month", "method"=>"monthly","schedule_data"=>array()),
    14=>array("schedule"=>"monthly_on", "schedule_title"=>"Monthly On", "schedule_description"=>"Run the task every month on the 4th at 15:00 If(day=4 and Hours=15)", "method"=>"monthlyOn","schedule_data"=>array("day","hours")),
    15=>array("schedule"=>"quarterly", "schedule_title"=>"Quarterly", "schedule_description"=>"Run the task every quarter", "method"=>"quarterly","schedule_data"=>array()),
    16=>array("schedule"=>"yearly", "schedule_title"=>"Yearly", "schedule_description"=>"Run the task every year", "method"=>"yearly","schedule_data"=>array())
    ),
    
    'ad_app_version_log' => array('1.0.0','1.0.1'),
    'ios_app_version_log' => array('1.0.0','1.0.1'),
    'custom_notification_schedule_min' => 10,

    'usertype'=>[1=>'Admin',2=>'Sales User',3=>'Dealer',4=>'Client'],

    'user_type' => array(
        1 => "Admin", 
        2 => "Sales User",
        3 => "Dealer",
    ),

    'client_type' => array(
        '1' => 'School',
        '2' => 'Classes',
        '3' => 'Store',
        '4' => 'Wholesales',
    ),
    'client_status' => array(
        '1' => 'Open',
        '2' => 'Verified',
        '3' => 'Inactive',
        '4' => 'Block',
    ),

    'client_status_color_code' => array(
        '1' => '#FCC91B',
        '2' => '#84FF90', 
        '3' => '#D9D9D9',
        '4' => '#FF5454'
    ),

    'product_type' => array(
        '0' => 'Product',
        '1' => 'Kit',
    ),
    'order_prefix' => 'ORD',
    'logged_user_client' => 'client',
    'logged_user_sales_user' => 'sales_user',
    'client_document_size' => array(
        array("W" => "", "H" => "")
    ),
    'client_document_ext_array' => array(
        'jpeg', 'png', 'jpg', 'pdf', 'doc', 'docx'
    ),
    'client_document_accept_ext_array' => array(
        '.png', '.jpg', '.jpeg', 'application/pdf', 'application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ),

];
?>
