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
    $('#col5_filter').change(function(){
        $('#col4_filter').attr('max',$(this).val());
    });
    $('#col4_filter').change(function(){
        $('#col5_filter').attr('min',$(this).val());
    });
    $('.client_name_search').on('keyup','[type="text"]',function(){
        $.ajax({
            type: "POST",
            url: clientFilter, 
            "data":{"_token":csrf_token,"search_arr":$(this).val(),'search_status':1},
            success: function (data) { 
                
                var selectId = $('#col1_filter').selectize();
                var control = selectId[0].selectize;
                control.clearOptions(); 
                if(data.length > 0) { 
                    for(var i = 0;i < data.length; i++) {
                        control.addOption(
                            {
                                value:data[i].id,
                                text:data[i].client_name
                            }
                        );
                    }
                }
            }
        });
    });
});
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
        "url": assets + "/" +panel_text + "/orders/challan/data",
    },
	"columns": [
        {"orderable": false, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": false},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true,"class": "text-right"},
        {"orderable": true, "class": "text-center"},
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
$('#datatable_list').scroll(function() {
    if ( $(".fixedHeader-floating").is(":visible") ) {
        $(".fixedHeader-floating").scrollLeft( $(this).scrollLeft() );
    }
});

