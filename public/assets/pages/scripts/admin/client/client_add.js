jQuery(document).ready(function () {
    $('#show_school_data').hide();
    jQuery("#client_name").focus();
    FormValidation.init();
    FormValidationaddress.init();
    FormValidationcontact.init();
    FormValidationverify.init();
    addidplus=1;
    setTimeout(function(){
        if(jQuery('#district').val()>0){
            getTalukabyDistrictsid();
        }
    }, 100);    
    jQuery('.selectize-state').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
    jQuery('.selectize-district').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
    jQuery('.selectize-taluka').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
    jQuery('.selectize-status').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
    jQuery('.selectize-state1').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
    jQuery('.selectize-district1').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
    jQuery('.selectize-taluka1').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
    jQuery('.selectize-designation_id').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
    jQuery('.selectize-grade').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
    jQuery('.selectize-payment_terms').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
    jQuery('.selectize-discount_category').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
    jQuery('.selectize-section').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
    jQuery('.selectize-school_type').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
    jQuery('.selectize-board').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
    jQuery('.selectize-medium').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
    $('#sales_user_id').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
    jQuery('select').change(function(){
        jQuery(this).closest('div').find('.help-block.help-block-error').html('');
    })
    jQuery('.selectize-state').change(function(){
        getDistricts();
    })
    jQuery('.selectize-state1').change(function(){
        getDistricts1();
    })
    jQuery('.selectize-client_type').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
	jQuery('.switchBootstrap').bootstrapSwitch();     
    jQuery('#frmadd #cash_discount1').on('switchChange.bootstrapSwitch', function (event, state) {
        if(jQuery('#frmadd #cash_discount1').prop('checked')){
            jQuery('#frmadd .cash_discount_1').removeClass('d-none');
        }else{
            jQuery('#frmadd .cash_discount_1').addClass('d-none');
        }
    });
    jQuery('#frmadd #cash_discount2').on('switchChange.bootstrapSwitch', function (event, state) {
        if(jQuery('#frmadd #cash_discount2').prop('checked')){
            jQuery('#frmadd .cash_discount_2').removeClass('d-none');
        }else{
            jQuery('#frmadd .cash_discount_2').addClass('d-none');
        }
    }); 
    jQuery(".fancybox").fancybox({
        openEffect  : 'none',
        closeEffect : 'none'
    });
    jQuery("#district").on('change','',function(){
        getTalukabyDistrictsid();
    });
    jQuery("body").on('click','#con_data_address .deleterow',function(){
       var deleterow=jQuery(this).closest('tr').find('.address_editid').val();
       if(deleterow.length>0){
            jQuery(this).closest('.table-responsive').find('.deleted_address').append('<input type="hidden" name="deleted_address[]" value="'+deleterow+'">');
       }
        jQuery(this).closest('tr').remove();
    });
    jQuery("body").on('click','#con_data_contact .deleterow',function(){
        var deleterow=jQuery(this).closest('tr').find('.contact_editid').val();
       if(deleterow.length>0){
            jQuery(this).closest('.table-responsive').find('.deleted_contact').append('<input type="hidden" name="deleted_contact[]" value="'+deleterow+'">');
       }
        jQuery(this).closest('tr').remove();
    });
    jQuery("#district1").on('change','',function(){
        getTalukabyDistrictsid1();
    });
    jQuery("#add_modal_box_add_address").hover(function(){
        jQuery('#add_address #editid').val('');
        jQuery('#add_address form').trigger("reset");
        jQuery('#add_address form .help-block-error').remove();
    });
    jQuery("#add_modal_box_add_address").click(function(){
        jQuery('#frmaddaddress #used_for_shipping').trigger('change');
        jQuery('#frmaddaddress #used_for_billing').trigger('change');
        addidplus++;
        jQuery('#add_address #addid').val(addidplus);
    })
    jQuery("#add_modal_box_add_contact").hover(function(){
        jQuery('#add_contact #editid').val('');
        jQuery('#add_contact form').trigger("reset");
        jQuery('#add_contact form .help-block-error').remove();
    })    
    jQuery("#add_modal_box_add_contact").click(function(){
        addidplus++;
        jQuery('#add_contact #addid').val(addidplus);
    })
    jQuery("#con_data_address").on('click','.editrow',function(){
        var maintr=jQuery(this).closest('tr');
        jQuery('#frmaddaddress #editid').val(maintr.find('.address_editid').val());
        jQuery('#frmaddaddress #addid').val(maintr.find('.address_addid').val());
        jQuery('#frmaddaddress #title').val(maintr.find('.address_title').val());
        jQuery('#frmaddaddress #address1').val(maintr.find('.address_address1').val());
        jQuery('#frmaddaddress #address2').val(maintr.find('.address_address2').val());
        jQuery('#frmaddaddress #mobile_number').val(maintr.find('.address_mobile_number').val());
        jQuery('#frmaddaddress #email').val(maintr.find('.address_email').val());
        jQuery('#frmaddaddress #zip_code').val(maintr.find('.address_zip_code').val());
        jQuery('#add_address form .help-block-error').remove();        
        if(maintr.find('.address_status').val()==1){
            jQuery('#frmaddaddress #status').prop('checked',true);
            jQuery('#frmaddaddress #status').trigger('change');
        }else{
            jQuery('#frmaddaddress #status').prop('checked',false);
            jQuery('#frmaddaddress #status').trigger('change');
        }
        if(maintr.find('.address_used_for_billing').val()==1){
            jQuery('#frmaddaddress #used_for_billing').prop('checked',true);
            jQuery('#frmaddaddress #used_for_billing').trigger('change');
        }else{
            jQuery('#frmaddaddress #used_for_billing').prop('checked',false);
            jQuery('#frmaddaddress #used_for_billing').trigger('change');
        }
        if(maintr.find('.address_used_for_shipping').val()==1){
            jQuery('#frmaddaddress #used_for_shipping').prop('checked',true);
            jQuery('#frmaddaddress #used_for_shipping').trigger('change');
        }else{
            jQuery('#frmaddaddress #used_for_shipping').prop('checked',false);
            jQuery('#frmaddaddress #used_for_shipping').trigger('change');
        }
        if(maintr.find('.address_is_default_billing').val()==1){
            jQuery('#frmaddaddress #is_default_billing').prop('checked',true);
            jQuery('#frmaddaddress #is_default_billing').trigger('change');
        }else{
            jQuery('#frmaddaddress #is_default_billing').prop('checked',false);
            jQuery('#frmaddaddress #is_default_billing').trigger('change');
        }
        if(maintr.find('.address_is_default_shipping').val()==1){
            jQuery('#frmaddaddress #is_default_shipping').prop('checked',true);
            jQuery('#frmaddaddress #is_default_shipping').trigger('change');
        }else{
            jQuery('#frmaddaddress #is_default_shipping').prop('checked',false);
            jQuery('#frmaddaddress #is_default_shipping').trigger('change');
        }
        
        //jQuery('#frmaddaddress #district1 [value="'+maintr.find('.address_district1').val()+'"]').html();
        var select = jQuery("#frmaddaddress #state1").selectize();
        var selectize = select[0].selectize;
        selectize.setValue(selectize.search(maintr.find('.address_state1').attr('dhtml')).items[0].id);
        jQuery('#add_address').modal('show');
        setTimeout(function(){
            var select = jQuery("#frmaddaddress #district1").selectize();
            var selectize = select[0].selectize;
            selectize.setValue(selectize.search(maintr.find('.address_district1').attr('dhtml')).items[0].id);
            setTimeout(function(){
                var select1 = jQuery("#frmaddaddress #taluka1").selectize();
                var selectize1 = select1[0].selectize;
                var talukahtml=maintr.find('.address_taluka1').attr('dhtml');
                selectize1.setValue(selectize1.search(talukahtml).items[0].id);
            },100);
        },100);
    });
    jQuery("#con_data_contact").on('click','.editrow',function(){
        var maintr=jQuery(this).closest('tr');
        jQuery('#frmaddcontact #editid').val(maintr.find('.contact_editid').val());
        jQuery('#frmaddcontact #addid').val(maintr.find('.contact_addid').val());
        jQuery('#frmaddcontact #full_name').val(maintr.find('.contact_full_name').val());
        jQuery('#frmaddcontact #mobile_number').val(maintr.find('.contact_mobile_number').val());
        jQuery('#frmaddcontact #whatsapp_number').val(maintr.find('.contact_whatsapp_number').val());
        jQuery('#frmaddcontact #designation_id').val(maintr.find('.contact_designation').val());
        jQuery('#frmaddcontact #date1').val(maintr.find('.contact_dob').val());
        jQuery('#frmaddcontact #department').val(maintr.find('.contact_department').val());
        jQuery('#add_contact form .help-block-error').remove();
        if(maintr.find('.contact_is_default').val()==1){
            jQuery('#frmaddcontact #is_default').prop('checked',true);
            jQuery('#frmaddcontact #is_default').trigger('change');
        }else{
            jQuery('#frmaddcontact #is_default').prop('checked',false);
            jQuery('#frmaddcontact #is_default').trigger('change');
        }
        console.log(maintr.find('.contact_status').val());
        if(maintr.find('.contact_status').val()==true || maintr.find('.contact_status').val()==1){
            jQuery('#frmaddcontact #status').prop('checked',true);
            jQuery('#frmaddcontact #status').trigger('change');
        }else{
            jQuery('#frmaddcontact #status').prop('checked',false);
            jQuery('#frmaddcontact #status').trigger('change');
        }
        var select = jQuery("#frmaddcontact #designation_id").selectize();
        var selectize = select[0].selectize;
        selectize.setValue(selectize.search(maintr.find('.contact_designation_id').attr('dhtml')).items[0].id);
        jQuery('#add_contact').modal('show');
    });
    jQuery('#frmadd #status').change(function(){
        if(jQuery(this).val()==2){
            jQuery('#verifiedpopup').modal('show');
        }
    });
    jQuery('#verifiedpopup #save_verified').click(function(){
        jQuery('#verified_date').val(jQuery('#vdate').val());
    });
    jQuery('#verifiedpopup #close_verified').click(function(){
        var $select = jQuery("#frmadd #status").selectize();
        var selectize = $select[0].selectize;
        selectize.setValue(selectize.search(jQuery('#frmadd #status').attr('oldval')).items[0].id);
    });

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
    
    $('#client_type').change(function(){
        var client_type = $(this).val();
        if(client_type != '') {
            if(client_type == 1){
                $('#show_school_data').show();
            }
            else{

                $('#show_school_data').hide();
            }
        }
    }).trigger('change');

    $('#board_id').change(function(){ 
        getMedium();
    }).trigger('change');
});

