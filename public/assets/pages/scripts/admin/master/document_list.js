$('#datatable_list').DataTable({
    "processing": true,
    "serverSide": true,
    "lengthMenu": [
        [10, 20, 50, 100, 150, -1],
        [10, 20, 50, 100, 150, "All"] // change per page values here
    ],
    "pageLength": REC_LIMIT, // default record count per page
    "ajax": assets + "/" + panel_text + "/documents/data",
	"columns": [
        {"orderable": false, "class": "text-center"},
        {"orderable": true},
        {"orderable": true, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": false, "class": "text-center"}
    ],
	"order": [[ 1, "asc" ]],	
	columnDefs: [{
        orderable: false,
        targets:   0
    }],
	select: {
        style:    'multi',
        selector: 'td:first-child'
    }
});

$(document).ready(function() { 
    FormValidation.init(); 
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
            messages: {
                title: {required: "Enter Title"},
                display_order: {required: "Enter Display Order"},
            },
            invalidHandler: function (event, validator) { //display error alert on form submit              
                success.hide();
                error.show();
            },
            errorPlacement: function (error, element) {
                if ($(element).is("select")) {
                    error.insertAfter(element.next(".modal-selectize-select"));
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
                    url: assets  + "/" +  panel_text + '/documents/data',
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        response = eval(data);
                        msg = response[0].msg;
                        isError = response[0].isError;
                        $("#msg-modal").trigger("click");
                        var str = displayMessageBox(isError, msg,0);
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
function add_edit_modal(id, mode) {
     clearfieldvalue();
    $('#customActionType').val('group_action');
    if (mode == "Add") {
        $('#customActionName').val('Add');
        $('#mode_title').text('Add');
        $('#title').val('');
        $('#display_order').val('');
        $('#status').prop('checked', true).trigger('change');
        $('#status').val('1');
        $('#status').prop( "checked", true );
        $('#add_modal_box').trigger('click');
    } else {
        $('#customActionName').val('Update');
        $('#mode_title').text('Update');
        $('#id').val(id);
        $('#title').val($("#gtitle" + id).html());
        $('#display_order').val($("#gorderno" + id).html());
        var status = $("#gstatus" + id).html();

        if(status == 1){
            $('.switchBootstrap').parent().parent().addClass('bootstrap-switch-on');
            $('#status').prop('checked', true).trigger('change');
        }else{
            $(".bootstrap-switch-id-status").addClass('bootstrap-switch-off');
            $('#status').prop('checked', false).trigger('change');
        }
        $('#add_modal_box').trigger('click');
    }
}
function clearfieldvalue() {
    var validator = $('#frmadd').validate();
    validator.resetForm();
    
    $('#series').closest('.form-group').removeClass('has-error');
}

// Function to close all models
function closeAllModals() {
    $('#btn_close_par').trigger("click");
    $('#btn_close_modal').trigger("click");
    $('.filter-cancel').trigger("click");
    $("#reset").trigger("click");
    window.location.href = assets + "/" + panel_text + "/" + route_for_popup;
}

// Function to Add More Records
function reloadAddPage(){
    $('#datatable_list').DataTable().ajax.reload();
    $('#btn_close_modal').trigger("click");
    $('#msg-modal-popup').trigger("click");

    setTimeout(function(){
        add_edit_modal( '' , 'Add')
    }, 500);
    
}