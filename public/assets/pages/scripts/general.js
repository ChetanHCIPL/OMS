jQuery(document).ready(function () {
    $('.selectize-select1').selectize({ create: false, dropdownParent: 'body'});
});

(function(window, document, $) {
  'use strict';
  //Custom File Input
  $('.form-group .custom-file input').change(function (e) {
    
      $(this).next('.custom-file-label').html(e.target.files[0].name);
  });
})(window, document, jQuery);
setTimeout(function(){ $("#list-alert").addClass('d-none') }, 1000);

function dt_search() {
    $('#filter_div').find('div').each(function (i) {
        var i = $(this).attr('data-column');  // getting column index
        var v = $('#col' + i + '_filter').val();  // getting search input value
        if (typeof v != "undefined" && v != '')
            //console.log(v);
            $('#datatable_list').DataTable().columns(i).search(v).draw()
    }); 
}

// ****** reset list datatable search filter
function reset_datatable() {

    $('form.reset-form')[0].reset(); // reset form list
    if($('.selectize-select')){ // clear selectize
        $('.selectize-select').each(function(){
            if($(this)[0] && $(this)[0].selectize){
                $(this)[0].selectize.clear();
            }
        });
    }
    // $('.select2-hidden-accessible').select2({data: [{id: '', text: ''}]});
    //clear select2
    $('#datatable_list').DataTable().columns().search('').draw(); // reset datatable value
}
$(document).ready(function(){
   
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
});


// Function to display message after complete request of page
function displayMessageBox(isError, msg,add_btn=1,isDisplayAddMore=1,isDisplayGoToList=1) {
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
                <!--h5><i class="la la-arrow-right"></i> ${heading}</h5-->
                <p class="${class_msg}"><strong>${msg}</strong></p>
            </div>
            <div class="modal-footer">`;
            
            if (isError) {
                html += `<button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Ok</button>`;
            }else{
                if(isDisplayAddMore == 1)
                {
                   
                    html += `<button type="button" id="add-new-record" class="btn grey btn-outline-secondary" onclick="reloadAddPage()">Add More</button>`;
                }    
                
                if(add_btn == 1){
                    if(mode=='Update' || mode=='SentEmailtoinquiry'){
                        html += `<button type="button" id="add-new-record" class="btn grey btn-outline-secondary" onclick="reloadPage()">Continue</button>`;
                    }
                }
                if(isDisplayGoToList == 1){
                    html += `<button type="button" class="btn btn-outline-success" onclick="closeAllModals()">Go To List</button>`;
                }   
            }

            html += `</div>`;
   
    setTimeout(function () {
        $('#btn_ok').focus();
    }, 1000);

    return html;
} 

// Function to display message after complete request of page
function displayMsgBox(isError, msg,add_btn=0) {
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
    //console.log(mode);
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
            if(mode == 'View')
            {
                 html += `<button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal" >Ok</button>`;
            }
            else{
                html += `<button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal"  onclick="reloadPage()" >Ok</button>`;
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
    // if(route_for_popup == 'admin/user/grid')
    // {
    //     window.location.href = assets + "/" + panel_text + "/" + route_for_popup;
    // }else{
    // }
}

// Function to Reload update page
function reloadPage() {
    if($("#customActionName").val() == 'Add'){
        window.location.href = assets + "/" + panel_text + "/" + route_for_popup + "/edit/"+$("#encoded_id").val();
    }else{
        window.location.reload();
    }
    
}
// Function to Add More Records
function reloadAddPage() {
    window.location.href = assets + "/" + panel_text + "/" + route_for_popup + "/add/";
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
function checkAction(mode, page_name,action_name){
    var IdArr = [];
    $('tbody').find('input[type="checkbox"]:checked').each(function () {
       IdArr.push($(this).val());
    });
    var token = $("input[name='_token']").val();
    if(action_name == 'undefined' || action_name ==  undefined ){ 
           action_name =  'data';
    }
    if(IdArr.length > 0){
        
         // Confirmation Pop-up 
        bootbox.confirm({
            closeButton: false,
            message: '<p class="alert alert-success"><strong>Are you sure you want to "'+mode+'" record(s) ?</strong></p>',
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
                            url: assets + "/" +panel_text + "/"+page_name+"/"+action_name,
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
                                    if(page_name == 'course-module'){
                                        page_name = 'module';
                                    }else if(page_name == 'course-module-chapter'){
                                        page_name = 'chapter';
                                    }else if(page_name == 'course-module-chapter-topic'){
                                        page_name = 'topic';
                                    }else if((page_name == 'plan')){
                                        check = response[0].check;
                                        if(check == 0){
                                            isDisplayAddMore = 0;
                                            $('#msg-modal-popup').modal({
                                                backdrop: 'static',
                                                keyboard: false
                                            });
                                            $("#msg-modal").trigger("click");
                                            var isDisplayAddMore = 0; //No
                                            var add_btn = 1; 
                                            var str = displayMessageBox(isError,msg,1,isDisplayAddMore);
                                            $("#msg-html").html(str);
                                            return false;
                                        }
                                    }
                                    $("#list-msg").html("Please select atleast one "+page_name+".");
                                    $("#list-alert").removeClass("alert-success");
                                    $("#list-alert").addClass("alert-danger");
                                    $("#list-alert").removeClass('d-none');
                                }
                                if(page_name == 'email-template' || page_name == 'auction')
                                {
                                    $("#datatable_1").DataTable().ajax.reload(); 
                                    $('#secttab a:first').click(); 
                                }else{
                                    $("#datatable_list").DataTable().ajax.reload();
                                }
                            }
                        });
                }
            }
        });
    }else{
        if(page_name == 'course-module'){
            page_name = 'module';
        }else if(page_name == 'course-module-chapter'){
            page_name = 'chapter';
        }else if(page_name == 'course-module-chapter-topic'){
            page_name = 'topic';
        }
        $("#list-msg").html("Please select atleast one "+page_name+".");
        $("#list-alert").removeClass("alert-success");
        $("#list-alert").addClass("alert-danger");
        $("#list-alert").removeClass('d-none');
    }
	//Commented by IvaNirmal on 2-9-19 according to client feedback
    //setTimeout(function(){ $("#list-alert").addClass('d-none') }, 5000);
}


