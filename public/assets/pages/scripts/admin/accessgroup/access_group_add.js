jQuery(document).ready(function () {
	FormValidation.init();

	$('.switchBootstrap').bootstrapSwitch();
	$("#roles_tabs").click(function(){
		$("#tab_1").addClass('active');
        $("#module").removeClass('active');
        $("#report").removeClass('active');
		$("#general").removeClass('active');
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
                access_group: {
                    required: true,
                },
            },
            messages: {
                access_group: {required: "Enter role name"},
            },
			invalidHandler: function (event, validator) { //display error alert on form submit              
                  // success.hide();
                  // error.show();
                   success.addClass('d-none');
                  error.removeClass('d-none');
                  window.scrollTo(error, -200);
                  setTimeout(function(){ error.addClass('d-none') }, 5000);
                  // tab Validation Error Trigger
                  tabValidationErrorTrigger(validator);   
              },
            errorPlacement: function (error, element) {
                if ($(element).is("select")) {
                    error.insertAfter(element.next(".form-control"));
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
            	/*alert(111);*/
                var formData = $('#frmadd').serializeArray();
                var msg = isError = "";
                $.ajax({
                    type: "POST",
                    url: assets + "/" +panel_text + '/access-role/data',
                    data: formData,
                    beforeSend: function() {
                      $(".data_loader").show();
                    },
                    success: function (data) {
                    	//alert(eval(data));
                        response = eval(data);
                        $(".data_loader").hide();
                        msg = response[0].msg;
                        isError = response[0].isError;
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



