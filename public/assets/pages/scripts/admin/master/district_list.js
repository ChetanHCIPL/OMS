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
    "ajax": assets + "/" +panel_text + "/districts/data",
	"columns": [
        {"orderable": false, "class": "text-center","width":"5%"},
        {"orderable": true,"width":"25%"},
        {"orderable": true, "class": "text-center","width":"15%"},
        {"orderable": true, "class": "text-center","width":"10%"},
        {"orderable": true, "class": "text-center","width":"10%"},
        {"orderable": true, "class": "text-center","width":"10%"},
        {"orderable": true, "class": "text-center","width":"10%"},
        {"orderable": true, "class": "text-center","width":"10%"},
        {"orderable": false, "class": "text-center","width":"10%"},
    ],
	"order": [[ 6, "desc" ]],
	columnDefs: [{
        orderable: false,
        targets:   0
    }],
	select: {
        style:    'multi',
        selector: 'td:first-child'
    }
});