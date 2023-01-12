jQuery(document).ready(function () {
    FormValidation.init();
    
    $('.switchBootstrap').bootstrapSwitch();

    //changeContent();
    // $(".selectize-select").select2({
    //   placeholder: "Select Section",
    //   allowClear: true
    // });

    // $(".selectize-select1").select2({
    //   placeholder: "Select Content Type",
    //   allowClear: true
    // });
    $('.selectize-select').selectize({ create: false, sortField: { field: 'text', direction: 'asc' }, dropdownParent: 'body'});
 
    $('#cc_email').tagging({
        "forbidden-chars": ["," ,"?","!",":", "*", "?", "/"],
        "edit-on-delete": false,
        "no-spacebar"  : true,
    });

    $('#from_email').tagging({
        "forbidden-chars": ["," ,"?","!",":", "*", "?", "/"],
        "edit-on-delete": false,
		"tags-limit":1,
        "no-spacebar"  : true,
        "no-backspace" : true
    });

    $('#reply_to').tagging({
        "forbidden-chars": ["," ,"?","!",":", "*", "?", "/"],
        "edit-on-delete": false,
		"tags-limit":1,
        "no-spacebar"  : true,
    });
    
    $('#cc_email').tagging( "add", cc );
    $('#from_email').tagging( "add", varfrom );
    $('#reply_to').tagging( "add", replyto );

 

    $('#cc_email').on( "add:after",function(el,val, tagging){   
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/; 
        if(!emailReg.test(val)){ 
           $('#cc_email').tagging("remove",val);
        }
     });

     $('#from_email').on( "add:after",function(el,val, tagging){ 
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        if(!emailReg.test(val)){
           $('#from_email').tagging("remove",val);
        }
     });

     $('#reply_to').on( "add:after",function(el,val, tagging){ 
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        if(!emailReg.test(val)){
           $('#reply_to').tagging("remove",val);
        }
     });

     $.validator.addMethod('checkFromEmail',function (value, element, requiredValue) {
              if($('#from_email .type-zone').val() == '' && $('input[name="from[]"]').val() == '')
              {
                $(".from_email-error").remove();
                $('<span id="from_email-error" class="from_email-error help-block help-block-error">Please enter from email</span>').insertAfter('#from_email');
              }
              if($('#cc_email .type-zone').val() == ''  && $('input[name="cc[]"]').val() == '' )
              { 
                $(".cc_email-error").remove();
                $('<span id="cc_email-error" class="cc_email-error help-block help-block-error">Please enter cc email</span>').insertAfter('#cc_email');
              }
              if($('#reply_to .type-zone').val() == ''  && $('input[name="reply_to[]"]').val() == '')
              { 
                $(".reply_to-error").remove();
                $('<span id="reply_to-error" class="reply_to-error help-block help-block-error">Please enter reply to email</span>').insertAfter('#reply_to');
              }
              return true;
            }
        );
});

var FormValidation = function () {
    // validation using icons
    var handleValidation = function () {
        var form = $('#frmadd');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
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
                type: {
                    required: true,
                    checkFromEmail:'on'
                },
                sectionName: {
                    required: true
                },
                from_email_hidden: {
                    required: true,
                },
                cc: {
                    required: true,
                    email : true,
                },                
                reply_to: {
                    // required: { 
                    //     depends: function(element) {
                    //          console.log("hii");    
                    //        // return $('#changePasswordChk').is(':checked');
                    //     } 
                    // } ,
                    required: true,
                    
                    // required: function(){
                    //   console.log("hii");
                    // },
                    email: true,
                },
                isLocationEmpty: true,
                mime: {
                    required: true,
                },
                content: {
                    required: true,
                },
            },
            messages: {
                type: { required: "Enter type"},
                sectionName: { required: "Please select section"},
                from_email_hidden: { required: "Enter from Email Id(s)", email: "Enter valid email"},
                cc_email: { required: "Enter cc", email: "Enter valid email"},
                reply_to: { required: "Enter reply to", email: "Enter valid email"},
                mime: { required: "Select email content type"},
                content: { required: "Enter content"},
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
                    error.insertAfter(element.next(".selectize-control")); 
                }
                else if ($(element).is("div")) { 
                    //alert('error');
                    //error.insertAfter(element.closest(".tagging "));
                }else {
                    error.insertAfter(element);
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
                var formData = new FormData(form);
                var msg = isError = ""; 
                $.ajax({
                    type: "POST",
                    url: assets + "/" +panel_text + '/email-template/data',
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        response = eval(data);
                        msg = response[0].msg;
                        isError = response[0].isError;
                        $("#msg-modal").trigger("click");
                        var isDisplayAddMore = 0; //No
                        var add_btn = 1; 
                        var str = displayMessageBox(isError,msg,1,isDisplayAddMore);
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
//On change mime type add and remove ckeditor
function changeContent(){
    mimetype =  $("select[name='mime']").val();

   if( mimetype == "1"){ // Html
        for (var i = 0; i < language_data; i++) {
              var lPageContent = document.getElementById("email_content_"+(i+1));
                CKEDITOR.replace(lPageContent, {
                    toolbar:  custom_toolbar,
                    language: 'en-gb',
                    on: {
                        pluginsLoaded: function() {
                          var editor = this,
                              config = editor.config;
                          
                            editor.ui.addRichCombo( 'my-combo', {
                              label: 'Add Widget',
                              title: 'Add Widget',
                      
                              panel: {               
                                  css: [ CKEDITOR.skin.getPath( 'editor' ) ].concat( config.contentsCss ),
                                  multiSelect: false,
                                  attributes: { 'aria-label': 'Add Widget' }
                              },
                  
                              init: function() {    
                                  this.startGroup( 'Choose Widget' );
                                  var temp = this;
                                  jQuery.each(widget_arr,function(index,val){
                                      var code = val.vWidgetCode
                                      var val_data = "widget("+code+")";
                                      temp.add(val_data, val.vWidgetName );
                                  });
                              },
                  
                              onClick: function( value ) {
                                  editor.focus();
                                  editor.fire( 'saveSnapshot' );
                                 
                                  editor.insertHtml( value );
                              
                                  editor.fire( 'saveSnapshot' );
                              }
                            });        
                        }        
                    } 
              });
        }
   }else if(mimetype == "2"){ //Text
        // if there is an existing instance of this editor
         for(name in CKEDITOR.instances)
        {
            CKEDITOR.instances[name].destroy(true); //remove ckeditor
                
        }
   }
}