// Function to display message after complete request of page
function displayMessageBox(isError, msg,add_btn=0) {
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
                html += `<button type="button" id="add-new-record" class="btn grey btn-outline-secondary" onclick="reloadAddPage()">Add More</button>`;
				if(add_btn == 1){
					html += `<button type="button" id="add-new-record" class="btn grey btn-outline-secondary" onclick="reloadPage()">Continue</button>`;
				}
                html += `<button type="button" class="btn btn-outline-success" onclick="closeAllModals()">Go To List</button>`;
            }

            html += `</div>`;
   
    setTimeout(function () {
        $('#btn_ok').focus();
    }, 1000);

    return html;
}
// Function to display message after complete request of modal
function displayMessageModalBox(isError, msg,displayHeader,displayBody,displayFooter) {
    var disHeader = true;
    var disBody = true;
    var disFooter = true;

    if(displayHeader == false){
        var disHeader = false;
    }
    if(displayBody == false){
        var disBody = false;
    }
    
    if(displayFooter == false){
        var disFooter = false;
    }
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
    if(disHeader != false){
        html += `<div class="modal-header ${class_nm} white">
                <h4 class="modal-title white" id="myModalLabel9"><i class="la la-tree"></i> ${heading}</h4>
                <button type="button" class="close" id="btn_close_par" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>`;
    }
    if(disBody != false){
        html += `<div class="modal-body">
                <h5><i class="la la-arrow-right"></i> ${heading}</h5>
                <p class="${class_msg}"><strong>${msg}</strong></p>
            </div>`;
    }
    if(disFooter != false){
        html += `<div class="modal-footer">`;
            if (isError) {
                html += `<button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Ok</button>`;
            }else{
                html += `<button type="button" class="btn grey btn-outline-secondary" onclick="reloadModal()">Add More</button>
                        <button type="button" class="btn btn-outline-success" onclick="closeAllModalsPopup()">Go To List</button>`;
            }

            html += `</div>`;
    }
    // html = `<div class="modal-header ${class_nm} white">
    //             <h4 class="modal-title white" id="myModalLabel9"><i class="la la-tree"></i> ${heading}</h4>
    //             <button type="button" class="close" id="btn_close_par" data-dismiss="modal" aria-label="Close">
    //                 <span aria-hidden="true">&times;</span>
    //             </button>
    //         </div>
    //         <div class="modal-body">
    //             <h5><i class="la la-arrow-right"></i> ${heading}</h5>
    //             <p class="${class_msg}"><strong>${msg}</strong></p>
    //         </div>
    //         <div class="modal-footer">`;
    //         if (isError) {
    //             html += `<button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Ok</button>`;
    //         }else{
    //             html += `<button type="button" class="btn grey btn-outline-secondary" onclick="reloadModal()">Add More</button>
    //                     <button type="button" class="btn btn-outline-success" onclick="closeAllModalsPopup()">Go To List</button>`;
    //         }

    //         html += `</div>`;
    setTimeout(function () {
        $('#btn_ok').focus();
    }, 1000);

    return html;
}
// Function to display delete message box after completion of delete msg
function displayDeleteMessageBox(isError, msg,list_page=0) {
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
                if(list_page == 1){
                    html += `<button type="button" class="btn btn-outline-success" onclick="closeAllModals()">Go To List</button>`;
                }
                html += `<button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal" onclick="reloadPage()">Ok</button>`;
            }
            

            html += `</div>`;
   
    setTimeout(function () {
        $('#btn_ok').focus();
    }, 1000);

    return html;
}
// Function to close all models
function closeAllModals() { 
    $('#btn_close_par').trigger("click");
	$('#btn_close_modal').trigger("click");
    $('.filter-cancel').trigger("click");
    $("#reset").trigger("click");
    
	window.location.href = assets + "/" + panel_text + "/" + route_for_popup + "/grid/";
}

