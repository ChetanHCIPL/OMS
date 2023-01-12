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
});
$('#created_at').dateDropper({
        dropWidth: 200,
        animate:false,
        //lock: today, 
        format: DATE_PICKER_FORMAT
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
        "url": assets + "/" +panel_text + "/contact-us/data",
    },
	"columns": [
        {"orderable": false, "class": "text-center"},
        {"orderable": true},
        {"orderable": false},
        {"orderable": true},
        {"orderable": true, "class": "text-center"},
        {"orderable": false, "class": "text-center"},
        {"orderable": false, "class": "text-center"}
    ],
	"order": [[ 4, "desc" ]],	
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

