$('#datatable_list').DataTable({
    "processing": true,
    "serverSide": true, 
    "lengthMenu": [
        [10, 20, 50, 100, 150, -1],
        [10, 20, 50, 100, 150, "All"] // change per page values here
    ],
    "pageLength": REC_LIMIT, // default record count per page
    "ajax": assets + "/" +panel_text + "/notification-template/data",
    "columns": [
            {"orderable": false, "class": "text-center"},
            {"orderable": true},
            {"orderable": true, "class": "text-center"},
            {"orderable": false, "class": "text-center"}
        ],
    "order": [[ 1, "desc" ]],      
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
    //  ***** set selectize 
    $('.selectize-select').selectize({ 
        create: false,
        sortField: {
            field: 'text',
            direction: 'asc'
        },
        dropdownParent: 'body'
    });
    if(window.localStorage.getItem('tabId') != null){
        $('#'+window.localStorage.getItem('tabId')).click(); 
        localStorage.removeItem('tabId');
    }else{
        $('#secttab a:first').click();  
        var tabid = $("#secttab > li ").find('a.active').attr('id');
        showData(tabid);
    }
   
});
$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
  var target = $(e.target).attr("id"); // activated tab
   showData(target);
});
//tab wise datatable load
function showData(id){
    //id = active tab_id
    $('#datatable_'+id).DataTable({
        "processing": true,
        "serverSide": true,
        "lengthMenu": [
            [10, 20, 50, 100, 150, -1],
            [10, 20, 50, 100, 150, "All"] // change per page values here
        ],
        "pageLength": REC_LIMIT, // default record count per page
        "ajax": {
            url: assets + "/" +panel_text + "/notification-template/data",
            data: {'sectionid': id, } ,
        },

        "columns": [
            {"orderable": false, "class": "text-center"},
            {"orderable": true},
            {"orderable": true, "class": "text-center"},
            {"orderable": false, "class": "text-center"}
        ],
        "order": [[ 1, "desc" ]],   
        columnDefs: [{
            orderable: false,
            targets:   0},{'width': '1%', 'targets': 0},
            {'width': '50%', 'targets': 1},
            {'width': '20%', 'targets': 2},
            {'width': '9%', 'targets': 3}],
        select: {
            style:    'multi',
            selector: 'td:first-child'
        },
        "bDestroy": true
    });
}

// Function: Datatable - Advanced Filter
function search_filter() {
    var tabid = $("#secttab > li ").find('a.active').attr('id');
    var filterArr = [];
    $('#filter_div').find('.column_filter').each(function () {
        var k = $(this).attr('name');  // search input name
        var v = $(this).val();  // search input value
        if (typeof v != "undefined" && v != ''){
            filterArr.push({'key':k, 'val':v.trim()});
        }
    }); 
    //set search value with json object
    $('#datatable_'+tabid).DataTable().columns(0).search(JSON.stringify(filterArr)).draw();
}

// Function: Datatable - Reset Advanced Filter
function reset_filter() {
     var tabid = $("#secttab > li ").find('a.active').attr('id');
    
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
   $('#datatable_'+tabid).DataTable().columns().search('').page.len(REC_LIMIT).draw(); 
}
//check,Uncheck all checkbox
$('body').on('change', '.group-checkable', function() {
    var tabid = $("#secttab > li ").find('a.active').attr('id');
    var rows, checked;
    rows = $('#datatable_'+tabid).find('tbody tr');
    checked = $(this).prop('checked');
    $.each(rows, function() {
        if(checked)
            $(this).find('td').closest('tr').addClass('selected');
        else
            $(this).find('td').closest('tr').removeClass('selected');

        var checkbox = $($(this).find('td').eq(0)).find('input').prop('checked', checked);

    });
});
// ***** Change Status OR delete multiple records from list.
function checkAction(mode, page_name){
    var tabid = $("#secttab > li ").find('a.active').attr('id');
    var IdArr = [];
    
    $('tbody').find('input[type="checkbox"]:checked').each(function () {
       IdArr.push($(this).val());
    });
    var token = $("input[name='_token']").val();
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
                if (result) {
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
                            $($('#datatable_'+tabid).find('th').eq(0)).find('input').prop('checked','');
                            $('#datatable_'+tabid).DataTable().ajax.reload();
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