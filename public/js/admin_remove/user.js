$(document).ready(function() {   
  $('#datatable_list').dataTable( {
      "processing": true,
      "serverSide": true,
      "pageLength": '10', 
      "ajax": {
          "data":{"_token":csrf_token},
          "url": userajaxlist,
          "type": "POST"
      },
       'columns': [
          {"orderable": false, "class": "text-center"},
          {"orderable": true},
          {"orderable": true},
          {"orderable": true},
          {"orderable": true}, 
          {"orderable": false, "class": "text-center"} 
        ],
        "order": [[ 1, "desc" ]],   
         columnDefs: [{
              orderable: false,
              targets:   0
          }],
  } );
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
});