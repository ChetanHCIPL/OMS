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
   
  $('.select2-placeholder').selectize();
  $('.select2-state').selectize();
  $('.select2-districts').selectize();
  $('.access_control').selectize();
  $(".select2-status").select2({
    placeholder: "Select Status",
    allowClear: true
  }); 
  if(temp_country_code!=''){
    
     $("#country_id").trigger('change');
  }
  
    $('#country_id').change(function(){ 
        getState();
    });
    $('#state_id').change(function(){ 
        getDistricts();
    }); 
    
    $('#districts_id').change(function(){ 
      getTaluka();
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
          // $('.mobile_auth_attemptDiv').children('div').find('input').val('');
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
                username: {
                    minlength: 3,
                    maxlength: 20,
                    required: true, 
                },
                zip: {
                    number: true,
                    minlength: 6,
                    maxlength: 6,
                },
                password : {
                    required: { 
                        depends: function(element) {
                            if($('#mode').val()=='Update'  ){ 
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
                }, mobile_isd:{
                  required: true
                },
                acess_groupid: {
                    required: true,
                },
                country_id:{
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
                mobile: {
                    required: true,
                    number: true,
                    maxlength: 10,
                    minlength: 10
                },
                image: {
                    extension: "jpeg|png|jpg|gif"
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
                   first_name: {required: "Enter first name.",lettersonly:"Enter only alphabets"},
                   last_name: {required: "Enter last name.",lettersonly:"Enter only alphabets"},
                   email: {required: "Enter email.",email:"Enter valid Email"},
                   username: {required: "Enter username."}, 
                   mobile: {required: "Enter mobile number."},
                   image: {extension: "Please select an image with a valid extension."}, 
                   mobile_isd: {required: "Select isd code."},
                   acess_groupid: {required: "Select access group."},
                   country_id: {required: "Select country."},
                   state_id: {required: "Select state."},
                   districts_id: {required: "Select districts."},
                   taluka_id: {required: "Select taluka."},
                   zip:{minlength: "Enter atleast 6 digit for zip code.", maxlength: "Enter 6 digit for zip code."},
                   password_confirmation:{required: "Enter confirm password.",minlength:"Please enter at least "+PASSWORD_MIN+" characters for conformation password.", maxlength:"Please enter maximum "+PASSWORD_MAX+" characters for conformation password.",equalTo:"Confirmation password does not match with new password"},
                   password:{required: "Enter password.",minlength:"Please enter at least "+PASSWORD_MIN+" characters for password.", maxlength:"Please enter maximum "+PASSWORD_MAX+" characters for password."}
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
                } else {
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

function closeAllModals()
{
  window.location.href = list; 
}*/
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
