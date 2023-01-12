var last_index = 0;
var FormValidation = function () {

    // validation using icons
    var handleValidation = function () {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
        var form = jQuery('#frmadd');
        var error = jQuery('.alert-danger', form);
        var success = jQuery('.alert-success', form);
        form.on('submit', function() {
            for(var instanceName in CKEDITOR.instances) {
                 CKEDITOR.instances[instanceName].updateElement();
             }
        })
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "", // validate all fields including form hidden input
            rules: {
                order_date: {
                    required: true,
                },
                client_id: {
                    required: true,
                },
                client_address_id: {
                    required: true,
                },
                client_contact_id: {
                    required: true,
                },
                sales_user_id: {
                    required: true,
                },
                order_form_photo: {
                    extension: "pdf",
                },
                client_ship_address_id: {
                    required: true,
                },
                dispatch_date: {
                    required: true,
                },
                transporter: {
                    required: true,
                },
                route_area: {
                    required: true,
                },
                payment_due_days: {
                    required: true,
                },
                due_date: {
                    required: true,
                },
                /*order_remark: {
                    required: true,
                },*/
            },
            messages: {
                order_date: {required: "Enter order date"},
                client_id:  {required: "Select client"},
                client_address_id:  {required: "Select client address"},
                client_contact_id:  {required: "Select client contact"},
                sales_user_id:  {required: "Select Sales user"},
                order_form_photo: {extension: "Please select a file with a valid extension."}, 
                client_ship_address_id:  {required: "Select Shpping address"},
                dispatch_date:  {required: "Select Dispatch Date"},
                transporter:  {required: "Select Transporter"},
                route_area:   {required: "Select Route area"},
                payment_due_days:   {required: "Select payment due days"},
                due_date:   {required: "Enter due date"},
                //order_remark:   {required: "Enter order remark"},
            },

            invalidHandler: function (event, validator) { //display error alert on form submit              
                success.addClass('d-none');
                error.removeClass('d-none'); 
                window.scrollTo(error, -200);
                setTimeout(function(){ error.addClass('d-none') }, 5000);
                //jQuery("#base-tab_1").trigger('click');
                // tab Validation Error Trigger
                tabValidationErrorTrigger(validator);  
                 
            },
            errorPlacement: function (error, element) {
                if (jQuery(element).is("select")) {
                    error.insertAfter(element.next(".selectize-select"));
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            },
            highlight: function (element) { // hightlight error inputs
                
                jQuery(element).closest('.form-group').removeClass("has-success").addClass('has-error'); // set error class to the control group   
            },
            unhighlight: function (element) { // revert the change done by hightlight
                
            },
            success: function (label, element) {
                var icon = jQuery(element).parent('.input-icon').children('i');
                jQuery(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                icon.removeClass("fa-warning").addClass("fa-check");
            },
            submitHandler: function (form) {

                var qty_error_flag = 0;
                $("input.prd_quantity").each(function() {

                    if(this.value <= 0) {

                        qty_error_flag = 1;
                    }
                });

                if( parseFloat($('#order_total').val()) > 0 && qty_error_flag == 0 ) {

                    $('.order-error-container').hide(); 

                    var formData = new FormData(form);
                    var msg = isError = "";
                    jQuery.ajax({
                        type: "POST",
                        url: assets + "/" +panel_text + '/orders/data',
                        data: formData,
                        contentType: false,
                        cache: false,
                        processData: false,
                         beforeSend: function() {
                          jQuery(".data_loader").show();
                        },
                        success: function (data) {
                            response = eval(data);
                            jQuery(".data_loader").hide();
                            msg = response[0].msg;
                            isError = response[0].isError;
                            jQuery('#msg-modal-popup').modal({
                                backdrop: 'static',
                                keyboard: false
                            });
                            jQuery("#msg-modal").trigger("click"); 
                            var str = displayMessageBox(isError,msg);
                            jQuery("#msg-html").html(str);
                        }
                    });
                }
                else {
                    $('.order-error-container').show();
                    theOffset = $('.products-container').offset();
                    var body = $("html, body");
                        body.stop().animate({scrollTop:theOffset.top - 50}, 500, 'swing', function() {
                    });
                }
            }
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleValidation();
        }
    };
}();
jQuery(document).ready(function () {

    $('.order-error-container').hide();

    addidplus=1;
    
    // Date Validation
    var dtToday = new Date();
    
    var month = dtToday.getMonth() + 1;
    var day = dtToday.getDate();
    var year = dtToday.getFullYear();
    if(month < 10)
        month = '0' + month.toString();
    if(day < 10)
        day = '0' + day.toString();
    
    var maxDate = year + '-' + month + '-' + day;

    // or instead:
    // var maxDate = dtToday.toISOString().substr(0, 10);

    $('.h_date').attr('min', maxDate);

    FormValidation.init();
    FormValidationaddress.init();
    jQuery('.selectize-select').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }, dropdownParent: 'body', plugins: ["remove_button"]});
    jQuery('#client_id').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }, dropdownParent: 'body'});
    jQuery('.selectize-state1').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
    jQuery('.selectize-district1').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
    jQuery('.selectize-taluka1').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
    $('.switchBootstrap').bootstrapSwitch();     
    $(".fancybox").fancybox({
        openEffect  : 'none',
        closeEffect : 'none'
    });
    // get data on change of client
    $('#client_id').change(function() { 
        var cid = $(this).val();
        var c_category_id = $('#client_id')[0].selectize;//get select
        $('#client_ids').val(cid);
        var category_id = c_category_id.options[c_category_id.items[0]]; //get current data() for the active option.
        if (category_id !== undefined){
            $('#client_discount_category_id').val(category_id.d_category_id);
        }

        $('#selected_address_id').closest('div').find('.help-block-error').remove();
        $('#client_ship_address_id').closest('div').find('.help-block-error').remove();
        $('#client_contact_id').closest('div').find('.help-block-error').remove();
        // get address data on change of client
        //console.log(addresslist);
        $.ajax({
            type: "POST",
            url: addresslist, 
            "data":{"_token":csrf_token,"client_id":cid},
            success: function (data) { 
            //    client=data.client;
                /*if(client.length>0){
                    console.log(client.sales_user_id);
                    var salesu=$('#sales_user_id').selectize();
                    var salesu1 = salesu[0].selectize;
                    salesu1.setValue(client.sales_user_id, false); 
                }*/
            //    data=data.address;
                var selectId = $('#client_address_id').selectize();
                var control = selectId[0].selectize;
                control.clearOptions(); 
                if(data.length > 0) { 
                    for(var i = 0;i < data.length; i++) {
                        if( data[i].use_for_billing == 1) {
                            control.addOption({value:data[i].id,text:data[i].address1});
                        }
                    }
                }

                control.setValue($('#selected_address_id').val(), false);
                 
                var selectId = $('#client_ship_address_id').selectize();
                var control = selectId[0].selectize;
                control.clearOptions(); 
                if(data.length > 0) { 
                    for(var i = 0;i < data.length; i++) {
                        if( data[i].use_for_shipping == 1 ) {
                            control.addOption({value:data[i].id,text:data[i].address1});
                        }
                    }
                }

                control.setValue($('#selected_ship_address_id').val(), false);

                if(data.length == 0 && cid > 0)
                {
                    $('#selected_address_id').closest('div').append('<span id="selected_address_id_error" class="help-block help-block-error">Biling address not found.</span>');
                    $('#client_ship_address_id').closest('div').append('<span id="client_ship_address_id_error" class="help-block help-block-error">Shipping address not found.</span>');
                }
        }
        });

        // get client contact names on change of client
        $.ajax({
            type: "POST",
            url: clientContacts, 
            "data":{"_token":csrf_token,"client_id":cid,},
            success: function (data) { 
                
                var selectId = $('#client_contact_id').selectize();
                var control = selectId[0].selectize;
                control.clearOptions(); 
                if(data.length > 0) { 
                    for(var i = 0;i < data.length; i++) {
                        var contact_str = data[i].full_name+" - ("+data[i].mobile_number+")";
                        control.addOption({value:data[i].id,text:contact_str});
                    }
                }

                if(data.length == 0 && cid > 0)
                {
                    $('#client_contact_id').closest('div').append('<span id="client_contact_id_error" class="help-block help-block-error">Contact name not found.</span>');
                }

                control.setValue($('#selected_contact_id').val(), false); 
            }
        });

    }).trigger('change');

    // get address details on change of client address
    $('#client_address_id').change(function() { 

        var cid = $(this).val();

        if(cid != '') {
            // get address data on change of client
            $.ajax({
                type: "POST",
                url: addressDetail, 
                "data":{"_token":csrf_token,"client_address_id":cid},
                success: function (data) { 
                    console.log(data);

                    $('#billing_address').show();
                    $('#billing_address').find('.billing-name').html(data.billingname);
                    $('#billing_address').find('.full-address').html(data.address);
                    /*$('#billing_address').find('.address1').text(data[0].address1);
                    $('#billing_address').find('.address2').text(data[0].address2);
                    $('#billing_address').find('.country').text(data[0].country_name);
                    $('#billing_address').find('.state').text(data[0].state_name);
                    $('#billing_address').find('.district').text(data[0].district_name);
                    $('#billing_address').find('.taluka').text(data[0].taluka_name);
                    $('#billing_address').find('.pincode').text(data[0].zip_code);*/
                }
            });
        }
        else {
            $('#billing_address').hide();
        }
    }).trigger('change');

    // get address details on change of client address
    $('#client_ship_address_id').change(function() { 
        var cid = $(this).val();
        if(cid != '') {
            // get address data on change of client
            $.ajax({
                type: "POST",
                url: addressDetail, 
                "data":{"_token":csrf_token,"client_address_id":cid},
                success: function (data) { 

                    $('#shipping_address').show();
                    $('#shipping_address').find('.shipping-name').html(data.billingname);
                    $('#shipping_address').find('.full-address').html(data.address);/*
                    $('#shipping_address').find('.address1').text(data[0].address1);
                    $('#shipping_address').find('.address2').text(data[0].address2);
                    $('#shipping_address').find('.country').text(data[0].country_name);
                    $('#shipping_address').find('.state').text(data[0].state_name);
                    $('#shipping_address').find('.district').text(data[0].district_name);
                    $('#shipping_address').find('.taluka').text(data[0].taluka_name);
                    $('#shipping_address').find('.pincode').text(data[0].zip_code);*/
                }
            });
        }
        else {
            $('#shipping_address').hide();
        }
    }).trigger('change');

    // get address details on change of client address
    $('#same_as_billing').click(function() { 

        var cid = $('#client_address_id').val();
        var selectId = $('#client_ship_address_id').selectize();
        var control = selectId[0].selectize;

        if( cid != '' && $("#same_as_billing").is(":checked") ) {
            
            control.setValue(cid, false); 
        }
        else {
            $('#shipping_address').hide();
            control.setValue(''); 
        }
    }).trigger('click');

    // show / hide filters on click on show filter button
    $('#filter_btn').click(function() { 
        $('#product_filters').toggle();

        if($('#product_filters').is(":visible")){

            $('#filter_btn').find('button').html('<i class="ft-filter"></i> Hide Filter');
        }
        else {

            $('#filter_btn').find('button').html('<i class="ft-filter"></i> Show Filter');

            $("#series")[0].selectize.clear();
            $("#medium_id").val('').trigger('change');
            $("#segment").val('').trigger('change');
        }        
    });

    // show products on click on add products button
    $('#default_quantity').on('blur','',function(){
        $('#default_quantity').closest('div').find('.help-block-error').remove();        
    })
    $('.add-products-btn').click(function() {
        $('#default_quantity').closest('div').find('.help-block-error').remove();        
        $('#product').closest('div').find('.help-block-error').remove(); 
        $('#select_cliect_name_error').html('');        
        var prods=$('#product').val();
        if(!$('#client_id').valid())
        {
            $('#select_cliect_name_error').html('please select client name');
            $('#select_cliect_name_error').css({"color": "red"});
            return false;
        }
        if(prods==''){
            $('#product').closest('div').append('<span id="product-error" class="help-block help-block-error">Select product.</span>');
            return false;
        }
        var DQVal=$('#default_quantity').val();
        if(DQVal.trim()==''){
            $('#default_quantity').after('<span id="default_quantity-error" class="help-block help-block-error">Enter default quatity.</span>');
            return false;
        }
        $('.order-error-container').hide(); 
        products_arr = [];
        $('#order_products table tbody:first tr').each(function(obj) {
            var elmt = $(this).find('td.weight');
            product_id = elmt.data('id');
            products_arr.push(parseInt(product_id));
        });
        arr = $('#product').val();
        var product_exists_count = 0;
        $.each(arr, function(index, item) {
            if($.inArray(parseInt(item), products_arr) != -1) {
                product_exists_count = 1;
            }
        });
        if( product_exists_count > 0 ) {
            var elmt = $('#product').siblings('.selectize-control');
            if($('#product-error').length == 0) {
                $('<span id="product-error" class="help-block help-block-error">Product(s) is already added! Please select different product(s).</span>').insertAfter(elmt);
            }
        }else{
            if($('#product-error').length > 0) {
                $('#product-error').remove();
            }
        }

        if($('#client_id').valid()) {
            if(product_exists_count <= 0 && $('#product').val() != '') {
                var c_category_id = $('#client_id')[0].selectize; //get select
                var category_id = c_category_id.options[c_category_id.items[0]]; //get current data() for the active option.
                //Get product details based on product ids
               $.ajax({
                    type: "POST",
                    url: productsData, //, "client_cat_id":category_id.d_category_id
                    "data":{"_token":csrf_token,"product_ids":$('#product').val(),'client_id':$('#client_id').val()},
                    success: function (data) { 
                        var data_str = '';
                        if(data.length > 0) { 
                            for(var i = 0; i < data.length; i++) {
                                current_index = last_index+i+1;
                                product_amount = parseFloat(data[i].mrp);
                                if($('#default_quantity').val() != '') {
                                    product_amount = parseFloat(data[i].mrp) * parseFloat($('#default_quantity').val());
                                }
                                var max_dis = 0;
                                if (data[i].max_discount != ''){
                                    max_dis = data[i].max_discount;
                                }
                                data_str += '<tr>';
                                   data_str += '<td scope="row" class="text-center product-index">'+current_index+'</td>';
                                   data_str += '<td class="text-center"><a href="#!" onclick ="delete_row($(this))" class="btn btn-danger"><i class="ft-trash-2"></i></a></td>';
                                   data_str += '<td class="weight" data-id="'+data[i].id+'" data-weight="'+ data[i].weight +'">'+data[i].name+'</td>';
                                   data_str += '<td><input type="hidden" class="form-control prd_id" value="'+ data[i].id +'" name="prd_id['+current_index+'][product_id]" id="prd_id_'+current_index+'" />'+data[i].code+'</td>';
                                   data_str += '<td><input type="hidden" class="form-control prd_weight" value="" name="prd_weight['+current_index+'][weight]" id="prd_weight_'+current_index+'" /><input type="number" min="1" max="'+data[i].max_order_qty+'" class="form-control prd_quantity" value="'+$('#default_quantity').val()+'" onchange="calculateProductAmount(this);" name="prd_quantity['+current_index+'][qty]" id="prd_quantity_'+current_index+'" /></td>';
                                   data_str += '<td class="text-right"> <input type="hidden" class="form-control prd_price" value="'+data[i].mrp+'" name="prd_price['+current_index+'][price]" id="prd_price_'+current_index+'" />'+data[i].mrp+'</td>';
                                   data_str += '<td><input type="hidden" class="form-control prd_dis_amount" value="" name="prd_dis_amount['+current_index+'][dis_price]" id="prd_dis_amount_'+current_index+'" /><input onchange="calculateProductAmount(this);" type="number" min="0" class="form-control prd_discount" value="" max="'+max_dis+'" name="prd_discount['+current_index+'][dic_percentage]" id="prd_discount_'+current_index+'" /></td>';
                                   data_str += '<td class="text-right prd_include_discount_'+current_index+'"><input type="hidden" class="form-control prd_include_discount" value="'+product_amount.toFixed(2)+'" name="prd_include_discount['+current_index+'][product_total]" id="prd_include_discount_'+current_index+'" /><input type="hidden" class="form-control prd_amount" value="'+product_amount.toFixed(2)+'" name="prd_amount['+current_index+'][prd_amount]" id="prd_amount_'+current_index+'" /><label class="prd_amt">'+product_amount.toFixed(2)+'</label></td>';
                                data_str += '</tr>';
                            }

                            last_index += data.length;
                        }
                        console.log(data_str);
                        $('#order_products tbody:first').append(data_str);
                        $('#order_products').show();

                        var total_qty = 0;
                        $("input[name='prd_quantity[]']").each(function() {
                            if(this.value != '') {
                                total_qty += parseInt(this.value);
                            }
                        });
                        $('.total-quantity').text(total_qty);
                        var total_weight = 0;
                        $("input[name='prd_quantity[]']").each(function() {

                            if(this.value != '') {
                                var weight = $(this).closest("tr").find(".weight").data('weight');
                                var qty = $(this).closest("tr").find(".prd_quantity").val();

                                if ( qty != ''  && weight != ''){
                                    total_weight = qty * weight;
                                }
                                $(this).closest("tr").find(".prd_weight").val(total_weight);
                            }
                        });
                        
                        // Calculate Sub Total.
                        calculateSubTotal();

                        // Calculate Discount.
                        calculateDiscount();

                        // Calculate NetTotal.
                        calculateNetTotal();

                        // Calculate Quantity.
                        calculateProductQuantity();

                        // Calculate Total Weight.
                        calculateAllProductWeight();
                    }
                });
            }
        }
        else {

            var body = $("html, body");
                body.stop().animate({scrollTop:0}, 500, 'swing', function() {
            });
        }
        reIndexProducts();
    });

    $('#apply_filter').click(function() { 
        $('#product').closest('div').find('.help-block-error').remove();
        if( $('#product_head').val() != '' ) {
            var searchObj = {
              "product_head": $('#product_head').val(),
              "medium_id": $('#medium_id').val(),
              "client_id": $('#client_id').val(),
              "segment": $('#segment').val(),
              "series": $('#series').val()
            };
            
            $.ajax({
                type: "POST",
                url: filterProducts, 
                "data":{"_token":csrf_token,"search_arr":searchObj},
                success: function (data) {                     
                    var selectId = $('#product').selectize();
                    var control = selectId[0].selectize;
                    control.clearOptions(); 
                    if(data.length > 0) { 
                        for(var i = 0;i < data.length; i++) {
                            control.addOption({
                                value:data[i].id,
                                text:data[i].name
                            });
                        }
                    }else{
                        $('#product').closest('div').append('<span id="product-error" class="help-block help-block-error">Product data not found.</span>');
                    }
                    //control.setValue($('#selected_product_id').val(), false);
                }
            });
        }
        else {
            $('#product_head').rules('add', { 
                required: true,
            });
            $('#product_head').valid();
        }
    }).trigger('click');


    $('#payment_due_days').on('change', function(){
        var s = $('#payment_due_days')[0].selectize; //get select
        var data = s.options[s.items[0]]; //get current data() for the active option.

        var chooseDate=new Date();
        chooseDate.setDate(chooseDate.getDate()+data.days);

        var month = chooseDate.getMonth() + 1;
        var day = chooseDate.getDate();
        
        if(month < 10)
            month = '0' + month.toString();
        if(day < 10)
            day = '0' + day.toString();
        
        var term_date = chooseDate.getFullYear() + '-' + month + '-' + day;
        $('#due_date').val(term_date);
     }).trigger('change');

   /* jQuery("#add_modal_box_add_address").hover(function(){
        jQuery('#add_address #editid').val('');
        jQuery('#add_address form').trigger("reset");
        jQuery('#add_address form .help-block-error').remove();
    });
    jQuery("#add_modal_box_add_address").click(function(){
        jQuery('#frmaddaddress #used_for_shipping').trigger('change');
        jQuery('#frmaddaddress #used_for_billing').trigger('change');
        addidplus++;
    });*/

     jQuery('.selectize-state1').change(function(){
        getDistricts1();
    })
    jQuery("#district1").on('change','',function(){
        getTalukabyDistrictsid1();
    });

    $('#client_id').change(function() {
        var selectId = $('#client_id').val();
        if(selectId != ""){ 
            $("#select_cliect_name_error").html('');
        }
        else{
            $('#select_cliect_name_error').html('please select client name');
            $('#select_cliect_name_error').css({"color": "red"});
        }
    });
   /* jQuery("#client_id").on('change','',function(){
       client_address_id = $( "#client_id").val();
    });*/
    jQuery('#used_for_billing').change(function(){
        if(!jQuery(this).prop('checked')){
            jQuery('#is_default_billing').closest('.custom-control.custom-checkbox').addClass('d-none');
            jQuery('#is_default_billing').prop('checked',false);
        }else{
            jQuery('#is_default_billing').closest('.custom-control.custom-checkbox').removeClass('d-none');
        }
    });
    jQuery('#used_for_shipping').change(function(){
        if(!jQuery(this).prop('checked')){
            jQuery('#is_default_shipping').closest('.custom-control.custom-checkbox').addClass('d-none');
            jQuery('#is_default_shipping').prop('checked',false);
        }else{
            jQuery('#is_default_shipping').closest('.custom-control.custom-checkbox').removeClass('d-none');
        }
    });

    $("#client_id")[0].selectize.on('type', function(){
        $.ajax({
            type: "POST",
            url: clientFilter, 
            "data":{"_token":csrf_token,"search_arr":arguments['0']},
            success: function (data) { 
                
                var selectId = $('#client_id').selectize();
                var control = selectId[0].selectize;
                control.clearOptions(); 
                if(data.length > 0) { 
                    for(var i = 0;i < data.length; i++) {
                        control.addOption(
                            {
                                value:data[i].id,
                                text:data[i].client_name
                            }
                        );
                    }
                }
            }
        });
    });
});

