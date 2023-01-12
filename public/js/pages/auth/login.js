jQuery(document).ready(function () {
   RegisterFormValidation.init();
   LoginFormValidation.init();
   if(sessionStorage.getItem("login_error") != null && sessionStorage.getItem("login_error_message") != null){
    $('.alert').removeClass('d-none');
    $('.alert').addClass('alert-danger');
    $('.alert').removeClass('alert-success');
    $('.alert').html(sessionStorage.getItem("login_error_message"));
    setTimeout(function () {
        $(".alert").addClass('d-none'); 
    }, 5000);
    sessionStorage.removeItem("login_error");
    sessionStorage.removeItem("login_error_message");
   }
    if((sessionStorage.getItem("reg_type") != null) && (sessionStorage.getItem("reg_type") == 'invite')){
        $('#reg_type').val(sessionStorage.getItem("reg_type"));
        sessionStorage.removeItem("reg_type");
    }
});
var RegisterFormValidation = function () {
    // validation using icons
    var handleRegisterValidation = function () {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
        var form = $('#register_form');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "", // validate all fields including form hidden input
            rules: {
                first_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 255,
                    name_regex: true,
                },
                last_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 255,
                    name_regex: true,
                },
                email: {
                    required: true,
                    email: true,
                    maxlength: 255,
                },
                password : {
                    required: true,
                    minlength: PASSWORD_MIN,
                    maxlength: PASSWORD_MAX,
                    password_regex:true,
                },
                password_confirmation : {
                    required: true,
                    minlength: PASSWORD_MIN,
                    maxlength: PASSWORD_MAX,
                    equalTo : "#password"
                },
                mobile: {
                    required: true,
                    phone_number: true,
                },
                company_name: {
                    required: { 
                        depends: function(element) {
                          if($('#corporate').prop('checked')){
                            return true;
                          }else{
                            return false;
                          }
                            
                        } 
                    }
                },
            },
            messages: {
                first_name: {required:first_name_required,minlength:first_name_min,maxlength:first_name_max,name_regex:first_name_regex},
                last_name: {required:last_name_required,minlength:last_name_min,maxlength:last_name_max,name_regex:last_name_regex},
                email: {required:email_required,maxlength:email_max,email:email_regex},
                password: {required:password_required,minlength: password_min, maxlength:password_max,password_regex:msg_password_regex},
                password_confirmation: {required:confirm_password_required,minlength: confirm_password_min, maxlength:confirm_password_max,equalTo:password_equalTo},
                mobile: {required:mobile_required,minlength: mobile_min, mobile_max:confirm_password_max,phone_number:mobile_regex},
                company_name: {required:company_name_required},
            },
			invalidHandler: function (event, validator) { //display error alert on form submit              
                success.hide();
                error.show();
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element); // for other inputs, just perform default behavior
            },
            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass("has-success").addClass('has-error'); // set error class to the control group   
            },
            unhighlight: function (element) { // revert the change done by hightlight

            },
            success: function (label, element) {
                var icon = $(element).parent('.input-icon').children('i');
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                icon.removeClass("fa-warning").addClass("fa-check");
            },
            submitHandler: function (form) {
                // var formData = $('#register_form').serializeArray();
                var form_data = new FormData(form);
                $(".loader_sign_up").removeClass('d-none');
                $(".btn_name_sign_up").html(lbl_loading);
                $.ajax({
                    type: "POST",
                    url: route_register,
                    data: form_data,
                    contentType: false,
                    cache: false,
                    processData: false, 
                    success: function (response) {
                        $(".loader_sign_up").addClass('d-none');
                        $(".btn_name_sign_up").html(lbl_sign_up);
                        var message = response.message;
                        var is_error = response.is_error;
                        if(is_error == 1){
                            $("#password").val('');
                            $("#password_confirmation").val('');
                            $('.alert').removeClass('d-none');
                            $('.alert').addClass('alert-danger');
                            $('.alert').removeClass('alert-success');
                        }else{
                            setTimeout(function () {
                                $('.sign_up').addClass('d-none');
                                $('.sign_in').removeClass('d-none');
                                $('.head_title').html(sign_in);
                            }, 500); 
                            $('form[name="register_form"]')[0].reset(); // reset form list
                            $('.alert').removeClass('d-none');
                            $('.alert').addClass('alert-success');
                            $('.alert').removeClass('alert-danger');
                            $('#login_email_mobile').focus();
                        }
                        $('html, body').animate({
                            scrollTop: $(".breadcrumb-item").offset().top
                        }, 1000);
                        $('.alert').html(message);
                        setTimeout(function () {
                            $(".alert").addClass('d-none'); 
                        }, 5000);
                    }
                });
            }
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleRegisterValidation();
        }
    };
}();

