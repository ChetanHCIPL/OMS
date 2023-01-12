jQuery(document).ready(function () {
    jQuery("#product_name").focus();
    FormValidation.init();
    jQuery('.selectize-select').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }, dropdownParent: 'body'});
	jQuery('.switchBootstrap').bootstrapSwitch();     
    jQuery(".fancybox").fancybox({
        openEffect  : 'none',
        closeEffect : 'none'
    });
    jQuery("#is_kit_product").click(function(){
        if(jQuery(this).prop("checked")){
            jQuery("#ftr").show();
        }else{
            jQuery("#ftr").hide();
        }
    })
    jQuery(".addplusclick").click(function(){
        //console.log();
        var tableG="";
        jQuery.each(jQuery('#productsel').val(), function( key, value ) {
            //alert( key + ": " + value );
            tableG=tableG+'<tr><th scope="row">'+(key+1)+'</th><td class="text-center"><a href="#" class="btn btn-danger"><i class="ft-trash-2"></i></a></td><td>'+value+'</td> <td class="text-right" >'+((key*10)+10)+'</td></tr>';
          });
        jQuery("#list-product").show();
        jQuery("#list-product tbody").html(tableG);
    })
    jQuery(".addplusclick").click(function(){
        
        var num1=parseInt(jQuery("#segmentsem").attr('data-num'),10);

        var mediumBoard_str = '';
        if( mediumBoard.length > 0 ){

            mediumBoard_str = '<select class="select2" name="medium[]" placeholder="Select Medium" data-placeholder="Select Medium" >';
            $.each(mediumBoard, function (key, val) {

                var selected = '';
                if(val.id == $("#Medium2").val()) {
                    selected = 'selected';
                }

                mediumBoard_str += '<option value="'+val.id+'" '+selected+'>'+val.name+' - '+val.board_name+'</option>';
            });

            mediumBoard_str += '</select>';
        }

        var segment_str = '';
        if( segment.length > 0 ){

            segment_str = '<select class="select2" name="segment[]" placeholder="Select Segment" data-placeholder="Select Segment" >';
            $.each(segment, function (key, val) {

                var selected = '';
                if(val.id == $("#segment1").val()) {
                    selected = 'selected';
                }

                segment_str += '<option value="'+val.id+'" '+selected+'>'+val.name+'</option>';
            });

            segment_str += '</select>';
        }

        var semester_str = '';
        if( semester.length > 0 ){

            semester_str = '<select class="select2" name="semester[]" placeholder="Select Semester" data-placeholder="Select Semester" ><option value="">Select Semester</option>';
            $.each(semester, function (key, val) {

                var selected = '';
                if(val.id == $("#semester2").val()) {
                    selected = 'selected';
                }

                semester_str += '<option value="'+val.id+'" '+selected+'>'+val.name+'</option>';
            });

            semester_str += '</select>';
        }

        duplicate_medium = 0;
        duplicate_segment = 0;
        duplicate_semester = 0;

        $("#segmentsem tbody.dataajax tr").each(function(i, obj) {

            duplicate_ind = '';
            row_medium = $(this).find('select[name="medium[]"]').val();
            row_segment = $(this).find('select[name="segment[]"]').val();
            row_semester = $(this).find('select[name="semester[]"]').val();

            if($('#Medium2').val() == row_medium) {
                duplicate_ind = i;
                duplicate_medium = 1;
            }

            if(duplicate_ind == i && row_segment == $("#segment1").val() ) {
                duplicate_segment = 1;
            }

            if(duplicate_ind == i && row_semester == $("#semester2").val() ) {
                duplicate_semester = 1;
            }
        });

        if(duplicate_medium == 1 && duplicate_segment == 1 && duplicate_semester == 1) {

            var elmt = $('#segmentsem').parent('.list-product');
            
            if($('#segment-mapping-error').length == 0) {

                $('<span id="segment-mapping-error" class="help-block help-block-error">Duplicate rows not allowed.</span>').insertAfter(elmt);
            }
        }
        else {

            if($('#segment-mapping-error').length > 0) {
                $('#segment-mapping-error').remove();
            }

            jQuery("#segmentsem tbody.dataajax").append('<tr><td class="text-center">'+num1+'</td><td>'+mediumBoard_str+'</td><td class="text-center">'+segment_str+'</td><td class="text-center">'+semester_str+'</td><td class="text-center"><a href="javascript:void(0);" class="btn btn-danger deletesegme"><i class="ft-trash-2"></i></a></td></tr>');
            jQuery("#optn_details tr td:first-child").html(parseInt(num1)+1);
            jQuery("#segmentsem").attr('data-num',parseInt(num1)+1);

            $('select[name="medium[]"]').select2();
            $('select[name="segment[]"]').select2();
            $('select[name="semester[]"]').select2();
        }
    });
    jQuery(".addProductclick").click(function(){
        
        var num1=parseInt(jQuery("#segmentsem").attr('data-num'),10);

        new_product_id = $("#product_id").val();

        all_selected_products = [];

        $("select[name='product_ids[]']").each(function(i, obj) {

            all_selected_products.push($(this).val());
        });

        var max_quantity = $('#product_quantity').attr('data-max');

        if($.inArray(new_product_id, all_selected_products) != -1) {

            var elmt = $('.product-mapping-tbl').parent('.list-product');

            if($('#product-quantity-error').length > 0) {
                $('#product-quantity-error').remove();
            }
            
            if($('#product-mapping-error').length == 0) {

                $('<span id="product-mapping-error" class="help-block help-block-error">Product is already added! Please select different product.</span>').insertAfter(elmt);
            }
        }
        else if(parseInt($('#product_quantity').val()) > parseInt(max_quantity)) {

            var elmt = $('.product-mapping-tbl').parent('.list-product');

            if($('#product-mapping-error').length > 0) {
                $('#product-mapping-error').remove();
            }
            
            if($('#product-quantity-error').length == 0) {

                $('<span id="product-quantity-error" class="help-block help-block-error">Please enter quantity less than or equal to max quantity: '+max_quantity+'</span>').insertAfter(elmt);
            }
        } 
        else {

            if($('#product-mapping-error').length > 0) {
                $('#product-mapping-error').remove();
            }

            if($('#product-quantity-error').length > 0) {
                $('#product-quantity-error').remove();
            }

            var allProducts_str = '';
            if( allProducts.length > 0 ){

                allProducts_str = '<select class="select2" name="product_ids[]" placeholder="Select Product" data-placeholder="Select Product" >';
                $.each(allProducts, function (key, val) {

                    var selected = '';
                    if(val.id == $("#product_id").val()) {
                        selected = 'selected';
                    }

                    allProducts_str += '<option value="'+val.id+'" '+selected+' data-maxquantity="'+val.max_order_qty+'">'+val.name+' - ₹'+val.mrp+'</option>';
                });

                allProducts_str += '</select>';
            }

            product_qty_str = '<input type="number" class="quantity input-sm form-control" name="product_quantity[]" min="1" id="product_qty_'+num1+'" value="'+$("#product_quantity").val()+'" />';
            
            jQuery("#segmentsem tbody.dataajax").append('<tr><td class="text-center">'+num1+'</td><td>'+allProducts_str+'</td><td class="text-center">'+product_qty_str+'</td><td class="text-center"><a href="javascript:void(0);" class="btn btn-danger deletesegme"><i class="ft-trash-2"></i></a></td></tr>');
            jQuery("#optn_details tr td:first-child").html(parseInt(num1)+1);
            jQuery("#segmentsem").attr('data-num',parseInt(num1)+1);
            
            $('select[name="product_ids[]"]').select2();
        }
    });
    jQuery("body").on('click','.deletesegme',function(){
        jQuery(this).closest('tr').remove();
        var num1=parseInt(0);
        jQuery.each(jQuery('.dataajax tr'), function( key, value ) {
            num1=parseInt(num1)+1;
            jQuery(this).find('td:first-child').html(num1);
        });
        jQuery("#segmentsem").attr('data-num',parseInt(num1)+1);
    });
    jQuery("body").on('click','.stockalert',function(){
        if(jQuery(this).val()==1){
            jQuery(".Stockq").removeClass('d-none');
        }else{
            jQuery(".Stockq").addClass('d-none');
        }
    });

    /*$('input[name="product_quantity[]"]').on('change', function() {

        row_quantity = $(this).val();

        max_quantity = $(this).parents('tr').find('select[name="product_ids[]"] option:selected').data('maxquantity');

        var index = $(this).data('attr');

        if( row_quantity > max_quantity ) {

            var elmt = $('.product-mapping-tbl').parent('.list-product');

            if($('#product_quantity_'+index).length == 0) {

                $('#product_quantity_'+index).text('Please enter quantity less than or equal to max quantity: '+max_quantity);
            }

            if($('#product-quantity-error').length == 0) {

                $('<span id="product-quantity-error" class="help-block help-block-error">Please enter quantity less than or equal to max quantity: '+max_quantity+'</span>').insertAfter(elmt);
            }
        }
        else {
            if($('#product_quantity_'+index).length > 0) {
                $('#product_quantity_'+index).remove();
            }
        }
    });*/
});

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
                'product_head_id':{
                    required: true,
                },
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                },
                series_id:{
					required:true,	
				},
                /*'medium[]':{
                    required: true,
                }
				'segment[]':{
					required:true,	
				},*/
                max_order_qty:{
                    required: true,
                    number:true,
                },
                hsn_number: {
                    required: true,
                    minlength: 3,
                    maxlength: 10,
                },
                products_code: {
                    required: true,
                    minlength: 3,
                    maxlength: 20,
                },
                mrp:{
					required:true,	
				},
                pages:{
					required:true,	
                    number:true,
				},
                badho:{
					required:true,
                    number:true,	
				},
                weight:{
					required:true,	
				},
                stock:{
					required:true,
                    numchar:true,
				},
                stock_alert_qty:{
                    required:"#stockalertyes:checked"
                }
            },
            messages: {
                
                'product_head_id': {required: "Select product head."},
                'medium[]': {required: "Select medium."},
                name: {required: "Enter product name.", minlength: "Enter atleast 3 characters for product name.", maxlength: "Enter maximum 100 characters for product name."},
                products_code: {required: "Enter product code.", minlength: "Enter atleast 3 characters for product code.", maxlength:"Enter maximum 20 characters for product code."},
                hsn_number: {required: "Enter hsn number.", minlength: "Enter atleast 3 characters for hsn number.", maxlength:"Enter maximum 10 characters for hsn number."},
				series_id:{required: "Select series."},
                'segment[]':{required: "Select segment."},
                mrp:{required: "Enter mrp."},
                pages:{required: "Enter number of pages."},
                badho:{required: "Enter badho."},
                weight:{required: "Enter weight."},
                stock:{required: "Enter stock.", number:"Enter valid number only"},
                stock_alert_qty:{required: "Enter stock alert qty."}
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
                if(element.id=='product_head_id'){
                    if(element.value==''){
                        jQuery('#frmadd').find('#product_head_id-error').remove();
                        dateSpan = document.createElement('span');
                        dateSpan.innerHTML = 'Select product head.';
                        dateSpan.setAttribute("class", "help-block help-block-error");
                        dateSpan.setAttribute("id", "product_head_id-error");
                        element.closest('.col-md-9').append(dateSpan);
                    }
                }
                if(element.id=='medium'){
                    if(element.value==''){
                        jQuery('#frmadd').find('#medium-error').remove();
                        dateSpan = document.createElement('span');
                        dateSpan.innerHTML = 'Select medium.';
                        dateSpan.setAttribute("class", "help-block help-block-error");
                        dateSpan.setAttribute("id", "medium-error");
                        element.closest('.col-md-9').append(dateSpan);
                    }
                }
                if(element.id=='series_id'){
                    if(element.value==''){
                        jQuery('#frmadd').find('#series-error').remove();
                        dateSpan = document.createElement('span');
                        dateSpan.innerHTML = 'Select series.';
                        dateSpan.setAttribute("class", "help-block help-block-error");
                        dateSpan.setAttribute("id", "series-error");
                        element.closest('.col-md-9').append(dateSpan);
                    }
                }
                if(element.id=='segment'){
                    if(element.value==''){
                        jQuery('#frmadd').find('#segment-error').remove();
                        dateSpan = document.createElement('span');
                        dateSpan.innerHTML = 'Select segment.';
                        dateSpan.setAttribute("class", "help-block help-block-error");
                        dateSpan.setAttribute("id", "segment-error");
                        element.closest('.col-md-9').append(dateSpan);
                    }
                }
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

				var qty_error = 0;
				$("input[name='product_quantity[]']").each(function(i, obj) {
					var qtyval = $(this).val();
					var qtymax = $(this).attr("data-max");
                    var id_name = '#product_quantity_'+i;
					//alert(parseInt(qtyval)+" === "+parseInt(qtymax));
					if(parseInt(qtyval) > parseInt(qtymax)) {
						qty_error++;
						var qty_msg = "Please enter a value less than or equal to "+qtymax+".";
						jQuery('#frmadd').find(id_name).remove();
                        dateSpan = document.createElement('span');
                        dateSpan.innerHTML = qty_msg;
                        dateSpan.setAttribute("class", "help-block help-block-error");
                        dateSpan.setAttribute("id", id_name);
                        $(this).closest('td').append(dateSpan);
					}
                    else {
                        $('#frmadd').find(id_name).remove();
                    }
				});
				if(qty_error == 0) {
					var formData = new FormData(form);
					var msg = isError = "";
					jQuery.ajax({
						type: "POST",
						url: assets + "/" +panel_text + '/products/data',
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

function deleteImage(){
    jQuery("#delete-image-box").trigger("click");
}
function closeAllModals() {
    jQuery('#btn_close_par').trigger("click");
    jQuery('#btn_close_modal').trigger("click");
    jQuery('.filter-cancel').trigger("click");
    jQuery("#reset").trigger("click");
    window.location.href = assets + "/" + panel_text + "/" + route_for_popup + "/grid/";
}
function deleteUploadedImage() {
    var id = jQuery("#id").val();
    var flag     = jQuery("#flag_old").val(); 
    var token      = jQuery("input[name='_token']").val();
    bootbox.confirm({
        message: '<p class="alert alert-success"><strong>Are you sure you want to delete?</strong></p>',
        buttons: {
            confirm: {
                label: "Confirm",
                className: 'btn btn-outline-success',
            },
            cancel: {
                label: "Cancel",
                className: 'btn grey btn-outline-secondary',
            }
        },
        callback: function (result) {
            if(result){
                $.ajax({
                    type: "POST",
                    url: assets + "/" + panel_text + "/products/data",
                    data: { 'customActionName': "DeleteImage",  'id': id, 'flag' : flag, '_token': token},
                    success: function (data) {
                        jQuery("#delete-image-box_btn_close").trigger("click");
                        response = eval(data);
                        msg = response[0].msg;
                        isError = response[0].isError;
                        if(isError == 0) {
                            jQuery('#delete-image').hide();
                            jQuery("#show-image").attr("src", '');
                            jQuery('#image_old').val('');
                            jQuery("#image").val('');
                            jQuery("#show-image").hide();
                        }
                        else {
                            jQuery("#msg-modal").trigger("click");
                            var str = displayMessageBox(isError, msg);
                            jQuery("#msg-html").html(str);
                        }
                    }
                });
            }
        }
    });
}

$('select[name="product_ids[]"]').change(function(){ 

    var maxQuantity = $(this).find(':selected').data('maxquantity');
    console.log(maxQuantity)
    $(this).parents('tr').find('input[name="product_quantity[]"]').attr({
        "data-max": maxQuantity,
    });
}).trigger('change');

$('#product_id').change(function() { 
   var maxQuantity = $(this).find(':selected').data('maxquantity');
    console.log(maxQuantity)
    $(this).parents('tr').find('#product_quantity').attr({
        "data-max": maxQuantity,
    });
}).trigger('change');