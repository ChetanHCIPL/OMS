$(document).ready(function(){
    $('#col5_filter').selectize({
        create: false,
        sortField: {
            field: 'text',
            direction: 'asc'
        },
    });
});
$('#datatable_list').DataTable({
    "processing": true,
    "serverSide": true,
    "lengthMenu": [
        [10, 20, 50, 100, 150, -1],
        [10, 20, 50, 100, 150, "All"] // change per page values here
    ],
    "pageLength": REC_LIMIT, // default record count per page
    "ajax": assets + "/" +panel_text + "/taluka/data",
	"columns": [
        {"orderable": false, "class": "text-center"},
        {"orderable": true},
        {"orderable": true, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": false, "class": "text-center"},
    ],
	"order": [[ 7, "desc" ]],	
	columnDefs: [{
        orderable: false,
        targets:   0
    }],
	select: {
        style:    'multi',
        selector: 'td:first-child'
    }
});