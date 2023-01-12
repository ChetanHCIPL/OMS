$(document).ready(function() {   
    var userRole = $('#user_role').val()
    $('#datatable_list').dataTable( {
        "processing": true,
        "serverSide": true,
        "pageLength": '10', 
        "ajax": {
            "data":{"_token":csrf_token,'user_role':userRole},
            "url": userajaxlist,
            "type": "POST"
        },
         'columns': [
            {"orderable": false, "class": "text-center"},
            {"orderable": false, "class": "text-center"},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true, "class": "text-center"},
            {"orderable": false, "class": "text-center"},
            {"orderable": true, "class": "text-center"},
            {"orderable": false, "class": "text-center"},
          ],
         "fnDrawCallback": function () {
            $(".fancybox").fancybox({
                openEffect  : 'none',
                closeEffect : 'none'
            });
          },
          "order": [[ 2, "asc" ]],   
          columnDefs: [{
                orderable: false,
                targets:   0
            }],
          select: {
              style:    'multi',
              selector: 'td:first-child'
          }
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
      
      //  ***** Check/Uncheck All checkbox
      $('body').on('change', '.group-checkable', function() {
          var rows, checked;
          rows = $('#datatable_list').find('tbody tr');
          checked = $(this).prop('checked');
          $.each(rows, function() {
              if(checked)
                  $(this).find('td').closest('tr').addClass('selected');
              else
                  $(this).find('td').closest('tr').removeClass('selected');
  
              var checkbox = $($(this).find('td').eq(0)).find('input').prop('checked', checked);
  
          });
      });
      $('.selectize-select').selectize({
          create: false,
          sortField: {
              field: 'text',
              direction: 'asc'
          },
      });
  });