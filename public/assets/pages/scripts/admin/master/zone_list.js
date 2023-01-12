$('#datatable_list').DataTable({
    "processing": true,
    "serverSide": true,
    "lengthMenu": [
        [10, 20, 50, 100, 150, -1],
        [10, 20, 50, 100, 150, "All"] // change per page values here
    ],
    "pageLength": REC_LIMIT, // default record count per page
    "ajax": assets + "/" + panel_text + "/zone/data",
	"columns": [
        {"orderable": false, "class": "text-center"},
        {"orderable": true},
        {"orderable": true, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": false, "class": "text-center"}
    ],
	"order": [[ 5, "desc" ]],	
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
    $('.selectize-select').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }, dropdownParent: 'body'});
});