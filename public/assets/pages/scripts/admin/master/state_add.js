jQuery(document).ready(function () {
    $("#state_name").focus();
    FormValidation.init();
    
    $('.selectize-select').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }, dropdownParent: 'body'});
    // $(".selectize-select").select2({
    //   placeholder: "Select Status",
    //   allowClear: true
    // });
    $('.selectize-select').change(function(){
        $(this).valid()
    });
	$('.switchBootstrap').bootstrapSwitch();
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
				country_code: {
					required: true,
				},
                state_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100,
                },
                state_code: {
                    required: true,
                    minlength: 2,
                    maxlength: 5,
                    LettersOnly: true,
                },
				 
				display_order:{
					number:true,	
				},
            },
            messages: {
				country_code: {required: "Select country."},
                state_name: {required: "Enter state name.", minlength: "Enter atleast 2 characters for state name.", maxlength: "Enter maximum 100 characters for state name."},
                state_code: {required: "Enter state code.", minlength: "Enter atleast 2 characters for state code.", maxlength:"Enter maximum 5 characters for state code."},
                display_order:{number: "Enter only numbers."},
            },

            invalidHandler: function (event, validator) { //display error alert on form submit              
                success.addClass('d-none');
                error.removeClass('d-none');
                window.scrollTo(error, -200);
                setTimeout(function(){ error.addClass('d-none') }, 5000);
                //$("#base-tab_1").trigger('click'); 
                // tab Validation Error Trigger
                tabValidationErrorTrigger(validator); 
            },
            errorPlacement: function (error, element) {
                if ($(element).is("select")) { 
                     error.insertAfter(element.next(".selectize-control")); 
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
                    url: assets + "/" +panel_text + '/state/data',
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