function getMedium(){
    var board_id = $('#board_id').val();
    $.ajax({
        type: "POST",
        url: mediumlist, 
        data:{"_token":csrf_token,"id":board_id},
        success: function (data) {
            //console.log(data);
            var selectId = $('#medium_id').selectize();
            var control = selectId[0].selectize
            control.clearOptions(); 
            if(data.length > 0) {
            
                for(var i = 0;i < data.length; i++) {
                    control.addOption({value:data[i].id,text:data[i].name});
                }
            }
            control.setValue(medium_id);    
        }
    });
}

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
                
                jQuery('#con_data_address .dataTables_empty').closest('tr').remove();
                var editid=jQuery('#frmaddaddress #editid').val();
                var addid=jQuery('#frmaddaddress #addid').val();
                var title=jQuery('#frmaddaddress #title').val();
                var address1=jQuery('#frmaddaddress #address1').val();
                var address2=jQuery('#frmaddaddress #address2').val();
                var mobile_number=jQuery('#frmaddaddress #mobile_number').val();
                var email=jQuery('#frmaddaddress #email').val();
                var s1=jQuery('#frmaddaddress #state1').val();
                state1=(jQuery('#frmaddaddress #state1 option[value="'+s1+'"]').html());
                var d1=jQuery('#frmaddaddress #district1').val();
                district1=(jQuery('#frmaddaddress #district1 option[value="'+d1+'"]').html());
                var t1=jQuery('#frmaddaddress #taluka1').val();
                taluka1=(jQuery('#frmaddaddress').find('#taluka1 option[value="'+t1+'"]').html());
                var zip_code=jQuery('#frmaddaddress #zip_code').val();
                var is_defaultb=jQuery('#frmaddaddress #is_default_billing').prop('checked');
                var isdefaultb=0;
                var billingdefault="";
                if(is_defaultb){
                    jQuery('#con_data_address').find('.address_is_default_billing').val(0);
                    jQuery('#con_data_address').find('.div_is_default_billing').html('<span class="badge badge-border danger round badge-danger">No</span>');
                    var billingdefault='<div class="div_is_default_billing"><span class="badge badge-border success round badge-success">Yes</span></div>';
                    isdefaultb=1;
                }else{
                    isdefaultb=0;
                    var billingdefault='<div class="div_is_default_billing"><span class="badge badge-border danger round badge-danger">No</span></div>';
                }
                var is_defaults=jQuery('#frmaddaddress #is_default_shipping').prop('checked');
                var isdefaults=0;
                var shippingdefault="";
                if(is_defaults){
                    jQuery('#con_data_address').find('.address_is_default_shipping').val(0);
                    jQuery('#con_data_address').find('.div_is_default_shipping').html('<span class="badge badge-border danger round badge-danger">No</span>');
                    var shippingdefault='<div class="div_is_default_shipping"><span class="badge badge-border success round badge-success">Yes</span></div>';
                    isdefaults=1;
                }else{
                    isdefaults=0;
                    var shippingdefault='<div class="div_is_default_shipping"><span class="badge badge-border danger round badge-danger">No</span></div>';
                }
                if(used_for_billing){
                    var htmlusedbill='<span class="badge badge-border success round badge-success">Yes</span>';
                    isbilling=1;
                }else{
                    isbilling=0;
                    var htmlusedbill='<span class="badge badge-border danger round badge-danger">No</span>';
                }
                var is_approved =jQuery('#frmaddaddress #is_approved').prop('checked');
                  if(is_approved){
                    var htmlisapprove='<span class="badge badge-border success round badge-success">Yes</span>';
                    var deleterow = '';
                    islocked=1;
                }else{
                    islocked=0;
                    var deleterow = '<a href="javascript:void(0);" class="btn btn-danger deleterow"><i class="ft-trash-2"></i></a>';
                   var htmlisapprove='<span class="badge badge-border success round badge-success">Yes</span>';
                }
                var status=jQuery('#frmaddaddress #status').prop('checked');
                isstatus=0;
                var Dhtml1="";
                if(status){
                    var statushtml='<span class="badge badge-border success round badge-success">Active</span>';
                    isstatus=1;
                }else{
                    isstatus=0;
                    var statushtml='<span class="badge badge-border danger round badge-danger">Inactive</span>';
                }
                var used_for_billing=jQuery('#frmaddaddress #used_for_billing').prop('checked');
                isbilling=0;
                var htmlusedbill="";
                if(used_for_billing){
                    var htmlusedbill='<span class="badge badge-border success round badge-success">Yes</span>';
                    isbilling=1;
                }else{
                    isbilling=0;
                    var htmlusedbill='<span class="badge badge-border danger round badge-danger">No</span>';
                }
                var used_for_shipping=jQuery('#frmaddaddress #used_for_shipping').prop('checked');
                isshipping=0;
                var htmlusedshipping="";
                if(used_for_shipping){
                    var htmlusedshipping='<span class="badge badge-border success round badge-success">Yes</span>';
                    isshipping=1;
                }else{
                    isshipping=0;
                    var htmlusedshipping='<span class="badge badge-border danger round badge-danger">No</span>';
                }
                var hidden='<input type="hidden" name="address_editid[]" class="address_editid" value="'+editid+'"><input type="hidden" name="address_addid[]" class="address_addid" value="'+addid+'"><input type="hidden" name="address_title[]" class="address_title" value="'+title+'"><input type="hidden" name="address_mobile_number[]" class="address_mobile_number" value="'+mobile_number+'"><input type="hidden" name="address_address1[]" class="address_address1" value="'+address1+'"><input type="hidden" name="address_address2[]" class="address_address2" value="'+address2+'"><input type="hidden" name="address_email[]" class="address_email" value="'+email+'"><input type="hidden" class="address_district1" dhtml="'+district1+'" name="address_district1[]" value="'+d1+'"><input type="hidden" class="address_state1" dhtml="'+state1+'" name="address_state1[]" value="'+s1+'"><input type="hidden" name="address_taluka1[]" dhtml="'+taluka1+'" class="address_taluka1" dhtml="'+taluka1+'" value="'+t1+'"><input type="hidden" name="address_zip_code[]" class="address_zip_code" value="'+zip_code+'"><input type="hidden" name="address_used_for_billing[]" class="address_used_for_billing" value="'+isbilling+'"><input type="hidden" name="address_used_for_shipping[]" class="address_used_for_shipping" value="'+isshipping+'"><input type="hidden" name="address_is_default_shipping[]" class="address_is_default_shipping" value="'+isdefaults+'"><input type="hidden" name="address_is_default_billing[]" class="address_is_default_billing" value="'+isdefaultb+'"><input type="hidden" name="is_approved[]" class="is_approved" value="'+is_approved+'"><input type="hidden" name="address_status[]" class="address_status" value="'+isstatus+'">';
                var addnew=jQuery('#con_data_address').find('.address_addid[value="'+jQuery('#frmaddaddress #addid').val()+'"]');
                if(jQuery('#frmaddaddress #editid').val()>0){
                    var trhtml=jQuery('#con_data_address').find('.address_editid[value="'+jQuery('#frmaddaddress #editid').val()+'"]').closest('tr');
                    trhtml.html('<td>'+hidden+''+title+' </td><td>'+address1+', '+address2+' <br>'+state1+', '+district1+', '+taluka1+', '+zip_code+'</td><td><i class="material-icons info font-medium-4">phone</i>'+mobile_number+'<br><i class="material-icons info font-medium-4">email</i>'+email+'</td><td class="text-center">'+htmlusedbill+'<br>'+billingdefault+'</td><td class="text-center">'+htmlusedshipping+'<br>'+shippingdefault+'</td><td class="text-center">'+statushtml+'</td><td class="text-center"><a href="javascript:void(0);" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light editrow">'+htmlusedshipping+'</td>');
                }else if(addnew.length>0){
                    var trhtml=jQuery('#con_data_address').find('.address_addid[value="'+jQuery('#frmaddaddress #addid').val()+'"]').closest('tr');
                    trhtml.html('<td>'+hidden+''+title+' </td><td>'+address1+', '+address2+' <br>'+state1+', '+district1+', '+taluka1+', '+zip_code+'</td><td><i class="material-icons info font-medium-4">phone</i>'+mobile_number+'<br><i class="material-icons info font-medium-4">email</i>'+email+'</td><td class="text-center">'+htmlusedbill+'<br>'+billingdefault+'</td><td class="text-center">'+htmlusedshipping+'<br>'+shippingdefault+'</td><td class="text-center">'+statushtml+'</td><td class="text-center"><a href="javascript:void(0);" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light editrow"><i class="la la-edit"></i></span></a>'+deleterow+'</td>');
                }else{
                    jQuery('#con_data_address').append('<tr><td>'+hidden+''+title+' </td><td>'+address1+', '+address2+' <br>'+state1+', '+district1+', '+taluka1+', '+zip_code+'</td><td><i class="material-icons info font-medium-4">phone</i>'+mobile_number+'<br><i class="material-icons info font-medium-4">email</i>'+email+'</td><td class="text-center">'+htmlusedbill+'<br>'+billingdefault+'</td><td class="text-center">'+htmlusedshipping+'<br>'+shippingdefault+'</td><td class="text-center">'+statushtml+'</td><td class="text-center"><a href="javascript:void(0);" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light editrow"><i class="la la-edit"></i></span></a>'+deleterow+'</td></tr>');
                }
                jQuery('#add_address').modal('hide');
                form.reset();
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

