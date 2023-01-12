$('#datatable_list').DataTable({
    "processing": true,
    "serverSide": true,
    "lengthMenu": [
        [10, 20, 50, 100, 150, -1],
        [10, 20, 50, 100, 150, "All"] // change per page values here
    ],
    "pageLength": REC_LIMIT, // default record count per page
    "ajax": assets + "/" +panel_text + "/state/data",
	"columns": [
        {"orderable": false, "class": "text-center","width":"1%"},
        {"orderable": true,"width":"37%"},
        {"orderable": true},
        {"orderable": true, "class": "text-center","width":"10%"},
        {"orderable": true, "class": "text-center","width":"10%"},
        {"orderable": true, "class": "text-center","width":"10%"},
        {"orderable": true, "class": "text-center","width":"10%"},
        {"orderable": false, "class": "text-center","width":"12%"},
    ],
	"order": [[ 5, "Desc" ]],	
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
    $('#col4_filter').selectize({
        create: false,
        sortField: {
            field: 'text',
            direction: 'asc'
        },
    }); 
});
