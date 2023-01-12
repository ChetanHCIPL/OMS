jQuery(document).ready(function () {
    $("#first_name").focus();
    $(".fancybox").fancybox({
        openEffect  : 'none',
        closeEffect : 'none',
    });
    FormValidation.init();
});

var FormValidation = function () {
    // validation using icons
    var handleValidation = function () {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
        var form = $('#frmadd');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "", // validate all fields including form hidden input
            rules: {
                first_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 50,
                    lettersonly:true
                },
                last_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 50,
                    lettersonly:true
                },
                email: {
                    required: true,
                    email: true,
                    maxlength: 100,
                },
                mobile: {
                    required: true,
                    maxlength: 10,
                    phone_number: true
                }, 
                current_password : {
                    required: { 
                        depends: function(element) {
                            return $('#changePasswordChk').is(':checked');
                        } 
                    } ,
                    minlength: PASSWORD_MIN,
                    maxlength: PASSWORD_MAX,
                },
                password : {
                    required: { 
                        depends: function(element) {
                            return $('#changePasswordChk').is(':checked');
                        } 
                    } ,
                    minlength: PASSWORD_MIN,
                    maxlength: PASSWORD_MAX,
                },
                password_confirmation : {
                    required: { 
                        depends: function(element) {
                            return $('#changePasswordChk').is(':checked');
                        } 
                    } ,
                    minlength: PASSWORD_MIN,
                    maxlength: PASSWORD_MAX,
                    equalTo: "#new_password"
                }
            },
            messages: {
                first_name: {required: "Enter first name",  minlength: "Enter at least 3 characters for first name.", maxlength: "Enter maximum 50 characters for first name.",lettersonly:"Enter only alphabets"},
                last_name: {required: "Enter last Name",  minlength: "Enter at least 3 characters for last name.", maxlength: "Enter maximum 50 characters for last name.",lettersonly:"Enter only alphabets"},
                email: {required: "Enter email", email: "Enter valid email", maxlength: "Enter maximum 100 characters for email."},
                mobile: {required: "Enter Mobile Number", maxlength: "Enter maximum 10 characters for mobile number."},
                current_password: {required: "Enter Current Password",minlength:"Please enter at least "+PASSWORD_MIN+" characters for password.", maxlength:"Please enter maximum "+PASSWORD_MAX+" characters for password."},
                password: {required: "Enter New Password", minlength:"Please enter at least "+PASSWORD_MIN+" characters for new password.", maxlength:"Please enter maximum "+PASSWORD_MAX+" characters for new password."},
                password_confirmation: {required: "Enter Confirm Password", minlength:"Please enter at least "+PASSWORD_MIN+" characters for conformation password.", maxlength:"Please enter maximum "+PASSWORD_MAX+" characters for conformation password.",equalTo:"Confirmation password does not match with new password"},
            },
            invalidHandler: function (event, validator) { //display error alert on form submit              
                success.hide();
                error.show();
            },
            errorPlacement: function (error, element) {
                if ($(element).is("select")) {
                    error.insertAfter(element.next(".selectize-select"));
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            },
            highlight: function (element) { // hightlight error inputs
                $(element)
                        .closest('.form-group').removeClass("has-success").addClass('has-error'); // set error class to the control group   
            },
            unhighlight: function (element) { // revert the change done by hightlight

            },
            success: function (label, element) {
                var icon = $(element).parent('.input-icon').children('i');
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                icon.removeClass("fa-warning").addClass("fa-check");
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                var msg = isError = "";
                $.ajax({
                    type: "POST",
                    url: assets + "/" +panel_text + '/user/save-edit-profile',
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        response = eval(data);
                        msg = response[0].msg;
                        isError = response[0].isError;
                        $("#msg-modal").trigger("click");
                        var str = displayProfileMessageBox(isError, msg);
                        $("#msg-html").html(str);
                    }
                });
            }
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleValidation();
        }
    };
}();

jQuery.validator.addMethod("username", function(value, element) { 
    return this.optional(element) || /^[a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*$/.test(value);
}, "Enter valid username");

jQuery.validator.addMethod("phone_number", function(value, element) {
    return this.optional(element) || /^(?=.*[0-9])[- +()0-9]+$/.test(value);
}, "Enter valid mobile number ");


// Function to display message after complete request of page
function displayProfileMessageBox(isError, msg) {
    if (isError) {
        var class_nm = "bg-danger";
        var class_msg = "alert alert-danger";
        var heading = "Error!";
        var icon = "fa-warning";
    } else {
        var class_msg = "alert alert-success";
        var class_nm = "bg-success";
        var heading = "Success!";
        var icon = "fa-check";
    }
    var html = ``;

    html = `<div class="modal-header ${class_nm} white">
                <h4 class="modal-title white" id="myModalLabel9"><i class="la la-tree"></i> ${heading}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5><i class="la la-arrow-right"></i> ${heading}</h5>
                <p class="${class_msg}"><strong>${msg}</strong></p>
            </div>
            <div class="modal-footer">`;
            if (isError) {
                html += `<button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Ok</button>`;
            }else{
                html += '<button type="button" class="btn btn-outline-success" id="btn_ok" data-dismiss="modal" onclick="window.location.reload(true);">Ok</button>';
            }
    html += `</div>`;

    setTimeout(function () {
        $('#btn_ok').focus();
    }, 1000);

    return html;
}

function deleteImage(){
    $("#delete-image-box").trigger("click");
}

function deleteUploadedImage() {
    var admin_id  = $("#admin_id").val();
    var image     = $("#image_old").val(); 
    var token     = $("input[name='_token']").val();
    bootbox.confirm({
        message: '<p class="alert alert-success"><strong>Are you sure you want to delete?</strong></p>',
        buttons: {
            confirm: {
                label: "Confirm",
                className: 'btn btn-outline-success',
            },
            cancel: {
                label: "Cancel",
                className: 'btn grey btn-outline-secondary',
            }
        },
        callback: function (result) {
            if(result){
                $.ajax({
                    type: "POST",
                    url: assets + "/" + panel_text + "/user/removeimage",
                    data: { 'customActionName': "DeleteImage",  'id': admin_id, 'image' : image, '_token': token},
                    success: function (data) {
                        $("#delete-image-box_btn_close").trigger("click");
                        response =  JSON.parse(data);
                        console.log(response); 
                        msg = response.msg; 
                        isError = response.isError;

                        if(isError != '1'){
                         
                            $('#delete-image').hide();
                            $("#show-image").attr("src", '');
                            $('#image_old').val('');
                            $("#image").val('');
                        } 
                        $("#msg-modal").trigger("click");
                        var str = displayMsgBox(isError, msg);
                        $("#msg-html").html(str);
                        
                    }
                });
            }
        }
    });
}

// Click on password chnage checkbox
$('#changePasswordChk').click(function() {
    if ($('#changePasswordChk').is(':checked')) {       
       $('#changePassDiv').removeClass('d-none');
       $('#changePasswordChk').val(1);
    } else {
       $('#changePassDiv').addClass('d-none');
        $('#changePasswordChk').val(0);
    }
});



function showhidePassword(Obj){
  var type =$(Obj).siblings('input').attr('type');
  var id =$(Obj).siblings('input').attr('id');

  if( type == "password") {
    $('#'+id).attr('type','text');
    $(Obj).attr('title', 'Hide password');
  } else {
    $('#'+id).attr('type','password');
    $(Obj).attr('title', 'Show password');
  }
}