// Function to close all models
function closeAllModalsPopup() {
    $('#btn_close_par').trigger("click");
    $('#btn_close_modal').trigger("click");
    $('.filter-cancel').trigger("click");
    $("#reset").trigger("click");
    window.location.href = assets + "/" + panel_text + "/" + route_for_popup;
}
// Function to Add More Records
function reloadAddPage() {
    window.location.href = assets + "/" + panel_text + "/" + route_for_popup + "/add/";
}
// Function to Reload update page
function reloadPage() {
	if($("#customActionName").val() == 'Add'){
		window.location.href = assets + "/" + panel_text + "/" + route_for_popup + "/edit/"+$("#encoded_id").val();
	}else{
		window.location.reload();
	}
	
}
// Function to Add more records for modal
function reloadModal(){
	$('#btn_close_modal').trigger("click");
	$('#btn_close_par').trigger("click");

    setTimeout(function(){ 
        $("#datatable_list").DataTable().ajax.reload();
        $('#add_edit_modal').trigger('click'); }
    , 1000);
}

// Function for Clear selection from Selectize
function resetSelectize(field_id) {
    if ($("#" + field_id)) {
        var $select = $('#' + field_id).selectize();
        if($select[0]){
            var control = $select[0].selectize;
            control.clear();
        }
    }
}

function backToSearchArea() {
    $("#display").val("HTML");
    $("#back_search").css('display', 'none');
    $('#print_btn').css('display', 'none');
    $("#serching_area").css('display', '');
    $(".searching_area").css('display', '');
    $("#searched_result").css('display', 'none');
    $("#serching_area_header").css('display', 'none');
    $("#serching_area_header1").css('display', 'none');
}

function BackToSearch(url){
    window.location = url;
}

function closeWindow(){
    parent.window.close();
}

function pollwin(url, w, h, winname){
    if (typeof winname == "undefined")
        winname = 'pollwindow' + Math.random();
    pollwindow = window.open(url, winname, 'top=0,left=0,status=no,toolbars=no,scrollbars=yes,width=' + w + ',height=' + h + ',maximize=no,resizable');
    pollwindow.focus();
}

function exportDataToExcel() {
    if($("#recordsCount").val() == 0){
        var error = 1;
        var message = "No Records to export!!"
        $("#msg-modal").trigger("click");
        var str = displayMessageBox(error, message);
        $("#msg-html").html(str);
    }else{
        $("#display").val("EXCEL");
        $("#frm_report").submit();
    }
}

function exportDataToPDF() {
    $("#display").val("PDF");
    $("#frm_report").submit();
}

function printReport(){
    if($("#recordsCount").val() == 0){
        var error = 1;
        var message = "No Records to print!!"
        $("#msg-modal").trigger("click");
        var str = displayMessageBox(error, message);
        $("#msg-html").html(str);
    }else{
        document.getElementById("div_print").style.display = 'none';
        window.print();
        document.getElementById("div_print").style.display = '';
    }
}