// ***** Change Status OR delete multiple records from list.
function checkActionold(mode, page_name,action_name){
    var IdArr = [];
    
    $('tbody').find('input[type="checkbox"]:checked').each(function () {
       IdArr.push($(this).val());
    });
    var token = $("input[name='_token']").val();
    if(IdArr.length > 0){
        var ans = confirm("Are you sure you want to "+mode+" record(s) ?");
        if (ans == true) {
            
            if(action_name == 'undefined' || action_name ==  undefined ){ 
                action_name =  'data';
            }
            $.ajax({
                type: "POST",
                url: assets + "/" +panel_text + "/"+page_name+"/"+action_name,
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
                    if(page_name == 'email-template')
                    { 
                        $("#datatable_1").DataTable().ajax.reload(); 
                    }else{
                        $("#datatable_list").DataTable().ajax.reload();
                    }
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
// function deleteSingleRecord(id,page_name){
//     var token = $("input[name='_token']").val();
//     var IdArr = [];
//     if(id > 0){
//         IdArr.push(id);
//         var ans = confirm("Are you sure you want to delete record ?");
//         if (ans == true) {
//             $.ajax({
//                 type: "POST",
//                 url: assets + "/" +panel_text + "/"+page_name+"/data",
//                 data: { 'customActionName': "Delete",  'id': IdArr, '_token': token},
//                 success: function (data) {
//                     response = eval(data);
//                     msg = response[0].msg;
//                     isError = response[0].isError;

//                     $("#msg-modal").trigger("click");
//                     var str = displayMessageBox(isError, msg);
//                     $("#msg-html").html(str);
//                 }
//             });
//         }
//     }
// }


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
            message: '<p class="alert alert-success"><strong>Are you sure you want to delete record ?</strong></p>',
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
    if($('.selectize-select1')){
    $('.selectize-select1').each(function(){
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
        }else if(element_type == "text" || element_type == "checkbox" || element_type == "password" ){
            element_type = 'input[name="' + element_name+'"]';
        }
       if(element_type == 'select-multiple')
       {    
            // for sub category for auction module
            var tab_id = $('select[name="sub_category_id[]"]').closest('.tab-pane').attr('aria-labelledby');        
       }else{
            var tab_id = $(element_type).closest('.tab-pane').attr('aria-labelledby');
       }
        var collaps_id = $("#"+tab_id).parent().closest('div').parent().attr('id');
        if ($('a[href="#'+collaps_id+'"]').attr('aria-expanded') == 'false')
            $('a[href="#'+collaps_id+'"]').trigger('click');
        $("#"+tab_id).trigger('click');
    }
}
 
function modalMessageBox(msg,heading,isError = 0) {
    if(isError)
    {
        var class_nm = "bg-danger";
       
    } else {
        var class_nm = "bg-success"; 
    }

    var html = ``; 
    html = `<div class="modal-header  ${class_nm}  white">
                <h4 class="modal-title white" id="myModalLabel9">  ${heading}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"> 
                <p><strong>${msg}</strong></p>
            </div>
            <div class="modal-footer">`; 
             
            html += `<button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Ok</button>`;
            
            html += `</div>`;
   
    setTimeout(function () {
        $('#btn_ok').focus();
    }, 1000); 
    return html;
}

function pollwin(url, w, h, winname){
    if (typeof winname == "undefined")
        winname = 'pollwindow' + Math.random();
    pollwindow = window.open(url, winname, 'top=0,left=0,status=no,toolbars=no,scrollbars=yes,width=' + w + ',height=' + h + ',maximize=no,resizable');
    pollwindow.focus();
}

function backToSearchArea() {
    $("#display").val("HTML");
    $("#back_search").css('display', 'none');
    $('#print_btn').css('display', 'none');
    $("#serching_area").css('display', '');
    $("#searched_result").css('display', 'none');
}

function BackToSearch(url){
    window.location = url;
}

function closeWindow(){
    parent.window.close();
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