$('#add_modal_box_address').click(function() {
    var selectId = $('#client_id').val();
    if(selectId != ""){ 
        $("#select_cliect_name_error").html('');
        $('#add_client_address').modal('toggle');
        $('#myModal').modal('show');
    }
    else{
        $('#select_cliect_name_error').html('please select client name');
        $('#select_cliect_name_error').css({"color": "red"});
    }
});
function clearfieldvalue() {
    var validator = $('#frmaddaddress').validate();
    validator.resetForm();
}

function delete_row(row) {
    row.closest('tr').remove();

    calculateProductAmount(row);

    reIndexProducts();
}

function calculateProductAmount(eleobj){
    var price = $(eleobj).closest('tr').find('.prd_price').val();
    var qty = $(eleobj).closest('tr').find('.prd_quantity').val();
    var discount = $(eleobj).closest('tr').find('.prd_discount').val();


    var product_amount=0
    if (qty != '' && price != ''){
        product_amount = parseFloat(qty) * parseFloat(price);
    }

    var discount_price = 0;
    if (discount != ''){
        discount_price = parseFloat(product_amount) * parseFloat(discount) / 100;
        exclude_discount = product_amount - discount_price;
        $(eleobj).closest('tr').find('.prd_dis_amount').val(discount_price.toFixed(2));
        $(eleobj).closest('tr').find('.prd_amt').text(exclude_discount.toFixed(2));
        $(eleobj).closest('tr').find('.prd_amount').val(exclude_discount.toFixed(2));
        $(eleobj).closest('tr').find('.prd_include_discount').val(product_amount.toFixed(2));
    }else{
        $(eleobj).closest('tr').find('.prd_dis_amount').val(0);
        $(eleobj).closest('tr').find('.prd_amt').text(product_amount.toFixed(2));
        $(eleobj).closest('tr').find('.prd_amount').val(product_amount.toFixed(2));
        $(eleobj).closest('tr').find('.prd_include_discount').val(product_amount.toFixed(2));
    }

    // Calculate Sub Total.
    calculateSubTotal();

    // Calculate Discount.
    calculateDiscount();

    // Calculate NetTotal.
    calculateNetTotal();

    // Calculate Quantity wise Weight.
    calculateProductWeight(eleobj);

    // Calculate Quantity.
    calculateProductQuantity();

    // Calculate Total Weight.
    calculateAllProductWeight();
    
    // //Calculate Without Discount

    // //Calculate Discount when discount Change
    // var prd_dis_amt = $(".prd_dis_amount");
    // var dis_total = 0;
    
    // for(var i = 0; i < prd_dis_amt.length; i++){
    //     if ($(prd_dis_amt[i]).val() == NaN || $(prd_dis_amt[i]).val() == '' ) {
    //         dis_total = parseFloat(dis_total) + parseFloat(0);
    //     }else{
    //         dis_total = parseFloat(dis_total)+ parseFloat($(prd_dis_amt[i]).val());
    //     }            
    // }
    // $('#dis_total').text('₹ '+dis_total.toFixed(2));

    // var net_total = parseFloat(prd_exl_dis_total.toFixed(2)) - parseFloat(dis_total.toFixed(2))
    // $('#net_total').text('₹ '+net_total.toFixed(2));
}

