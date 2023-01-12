jQuery(document).ready(function () {
    FormValidation.init();
    $('.selectize-select').selectize({dropdownParent: 'body'});

});
$('a[data-toggle="collapse"]').on('click', function (e) {
    var tabname = $(e.target).attr("id");
    id =tabname.split('tab');
    $(".tab_nav").removeClass('active');
    $('#accordion'+id[0]+'>div.card-body').find('ul.nav-tabs li:first>a').click();
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
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "", // validate all fields including form hidden input
            rules: {},

            invalidHandler: function (event, validator) { //display error alert on form submit              
                success.hide();
                error.show();
            },

            errorPlacement: function (error, element) { // render error placement for each input type
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
                    url: assets + "/" +panel_text + '/setting/data',
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
                        var str = displaySettingMessageBox(isError, msg);
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


// Function to display message after complete request of page
function displaySettingMessageBox(isError, msg) {
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
                html += '<button type="button" class="btn btn-outline-success" id="btn_ok" data-dismiss="modal" onclick="window.location.reload(true);">Ok</button>';
    html += `</div>`;

    setTimeout(function () {
        $('#btn_ok').focus();
    }, 1000);

    return html;
}


