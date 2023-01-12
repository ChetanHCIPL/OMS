var last_index = 0;
jQuery(document).ready(function () {

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

    //FormValidation.init();
    jQuery('.selectize-select').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }, dropdownParent: 'body', plugins: ["remove_button"]});

    // get data on change of client
    jQuery('body').on('keyup','.enter_discount',function(){
        jQuery(this).val(validatenumberwithdot(jQuery(this).val()));
    })
    $('#client_id').change(function() { 

        var cid = $(this).val();
        // get address data on change of client
        $.ajax({
            type: "POST",
            url: addresslist, 
            "data":{"_token":csrf_token,"client_id":cid},
            success: function (data) { 
                
                var selectId = $('#client_address_id').selectize();
                var control = selectId[0].selectize;
                control.clearOptions(); 

                var selectShipId = $('#client_ship_address_id').selectize();
                var controlShip = selectShipId[0].selectize;
                controlShip.clearOptions(); 
                if(data.length > 0) { 
                    for(var i = 0;i < data.length; i++) {

                        if( data[i].use_for_billing == 1) {

                            control.addOption({value:data[i].id,text:data[i].address1});
                        }
                        if( data[i].use_for_shipping == 1 ) {

                            controlShip.addOption({value:data[i].id,text:data[i].address1});
                        }
                    }
                }
                control.setValue($('#selected_address_id').val(), false); 
                controlShip.setValue($('#selected_ship_address_id').val(), false); 
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
                    $('#billing_address').show();
                    $('#billing_address').find('.billing-name').html(data.billingname);
                    $('#billing_address').find('.full-address').html(data.address);/*
                    $('#billing_address').find('.address1').text(data[0].address1);
                    $('#billing_address').find('.address2').text(data[0].address2);
                    $('#billing_address').find('.country').text(data[0].country_name);
                    $('#billing_address').find('.state').text(data[0].state_name);
                    $('#billing_address').find('.district').text(data[0].district_name);
                    $('#billing_address').find('.taluka').text(data[0].taluka_name);
                    $('#billing_address').find('.pincode').text(data[0].zip_code);*/
                }
            });
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
    $('.add-products-btn').click(function() { 
        if($('#product').val() != '') {
           //Get product details based on product ids
           $.ajax({
                type: "POST",
                url: productsData, 
                "data":{"_token":csrf_token,"product_ids":$('#product').val(), "client_id":$('#client_id').val(),'client_cat_id':''},
                success: function (data) { 
                    var data_str = '';
                    if(data.length > 0) { 
                        for(var i = 0; i < data.length; i++) {
                            console.log(data[i]);
                            current_index = last_index+i+1;
                            product_amount = parseFloat(data[i].mrp);
                            if($('#default_quantity').val() != '') {
                                product_amount = parseFloat(data[i].mrp) * parseFloat($('#default_quantity').val());
                            }
                            data_str += '<tr>';
                               data_str += '<th scope="row">'+current_index+'</th>';
                               data_str += '<td class="text-center"><a href="#!" onclick ="delete_row($(this))" class="btn btn-danger"><i class="ft-trash-2"></i></a></td>';
                               data_str += '<td class="weight" data-weight="'+ data[i].weight +'">'+data[i].name+'</td>';
                               data_str += '<td><input type="hidden" class="form-control prd_id" value="'+ data[i].id +'" name="prd_id['+current_index+'][product_id]" id="prd_id_'+current_index+'" />'+data[i].code+'</td>';
                               data_str += '<td class="text-center">'+data[i].stock+'</td>';
                               data_str += '<td class="text-center">'+data[i].stock+'</td>';
                               data_str += '<td><input type="hidden" class="form-control prd_weight" value="" name="prd_weight['+current_index+'][weight]" id="prd_weight_'+current_index+'" /><input type="number" max="'+data[i].max_order_qty+'" class="form-control prd_quantity" value="'+$('#default_quantity').val()+'" onchange="calculateProductAmount(this);" name="prd_quantity['+current_index+'][qty]" id="prd_quantity_'+current_index+'" /></td>';
                               data_str += '<td class="text-right"> <input type="hidden" class="form-control prd_price" value="'+data[i].mrp+'" name="prd_price['+current_index+'][price]" id="prd_price_'+current_index+'" />'+data[i].mrp+'</td>';
                               data_str += '<td><input type="hidden" class="form-control prd_dis_amount" value="" name="prd_dis_amount['+current_index+'][dis_price]" id="prd_dis_amount_'+current_index+'" /><input onchange="calculateProductAmount(this);" type="number" class="form-control prd_discount" value="" name="prd_discount['+current_index+'][dic_percentage]" id="prd_discount_'+current_index+'" /></td>';
                               data_str += '<td class="text-right prd_include_discount_'+current_index+'"><input type="hidden" class="form-control prd_include_discount" value="'+product_amount.toFixed(2)+'" name="prd_include_discount['+current_index+'][product_total]" id="prd_include_discount_'+current_index+'" /><input type="hidden" class="form-control prd_amount" value="'+product_amount.toFixed(2)+'" name="prd_amount['+current_index+'][prd_amount]" id="prd_amount_'+current_index+'" /><label class="prd_amt">'+product_amount.toFixed(2)+'</label></td>';
                            data_str += '</tr>';
                        }

                        last_index += data.length;
                    }

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
        }else{

        }
    });

    $('#apply_filter').click(function() { 
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
                        control.addOption(
                            {
                                value:data[i].id,
                                text:data[i].name
                            }
                        );
                    }
                }
                //control.setValue($('#selected_product_id').val(), false);
            }
        });
    });

    // Change order status to challan generated
    $('#challan_generate').click(function() { 
        console.log('demo');
        var formData = {
          "id": $('#id').val(),
          "sales_user_id": $('#sales_user_id').val(),
        //   "client_ship_address_id": $('#client_ship_address_id').val(),
        //   "client_contact_id": $('#client_contact_id').val(),
        //   "client_address_id": $('#client_address_id').val(),
          "status": 3
        };
        
        $.ajax({
            type: "POST",
            url: challanGenerate, 
            "data":{"_token":csrf_token,formData},
            success: function (data) { 
                console.log(data)
                response = eval(data);
                jQuery(".data_loader").hide();
                msg = response.msg;
                isError = response.isError;
                jQuery('#msg-modal-popup').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                jQuery("#msg-modal").trigger("click"); 
                var str = displayMessageBox(isError,msg,0,0,0);
                jQuery("#msg-html").html(str);
            }
        });
    });


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
     });
   
});

