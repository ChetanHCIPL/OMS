$(document).ready(function() {
    //  ***** set selectize 
    $('a.nav-link').click(function(){
       // $('#frmlist').trigger('click');

       $('#orderstatus').val($(this).attr('data-id'));
       datatable_search_filter();
    });
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
        "url": assets + "/" +panel_text + "/orders/data?orderstatus="+orderstatus,
    },
	"columns": [
        {"orderable": false, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": false},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true, "class": "text-right"},
        {"orderable": true, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": false, "class": "text-center"}
    ],
	"order": [[ 1, "DESC" ]],	
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