// Calculate Sub Total
function calculateSubTotal (){
    
    var prd_exl_dis_amt = $(".prd_include_discount");
    var prd_exl_dis_total = 0;
    
    for(var i = 0; i < prd_exl_dis_amt.length; i++){
        prd_exl_dis_total = parseFloat(prd_exl_dis_total)+ parseFloat($(prd_exl_dis_amt[i]).val());
    }
    $('#sub_total_value').val(prd_exl_dis_total.toFixed(2));
    $('#sub_total').text('₹ '+prd_exl_dis_total.toFixed(2));
    
}

// Calculate Disocunt
function calculateDiscount (){
    var discount = $(".prd_dis_amount");
    var discount_price = 0;
    
    for(var i = 0; i < discount.length; i++){
        if ($(discount[i]).val() != ''){
            discount_price = parseFloat(discount_price)+ parseFloat($(discount[i]).val());
        }else{
            discount_price = parseFloat(discount_price)+ parseFloat(0);
        }
    }
    $('#dis_total_value').val(discount_price.toFixed(2));
    $('#dis_total').text('₹ '+discount_price.toFixed(2));
    
}

// Calculate Net Totals
function calculateNetTotal (){
    var sub_total = $("#sub_total_value").val();
    var discount_total = $("#dis_total_value").val();


    var net_total = sub_total;
    if (discount_total != 0 || discount_total != '' || discount_total != 'undefined'){
        net_total = parseFloat(net_total) - parseFloat(discount_total);
    }
    $('#net_total').text('₹ '+net_total.toFixed(2));
    $('#order_total').val(net_total.toFixed(2));
}