function delete_row(row) {
    // Calculate Sub Total.
    
    $('#sub_total').text('₹ 0.00');
    $('#dis_total').text('₹ 0.00');
    $('.total-weight').text('0');
    $('.total-quantity').text('0');
    $('#net_total').text('0');
     
    row.closest('tr').remove();
}

function deleteLastRow(tableID = 'datatable_list') {
    var table = document.getElementById(tableID);
    var rowCount = table.rows.length;
    table.deleteRow(rowCount -1);
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
                'order_date':{
                    required: true,
                },
            },
            messages: {
                'order_date': {required: "Select order date."}
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
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleValidation();
        }
    };
}();

jQuery.validator.addMethod("LettersOnly", function(value, element) { 
	// allow letters and spaces only
    return this.optional(element) || /^[a-zA-Z]+$/i.test(value);
}, "Enter only Letters");
jQuery.validator.addMethod("numchar", function(value, element) { 
    return this.optional(element) || /^[-!@#$%&*+0-9]+$/i.test(value);
}, "Letters are not allowed.");

function closeAllModals() {
    jQuery('#btn_close_par').trigger("click");
    jQuery('#btn_close_modal').trigger("click");
    jQuery('.filter-cancel').trigger("click");
    jQuery("#reset").trigger("click");
    window.location.href = assets + "/" + panel_text + "/" + route_for_popup + "/grid/";
}
function validatenumberwithdot(s) {
    var rgx = /^[0-9]*\.?[0-9]*$/;
    //var rgx = '/^(100(\.0{1,2})?|([0-9]?[0-9](\.[0-9]{1,2})))$/';
    var val=s.match(rgx);
    console.log(val.split("."));
    return s.match(rgx);;
    
}