var mon_short = new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
var mon_full = new Array("January", "Februay", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

function convertDatePickerFormat(date) {
    if (date != "" && date != "0000-00-00") {
        var date_picker = new Date(date);
        var date_format_arr = DATE_DISPLAY_FORMAT.split(DATE_PICKER_SEP);
        var final_date = DATE_DISPLAY_FORMAT;
        for (i = 0; i < date_format_arr.length; i++) {
            var rep = '';
            switch (date_format_arr[i]) {
                case "d":
                    rep = date_picker.getDate();
                    break;
                case "dd":
                    rep = date_picker.getDate();
                    break;
                case "m":
                    rep = date_picker.getMonth() + 1;
                    break;
                case "mm":
                    rep = date_picker.getMonth() + 1;
                    break;
                case "M":
                    rep = mon_short[date_picker.getMonth()];
                    break;
                case "MM":
                    rep = mon_full[date_picker.getMonth()];
                    break;
                case "yyyy":
                    rep = date_picker.getFullYear();
                    break;
                case "yy":
                    rep = date_picker.getFullYear();
                    break;
            }
            //alert(date_format_arr[i]+"="+rep+"("+final_date+")");
            if (rep != "")
                final_date = final_date.replace(date_format_arr[i], rep);
        }
        return final_date;
    } else
        return "";
}

// Remove empty elements from array : Single Dimentional Array
function cleanArray(actual) {
    var total = actual.length;
    var newArray = new Array();
    for (var i = 0; i < total; i++) {
        if (actual[i]) {
            newArray.push(actual[i]);
        }
    }
    return newArray;
}

// Genertae random string
function randomString(string_length) {
    var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
    
    if(string_length == ""){
        string_length = 5;
    }
    var randomstring = '';
    for (var i=0; i<string_length; i++) {
        var rnum = Math.floor(Math.random() * chars.length);
        randomstring += chars.substring(rnum,rnum+1);
    }
    return randomstring;
}

//  ***** Check/Uncheck All checkbox
$('body').on('change', '.group-checkable', function() {
    var rows, checked;
    rows = $('#datatable_list').find('tbody tr');
    checked = $(this).prop('checked');
    $.each(rows, function() {
        if(checked)
            $(this).find('td').closest('tr').addClass('selected');
        else
            $(this).find('td').closest('tr').removeClass('selected');

        var checkbox = $($(this).find('td').eq(0)).find('input').prop('checked', checked);

    });
});

//  ***** Check/Uncheck checkbox on click of first cell
$("#datatable_list tbody").on('click', 'tr td:first-child', function () {
    var checked = false;
    if(!$(this).parent('tr').hasClass('selected')) {
        checked = true;
    }
    $($(this).eq(0)).find('input').prop('checked', checked);

    // ***** if all checkbox select/deselect manually from single page, check/uncheck .group-checkable checkbox
    var tot_cnt = $('tbody > tr > td:nth-child(1) input[type="checkbox"]', $('#datatable_list')).length;
    var checked_cnt = $('tbody > tr > td:nth-child(1) input[type="checkbox"]:checked', $('#datatable_list')).length;
    if(tot_cnt == checked_cnt) {
        $('.group-checkable').prop("checked",true);
    }else {
        $('.group-checkable').prop("checked",false);
    }
}); 
// ***** Change Status OR delete multiple records from list.
function checkAction(mode, page_name){
    var IdArr = [];
    $('tbody').find('input[type="checkbox"]:checked').each(function () {
       IdArr.push($(this).val());
    });
    var token = $("input[name='_token']").val();
    if(IdArr.length > 0){
        
         // Confirmation Pop-up
        bootbox.confirm({
            message: "Are you sure you want to "+mode+" record(s) ?",
            buttons: {
                confirm: {
                    label: "Confirm"
                },
                cancel: {
                    label: "Cancel"
                }
            },
            callback: function (result) {
                if(result){
                       $.ajax({
                            type: "POST",
                            url: assets + "/" +panel_text + "/"+page_name+"/data",
                            data: { 'customActionName': mode,  'id': IdArr, '_token': token},
                            success: function (data) {
                                response = eval(data);
                                msg = response[0].msg;
                                isError = response[0].isError;
                                if(isError == 0) {
                                    $("#list-msg").html(msg)
                                    $("#list-alert").removeClass("alert-danger");
                                    $("#list-alert").addClass("alert-success");
                                    $("#list-alert").removeClass('d-none');
                                }
                                else {
                                    $("#list-msg").html("Please select atleast one record.");
                                    $("#list-alert").removeClass("alert-success");
                                    $("#list-alert").addClass("alert-danger");
                                    $("#list-alert").removeClass('d-none');
                                }
                                $("#datatable_list").DataTable().ajax.reload();
                            }
                        });
                }
            }
        });
    }else{
        $("#list-msg").html("Please select atleast one record.");
        $("#list-alert").removeClass("alert-success");
        $("#list-alert").addClass("alert-danger");
        $("#list-alert").removeClass('d-none');
    }

    setTimeout(function(){ $("#list-alert").addClass('d-none') }, 5000);
}
function checkActionold(mode, page_name){
    var IdArr = [];
    $('tbody').find('input[type="checkbox"]:checked').each(function () {
       IdArr.push($(this).val());
    });
    var token = $("input[name='_token']").val();
    if(IdArr.length > 0){
        var ans = confirm("Are you sure you want to "+mode+" record(s) ?");
     //   alert( assets + "/" +panel_text + "/"+page_name+"/data");
        if (ans == true) {
            $.ajax({
                type: "POST",
                url: assets + "/" +panel_text + "/"+page_name+"/data",
                data: { 'customActionName': mode,  'id': IdArr, '_token': token},
                success: function (data) {
                    response = eval(data);
                    msg = response[0].msg;
                    isError = response[0].isError;
                    if(isError == 0) {
                        $("#list-msg").html(msg)
                        $("#list-alert").removeClass("alert-danger");
                        $("#list-alert").addClass("alert-success");
                        $("#list-alert").removeClass('d-none');
                    }
                    else {
                        $("#list-msg").html("Please select atleast one record.");
                        $("#list-alert").removeClass("alert-success");
                        $("#list-alert").addClass("alert-danger");
                        $("#list-alert").removeClass('d-none');
                    }
                    $("#datatable_list").DataTable().ajax.reload();
                }
            });
        }
    }else{
        $("#list-msg").html("Please select atleast one record.");
        $("#list-alert").removeClass("alert-success");
        $("#list-alert").addClass("alert-danger");
        $("#list-alert").removeClass('d-none');
    }

    setTimeout(function(){ $("#list-alert").addClass('d-none') }, 5000);
}
//******* Delete Single Record From View Page **********//
function deleteSingleRecords(id,page_name){
	var token = $("input[name='_token']").val();
	var IdArr = [];
    if(id > 0){
		IdArr.push(id);
        var ans = confirm("Are you sure you want to delete record ?");
        if (ans == true) {
            $.ajax({
                type: "POST",
                url: assets + "/" +panel_text + "/"+page_name+"/data",
                data: { 'customActionName': "Delete",  'id': IdArr, '_token': token},
                success: function (data) {
                    response = eval(data);
                    msg = response[0].msg;
                    isError = response[0].isError;
                    $("#msg-modal").trigger("click");
					var str = displayMessageBox(isError, msg);
					$("#msg-html").html(str);
                }
            });
        }
    }
}

// FUNCTION: Delete Product with confirmation popup
function deleteSingleRecord(id, page_name){
    // var IdArr = [];
    // $('tbody').find('input[type="checkbox"]:checked').each(function () {
    //    IdArr.push($(this).val());
    // });
    // var token = $("input[name='_token']").val();
    var token = $("input[name='_token']").val();
    var IdArr = [];

    if(id > 0){
        IdArr.push(id);
        // Confirmation Pop-up
        bootbox.confirm({
            message: "Are you sure you want to delete record ?",
            buttons: {
                confirm: {
                    label: "Confirm"
                },
                cancel: {
                    label: "Cancel"
                }
            },
            callback: function (result) {
                if(result){
                       $.ajax({
                            type: "POST",
                            url: assets + "/" +panel_text + "/"+page_name+"/data",
                            data: { 'customActionName': "Delete",  'id': IdArr, '_token': token},
                            success: function (data) {
                                response = eval(data);
                                msg = response[0].msg;
                                isError = response[0].isError;
                                $("#msg-modal").trigger("click");
                                var str = displayMessageBox(isError, msg);
                                $("#msg-html").html(str);
                            }
                        });
                }
            }
        });
    }else{
        $("#list-msg").html("Please select atleast one record.");
        $("#list-alert").removeClass("alert-success");
        $("#list-alert").addClass("alert-danger");
        $("#list-alert").removeClass('d-none');
    }
    setTimeout(function(){ $("#list-alert").addClass('d-none') }, 5000);
}


// ****** Searching for list datatable
function search_datatable() {
    $('.datatable_search > tr').find('td').each (function(i) {
        var i = $(this).attr('data-column');  // getting column index
        var v = $('#col'+i+'_filter').val();  // getting search input value
        if(typeof v != "undefined" && v != '')
            // console.log(v);
            $('#datatable_list').DataTable().columns(i).search(v).draw()
    }); 
}

function dt_search() {
    $('#filter_div').find('div').each(function (i) {
        var i = $(this).attr('data-column');  // getting column index
        var v = $('#col' + i + '_filter').val();  // getting search input value
        if (typeof v != "undefined" && v != '')
            // console.log(v);
            $('#datatable_list').DataTable().columns(i).search(v).draw()
    }); 
}

// ****** reset list datatable search filter
function reset_datatable() {
    $('form[name="frmlist"]')[0].reset(); // reset form list
    if($('.selectize-select')){ // clear selectize
        $('.selectize-select').each(function(){
            if($(this)[0] && $(this)[0].selectize){
                $(this)[0].selectize.clear();
                //$(this)[0].selectize.clearOptions();
            }
        });
    }
    $('#datatable_list').DataTable().columns().search('').draw(); // reset datatable value
}

// Function: Datatable - Advanced Filter
function datatable_search() {
    var filterArr = [];
    $('#filter_div').find('.column_filter').each(function () {
        var k = $(this).attr('name');  // getting search input name
        var v = $(this).val();  // getting search input value
        if (typeof v != "undefined" && v != ''){
            filterArr['filterParams['+k+']'] = v.trim();
        }
    }); 
    $('#datatable_list').DataTable().destroy();
    load_datatable(filterArr); 
}

// Function: Datatable - Reset Advanced Filter
function datatable_reset() {
    $('form[name="frmlist"]')[0].reset(); // reset form list
    if($('.selectize-select')){ // clear selectize
        $('.selectize-select').each(function(){
            if($(this)[0] && $(this)[0].selectize){
                $(this)[0].selectize.clear();
            }
        });
    }
    datatable_search();
}

// Function: Datatable - Advanced Filter
function datatable_search_filter() {
    var filterArr = [];
    $('#filter_div').find('.column_filter').each(function () {
        var k = $(this).attr('name');  // search input name
        var v = $(this).val();  // search input value
        if (typeof v != "undefined" && v != ''){
            filterArr.push({'key':k, 'val':v.trim()});
        }
    }); 
    //set search value with json object
    $('#datatable_list').DataTable().columns(0).search(JSON.stringify(filterArr)).draw();
}

// Function: Datatable - Reset Advanced Filter
function datatable_reset_filter() {
    $('form[name="frmlist"]')[0].reset(); // reset form data
    $('form[name="frmlist"]').find(':input').not(':hidden').val('');
    if($('.selectize-select')){ // clear selectize
        $('.selectize-select').each(function(){
            if($(this)[0] && $(this)[0].selectize){
                $(this)[0].selectize.clear();
            }
        });
    }
    //Reset datatable value with default page limit
    $('#datatable_list').DataTable().columns().search('').page.len(REC_LIMIT).draw(); 
}
// Function : Tab wise Validation Error Trigger
function tabValidationErrorTrigger(validator){
    var errors = validator.numberOfInvalids();
    if (errors) {
        var element_type = validator.errorList[0].element.type;
        var element_name = validator.errorList[0].element.name;
        if(element_type == "textarea"){
            element_type = 'textarea[name="' + element_name+'"]';
        }else if(element_type == "select-one"){
            element_type = 'select[name="' + element_name+'"]';
        }else if(element_type == "text"){
            element_type = 'input[name="' + element_name+'"]';
        }
       
        var tab_id = $(element_type).closest('.tab-pane').attr('aria-labelledby');
        var collaps_id = $("#"+tab_id).parent().closest('div').parent().attr('id');
        if ($('a[href="#'+collaps_id+'"]').attr('aria-expanded') == 'false')
            $('a[href="#'+collaps_id+'"]').trigger('click');
        $("#"+tab_id).trigger('click');
        
    }
}
// ****** File Upload
(function(window, document, $) {
  'use strict';
  //Custom File Input
  $('.form-group .custom-file input').change(function (e) {
    
      $(this).next('.custom-file-label').html(e.target.files[0].name);
  });
})(window, document, jQuery);

$(document).ready(function(){
    $('.nav-link').click(function () {
        $('.nav-link').not(this).removeClass('active');
    });
    $("#show_filter").click(function () {
        $(this).addClass('d-none');
        $("#hide_filter").removeClass('d-none');
        $('#filter_div').removeClass('d-none');
    });
    $("#hide_filter").click(function () {
        $(this).addClass('d-none');
        $("#show_filter").removeClass('d-none');
        $('#filter_div').addClass('d-none');
    });
	/*$('.skin-square input').iCheck({
        checkboxClass: 'icheckbox_square-purple',
        radioClass: 'iradio_square-purple',
    });*/
	
});