//Calculate Weight 
function calculateProductWeight (eleobj){
    
    var weight = $(eleobj).closest("tr").find(".weight").data('weight');
    var qty = $(eleobj).closest("tr").find(".prd_quantity").val();

    var total_weight = 0;
    if ( qty != ''  && weight != ''){
        total_weight = qty * weight;
    }

    $(eleobj).closest("tr").find(".prd_weight").val(total_weight);
}

//Calculate Weight 
function calculateProductQuantity (){
    
    var qty = $(".prd_quantity");
    var total_qty = 0;
    
    for(var i = 0; i < qty.length; i++){
        if ($(qty[i]).val() != ''){
            total_qty = parseFloat(total_qty)+ parseFloat($(qty[i]).val());
        }else{
            total_qty = parseFloat(total_qty)+ parseFloat(0);
        }
    }
    $('.total-quantity').text(total_qty);
}

//Calculate Weight 
function calculateAllProductWeight (){
    
    var weight = $(".prd_weight");
    var total_weight = 0;
    
    for(var i = 0; i < weight.length; i++){
        if ($(weight[i]).val() != ''){
            total_weight = parseFloat(total_weight)+ parseFloat($(weight[i]).val());
        }else{
            total_weight = parseFloat(total_weight)+ parseFloat(0);
        }
    }
    $('.total-weight').text(total_weight);
}


