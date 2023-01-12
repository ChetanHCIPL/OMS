$(document).ready(function() {
    $('#secttab a:first').click();    
    //  ***** set selectize  
    $('.selectize-select').selectize({
        create: false,
        sortField: {
            field: 'text',
            direction: 'asc'
        },
    }); 
   var tabid = $("#secttab > li ").find('a.active').attr('id'); 
   showData(tabid);
   $('.nav-link').click(function() {
       var target = $(this).attr("id"); // activated tab
       showData(target);
       reset_filter() ;
    });
});
$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
          var target = $(e.target).attr("id"); // activated tab 
          showData(target);
          reset_filter() ;
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
            url: assets + "/" +panel_text + "/email-template/data",
            data: {'sectionid': id, } ,
        },

        "columns": [
            {"orderable": false, "class": "text-center"},
            {"orderable": true},
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


//  ***** Check/Uncheck checkbox on click of first cell
$("#datatable_1 tbody").on('click', 'tr td:first-child', function () {
    var checked = false;
    if(!$(this).parent('tr').hasClass('selected')) {
        checked = true;
    }
    $($(this).eq(0)).find('input').prop('checked', checked);

    // ***** if all checkbox select/deselect manually from single page, check/uncheck .group-checkable checkbox
    var tot_cnt = $('tbody > tr > td:nth-child(1) input[type="checkbox"]', $('#datatable_1')).length;
    var checked_cnt = $('tbody > tr > td:nth-child(1) input[type="checkbox"]:checked', $('#datatable_1')).length;
    if(tot_cnt == checked_cnt) {
        $('.group-checkable').prop("checked",true);
    }else {
        $('.group-checkable').prop("checked",false);
    }
});


//  ***** Check/Uncheck All checkbox
$('body').on('change', '.group-checkable', function() {
    var rows, checked;
    var divId = $(this).closest('table').attr('id');
    rows = $('#'+divId).find('tbody tr');
    checked = $(this).prop('checked');
    $.each(rows, function() {
        if(checked)
            $(this).find('td').closest('tr').addClass('selected');
        else
            $(this).find('td').closest('tr').removeClass('selected');

        var checkbox = $($(this).find('td').eq(0)).find('input').prop('checked', checked);

    });


});