var FormValidationcontact = function () {
    // validation using icons
    var handleValidation = function () {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
        var form = jQuery('#frmaddcontact');
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
                designation_id:{
                    required: true
                },
                full_name: {
                    required: true,
                    notNumber: true,
                    number: false,
                    minlength: 3,
                    maxlength: 150,
                },
                // billing_name: {
                //     required: true,
                //     minlength: 3,
                //     maxlength: 100,
                // },
                mobile_number: {
                    required: true,
                    number: true,
                    minlength: 10,
                    maxlength: 10,
                },
                whatsapp_number: {
                    required: true,
                    number: true,
                    minlength: 10,
                    maxlength: 10,
                },
                zip_code: {
                    required: true,
                    number: true,
                    minlength: 6,
                    maxlength: 6,
                },
                date1:{
                    minAge: 18,
                }
            },
            messages: {
                'designation_id':{required: "Select designation."},
                full_name:{required: "Enter full name.", minlength: "Enter atleast 3 characters for full name.", maxlength: "Enter maximum 150 characters for full name.",alphanumeric:"Enter valid full name."},
                // billing_name:{required: "Enter billing name.", minlength: "Enter atleast 3 characters for billing name.", maxlength: "Enter maximum 100 characters for billing name."},
                mobile_number:{required: "Enter mobile no.",number:'Only number allow for mobile number.',minlength: "Enter 10 number for mobile no.",maxlength: "Enter 10 number for mobile no."},
                whatsapp_number:{required: "Enter whatsapp no.",number:"Ony number allow for whatsapp no.", minlength: "Enter 10 number for whatsapp no.", maxlength: "Enter 10 number for whatsapp no."},
                zip_code:{required: "Enter zip code.",number:"Ony number allow for zip code.", minlength: "Enter 10 number for zip code.", maxlength: "Enter 10 number for zip code."}
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
            highlight: function (element) {

            },
            unhighlight: function (element) { // revert the change done by hightlight
            },
            success: function (label, element) {
                var icon = jQuery(element).parent('.input-icon').children('i');
                jQuery(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                icon.removeClass("fa-warning").addClass("fa-check");
            },
            submitHandler: function (form) {
                jQuery('#con_data_contact .dataTables_empty').closest('tr').remove();
                var editid=jQuery('#frmaddcontact #editid').val();
                var addid=jQuery('#frmaddcontact #addid').val();
                var fullname=jQuery('#frmaddcontact #full_name').val();
                var mobile_number=jQuery('#frmaddcontact #mobile_number').val();
                var whatsapp_number=jQuery('#frmaddcontact #whatsapp_number').val();
                var designation_id=jQuery('#frmaddcontact #designation_id').val();
                var dob=jQuery('#frmaddcontact #date1').val();
                designation=(jQuery('#frmaddcontact #designation_id option[value="'+designation_id+'"]').html());
                var department=jQuery('#frmaddcontact #department').val();
                var date1=jQuery('#frmaddcontact #date1').val();
                var is_default=jQuery('#frmaddcontact #is_default').prop('checked');
                var isdefault=0;
                var Dhtml="";
                if(is_default){
                    console.log(is_default);
                    jQuery('#con_data_contact').find('.contact_is_default').val(0);
                    jQuery('#con_data_contact').find('.div_is_default').html('<span class="badge badge-border danger round badge-danger">No</span>');
                    var Dhtml='<td class="text-center div_is_default"><span class="badge badge-border success round badge-success">Yes</span></td>';
                    var isdefault=1;
                }else{
                    var isdefault=0;
                    var Dhtml='<td class="text-center div_is_default"><span class="badge badge-border danger round badge-danger">No</span></td>';
                }
                var status=jQuery('#frmaddcontact #status').prop('checked');
                var Dhtml1="";
                isstatus=0;
                if(status){
                    var Dhtml1='<td class="text-center"><span class="badge badge-border success round badge-success">Active</span></td>';
                    isstatus=1;
                }else{
                    isstatus=0;
                    var Dhtml1='<td class="text-center"><span class="badge badge-border danger round badge-danger">Inactive</span></td>';
                }
                var hidden='<input type="hidden" name="contact_editid[]" class="contact_editid" value="'+editid+'"><input type="hidden" name="" class="contact_addid" value="'+addid+'"><input type="hidden" name="contact_full_name[]" class="contact_full_name" value="'+fullname+'"><input type="hidden" name="contact_mobile_number[]" class="contact_mobile_number" value="'+mobile_number+'"><input type="hidden" name="contact_whatsapp_number[]" class="contact_whatsapp_number" value="'+whatsapp_number+'"><input type="hidden" name="contact_designation_id[]" dhtml="'+designation+'" class="contact_designation_id" value="'+designation_id+'"><input type="hidden" name="contact_dob[]" class="contact_dob" value="'+dob+'"><input type="hidden" name="contact_department[]" class="contact_department" value="'+department+'"><input type="hidden" name="contact_is_default[]" class="contact_is_default" value="'+isdefault+'"><input type="hidden" name="contact_status[]" class="contact_status" value="'+isstatus+'">';
                var addnew=jQuery('#con_data_contact').find('.contact_addid[value="'+jQuery('#frmaddcontact #addid').val()+'"]');
                var deleterow = '<a href="javascript:void(0);" class="btn btn-danger deleterow"><i class="ft-trash-2"></i></a>';
                if(editid>0){
                    var trhtml=jQuery('#con_data_contact').find('.contact_editid[value="'+jQuery('#frmaddcontact #editid').val()+'"]').closest('tr');
                    trhtml.html('<td>'+hidden+''+fullname+' </td><td>'+mobile_number+'</td><td>'+whatsapp_number+'</td><td class="text-center">'+designation+'</td><td class="text-center">'+department+'</td>'+Dhtml+''+Dhtml1+'<td class="text-center"><a href="javascript:void(0);" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light editrow"><i class="la la-edit"></i></span></a>'+deleterow+'</td>');
                }else if(addnew.length>0){
                    var trhtml=jQuery('#con_data_contact').find('.contact_addid[value="'+jQuery('#frmaddcontact #addid').val()+'"]').closest('tr');
                    trhtml.html('<td>'+hidden+''+fullname+' </td><td>'+mobile_number+'</td><td>'+whatsapp_number+'</td><td class="text-center">'+designation+'</td><td class="text-center">'+department+'</td>'+Dhtml+''+Dhtml1+'<td class="text-center"><a href="javascript:void(0);" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light editrow"><i class="la la-edit"></i></span></a>'+deleterow+'</td>');
                }else{
                   jQuery('#con_data_contact').append('<tr><td>'+hidden+''+fullname+' </td><td>'+mobile_number+'</td><td>'+whatsapp_number+'</td><td class="text-center">'+designation+'</td><td class="text-center">'+department+'</td>'+Dhtml+''+Dhtml1+'<td class="text-center"><a href="javascript:void(0);" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light editrow"><i class="la la-edit"></i></span></a>'+deleterow+'</td></tr>');
                }
                
                addid++;
                form.reset();
                jQuery('#add_contact').modal('hide');
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

var FormValidationverify = function () {
    // validation using icons
    var handleValidation = function () {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
        var form = jQuery('#frmaddVerify');
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
                vdate:{
                    required: true
                },
            },
            messages: {
                'vdate':{required: "Select Date."},
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
            highlight: function (element) {

            },
            unhighlight: function (element) { // revert the change done by hightlight
            },
            success: function (label, element) {
                var icon = jQuery(element).parent('.input-icon').children('i');
                jQuery(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                icon.removeClass("fa-warning").addClass("fa-check");
            },
            submitHandler: function (form) {
                form.reset();
                jQuery('#verifiedpopup').modal('hide');
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
                client_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                },
                // billing_name: {
                //     required: true,
                //     minlength: 3,
                //     maxlength: 100,
                // },
                tally_client_name:{
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                },
                mobile_number: {
                    required: true,
                    minlength: 10,
                    maxlength: 10,
                },
                whatsapp_number: {
                    required: true,
                    minlength: 10,
                    maxlength: 10,
                },
                discount_category: {
                    required: true,
                },
                sales_user_id: {
                    required: true,
                },
                school_type:{
                    required: true,
                },
                state:{
                    required: true,
                },
                district:{
                    required: true,
                },
                email:{
                    required: true,
                    email: true,
                },
                taluka :{
                    required: true,
                },
                client_type:{
                    required: true,
                },
                zip_code:{
                    required: true,
                    number: true,
                    minlength: 6,
                    maxlength: 6,
                },
                grade_id:{
                    required: true,
                },
                payment_term_id:{
                    required: true,
                },
                username: {
                    minlength: 3,
                    maxlength: 20,
                    required: true, 
                },
                password : {
                    required: { 
                        depends: function(element) {
                            if($('#customActionName').val()=='Update'){ 
                              if($('#changePasswordChk').prop('checked')){
                                return true;
                              }else{
                                return false;
                              }
                            }else{ 
                                return true;
                            }
                        } 
                    } ,
                    minlength: PASSWORD_MIN,
                    maxlength: PASSWORD_MAX,
                    password_regex:true,
                },
                password_confirmation : {
                    required: { 
                        depends: function(element) {
                            return $('#changePasswordChk').is(':checked');                
                        }
                    } ,
                    minlength: PASSWORD_MIN,
                    maxlength: PASSWORD_MAX,
                    equalTo: "#new_password"
                },
            },
            messages: {
                client_name:{required: "Enter client name.", minlength: "Enter atleast 3 characters for client name.", maxlength: "Enter maximum 100 characters for client name."},
                // billing_name:{required: "Enter billing name.", minlength: "Enter atleast 3 characters for billing name.", maxlength: "Enter maximum 100 characters for billing name."},
                tally_client_name:{required: "Enter tally client name.", minlength: "Enter atleast 3 characters for tally client name.", maxlength: "Enter maximum 100 characters for tally client name."},
                mobile_number:{required: "Enter registered mobile.", minlength: "Enter atleast 10 digit for registered mobile.", maxlength: "Enter maximum 10 digit for registered mobile."},
                whatsapp_number:{required: "Enter whatsapp no.", minlength: "Enter 10 digit for whatsApp no.", maxlength: "Enter 10 digit for whatsapp no."},
                discount_category:{required: "Select discount category."},
                sales_user_id:{required: "Select sales user."},
                client_type:{required: "Select type."},
                taluka:{required:"Select taluka."},
               zip_code:{required:"Enter zip.",minlength: "Enter atleast 6 digit for zip code.", maxlength: "Enter 6 digit for zip code."},
                grade_id:{required:"Select grade."},
                payment_term_id:{required:"Select payment term."},
                email:{required: "Enter email.", email:'Enter valid email.'},
                username: {required: "Enter username."},
                password:{required: "Enter password.",minlength:"Please enter at least "+PASSWORD_MIN+" characters for password.", maxlength:"Please enter maximum "+PASSWORD_MAX+" characters for password."},
                password_confirmation:{required: "Enter confirm password.",minlength:"Please enter at least "+PASSWORD_MIN+" characters for conformation password.", maxlength:"Please enter maximum "+PASSWORD_MAX+" characters for conformation password.",equalTo:"Confirmation password does not match with new password"}
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
                //console.log(element.id+' - '+element.value);
                if(element.id=='state'){
                    if(element.value==''){
                        jQuery('#frmadd').find('#state-error').remove();
                        dateSpan = document.createElement('span');
                        dateSpan.innerHTML = 'Select state.';
                        dateSpan.setAttribute("class", "help-block help-block-error");
                        dateSpan.setAttribute("id", "state-error");
                        element.closest('.col-md-6').append(dateSpan);
                    }
                }
                if(element.id=='discount_category'){
                    if(element.value==''){
                        jQuery('#frmadd').find('#discount_category-error').remove();
                        dateSpan = document.createElement('span');
                        dateSpan.innerHTML = 'Select discount category.';
                        dateSpan.setAttribute("class", "help-block help-block-error");
                        dateSpan.setAttribute("id", "discount_category-error");
                        element.closest('.col-md-6').append(dateSpan);
                    }
                }
                if(element.id=='sales_user_id'){
                    if(element.value==''){
                        jQuery('#frmadd').find('#sales_user_id-error').remove();
                        dateSpan = document.createElement('span');
                        dateSpan.innerHTML = 'Select sales user.';
                        dateSpan.setAttribute("class", "help-block help-block-error");
                        dateSpan.setAttribute("id", "sales_user_id-error");
                        element.closest('.col-md-6').append(dateSpan);
                    }
                }
                if(element.id=='client_type'){
                    if(element.value==''){
                        jQuery('#frmadd').find('#client_type-error').remove();
                        dateSpan = document.createElement('span');
                        dateSpan.innerHTML = 'Select type.';
                        dateSpan.setAttribute("class", "help-block help-block-error");
                        dateSpan.setAttribute("id", "client_type-error");
                        element.closest('.col-md-6').append(dateSpan);
                    }
                }
                if(element.id=='district'){
                    if(element.value==''){
                        jQuery('#frmadd').find('#district-error').remove();
                        dateSpan = document.createElement('span');
                        dateSpan.innerHTML = 'Select district.';
                        dateSpan.setAttribute("class", "help-block help-block-error");
                        dateSpan.setAttribute("id", "district-error");
                        element.closest('.col-md-6').append(dateSpan);
                    }
                }
                if(element.id=='taluka'){
                    if(element.value==''){
                        jQuery('#frmadd').find('#taluka-error').remove();
                        dateSpan = document.createElement('span');
                        dateSpan.innerHTML = 'Select taluka.';
                        dateSpan.setAttribute("class", "help-block help-block-error");
                        dateSpan.setAttribute("id", "taluka-error");
                        element.closest('.col-md-6').append(dateSpan);
                    }
                }
                if(element.id=='grade_id'){
                    if(element.value==''){
                        jQuery('#frmadd').find('#grade_id-error').remove();
                        dateSpan = document.createElement('span');
                        dateSpan.innerHTML = 'Select grade.';
                        dateSpan.setAttribute("class", "help-block help-block-error");
                        dateSpan.setAttribute("id", "grade_id-error");
                        element.closest('.col-md-8').append(dateSpan);
                    }
                }
                if(element.id=='payment_term_id'){
                    if(element.value==''){
                        jQuery('#frmadd').find('#payment_term_id-error').remove();
                        dateSpan = document.createElement('span');
                        dateSpan.innerHTML = 'Select payment term.';
                        dateSpan.setAttribute("class", "help-block help-block-error");
                        dateSpan.setAttribute("id", "payment_term_id-error");
                        element.closest('.col-md-8').append(dateSpan);
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
                var formData = new FormData(form);
                var msg = isError = "";
                jQuery.ajax({
                    type: "POST",
                    url: assets + "/" +panel_text + '/client/data',
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

$.validator.addMethod("password_regex", function(value, element) { 
    return this.optional(element) || PASSWORD_FORMAT.test(value);
}, "Enter at least one uppercase letter, one lowercase letter, one number and one special character for password.");

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
                jQuery.ajax({
                    type: "POST",
                    url: assets + "/" + panel_text + "/client/data",
                    data: { 'customActionName': "DeleteImage",  'id': id, 'flag' : flag, '_token': token},
                    success: function (data) {
                        jQuery("#delete-image-box_btn_close").trigger("click");
                        response = eval(data);
                        msg = response[0].msg;
                        isError = response[0].isError;
                        if(isError == 0) {
                            jQuery('#delete-image').hide();
                            jQuery("#show-image").attr("src", '');
                            jQuery('#flag_old').val('');
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
function getTalukabyDistrictsid(){  
    var did = jQuery("#district").val();
    jQuery.ajax({
          type: "POST",
          url: talukaurl,
          data: { "_token":csrf_token,"did":did},
          success: function (data) {
             var selectId = jQuery('#taluka').selectize();
              var control = selectId[0].selectize;
              control.clearOptions();
              if(data.length > 0) {
                  for(var i = 0;i < data.length; i++) {
                      control.addOption({value:data[i].id,text:data[i].taluka_name});
                  }
              } 
              control.setValue(jQuery('#taluka').attr('data-seleted'), false);
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
  function getDistricts(){  
    var sid = jQuery('#state').val();
    jQuery.ajax({
          type: "POST",
          url: districtlist,
          data: { "_token":csrf_token,"stateid":sid},
          success: function (data) {
             var selectId = jQuery('#district').selectize();
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

jQuery.validator.addMethod("notNumber", function(value, element, param) {
   var reg = /[0-9]/;
   if(reg.test(value)){
         return false;
   }else{
           return true;
   }
}, "Enter valid full name.");

jQuery.validator.addMethod("minAge", function(value, element,min) {
    var today = new Date();
    var birthDate = new Date(value);
    var age = today.getFullYear() - birthDate.getFullYear();
    if (age > min+1) { return true; }
 
    var m = today.getMonth() - birthDate.getMonth();
 
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) { age--; }
 
    return age >= min;
}, "You must be at least 18 years old!");

$('#add_modal_box_add_address').click(function () {
    $('#frmaddaddress').trigger("reset");
});

$('#changePasswordChk').click(function() {
    if ($('#changePasswordChk').prop('checked')) {       
        $('.changePassDiv').removeClass('d-none');
    } else {
        $('.changePassDiv').addClass('d-none');
        $('.changePassDiv').children('div').find('input').val('');
    }
});

function showhidePassword(Obj){
  var type =$(Obj).siblings('input').attr('type');
  var id =$(Obj).siblings('input').attr('id');

  if( type == "password") {
    $('#'+id).attr('type','text');
    $(Obj).attr('title', 'Hide password');
  } else {
    $('#'+id).attr('type','password');
    $(Obj).attr('title', 'Show password');
  }
}

function deleteDocumentUploadedFile(id) { 
    var token = jQuery("input[name='_token']").val();
    var document_file = jQuery('#old_doc_file_'+id).val();
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
                jQuery.ajax({
                    type: "POST",
                    url: assets + "/" + panel_text + "/client/data",
                    data: { 'customActionName': "DeleteClientDocument",  'id': id, 'document_file': document_file, '_token': token},
                    success: function (data) {
                        jQuery("#delete-image-box_btn_close").trigger("click");
                        response = eval(data);
                        msg = response[0].msg;
                        isError = response[0].isError;
                        if(isError == 0) {
                            jQuery('#document_link_'+id).hide();
                            jQuery('#old_doc_file_'+id).val('');
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