jQuery.validator.addMethod("LettersOnly", function(value, element) { 
    // allow letters and spaces only
    return this.optional(element) || /^[a-zA-Z]+$/i.test(value);
}, "Enter only Letters");
jQuery.validator.addMethod("numchar", function(value, element) { 
    return this.optional(element) || /^[-!@#$%&*+0-9]+$/i.test(value);
}, "Letters are not allowed.");

var FormValidationaddress = function () {
    // validation using icons
    var handleValidation = function () {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
        var form = jQuery('#frmaddaddress');
        var error = jQuery('.alert-danger', form);
        var success = jQuery('.alert-success', form);
        form.on('submit', function() {
            for(var instanceName in CKEDITOR.instances) {
                 CKEDITOR.instances[instanceName].updateElement();
             }
        })
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "", // validate all fields including form hidden input
            rules: {
                title: {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                },
                address1: {
                    required: true,
                    minlength: 3,
                    maxlength: 200,
                },
                address2: {
                    required: true,
                    minlength: 3,
                    maxlength: 200,
                },
                mobile_number: {
                    required: true,
                    minlength: 10,
                    maxlength: 10,
                },
                state1:{
                    required: true,
                },
                district1:{
                    required: true,
                },
                taluka1:{
                    required: true,
                },
                zip_code:{
                    required: true,
                    number: true,
                    minlength:6,
                    maxlength:6,
                },
            },
            messages: {
                title:{required: "Enter title.", minlength: "Enter atleast 3 characters for title.", maxlength: "Enter maximum 100 characters for title."},
                address1:{required: "Enter address 1.", minlength: "Enter atleast 3 characters for address 1.", maxlength: "Enter maximum 200 characters for address 1."},
                address2:{required: "Enter address 2.", minlength: "Enter atleast 3 characters for address 2.", maxlength: "Enter maximum 200 characters for address 2."},
                whatsapp_number:{required: "Enter whatsapp no.", minlength: "Enter atleast 10 digit for whatsApp no.", maxlength: "Enter 10 digit for whatsapp no."},
                mobile_number:{required: "Enter mobile no.", minlength: "Enter 10 digit for mobile no.", maxlength: "Enter 10 digit for mobile no."},
                district1:{required: "Select district."},
                state1:{required: "Select state."},
                taluka1:{required: "Select taluka."},
                zip_code:{required: "Select zip code.", minlength: "Enter 6 digit for zip code.", maxlength: "Enter 6 digit for zip code."},
            },
            invalidHandler: function (event, validator) { //display error alert on form submit              
                success.addClass('d-none');
                error.removeClass('d-none'); 
                window.scrollTo(error, -200);
                setTimeout(function(){ error.addClass('d-none') }, 5000);
                // tab Validation Error Trigger
                tabValidationErrorTrigger(validator);  
                 
            },
            errorPlacement: function (error, element) {
                if (jQuery(element).is("select")) {
                    error.insertAfter(element.next(".selectize-select"));
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            },
            highlight: function (element) {
                if(element.id=='state1'){
                    if(element.value==''){
                        jQuery('#frmaddaddress').find('#state1-error').remove();
                        dateSpan = document.createElement('span');
                        dateSpan.innerHTML = 'Select state.';
                        dateSpan.setAttribute("class", "help-block help-block-error");
                        dateSpan.setAttribute("id", "state1-error");
                        element.closest('.col-sm-5').append(dateSpan);
                        return false;
                    }
                }
                if(element.id=='district1'){
                    if(element.value==''){
                        jQuery('#frmaddaddress').find('#district1-error').remove();
                        dateSpan = document.createElement('span');
                        dateSpan.innerHTML = 'Select district.';
                        dateSpan.setAttribute("class", "help-block help-block-error");
                        dateSpan.setAttribute("id", "district1-error");
                        element.closest('.col-sm-5').append(dateSpan);
                        return false;
                    }
                }
                if(element.id=='taluka1'){
                    if(element.value==''){
                        jQuery('#frmaddaddress').find('#taluka1-error').remove();
                        dateSpan = document.createElement('span');
                        dateSpan.innerHTML = 'Select taluka.';
                        dateSpan.setAttribute("class", "help-block help-block-error");
                        dateSpan.setAttribute("id", "taluka1-error");
                        element.closest('.col-sm-5').append(dateSpan);
                        return false;
                    }
                }
                
            },
            unhighlight: function (element) { // revert the change done by hightlight
                
            },
            success: function (label, element) {
                var icon = jQuery(element).parent('.input-icon').children('i');
                jQuery(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                icon.removeClass("fa-warning").addClass("fa-check");
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                console.log(formData);
                var msg = isError = last_id = "";
                jQuery(".data_loader").show();
                jQuery.ajax({
                    type: "POST",
                    url: assets + "/" +panel_text + '/orders/data',
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                     beforeSend: function() {
                      jQuery(".data_loader").show();
                    },
                    success: function (data) {
                        jQuery(".data_loader").hide();
                        response = eval(data);
                        msg = response[0].msg;
                        isError = response[0].isError;
                        if(isError == 0){
                            $('#client_id').change(function() { 
                                var cid = $(this).val();
                                $('#client_ids').val(cid);
                                    // get address data on change of client
                                $.ajax({
                                    type: "POST",
                                    url: addresslist, 
                                    "data":{"_token":csrf_token,"client_id":cid},
                                    success: function (data) { 
                                        var selectId = $('#client_address_id').selectize();
                                        var control = selectId[0].selectize;
                                        control.clearOptions(); 
                                        if(data.length > 0) { 
                                            for(var i = 0;i < data.length; i++) {
                                                if( data[i].use_for_billing == 1) {
                                                    control.addOption({value:data[i].id,text:data[i].address1});

                                                }
                                            }
                                        }
                                        control.setValue($('#selected_address_id').val(), false); 
                                    }
                                });
                            }).trigger('change');
                            $('#btn_close_modal').trigger('click');
                            form.reset();
                        }
                    }
                });
            }
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleValidation();
        }
    };
}();

function getDistricts1(){  
    var sid = jQuery('#state1').val();
    jQuery.ajax({
          type: "POST",
          url: districtlist,
          data: { "_token":csrf_token,"stateid":sid},
          success: function (data) {
             var selectId = jQuery('#district1').selectize();
              var control = selectId[0].selectize;
              control.clearOptions();
              if(data.length > 0) {
                  for(var i = 0;i < data.length; i++) {
                      control.addOption({value:data[i].id,text:data[i].district_name});
                  }
              } 
            //  control.setValue(jQuery('#selected_district_id').val(), false);
          }
      });  
  }
   function getTalukabyDistrictsid1(){  
    var did = jQuery("#district1").val();
    jQuery.ajax({
          type: "POST",
          url: talukaurl,
          data: { "_token":csrf_token,"did":did},
          success: function (data) {
             var selectId = jQuery('#taluka1').selectize();
              var control = selectId[0].selectize;
              control.clearOptions();
              if(data.length > 0) {
                  for(var i = 0;i < data.length; i++) {
                      control.addOption({value:data[i].id,text:data[i].taluka_name});
                  }
              } 
              //control.setValue(jQuery('#selected_district_id').val(), false);
          }
      });  
  }

function closeAllModals() {
    jQuery('#btn_close_par').trigger("click");
    jQuery('#btn_close_modal').trigger("click");
    jQuery('.filter-cancel').trigger("click");
    jQuery("#reset").trigger("click");
    window.location.href = assets + "/" + panel_text + "/" + route_for_popup + "/list/";
}

// Function: Reindex products after adding/removing any product
function reIndexProducts(){
  var ind = 1;
 
  $('#order_products table tbody:first tr').each(function(obj) {

    $(this).find('td.product-index').text(ind);
    ind++;
  });

  last_index = (ind - 1);
}