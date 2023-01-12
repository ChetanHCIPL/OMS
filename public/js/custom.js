/*******Start Timer************/
var current_date_time = $("#current_date_time").val();
if(current_date_time){
    console.log(current_date_time);
	var serverdate=new Date(current_date_time);
    console.log(serverdate);
}

function getWishlistCount()
{ 
    // /get-wishlist-coun
   $.ajax({
            type: "POST",
            url: route_get_count_wishlist,
            data: {'_token':$('input[name="_token"]').val()},
            success: function (response) {
                var is_error = response['is_error'];
                var message = response['message'];
                var data = response['data'];
                 
                $('#wishlist_count').html(data);
            }
        });
}
function timelength(what){
	var output=(what.toString().length==1)? "0"+what : what
	return output
}
function myTimer() { 
   serverdate.setSeconds(serverdate.getSeconds()+1);
   var timestring=timelength(serverdate.getHours())+":"+timelength(serverdate.getMinutes())+":"+timelength(serverdate.getSeconds())
   $('#servertime').html(timestring);
}
setInterval("myTimer()", 1000);
/*******End Timer************/
// Slick Slider Js 
$(document).ready(function() {
    if(logged_in_flag != ''){

        getWishlistCount();
    }

    SubscriptionFormValidation.init();
    $("#live_auction_data").slick({
        dots: false,
        arrows: true,
        infinite: true,
        autoplay: true,
        slidesToShow: 4,
        slidesToScroll: 1,
        responsive: [{
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    infinite: true,
                    dots: true
                }
            },
            {
                breakpoint: 768,
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


/*** Live Hot Deals ***/
$(document).ready(function() {
    $(".hot_deal").slick({
        dots: false,
        arrows: true,
        autoplay: true,
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        responsive: [{
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    infinite: true,
                    dots: true
                }
            },
            {
                breakpoint: 768,
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

/*** Upcoming Events ***/
$(document).ready(function() {
    $(".upcoming_event").slick({
        dots: false,
        arrows: true,
        infinite: true,
        slidesToShow: 5,
        slidesToScroll: 1,
        responsive: [{
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

/*** News ***/
$(document).ready(function() {
    $(".news").slick({
        dots: false,
        arrows: true,
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        responsive: [{
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    infinite: true,
                    dots: true
                }
            },
            {
                breakpoint: 768,
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
        ],
    });
});



/*** Our Partners ***/
$(document).ready(function() {
    $(".our_partners").slick({
        dots: false,
        arrows: true,
        infinite: true,
        slidesToShow: 6,
        slidesToScroll: 1,
        responsive: [{
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

/*** Single Product Slider ***/
$(document).ready(function() {
    $(".product__slider-main").slick({
        dots: false,
        arrows: true,
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        asNavFor: '.product__slider-thmb',
    });

    $(".product__slider-thmb").slick({
        dots: false,
        arrows: false,
        infinite: true,
        slidesToShow: 16,
        slidesToScroll: 1,
        asNavFor: '.product__slider-main',
        focusOnSelect: true
    });
});
// Slick Slider Js 
/*$(document).ready(function() {
      $(".regular").slick({
        dots: false,
        arrows: true,
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1, 
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 3,
              infinite: true,
              dots: false
            }
          },
          {
            breakpoint: 768,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 2
            }
          },
          {
            breakpoint: 481,
            settings: {
              dots: false,
              arrows: true,
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]
      });
    });*/

$(document).ready(function() {
    // $("#sidebar").mCustomScrollbar({
    //   theme: "minimal"
    // });

    $('#dismiss, .overlay').on('click', function() {
        $('#sidebar').removeClass('active');
        $('.overlay').removeClass('active');
    });

    $('#sidebarCollapse').on('click', function() {
        $('#sidebar').addClass('active');
        $('.overlay').addClass('active');
        $('.collapse.in').toggleClass('in');
        $('a[aria-expanded=true]').attr('aria-expanded', 'false');
    });
});

/** Menu Dropdown Js **/

$('body').on('mouseenter mouseleave', '.dropdown-hover', function(e) {
    var dropdown = $(e.target).closest('.dropdown-hover');
    dropdown.addClass('show');

    setTimeout(function() {
        dropdown[dropdown.is(':hover') ? 'addClass' : 'removeClass']('show');
    }, 300);
});


/**** Accordation ****/

$('.collapse').on('shown.bs.collapse', function() {
    $(this).parent().find(".glyphicon-plus").removeClass("glyphicon-plus").addClass("glyphicon-minus");
}).on('hidden.bs.collapse', function() {
    $(this).parent().find(".glyphicon-minus").removeClass("glyphicon-minus").addClass("glyphicon-plus");
});
$(document).ready(function() {
    $('.live-auction-box').each(function() {
        var auction_id = $(this).find('span.live_date_time').attr('auction_id');
       
        var end_date = $(this).find('span.live_date_time').attr('end_time'); 
        setLiveTimerForAuction(auction_id, end_date);
    });
    $('.deal-auction-box').each(function() {
        var auction_id = $(this).find('span.deal_date_time').attr('auction_id');
        var end_date = $(this).find('span.deal_date_time').attr('end_time');
        setDealTimerForAuction(auction_id, end_date);
    });
});
/**** Timer for Live Auction ****/
function setLiveTimerForAuction(auction_id,end_date) {
    
    $('#live_timer_' + auction_id).countdowntimer({
        dateAndTime: end_date,
        startDate: current_time,
        size: "xs",
        displayFormat: "DHMS",
        labelsFormat: true,
        timeUp: function() {            
		  $('#live_timer_' + auction_id).html(lbl_time_is_up);
		  $('#live_timer_' + auction_id).siblings('h4').html('');
		  $('.bid_btn_' + auction_id).addClass('d-none');
        }
    });
}
/**** Timer for Live Auction ****/
function setDealTimerForAuction(auction_id, end_date) {
    $('#deal_timer_' + auction_id).countdowntimer({
        dateAndTime: end_date,
        startDate: current_time,
        size: "xs",
        displayFormat: "Dhms",
		labelsFormat : true,
        timeUp: function() {
			$('#deal_timer_' + auction_id).html(lbl_time_is_up);
			$('#deal_timer_' + auction_id).siblings('h4').html('');
			$('.buy_btn_' + auction_id).addClass('d-none');
        }
    });
}

/********Start Search***********/
$("#category_dd a").on("click", function () {
	var id = $(this).data('id');
	if(id > 0 || id == '-1'){
		$("#search_category_id").val(id);
	}else{
		$("#search_category_id").val('');
	}
	var text = $(this).text();
	$("#cat_text").html(text);
});

$("#category_dd a").click(function(){
    validateSearch();
});

function validateSearch(){
    // if($("#search").val() == '' && $("#search_category_id").val() == ''){
    //     $("#validate_search").removeClass('d-none');
    //     return false;
    // }
    var header_product_list = $("#header_product_list").val();
    header_product_list = header_product_list.replace(':attr_name',make_slug($("#cat_text").html()));
    if($("#search_category_id").val() == '-1'){
        var new_header_product_list = header_product_list.replace(':attr_id','');
        if($("#search").val() != ''){
            window.location.href = new_header_product_list+"?search="+$("#search").val();
        }else{
            window.location.href = new_header_product_list;
        }
    }else{
        if($("#search_category_id").val() != '' && $("#search").val() != ''){
            var new_header_product_list = header_product_list.replace(':attr_id',$("#search_category_id").val());
            window.location.href = new_header_product_list+"?parent=0&search="+$("#search").val();
        }else if($("#search_category_id").val() != '' && $("#search").val() == ''){
            var new_header_product_list = header_product_list.replace(':attr_id',$("#search_category_id").val());
            window.location.href = new_header_product_list+"?parent=0";
        }else if($("#search_category_id").val() == '' && $("#search").val() != ''){
            var search_list = $("#header_product_list").val();
            search_list = search_list.replace(':attr_name','all');
            new_search_list = search_list.replace(':attr_id','');
            window.location.href = new_search_list+"?search="+$("#search").val();
        }
    }
}

/*function validateSearch(){
	if($("#search").val() == '' && $("#category_id").val() == ''){
		$("#validate_search").removeClass('d-none');
		return false;
	}
	return true;
}*/
/********End Search**************/

var SubscriptionFormValidation = function () {
    $('#subscrip_success').html(''); 
    $('#subscrip_error').html('');
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
                email: {
                    required:enter_email_address,
                    maxlength:enter_valid_email_address,
                    email:enter_valid_email_address
                },
            },
            invalidHandler: function (event, validator) { //display error alert on form submit              
                success.hide();
                error.show();
            },
            errorPlacement: function (error, element) {
                error.insertAfter('.input-group'); // for other inputs, just perform default behavior
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
                $("#btn_subscribe").html(lbl_loading);
                $.ajax({
                    type: "POST",
                    url: formData,
                    data: {'_token':$('input[name="_token"]').val(),'email':$('#subscribe_email').val()},
                    success: function (response) {
                        $("#btn_subscribe").html(lbl_subscribe); 
                        $('form[name="subscribe_form"]')[0].reset(); // reset form list
                        $('#subscrip_success').html(''); 
                        $('#subscrip_error').html('');
                        var message = response.msg;
                        var is_error = response.is_error;
                        if(is_error == 1){
                            $('#subscrip_success').html(''); 
                            $('#subscrip_error').html(message);
                        }else{
                            $('#subscrip_success').html(message); 
                            $('#subscrip_error').html('');
                        } 
                        $('.alert').html(message);
                        setTimeout(function () {
                             $('#subscrip_success').html(''); 
                             $('#subscrip_error').html('');
                        }, 5000);
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

/***********Start Add To Wishlist*************/
function addToWishlist(Obj,auc_id,is_detail){
	if(is_detail === undefined){
		is_detail = '0';
	}
    if(logged_in_flag != ''){
    	$.ajax({
    		type: "POST",
    		url: route_add_to_wishlist,
    		data: {'_token':$('input[name="_token"]').val(),'auction_id':auc_id},
    		success: function (response) {
    			var is_error = response['is_error'];
    			var message = response['message'];
    			var data = response['data'];
				if(is_detail == 0){
					$("#msg-modal").trigger('click');
					var msg_html = displayMessageBox(message);
					$("#msg-html").html(msg_html);
				}else{
					if(is_error == '1'){
						$(".wishlist-error").html(message);
						setTimeout(function() {
    						$('.wishlist-error').html('');
    					}, 4000); 
						
						$(".wishlist-error-"+auc_id).html(message);
						setTimeout(function() {
    						$(".wishlist-error-"+auc_id).html('');
    					}, 4000);
					}else{

						$(".wishlist-success").html(message);
						setTimeout(function() {
    						$('.wishlist-success').html('');
    					}, 4000);

						$(".wishlist-success-"+auc_id).html(message);
						setTimeout(function() {
    						$(".wishlist-success-"+auc_id).html('');
    					}, 4000);  
					}
					
				}
    			if(data['action'] == 1){
    				$(Obj).find('i').removeClass('fa fa-heart-o').addClass('fa fa-heart');
    				$(Obj).attr('title',lbl_remove_from_wishlist);
    				$(Obj).find('span').html(lbl_remove_from_wishlist);
					if(request_type == 'saved'){
						$("grid_"+auc_id).remove();
						$("list_"+auc_id).remove();
					}
    			}else if(data['action'] == 2){
    				$(Obj).find('i').removeClass('fa fa-heart').addClass('fa fa-heart-o');
    				$(Obj).attr('title',lbl_add_to_wishlist);
    				$(Obj).find('span').html(lbl_add_to_wishlist);
                    $("#list_"+auc_id).remove();
    			}
    		}
    	});
    }else{
        //alert(msg_login_to_continue_wishlist)
        sessionStorage.setItem("login_error","1");
        sessionStorage.setItem("login_error_message",msg_login_to_continue_wishlist);
        window.location.href = route_login;
    }
}
/***********End Add To Wishlist*************/
