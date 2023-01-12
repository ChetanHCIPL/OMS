jQuery(document).ready(function () {
    $("#zone_name").focus();
    FormValidation.init();
    $('.selectize-select').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }, dropdownParent: 'body'});

    $('.switchBootstrap').bootstrapSwitch();
     $('.selectize-select').change(function(){
        $(this).valid()
    }); 
    // on page load call state data once
    $('#country_id').change(function()
    { 
        getState();
    }); 
    
    // call back function used for filling
    function successCallBackFillStates(response){
        fillStatedropwdown('state_id',response);
    }

    // Fill State dropdown
    function fillStatedropwdown(select_id,data)
    {
        var selectId = $('#'+select_id).selectize();
        var control = selectId[0].selectize;
        control.clearOptions();
        if(data.length > 0) {
            for(var i = 0;i < data.length; i++) {
                control.addOption({value:data[i].id,text:data[i].state_name});
            }
        }
        control.setValue($('#selected_'+select_id).val(), false);
    } 
});

//Get State 
function getState(){
    var cid = $('#country_id').val();
    $.ajax({
        type: "POST",
        url: statelist, 
         "data":{"_token":csrf_token,"countryid":cid},
        success: function (data) { 
            
            var selectId = $('#state_id').selectize();
            var control = selectId[0].selectize;
            control.clearOptions(); 
            if(data.length > 0) { 
                for(var i = 0;i < data.length; i++) {
                    control.addOption({value:data[i].id,text:data[i].state_name});
                }
            }
            control.setValue($('#selected_state_id').val(), false); 
        }
    }); 
}

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
                zone_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100,
                },
                zone_code:{
                    required: true,
                    minlength: 2,
                    maxlength: 5
                },
                country_id: {
                    required: true,
                },
                state_id: {
                    required: true,
                }
			},
            messages: {
                zone_name: {required: "Enter zone name.", minlength: "Enter atleast 2 characters for zone name.", maxlength: "Enter maximum 100 characters for zone name."},
                country_id: {required: "Select country."},
                zone_code: {required: "Enter zone code.", minlength: "Enter atleast 2 characters for zone code.", maxlength: "Enter maximum 5 characters for zone code."},
                state_id: {required: "Select state."},
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
                    url: assets + "/" +panel_text + '/zone/data',
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