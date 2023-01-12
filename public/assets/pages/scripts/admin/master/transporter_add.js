jQuery(document).ready(function () {
    $("#name").focus();
    FormValidation.init();
    $('.selectize-select').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }, dropdownParent: 'body'});
	$('.switchBootstrap').bootstrapSwitch();     
    $(".fancybox").fancybox({
        openEffect  : 'none',
        closeEffect : 'none'
    });

    $('[name^="discount"]').each(function() {
        $(this).rules('add', {
            max: function() {
                return parseInt($('#max_discount').val());
            },
            messages: {
                max: "Discount should not be greater than "+parseInt($('#max_discount').val()),
            }
        })
    });
});

var FormValidation = function () {
    // validation using icons
    var handleValidation = function () {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
        var form = $('#frmadd');
        var max_discount = $('#max_discount').val();
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "", // validate all fields including form hidden input
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100,
                },
            },
            messages: {
                name: {required: "Enter Transporter name."}                
            },

            invalidHandler: function (event, validator) { //display error alert on form submit              
                success.addClass('d-none');
                error.removeClass('d-none'); 
				window.scrollTo(error, -200);
				setTimeout(function(){ error.addClass('d-none') }, 5000);
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
                    url: assets + "/" +panel_text + '/transporter/data',
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
                        $('#msg-modal-popup').modal({
                                backdrop: 'static',
                                keyboard: false
                        });
                        $("#msg-modal").trigger("click"); 
                        var str = displayMessageBox(isError,msg);
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
    return this.optional(element) || /^[a-zA-Z]+$/i.test(value);
}, "Enter only Letters");
jQuery.validator.addMethod("numchar", function(value, element) { 
    return this.optional(element) || /^[-!@#$%&*+0-9]+$/i.test(value);
}, "Letters are not allowed.");
function deleteImage(){
    $("#delete-image-box").trigger("click");
}
function closeAllModals() {
    $('#btn_close_par').trigger("click");
    $('#btn_close_modal').trigger("click");
    $('.filter-cancel').trigger("click");
    $("#reset").trigger("click");
    window.location.href = assets + "/" + panel_text + "/" + route_for_popup + "/grid/";
}

jQuery(".addplusclick").click(function(){
        
    var num1=parseInt(jQuery("#transport").attr('data-num'),10);
    var repeater = jQuery("#optn_details").html();
    
    jQuery("#transport tbody.dataajax").append(repeater);
    jQuery("#optn_details tr td:first-child").html(parseInt(num1)+1);
    jQuery("#transport").attr('data-num',parseInt(num1)+1);
});

jQuery("body").on('click','.deletesegme',function(){
    jQuery(this).closest('tr').remove();
    var num1=parseInt(0);
    jQuery.each(jQuery('.dataajax tr'), function( key, value ) {
        num1=parseInt(num1)+1;
        jQuery(this).find('td:first-child').html(num1);
    });
    jQuery("#segmentsem").attr('data-num',parseInt(num1)+1);
})