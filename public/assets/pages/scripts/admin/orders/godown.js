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
    $('body').on('click','.viewclientorder',function(){
        if($(this).hasClass('show')){
            $('#datatable_list').find('.Data-'+$(this).attr('data-clientid')).remove();
            $(this).removeClass('show');
        }else{
            $(this).addClass('show');  
            var closetr=$(this).closest('tr');
            $.ajax({
                type: "POST",
                url: assets + "/" +panel_text + "/orders/godown/clientdata", 
                "data":{"_token":csrf_token,'dataname':$(this).attr('datasnane'),'datamax':$(this).attr('datamax'),'datasname':$(this).attr('datasnane'),"client_id":$(this).attr('data-clientid')},
                success: function (data) { 
                    if(data.error==0){
                        closetr.after(data.data)
                    }
                }
            });
        }
    })
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
        "url": assets + "/" +panel_text + "/orders/godown/data",
    },
	"columns": [
        {"orderable": false},
        {"orderable": true, "class": "text-left"},
        {"orderable": true, "class": "text-left"},
        {"orderable": true, "class": "text-left"},
        {"orderable": true, "class": "text-left"},
        {"orderable": true, "class": "text-left"},
        /*{"orderable": false, "class": "text-left"}*/
    ],
	"order": [[ 1, "DESC" ]],	
	columnDefs: [{
        orderable: false,
        targets:   0
    }],
   /* 'createdRow': function(row, data, dataIndex){
        // Use empty value in the "Office" column
        // as an indication that grouping with COLSPAN is needed
        
           // Add COLSPAN attribute
           $('td:eq(0)', row).attr('colspan', 5);
           $('td:eq(1)', row).css('display', 'none');
           $('td:eq(2)', row).css('display', 'none');
           $('td:eq(3)', row).css('display', 'none');
           $('td:eq(4)', row).css('display', 'none');
           
        
     },  */
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

function addRowsMore(Obj,cid){
    var plus_hidden = $(Obj).closest('tr').find(".plus_hidden").val();
    if(plus_hidden == 0){
        $.ajax({
            type: "POST",
            url: assets + "/" +panel_text + "/orders/godown/clientdata", 
            "data":{"_token":csrf_token,'dataname':$(Obj).attr('datasnane'),'datamax':$(Obj).attr('datamax'),'dataname':$(Obj).attr('datasnane'),"client_id":cid},
            success: function (data) {
                setGodownChallanDetailData(Obj,data, cid);
            }
        });
    }else{
        var response = {};
        setGodownChallanDetailData(Obj,response, cid);
    }
}

function setGodownChallanDetailData(Obj,response, cid){
    var plus_hidden = $(Obj).closest('tr').find(".plus_hidden").val();
    if(plus_hidden == 0){
        $(Obj).removeClass('fa-plus-square').addClass('fa-minus-square');
        var data_str = '';
        if(response.datamax>0){
            data_str += '<tr>'; 
                data_str += '<td colspan="1">&nbsp;</td>';
                data_str += '<td colspan="7">'; 
                    data_str += '<table class="table table-bordered table-hover">';
                        data_str += '<thead>';
                            data_str += '<th width="2%">#</th>';
                            data_str += '<th>Product Name</th>';
                            data_str += '<th>SKU</th>';
                            data_str += '<th>Order Quantity</th>';
                            data_str += '<th>Available Quantity</th>';
                            data_str += '<th>Weight</th>';
                            data_str += '<th>Action</th>';
                        data_str += '</thead>';
                        data_str += '<tbody>';
                        for(i=0;i<response.datamax;i++){
                            data_str += '<tr>';
                                data_str += '<td>'+(i+1)+'</td>';
                                data_str += '<td>CBSE</td>';
                                data_str += '<td>Deqa</td>';
                                data_str += '<td>5</td>';
                                data_str += '<td>48</td>';
                                data_str += '<td>12</td>';
                                data_str += '<td> <button type="button" class="btn btn-info" data-toggle="modal" data-target="#default">Dispatch</button></td>';
                            data_str += '</tr>';
                        }
                        data_str += '</tbody>';
                    data_str += '</table>';
                data_str += '</td>'; 
            data_str += '</tr>'; 
        }else{
            data_str += '<tr>'; 
                data_str += '<td colspan="7">Something went wrong</td>';
            data_str += '</tr>';

        }
        
        $(data_str).insertAfter($(Obj).closest('tr'));
        $(Obj).closest('tr').find(".plus_hidden").val(1)

    }else{
        $(Obj).removeClass('fa-minus-square').addClass('fa-plus-square');
        $(Obj).closest('tr').next().remove();
        $(Obj).closest('tr').find(".plus_hidden").val(0)
    }
}
