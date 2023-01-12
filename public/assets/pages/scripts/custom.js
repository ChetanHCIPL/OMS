
// Function : Used to check image Exist Error on List Page
function isUserImageExist(obj){
    //var no_image_url = assets+'/uploads/admin/images/no_image/';
    //var no_image_url = assets+'/uploads/admin/images/';
    var no_image_url = assets+'/uploads/admin/user/';
    var no_image_name = $(obj).attr('noimage');
    if($(obj).attr('src') != no_image_url+no_image_name){
        $(obj).attr('src', no_image_url+no_image_name);
        $(obj).attr('title', no_image_name);
        //console.log($(obj).attr('src'));
    }

    if($(obj).hasClass('product-gallery-img') == false){
        if($(obj).closest('a').hasClass('fancybox') == true){
            $(obj).closest('a.fancybox').replaceWith(obj);
        }else if( ($(obj).parent('a').hasClass('fancybox') == false) && ($(obj).hasClass('flag') == false)){
            $(obj).siblings().remove();
            $(obj).remove();
        }else{
            $(obj).parent('a.fancybox').replaceWith(obj);
        }
    }
}
// Function : Used to check image Exist Error on List Page
function isImageExist(obj){
    
    var no_image_url = assets+'/images/no_image/';
    var no_image_name = $(obj).attr('noimage');

    if($(obj).attr('src') != no_image_url+no_image_name){
        $(obj).attr('src', no_image_url+no_image_name);
    }

    if($(obj).hasClass('product-gallery-img') == false){
        if($(obj).closest('a').hasClass('fancybox') == true){
            $(obj).closest('a.fancybox').replaceWith(obj);
        }else if( ($(obj).parent('a').hasClass('fancybox') == false) && ($(obj).hasClass('flag') == false)){
            $(obj).siblings().remove();
            $(obj).remove();
        }else{
            $(obj).parent('a.fancybox').replaceWith(obj);
        }
    }
}

// Find month differnce between two dates
function getMonthDifference(startDate, endDate) {

    const [day, month, year] = startDate.split('-');
    const result_startDate = [year, month, day].join('-');
    
    const [day_to, month_to, year_to] = endDate.split('-');
    const result_endDate = [year_to, month_to, day_to].join('-');

    var convert_start_date = new Date(result_startDate);
    var convert_end_date = new Date(result_endDate);

  return (
    convert_end_date.getMonth() -
    convert_start_date.getMonth() +
    12 * (convert_end_date.getFullYear() - convert_start_date.getFullYear())
  );
}