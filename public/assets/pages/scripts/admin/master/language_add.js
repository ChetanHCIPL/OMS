jQuery(document).ready(function () {
    $("#lang_name").focus();
    FormValidation.init();
    $('.selectize-select').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }, dropdownParent: 'body'});
    $('.switchBootstrap').bootstrapSwitch();
    $(".fancybox").fancybox({
        openEffect  : 'none',
        closeEffect : 'none'
    });
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
                lang_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100,
                },
                lang_code: {
                    required: true,
                    minlength: 2,
                    maxlength: 3,
                    LettersOnly: true,
                },
            },
            messages: {
                lang_name: {required: "Enter language name.", minlength: "Enter atleast 2 characters for language name.", maxlength: "Enter maximum 100 characters for language name."},
                lang_code: {required: "Enter language code.", minlength: "Enter atleast 2 characters for language code.", maxlength:"Enter maximum 3 characters for language code."},
            },
            invalidHandler: function (event, validator) { //display error alert on form submit              
                success.addClass('d-none');
                error.removeClass('d-none');
				window.scrollTo(error, -200);
				setTimeout(function(){ error.addClass('d-none') }, 5000);
                // tab Validation Error Trigger
                tabValidationErrorTrigger(validator);
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
                    url: assets + "/" +panel_text + '/language/data',
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                     processData: false,
                    beforeSend: function() {
                      $(".data_loader").show();
                    },
                    success: function (data) {
                        response = eval(data);
                        $(".data_loader").hide();
                        msg = response[0].msg;
                        isError = response[0].isError;
                        $("#msg-modal").trigger("click");
                        var str = displayMessageBox(isError, msg);
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

jQuery.validator.addMethod("LettersOnly", function(value, element) { 
	// allow letters and spaces only
    return this.optional(element) || /^[a-z]+$/i.test(value);
}, "Enter only Letters");

function deleteImage(){
   $("#delete-image-box").trigger("click");
}

function deleteUploadedImage() {
    var id = $("#id").val();
    var image     = $("#image_old").val(); 
    var token      = $("input[name='_token']").val();
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
                    url: assets + "/" + panel_text + "/language/data",
                    data: { 'customActionName': "DeleteImage",  'id': id, 'image' : image, '_token': token},
                    success: function (data) {
                        
                        response = eval(data);
                        msg = response[0].msg;
                        isError = response[0].isError;
                        $("#delete_image_box_btn_close").trigger("click");
                        
                        if(isError == 0) {
                            $('#delete-image').hide();
                            $("#show-image").attr("src", '');
                            $('#image_old').val('');
                            $("#image").val('');
                            $("#show-image").hide();
                        }
                        else {
                            $("#msg-modal").trigger("click");
                            var str = displayMessageBox(isError, msg);
                            $("#msg-html").html(str);
                        }
                    }
                });
            }
        }
    });
}

$('#mySelect2').on('select2:select', function (e) {
    var data = e.params.data;
    console.log(data);
});