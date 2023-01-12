jQuery(document).ready(function () {
    $("#state_name").focus();
    FormValidation.init();
    $('.selectize-select').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }, dropdownParent: 'body'});
    $('#inquiry_status').selectize({ dropdownParent: 'body'});
    // $(".selectize-select").select2({
    //   placeholder: "Select Status",
    //   allowClear: true
    // });
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

        form.on('submit', function() {
            for(var instanceName in CKEDITOR.instances) {
                 CKEDITOR.instances[instanceName].updateElement();
             }
        });
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "", // validate all fields including form hidden input
            rules: {
                email_to: {
                    required: true,
                    email: true
                },
                email_from: {
                    required: true,
                    email: true
                },
                email_subject: {
                    required: true,
                },
                email_message: {
                    required: true,
                },
                email_status:{
                     required: true
                }
            },
            messages: {
                email_to: {required: "Enter receiver's email address.",email: "Enter valid email address."},
                email_from: {required: "Enter sender's email address.",email: "Enter valid email address."},
                email_subject: {required: "Enter subject."},
                email_message: {required: "Enter message."},
                email_status: {required: "Select status."}
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
                    url: assets + "/" +panel_text + '/contact-us/data',
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
                        $("#msg-modal").trigger("click");
                        var str = displayMessageBox(isError, msg,1,0);
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

jQuery.validator.addMethod("checkPercantage", function(value, element) { 
    var certificate_criteria = $('#certificate_criteria').val();
    if (certificate_criteria.match(/(^100([.]0{1,2})?)$|(^\d{1,2}([.]\d{1,2})?)$/i)) {
        return true;
    }
}, "Please enter valid percantage.");

// FUNCTION: Load Datatable
$('#datatable_list').DataTable({
    "processing": true,
    "serverSide": true,
    "lengthMenu": [
        [10, 20, 50, 100, 150, -1],
        [10, 20, 50, 100, 150, "All"] // change per page values here
    ],
    "pageLength": REC_LIMIT, // default record count per page
    "ajax": {
          "data":{"_token":csrf_token,'contact_id':contact_id},
          "url": assets + "/" +panel_text + "/contact-mail-logs/data",
          "type": "GET",
          //"url": assets + "/" +panel_text + "/course-module/data",
      },
    "columns": [
        {"orderable": false, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": false, "class": "text-center"},
        {"orderable": false, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": true, "class": "text-center"}
    ],
    "order": [[ 4, "desc" ]],    
    columnDefs: [{
        orderable: false,
        targets:   0
    }],
    select: {
        style:    'multi',
        selector: 'td:first-child'
    }
});
$('#datatable_list').scroll(function() {
    if ( $(".fixedHeader-floating").is(":visible") ) {
        $(".fixedHeader-floating").scrollLeft( $(this).scrollLeft() );
    }
});
 