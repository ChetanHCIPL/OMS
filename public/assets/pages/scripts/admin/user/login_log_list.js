$('#datatable_list').DataTable({
    "processing": true,
    "serverSide": true,
    "lengthMenu": [
        [10, 20, 50, 100, 150, -1],
        [10, 20, 50, 100, 150, "All"] // change per page values here
    ],
    "pageLength": REC_LIMIT, // default record count per page
    "ajax": {
        "url": assets + "/" +panel_text + "/login-history/data",
      },
    "columns": [
        {"orderable": false, "class": "text-center"},
        {"orderable": true},
        {"orderable": true, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": true, "class": "text-center"},
        {"orderable": false, "class": "text-center"},
        {"orderable": false}
    ],
    "order": [[ 3, "desc" ]],   
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
    $('#col2_filter').dateDropper({
        dropWidth: 200,
        animate:false,
        lock: today, 
        format: DATE_PICKER_FORMAT
    });
    $('#col3_filter').dateDropper({
        dropWidth: 200,
        animate:false,
        lock: today, 
        format: DATE_PICKER_FORMAT
    });
    $('#col4_filter').dateDropper({
        dropWidth: 200,
        animate:false,
        lock: today, 
        format: DATE_PICKER_FORMAT
    });   
    $('#col5_filter').dateDropper({
        dropWidth: 200,
        animate:false,
        lock: today, 
        format: DATE_PICKER_FORMAT
    });
});
