jQuery(document).ready(function () {
   ResetPasswordFormValidation.init();
});
var ResetPasswordFormValidation = function () {
    // validation using icons
    var handleResetPasswordValidation = function () {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
        var form = $('#reset_password_form');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "", // validate all fields including form hidden input
            rules: {
                password : {
                    required: true,
                    minlength: PASSWORD_MIN,
                    maxlength: PASSWORD_MAX,
                    password_regex:true
                },
                password_confirmation : {
                    required: true,
                    minlength: PASSWORD_MIN,
                    maxlength: PASSWORD_MAX,
                    equalTo : "#password"
                },
            },
            messages: {
                password: {required:password_required,minlength: password_min, maxlength:password_max,password_regex:msg_password_regex},
                password_confirmation: {required:confirm_password_required,minlength: confirm_password_min, maxlength:confirm_password_max,equalTo:password_equalTo}
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
                var formData = $('#reset_password_form').serializeArray();
                $(".loader_sign_in").removeClass('d-none');
                $(".btn_name_sign_in").html(lbl_loading);
                $.ajax({
                    type: "POST",
                    url: route_reset_password,
                    data: formData,
                    success: function (response) {
                        $('form[name="reset_password_form"]')[0].reset(); // reset form list
                        setTimeout(function () {
                            $(".loader_sign_in").addClass('d-none');
                            $(".btn_name_sign_in").html(lbl_reset_password);
                        }, 500);
                        var message = response.message;
                        var is_error = response.is_error;
                        $(window).scrollTop();
                        if(is_error == 1){
                            $('.alert').removeClass('alert-success');
                            $('.alert').addClass('alert-danger');
                            $('.alert').html(message);
                            setTimeout(function () {
                                $(".alert").addClass('d-none'); 
                            }, 5000);
                        }else{
                            window.location.href = route_login;
                        }
                        
                    }
                });
            }
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleResetPasswordValidation();
        }
    };
}();
jQuery.validator.addMethod("password_regex", function(value, element) { 
    return this.optional(element) || PASSWORD_FORMAT.test(value);
}, msg_password_regex);