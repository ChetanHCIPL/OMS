var tabId = '';
jQuery(document).ready(function () {
    FormValidation.init();
    $('.selectize-select').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }, dropdownParent: 'body'});
    $('.switchBootstrap').bootstrapSwitch();

    /*$('.meta-textarea-length').maxlength({
    threshold: 250,
    warningClass: "badge badge-success",
    limitReachedClass: "badge badge-danger",
  });*/
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
                // type: {
                //     required: true,
                // },
                // sectionName: {
                //     required: true,
                // },
                 content: {
                    required: true,
                },
            },
            messages: {
                type: { required: "Enter type"},
                sectionName: { required: "Enter section name"},
                content: { required: "Enter content"},

            },

            invalidHandler: function (event, validator) { //display error alert on form submit              
                success.hide();
                error.show();
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
                    url: assets + "/" +panel_text + '/sms-template/data',
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $(".data_loader").show();
                    },
                    success: function (data) {
                        response = eval(data);
                        $(".data_loader").hide();
                        msg = response[0].msg;
                        isError = response[0].isError;
                        tabId = response[0].section_id;
                        $("#msg-modal").trigger("click");
                        var isDisplayAddMore = 0; //No
                        var add_btn = 1; 
                        var str = displayMessageBox(isError,msg,1,isDisplayAddMore);
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

function closeAllModals(){
  $('#btn_close_par').trigger("click");
  $('#btn_close_modal').trigger("click");
  $('.filter-cancel').trigger("click");
  $("#reset").trigger("click");
  window.localStorage.setItem('tabId', tabId)
  window.location.href = assets + "/" + panel_text + "/" + route_for_popup + "/grid/";
}