jQuery(document).ready(function () {
   EmailFormValidation.init();
});
var EmailFormValidation = function () {
    // validation using icons
    var handleEmailValidation = function () {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
        var form = $('#email_form');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "", // validate all fields including form hidden input
            rules: {
                email: {
                    required: true,
                    email: true,
                    maxlength: 255,
                }
            },
            messages: {
                email: {required:email_required,maxlength:email_max,email:email_regex},
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
                var formData = $('#email_form').serializeArray();
                $(".loader_sign_in").removeClass('d-none');
                $(".btn_name_sign_in").html(lbl_loading);
                $.ajax({
                    type: "POST",
                    url: route_password_reset,
                    data: formData,
                    success: function (response) {
                        $('form[name="email_form"]')[0].reset(); // reset form list
                        setTimeout(function () {
                            $(".loader_sign_in").addClass('d-none');
                            $(".btn_name_sign_in").html(lbl_forgot_password);
                        }, 500);
                        var message = response.message;
                        var is_error = response.is_error;
                        $(window).scrollTop();
                        $('.alert').removeClass('d-none');
                        if(is_error == 1){
                            $('.alert').removeClass('alert-success');
                            $('.alert').addClass('alert-danger');
                        }else{
                            $('.alert').removeClass('alert-danger');
                            $('.alert').addClass('alert-success');
                        }
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
            handleEmailValidation();
        }
    };
}();