var LoginFormValidation = function () {
    // validation using icons
    var handleLoginValidation = function () {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
        var form = $('#login_form');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "", // validate all fields including form hidden input
            rules: {
                login_email_mobile: {
                    required: true,
                },
                login_password : {
                    required: true,
                },
            },
            messages: {
                login_email_mobile: {required:login_email_mobile_required},
                login_password: {required:password_required},
            },
            invalidHandler: function (event, validator) { //display error alert on form submit              
                success.hide();
                error.show();
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element); // for other inputs, just perform default behavior
            },
            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass("has-success").addClass('has-error'); // set error class to the control group   
            },
            unhighlight: function (element) { // revert the change done by hightlight

            },
            success: function (label, element) {
                var icon = $(element).parent('.input-icon').children('i');
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                icon.removeClass("fa-warning").addClass("fa-check");
            },
            submitHandler: function (form) {
                var formData = $('#login_form').serializeArray();
                $(".loader_sign_in").removeClass('d-none');
                $(".btn_name_sign_in").html(lbl_loading);
                $.ajax({
                    type: "POST",
                    url: route_login_post,
                    data: formData,
                    success: function (response) {
                        $('form[name="login_form"]')[0].reset(); // reset form list
                        setTimeout(function () {
                            $(".loader_sign_in").addClass('d-none');
                            $(".btn_name_sign_in").html(lbl_sign_in);
                        }, 500);
                        var message = response.message;
                        var is_error = response.is_error;
                        var data = response.data;
                        if(is_error == 1){
                            $(window).scrollTop();
                            $('.alert').removeClass('d-none');
                            $('.alert').addClass('alert-danger');
                            $('.alert').html(message);
                            setTimeout(function () {
                                $(".alert").addClass('d-none'); 
                            }, 5000);
                            if( typeof response.data != "undefined"  && response.data['login_email_mobile'] != ""){
                                $("#login_email_mobile").val(response.data['login_email_mobile']);
                            }
                        }
                        else{
							if(data.redirectPage !=''){
								window.location.assign(data.redirectPage);	
							}else{
								window.location.href = route_dashboard;
							}
                        }
                    }
                });
            }
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleLoginValidation();
        }
    };
}();
// \u0600-\u06FF
jQuery.validator.addMethod("name_regex", function(value, element) { 
    return this.optional(element) || /^[a-zA-Z\u0600-\u06FF ]*$/.test(value);
},"Only letters and space are allowed.");
// jQuery.validator.addMethod("name_regexe", function(value, element) { 
//     return this.optional(element) || /^[a-zA-Z ]*$/.test(value);
// },"Only letters and space are allowed.");
jQuery.validator.addMethod("phone_number", function(value, element) {
    return this.optional(element) || /^[0-9]+$/.test(value);
}, "Enter valid mobile number ");
jQuery.validator.addMethod("password_regex", function(value, element) { 
    return this.optional(element) || PASSWORD_FORMAT.test(value);
}, msg_password_regex);

function  showHideSignInUp(action) {
    if(action == 'sign_up'){
        var form = $('#login_form');
        var validator = form.validate();
        validator.resetForm();
        $('form[name="login_form"]')[0].reset();
        $('.sign_in').addClass('d-none');
        $('.sign_up').removeClass('d-none');
        $('.head_title').html(sign_up);
        $('.alert').addClass('d-none');
    }else if(action == 'sign_in'){
        var form = $('#register_form');
        var validator = form.validate();
        validator.resetForm();
        $('form[name="register_form"]')[0].reset(); 
        $('.sign_up').addClass('d-none');
        $('.sign_in').removeClass('d-none');
        $('.head_title').html(sign_in);
        $('.alert').addClass('d-none');
    }
}

function showTypeData(type){
    if(type==CUST_INDIVIDUAL){
        $("#company_div").addClass('d-none');
        $("#cus_identify_div").addClass('d-none');
    }else if(type==CUST_CORPORATE){
        $("#company_div").removeClass('d-none');
        $("#cus_identify_div").removeClass('d-none');
    }

}

function addMoreCorporteIdentity(){
    var inputs_count = $("#cus_identify_div").find($(".cus_identify_file")).length;
    //alert(customer_indentification_ext_array);
    if(inputs_count < MAX_NO_OF_CUST_IDENTIFICATION_DOC){

        var str = `
            <div class="form-group" id="identification_div_${inputs_count+1}">
              <div class="cus_identify_file">
                <label id="c_file_${inputs_count+1}" class ="identify_file" for="identification_${inputs_count+1}">${lbl_upload} ${lbl_identification}</label><input type="file" id="identification_${inputs_count+1}" name="identification[]" class="cus_file" onchange="showFilename(this,${inputs_count+1})"/>
              </div>
              <a href="javascript:void(0);" class="col-md-3 delete_file" onclick="removeCorporteIdentity(${inputs_count+1})"><i class="fa fa-times" aria-hidden="true"></i></a>
            </div>
        `;
               
        $("#cus_identify_div").append(str);

    }

    if($("#cus_identify_div").find($(".cus_identify_file")).length == MAX_NO_OF_CUST_IDENTIFICATION_DOC){
        $("#add_more_identity").addClass('d-none');
    }
}

function removeCorporteIdentity(id){
    $( "#identification_div_"+id ).remove();
    $("#add_more_identity").removeClass('d-none');
}
function showFilename(obj,ind){
    var file = $(obj)[0].files[0].name;
    $("#c_file_"+ind).attr("title", file);
    if(file.length > 15){
        var file = file.substring(0, 14)+`...`;
    }
    $('#c_file_'+ind).text(file);
}
