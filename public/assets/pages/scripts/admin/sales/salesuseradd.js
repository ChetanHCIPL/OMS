$(document).ready(function() {
    FormValidation.init();
    $(".fancybox").fancybox({
          openEffect  : 'none',
          closeEffect : 'none',
    });
    if($('#id').val() == ''){
        getState();
    }
    $('.switchBootstrap').bootstrapSwitch();
     
    $('.select2-placeholder').selectize({plugins: ["remove_button"]});
    $('.access_control').selectize({ create: false, sortField: { field: 'value', direction: 'asc' }});
    $('.access_control_noorder').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }});
     
   
    $(".select2-status").select2({
      placeholder: "Select Status",
      allowClear: true
    }); 
    if(temp_country_code!=''){
       $("#country_id").trigger('change');
    }
    $('.access_control').change(function(){
        $(this).valid()
    });
      $('#country_id').change(function(){ 
          getState();
      });

      $('#state_id').change(function(){ 
          getDistricts();
      }); 

      $('#districts_id').change(function(){ 
        getTaluka();
    });
      // get address details on change of client address
  $('#sales_structure_id').change(function() { 
    $('.sales_structure').find('.help-block.help-block-error').remove();
    var selarray=['#sales_state_id','#user_type_rsm','#user_type_rsm_state','#user_type_rsm_zone','#user_type_zsm_district','#user_type_zsm','#user_type_dyzsm','#user_type_asm','#user_type_aso_asm_taluka'];
    selarray.forEach(function(item) {
      var $select = $(item).selectize();
    var selectize = $select[0].selectize;
    selectize.setValue('');
    });
     var sales_user_type = $(this).val();
     if(sales_user_type != '') {
       $('#div_rsm').removeClass('d-none');
       $('#user_type_rsm_state').removeClass('d-none');
       $('#user_type_rsm_zone').removeClass('d-none');
       $('#user_type_zsm_district').removeClass('d-none');
       $('#user_type_dyzsm').removeClass('d-none');
       $('#user_type_asm_dyzsm').removeClass('d-none');
       $('#user_type_aso_asm_taluka').removeClass('d-none');
       $('#div_zsm').removeClass('d-none');
         if(sales_user_type == 1){
           $('#div_sales_state').removeClass('d-none');
           $('#div_rsm_state').addClass('d-none');
           $('#div_rsm_zone').addClass('d-none');
           $('#div_rsm').addClass('d-none');
           $('#div_zsm_district').addClass('d-none');
           $('#div_dy_zsm').addClass('d-none');
           $('#div_asm_dy_zsm').addClass('d-none');
           $('#div_aso_asm_district').addClass('d-none');
           $('#div_zsm').addClass('d-none');
        }
        else if(sales_user_type == 2){
           $('#div_sales_state').addClass('d-none');
           $('#div_dy_zsm').addClass('d-none');
           $('#div_asm_dy_zsm').addClass('d-none');
            $('#div_aso_asm_district').addClass('d-none');
           $('#div_rsm').removeClass('d-none');
           $('#div_rsm_state').removeClass('d-none');
           $('#div_rsm_zone').removeClass('d-none');
           $('#div_zsm_district').addClass('d-none');
           $('#div_zsm').addClass('d-none');
        }
       else if(sales_user_type == 3){
           $('#div_sales_state').addClass('d-none');
           $('#div_dy_zsm').addClass('d-none');
           $('#div_asm_dy_zsm').addClass('d-none');
            $('#div_aso_asm_district').addClass('d-none');
           $('#div_rsm').removeClass('d-none');
           $('#div_rsm_state').removeClass('d-none');
           $('#div_rsm_zone').addClass('d-none');
           $('#div_zsm_district').removeClass('d-none');
           $('#div_zsm').removeClass('d-none');
        }
       else if(sales_user_type == 4){
           $('#div_sales_state').addClass('d-none');
           $('#div_asm_dy_zsm').addClass('d-none');
           $('#div_aso_asm_district').addClass('d-none');
           $('#div_rsm').removeClass('d-none');
           $('#div_rsm_state').removeClass('d-none');
           $('#div_rsm_zone').addClass('d-none');
           $('#div_dy_zsm').removeClass('d-none');
           $('#div_zsm_district').removeClass('d-none');
        }
         else if(sales_user_type == 5){
           $('#div_sales_state').addClass('d-none');
           $('#div_zsm_district').addClass('d-none');
           $('#div_rsm').removeClass('d-none');
           $('#div_rsm_state').removeClass('d-none');
           $('#div_rsm_zone').addClass('d-none');
           $('#div_dy_zsm').removeClass('d-none');
           $('#div_asm_dy_zsm').removeClass('d-none');
           $('#div_aso_asm_district').removeClass('d-none');
        }
     }
 });
     $('#changePasswordChk').click(function() {
         if ($('#changePasswordChk').prop('checked')) {       
            $('.changePassDiv').removeClass('d-none');
          } else {
            $('.changePassDiv').addClass('d-none');
            $('.changePassDiv').children('div').find('input').val('');
          }
     });
  
     $('#is_ip_auth').click(function() {
         if ($('#is_ip_auth').prop('checked')) {       
            $('.ip_authDiv').removeClass('d-none');
          } else {
            $('.ip_authDiv').addClass('d-none');
          }
     });
  
     $('#is_mobile_auth').click(function() {
         if ($('#is_mobile_auth').prop('checked')) {       
            $('.mobile_auth_attemptDiv').removeClass('d-none');
          } else {
            $('.mobile_auth_attemptDiv').addClass('d-none');
          }
     });
     $('#removeImage').click(function()
     { 
         var sid = $(this).attr('data-id');  
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
                      "data":{"_token":csrf_token,"id":sid},
                      "url": removeimage,
                      "type": "post",
                      "success":function(data)
                      {
                          response =  JSON.parse(data);
                          //console.log(response); 
                          msg = response.msg; 
                          isError = response.isError;
                          if(isError != '1')
                          {
                             $('#image_section').html('');
                          }
                          $("#msg-modal").trigger("click");
                          var str = displayMsgBox(isError, msg);
                          $("#msg-html").html(str); 
                      }
                    });
                  }
              }
          });
     });
     $('.selectize-movie').selectize({
          maxItems: 1,
          valueField: 'isd_code',
          labelField: 'mobile_isd',
          searchField: 'isd_code',
          options: country, 
          create: false,
          render: {
            item: function(item, escape) {
              return '<span><img src="' + escape(item.flag) + '" alt=""></span>' +
              '<span>'+ escape(item.isd_code)+"</span>";
            },
            option: function(item, escape) {
              return '<div  class="text-left">' +
                '<img style="height: 18px;width: 24px;" src="' + escape(item.flag) + '" alt="">' +
                '<span class="title"> ' +
                  '<span class="name">' + escape(item.isd_code) + '</span>' +
                '</span>' +
              '</div>';
            }
          }
    });
     $('.selectize-select-multiple').selectize({
          plugins: ['remove_button'],
          create: true,maxItems: null,
          dropdownParent: 'body',
          onDelete: function (values) {
              return confirm('Are you sure you want to remove these access group?');
          }
      });
      $('.selectize-select-multipleIp').selectize({
          plugins: ['remove_button'],
          create: true,maxItems: null,
          dropdownParent: 'body',
          // onDelete: function (values) {
          //     return confirm('Are you sure you want to remove these Ip?');
          // }
      });
      // sales structure
      $('#user_type_rsm_state').change(function(){
          getZoneListState();
          getDistrictsListState();
          // getRSMUserlist();
      })
      $('#user_type_zsm').change(function(){
        getDistrictsListState();
        getDyZsmlistbyuser();
        getASMListFromZSM();
      })
      $('#user_type_rsm').change(function(){
        getZSMListFromRSM();
      })
      $('#user_type_asm').change(function(){
        getTalukaListFromASM();
      })
      
      
      
      // End sales structure
  });
  
  
  var FormValidation = function () {
      // validation using icons
      var handleValidation = function () {
        
            // for more info visit the official plugin documentation: 
            // http://docs.jquery.com/Plugins/Validation
            var form = $('#useraddForm');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);
            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "", // validate all fields including form hidden input
               rules: {
                  first_name:{
                    required: true,
                    minlength: 3,
                    maxlength: 50,
                  }, 
                  last_name:{
                    required: true,
                    minlength: 3,
                    maxlength: 50,
                  },
                  email: {
                      required: true,
                      email: true,
                      maxlength: 100,
                  },
                  image: {
                    extension: "jpeg|png|jpg|gif"
                  },
                  username: {
                      minlength: 3,
                      maxlength: 20,
                      required: true, 
                  },
                  password : {
                      required: { 
                          depends: function(element) {
                              if($('#mode').val()=='Update'){ 
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
                  sales_structure_id:{
                    required: true
                  }, 
                  'sales_state_id[]':{
                    required: { 
                      depends: function(element) {
                        if($('#sales_structure_id').val()=='1'){ 
                            return true;
                        }else{ 
                            return false;
                        }
                      }
                    }
                  }, 
                  user_type_rsm_state:{
                    required: { 
                      depends: function(element) {
                        if($('#sales_structure_id').val()>=2){ 
                            return true;
                        }else{ 
                            return false;
                        }
                      }
                    }
                  },
                  user_type_rsm:{
                    required: { 
                      depends: function(element) {
                        if($('#sales_structure_id').val()>=2){ 
                            return true;
                        }else{ 
                            return false;
                        }
                      }
                    }
                  },
                  user_type_rsm_zone:{
                    required: { 
                      depends: function(element) {
                        if($('#sales_structure_id').val()=='2'){ 
                            return true;
                        }else{ 
                            return false;
                        }
                      }
                    }
                  },
                  user_type_zsm:{
                    required: { 
                      depends: function(element) {
                        if($('#sales_structure_id').val()>=3){ 
                            return true;
                        }else{ 
                            return false;
                        }
                      }
                    }
                  },
                  'user_type_zsm_district[]':{
                    required: { 
                      depends: function(element) {
                        if($('#sales_structure_id').val()==3 || $('#sales_structure_id').val()==4){ 
                            return true;
                        }else{ 
                            return false;
                        }
                      }
                    }
                  },
                  user_type_aso_asm_taluka:{
                    required: { 
                      depends: function(element) {
                        if($('#sales_structure_id').val()==5){ 
                            return true;
                        }else{ 
                            return false;
                        }
                      }
                    }
                  },
                  user_type_asm:{
                    required: { 
                      depends: function(element) {
                        if($('#sales_structure_id').val()==5){ 
                            return true;
                        }else{ 
                            return false;
                        }
                      }
                    }
                  },
                  mobile: {
                      required: true,
                      number: true,
                      maxlength: 10,
                      minlength: 10
                  },
                  whatsapp_number: {
                    required: true,
                    number: true,
                    maxlength: 10,
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
                  adhar_no:{
                    required: true,
                    minlength:12,
                    maxlength: 12,
                  },
                  designation_id:{
                    required: true
                  },
                  whatsapp:{
                    required: true
                  },
                  area:{
                    required: true
                  },
                  state_id:{
                    required: true
                  },
                  districts_id:{
                    required: true
                  },
                  taluka_id:{
                    required: true
                  },
                  address:{
                    required: true
                  },
                 zip:{
                    minlength: 6,
                    maxlength: 6,
                },
                }, 
                messages: {
                     first_name: {required: "Enter first name.",lettersonly:"Enter only alphabets"},
                     last_name: {required: "Enter last name.",lettersonly:"Enter only alphabets"},
                     email: {required: "Enter email address.",email:"Enter valid email address."},
                     image: {extension: "Please select an image with a valid extension."}, 
                     username: {required: "Enter username."}, 
                     mobile: {required: "Enter mobile number."},
                     mobile_isd: {required: "Select isd code."},
                     adhar_no: {required: "Enter aadhar number.",minlength: "Enter 12 digit for aadhar number.", maxlength: "Enter 12 digit for aadhar number."},
                     designation_id: {required: "Select designation."},
                     whatsapp_number: {required: "Enter whatsapp number."},
                     sales_structure_id: {required: "Select sales structure."},
                     state_id: {required: "Select state."},
                     districts_id: {required: "Select district."},
                     taluka_id: {required: "Select taluka."},
                     address: {required: "Enter address."},
                     area: {required: "Select area."},
                     "acess_groupid[]": {required: "Select access group."},
                      zip:{required:"Enter zip.",minlength: "Enter atleast 6 digit for zip code.", maxlength: "Enter 6 digit for zip code."},
                     password_confirmation:{required: "Enter confirm password.",minlength:"Please enter at least "+PASSWORD_MIN+" characters for conformation password.", maxlength:"Please enter maximum "+PASSWORD_MAX+" characters for conformation password.",equalTo:"Confirmation password does not match with new password."},
                     password:{required: "Enter password.",minlength:"Please enter at least "+PASSWORD_MIN+" characters for password.", maxlength:"Please enter maximum "+PASSWORD_MAX+" characters for password."},
                     'sales_state_id[]':{required: "Select state."},
                     user_type_rsm_state:{required: "Select state."},
                     user_type_rsm:{required: "Select rsm."},
                     user_type_rsm_zone:{required: "Select zone."},
                     user_type_zsm:{required: "Select zsm."},
                     'user_type_zsm_district[]':{required: "Select district."},
                     user_type_aso_asm_taluka:{required: "Select taluka."},  
                     user_type_asm:{required: "Select asm."},
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
                  //console.log($(element).val()=='');
                  if($(element).attr("id")=='sales_state_id'){
                    if($(element).val()==''){
                      $(element).closest('div').find('.select2.select2-container').after('<span id="sales_state_id-error" class="help-block help-block-error">Select state.</span>');
                    }
                  }
                  if($(element).attr("id")=='user_type_zsm_district'){
                    if($(element).val()==''){
                     // $(element).closest('div').find('.select2.select2-container').after('<span id="user_type_zsm_district-error" class="help-block help-block-error">Select district.</span>');
                    }
                  } 
                 if ($(element).is("select")) {
                      if($(element).is("select[name='mobile_isd']")){ 
                         error.insertAfter(element.closest(".form-control-position"));
                      }else{ 
                           error.insertAfter(element.next(".selectize-control")); 
                      } 
                      //selectize-control
                  } else if($(element).is("input[name='mobile']")){
                      //error.insertAfter(element.next("div")); 
                        error.insertAfter(element);
                  }else{
                       error.insertAfter(element); // for other inputs, just perform default behavior 
                  } 
                },
                highlight: function (element) { // hightlight error inputs
                    $(element)
                            .closest('.form-group').removeClass("has-success").addClass('has-error'); // set error class to the control group   
                },
                unhighlight: function (element) { // revert the change done by hightlight
  
                },
                success: function (label, element) {
                    var icon = $(element).parent('.input-icon').children('i');
                    $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                    icon.removeClass("fa-warning").addClass("fa-check");
                },
                submitHandler: function (form) {  
                 /* return true;*/ 
                    var formData = new FormData(form);  
                    var msg = isError = "";
                    $.ajax({
                        type: "POST",
                        url: route_user_add,
                        data: formData,
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function (data) { 
                          response = eval(data);
                          msg = response[0].msg; 
                          isError = response[0].isError;
                          $("#msg-modal").trigger("click");
                          var str = displayMessageBox(isError, msg);
                          $("#msg-html").html(str);
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
  jQuery.validator.addMethod("password_regex", function(value, element) { 
      return this.optional(element) || PASSWORD_FORMAT.test(value);
  }, "Enter at least one uppercase letter, one lowercase letter, one number and one special character for password.");
  
  /*function reloadAddPage() { 
    window.location.href = adduser;
  }
  */
  function closeAllModals()
  {
    window.location.href = list; 
  }
  //Get State 
  function getState(){
      var country_id = $('#country_id').val();
      $.ajax({
          type: "POST",
          url: statelist, 
          data:{"_token":csrf_token,"countryid":country_id},
          success: function (data) {
             
              var selectId = $('#state_id').selectize();
              var control = selectId[0].selectize;
              control.clearOptions(); 
              if(data.length > 0) {
                
                  for(var i = 0;i < data.length; i++) {
                      control.addOption({value:data[i].id,text:data[i].state_name});
                  }
              }
              control.setValue($('#selected_state_id').val(), false);
              
          }
      });
  }
  //State change // Get Districts list
  function getDistricts(){  
    var sid = $('#state_id').val();
      $.ajax({
          type: "POST",
          url: districtlist,
          data: { "_token":csrf_token,"stateid":sid},
          success: function (data) {
             var selectId = $('#districts_id').selectize();
              var control = selectId[0].selectize;
              control.clearOptions();
              if(data.length > 0) {
                  for(var i = 0;i < data.length; i++) {
                      control.addOption({value:data[i].id,text:data[i].district_name});
                  }
              } 
              control.setValue($('#selected_district_id').val(), false);
          }
      });  
  }

  //District change // Get Taluka list
  function getTaluka(){  
    var sid = $('#districts_id').val();
      $.ajax({
          type: "POST",
          url: talukaList,
          data: { "_token":csrf_token,"did":sid},
          success: function (data) {
             var selectId = $('#taluka_id').selectize();
              var control = selectId[0].selectize;
              control.clearOptions();
              if(data.length > 0) {
                  for(var i = 0;i < data.length; i++) {
                      control.addOption({value:data[i].id,text:data[i].taluka_name});
                  }
              } 
              control.setValue($('#selected_taluka_id').val(), false);
          }
      });  
  }
  function getZoneListState(){  
    $('#user_type_rsm_zone').closest('div').find('#user_type_rsm_zone-error').remove();
    $('#user_type_rsm_zone').closest('div').find('#no-user_type_rsm_zone-error').remove();
    var sid = $('#user_type_rsm_state').val();
      $.ajax({
          type: "POST",
          url: zoneajax,
          data: { "_token":csrf_token,"sid":sid},
          success: function (data) {
             var selectId = $('#user_type_rsm_zone').selectize({sortField: { field: 'value', direction: 'asc' }});
              var control = selectId[0].selectize;
              control.clearOptions();
              if(data.length > 0) {
                  for(var i = 0;i < data.length; i++) {
                      control.addOption({value:data[i].id,text:data[i].zone_name});
                  }
              }else{
                $('#user_type_rsm_zone').closest('div').append('<span id="no-user_type_rsm_zone-error" class="help-block help-block-error">No zone found.</span>');
              } 
          }
      });  
  }
  
  function getRSMUserlist(){  
    $('#user_type_rsm_state').closest('div').find('#user_type_rsm_state-error').remove();
    $('#user_type_rsm').closest('div').find('#user_type_rsm-error').remove();
    $('#user_type_rsm').closest('div').find('#no-user_type_rsm-error').remove();
    var sid = $('#user_type_rsm_state').val();
      $.ajax({
          type: "POST",
          url: salesulist,
          data: { "_token":csrf_token,'getRMlist':1,"sid":sid},
          success: function (data) {
             var selectId = $('#user_type_rsm').selectize({sortField: { field: 'value', direction: 'asc' }});
              var control = selectId[0].selectize;
              control.clearOptions();
              if(data.length > 0) {
                  for(var i = 0;i < data.length; i++) {
                    control.addOption({value:data[i].id,text:data[i].first_name+' '+data[i].last_name});
                  }
              }else{
                $('#user_type_rsm').closest('div').append('<span id="no-user_type_rsm-error" class="help-block help-block-error">No rsm found.</span>');
              } 
          }
      });  
  }
  function getZSMListFromRSM(){  
    $('#user_type_zsm').closest('div').find('#user_type_zsm-error').remove();
    $('#user_type_zsm').closest('div').find('#no-user_type_zsm-error').remove();
    var rsm = $('#user_type_rsm').val();
      $.ajax({
          type: "POST",
          url: salesulist,
          data: { "_token":csrf_token,"rsm":rsm},
          success: function (data) {
             var selectId = $('#user_type_zsm').selectize({sortField: { field: 'value', direction: 'asc' }});
              var control = selectId[0].selectize;
              control.clearOptions();
              if(data.length > 0) {
                  for(var i = 0;i < data.length; i++) {
                      control.addOption({value:data[i].id,text:data[i].name+ ' ('+data[i].zone_name+')'});
                  }
              }else{
                $('#user_type_zsm').closest('div').append('<span id="no-user_type_zsm-error" class="help-block help-block-error">No zsm found.</span>');
              } 
          }
      });  
  }
  function getDyZsmlistbyuser(){  
    var zsm = $('#user_type_zsm').val();
      $.ajax({
          type: "POST",
          url: salesulist,
          data: { "_token":csrf_token,"zsm":zsm},
          success: function (data) {
             var selectId = $('#user_type_dyzsm').selectize({sortField: { field: 'value', direction: 'asc' }});
              var control = selectId[0].selectize;
              control.clearOptions();
              if(data.length > 0) {
                  for(var i = 0;i < data.length; i++) {
                      control.addOption({value:data[i].id,text:data[i].name});
                  }
              } 
          }
      });  
  }
  function getASMListFromZSM(){  
    $('#user_type_asm').closest('div').find('#user_type_asm-error').remove();
    $('#user_type_asm').closest('div').find('#no-user_type_asm-error').remove();
    var zsm = $('#user_type_zsm').val();
    var dyzsm = $('#user_type_dyzsm').val();
      $.ajax({
          type: "POST",
          url: salesulist,
          data: { "_token":csrf_token,'getasm':1,"zsm":zsm,"dyzsm":dyzsm},
          success: function (data) {
             var selectId = $('#user_type_asm').selectize({sortField: { field: 'value', direction: 'asc' }});
              var control = selectId[0].selectize;
              control.clearOptions();
              if(data.length > 0) {
                  for(var i = 0;i < data.length; i++) {
                     control.addOption({value:data[i].id,text:data[i].name});
                  }
              }else{
                $('#user_type_asm').closest('div').append('<span id="no-user_type_asm-error" class="help-block help-block-error">No asm found.</span>');
              } 
          }
      });  
  }
  function getTalukaListFromASM(){  
    $('#user_type_aso_asm_taluka').closest('div').find('#user_type_aso_asm_taluka-error').remove();
    $('#user_type_aso_asm_taluka').closest('div').find('#no-user_type_aso_asm_taluka-error').remove();
    var asm = $('#user_type_asm').val();
      $.ajax({
          type: "POST",
          url: salesulist,
          data: { "_token":csrf_token,"asm":asm},
          success: function (data) {
             var selectId = $('#user_type_aso_asm_taluka').selectize({sortField: { field: 'value', direction: 'asc' }});
              var control = selectId[0].selectize;
              control.clearOptions();
              if(data.length > 0) {
                  for(var i = 0;i < data.length; i++) {
                     control.addOption({value:data[i].id,text:data[i].taluka_name});
                  }
              }else{
                $('#user_type_aso_asm_taluka').closest('div').append('<span id="no-user_type_aso_asm_taluka-error" class="help-block help-block-error">No taluka found.</span>');
              } 
          }
      });  
  }
  function getDistrictsListState(){  
    $('#user_type_zsm_district').closest('div').find('#user_type_zsm_district-error').remove();
    $('#user_type_zsm_district').closest('div').find('#no-user_type_zsm_district-error').remove();
    var sid = $('#user_type_rsm_state').val();
    var zsmid =$('#user_type_zsm').val();
      $.ajax({
          type: "POST",
          url: districtlist,
          data: { "_token":csrf_token,"stateid":sid,'zsmid':zsmid},
          success: function (data) {
             var selectId = $('#user_type_zsm_district').selectize({sortField: { field: 'value', direction: 'asc' }});
              var control = selectId[0].selectize;
              control.clearOptions();
              if(data.length > 0) {
                  for(var i = 0;i < data.length; i++) {
                      control.addOption({value:data[i].id,text:data[i].district_name});
                      console.log(data[i].id+','+data[i].district_name);
                  }
              }else{
                $('#user_type_zsm_district').closest('div').append('<span id="no-user_type_zsm_district-error" class="help-block help-block-error">No taluka found.</span>');
              } 
          }
      });  
  }
  function showhidePassword(Obj,pwd_type){
    var type =$(Obj).siblings('input').attr('type');
    var id =$(Obj).siblings('input').attr('id');
  
    if( type == "password") {
      $('#'+id).attr('type','text');
      $('.'+pwd_type).addClass('pwd_eye_show');
      $(Obj).attr('title', 'Hide password');
    } else {
      $('#'+id).attr('type','password');
      $('.'+pwd_type).removeClass('pwd_eye_show');
      $(Obj).attr('title', 'Show password');
    }
  }