// Slick Slider Js 

    $(document).on('ready', function() {
      $(".regular").slick({
        dots: false,
        arrows: true,
        infinite: true,
        slidesToShow: 6,
        slidesToScroll: 1, 
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 3,
              infinite: true,
              dots: true
            }
          },
          {
            breakpoint: 600,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 2
            }
          },
          {
            breakpoint: 480,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]
      });
    });
 
// Slick Slider Js 
      $(document).ready(function () {
        $("#sidebar").mCustomScrollbar({
          theme: "minimal"
        });

        $('#dismiss, .overlay').on('click', function () {
            $('#sidebar').removeClass('active');
            $('.overlay').removeClass('active');
        });

        $('#sidebarCollapse').on('click', function () {
          $('#sidebar').addClass('active');
          $('.overlay').addClass('active');
          $('.collapse.in').toggleClass('in');
          $('a[aria-expanded=true]').attr('aria-expanded', 'false');
        });
        SubscriptionFormValidation.init();
      });

// Header Fixed 
  $(window).scroll(function() {    
    var scroll = $(window).scrollTop();

    if (scroll >= 100) {
        $(".header_center").addClass("fixed-top");
    } else {
        $(".header_center").removeClass("fixed-top");
    }
});
var SubscriptionFormValidation = function () {
    // validation using icons
    var handleSubscriptionValidation = function () {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
        var form = $('#subscribe_form');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "", // validate all fields including form hidden input
            rules: {
                email: {
                    required: true,
                    email: true,
                    maxlength: 255,
                },
            },
            messages: {
                // email: {required:email_required,maxlength:email_max,email:email_regex},
            },
            invalidHandler: function (event, validator) { //display error alert on form submit              
                success.hide();
                error.show();
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element); // for other inputs, just perform default behavior
            },
            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass("has-success").addClass('has-error'); // set error class to the control group   
            },
            unhighlight: function (element) { // revert the change done by hightlight

            },
            success: function (label, element) {
                var icon = $(element).parent('.input-icon').children('i');
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                icon.removeClass("fa-warning").addClass("fa-check");
            },
            submitHandler: function (form) {
                var formData = $('#subscribe_form').serializeArray();
                $(".loader_sign_up").removeClass('d-none');
                $(".btn_name_sign_up").html(lbl_loading);
                $.ajax({
                    type: "POST",
                    url: route_register,
                    data: formData,
                    success: function (response) {
                        /*$('form[name="subscribe_form"]')[0].reset(); // reset form list
                        setTimeout(function () {
                            $(".loader_sign_up").addClass('d-none');
                            $(".btn_name_sign_up").html(lbl_sign_up);
                        }, 500);
                        var message = response.message;
                        var is_error = response.is_error;
                        if(is_error == 1){
                            $('.alert').removeClass('d-none');
                            $('.alert').addClass('alert-danger');
                        }else{
                            $('.alert').removeClass('d-none');
                            $('.alert').removeClass('alert-danger');
                            $('.alert').addClass('alert-success');
                            $('#login_email_mobile').focus();
                        }
                        $('html, body').animate({
                            scrollTop: $(".breadcrumb-item").offset().top
                        }, 1000);
                        $('.alert').html(message);
                        setTimeout(function () {
                            $(".alert").addClass('d-none'); 
                        }, 5000);*/
                    }
                });
            }
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleSubscriptionValidation();
        }
    };
}();