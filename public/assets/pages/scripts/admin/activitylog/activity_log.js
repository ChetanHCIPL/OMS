
var old_date = "";

jQuery(document).ready(function () {
    $('.selectize-select').selectize({create: false, sortField: { field: 'text', direction: 'asc' }, dropdownParent: 'body'});
    $('#startlimit').val('0');
    loadActivityLog();
    $('#date_from').dateDropper({
        dropWidth: 200,
        animate:false,
        lock: today, 
        format: DATE_PICKER_FORMAT
    });
    $('#date_to').dateDropper({
        dropWidth: 200,
        animate:false,
        lock: today, 
        format: DATE_PICKER_FORMAT
    });
});

function loadActivityLogSingleOrUser() {
    $("#timeline").html('');
    $('#startlimit').val('0');
    loadActivityLog();
    old_date = "";
}

function getLogDetails(id, Title) {
    $('#activity-box').trigger("click");
    $(".modal-title").html(Title);
    var token = $('#token').val();

    $.ajax({
        type: 'POST',
        data: {'_token': token, 'id': id},
        url: assets + "/" + panel_text + "/get-activity-log-detail",
        cache: false, 
        success: function (data) {
            $('#data').html(data);
        }
    });
}

var glob_cnt  = 0;
var old_admin_id = 0;
var date_ind = 0;

function loadActivityLog() {
    $(".data_loader").show();
    var token       = $('#token').val();
    var module_id   = $('#access_module_id').val();
    var first_name  = $('#first_name').val();
    var last_name   = $('#last_name').val();
    var username    = $('#username').val();
    var date_from   = $('#date_from').val();
    var date_to     = $('#date_to').val();
    var endlimit    = 30;
    var startlimit  = $('#startlimit').val();
    
    $.ajax({
        url: assets + "/" + panel_text + "/get-activity-log",
        type: 'POST',
        data: {'_token': token, 'module_id': module_id, 'first_name': first_name, 'last_name': last_name, 'username': username, 'date_from': date_from, 'date_to': date_to, 'startlimit': startlimit, 'endlimit': endlimit},
        success: function (response) {
            //$("#load_more").button("reset");
           $(".data_loader").hide();
            var data = eval(response);
            var cnt = Object.keys(data).length;
            $("#alert-row").hide();
            glob_cnt += cnt;
            var index = 0;
            if (cnt > 0) {
                var str = "";
                var str1 = "";
                /*if($('#startlimit').val() == 0){
                   // $("#timeline").html('');
                 //  console.log(111);
                }*/
                for(date in data){
                    var dateformate = new Date(date);
                    var valueData = data[date];
                    var valueLength = data[date].length;
                    var timeline_icon = '';
                    var timeline_icon_color = '';
                    if(old_date != date){
                        if(date_ind > 0 && date_ind%2 == 1) {
                            timeline_icon = '<i class="material-icons">speaker_notes</i>';
                            timeline_icon_color = 'bg-red';
                        }else {
                            timeline_icon = '<i class="material-icons">list_alt</i>';
                            timeline_icon_color = 'bg-teal'; 
                        }
                        str +='<ul class="timeline">';
                            str +='<li class="timeline-line"></li>';
                            str +='<li class="timeline-group">';
                                str +='<a href="#" class="btn btn-primary"><i class="ft-calendar"></i> '+date+'</a>';
                            str +='</li>';
                        str +='</ul>';
                        str +='<ul class="timeline">';
                            str +='<li class="timeline-line"></li>';
                            str +='<li class="timeline-item">';
                                        str +='<div class="timeline-badge">';
                                            str +='<span class="'+timeline_icon_color+' bg-lighten-1" data-toggle="tooltip" data-placement="right">'+timeline_icon+'</span>';
                                        str +='</div>';
                                        str +='<div class="timeline-card card border-grey border-lighten-2">';  
                                            str +='<div class="card-content">';
                                                str +='<div class="card-body">';  
                                                    str +='<ul class="list-group list-group-flush activity_timeline_log_ul_'+date+'">'
                                                if(valueLength > 0){
                                                    for (var a = 0; a < valueLength; a++) {
                                                        if(valueData[a]['access_module'] != null) {
                                                            var title = valueData[a]['log_text'];

                                                            if (valueData[a]['edit_url'] != "" && valueData[a]['edit_url'] != undefined)
                                                                var link = '<a href="' + valueData[a]['edit_url'] + '" target="_blank">' + valueData[a]['access_module'] +  '</a>';
                                                            else
                                                                var link = valueData[a]['access_module'] ;

                                                            str +='<li class="list-group-item">';
                                                            str +='<div class="row">';
                                                                str +='<div class="col-lg-8 col-12">';
                                                                    str += '<span><strong>' + link + '</strong></span>: ' + valueData[a]['log_text'] + '&nbsp<a href="javascript:;" onclick="getLogDetails(' + valueData[a]['id'] + ',\'' + title + '\')"><i class="material-icons">library_books</i></a>';
                                                                str +='</div>';
                                                                str +='<div class="col-lg-4 col-12 text-right">';
                                                                     str +='<p class="card-subtitle text-muted pt-1">';
                                                                         str += '<span class="font-small-3">'+valueData[a]['AddedDate'] + ' '+valueData[a]['AddedTime']+ '</span>';
                                                                    str +='</p>';
                                                                str +='</div>';
                                                            str +='</div>';
                                                            str +='</li>';
                                                            index++;
                                                        }
                                                    } 
                                                }
                                                str +='</ul>';
                                            str +='</div>';
                                        str +='</div>';
                                    str +='</div>';
                            str +='</li>';
                        str +='</ul>';
                        date_ind++;
                    }else {
                        if(valueLength > 0){
                            for (var a = 0; a < valueLength; a++) {
                                if(valueData[a]['access_module'] != null) {
                                    var title = valueData[a]['log_text'];
                                    if (valueData[a]['edit_url'] != "" && valueData[a]['edit_url'] != undefined)
                                        var link = '<a href="' + valueData[a]['edit_url'] + '" target="_blank">' + valueData[a]['access_module'] +  '</a>';
                                    else
                                        var link = valueData[a]['access_module'] ;
                                    str1 +='<li class="list-group-item">';
                                    str1 +='<div class="row">';
                                        str1 +='<div class="col-lg-8 col-12">';
                                            str1 += '<span><strong>' + link + '</strong></span>: ' + valueData[a]['log_text'] + '&nbsp<a href="javascript:;" onclick="getLogDetails(' + valueData[a]['id'] + ',\'' + title + '\')"><i class="material-icons">library_books</i></a>';
                                        str1 +='</div>';
                                        str1 +='<div class="col-lg-4 col-12 text-right">';
                                             str1 +='<p class="card-subtitle text-muted pt-1">';
                                                 str1 += '<span class="font-small-3">'+valueData[a]['AddedDate'] + ' '+valueData[a]['AddedTime']+ '</span>';
                                            str1 +='</p>';
                                        str1 +='</div>';
                                    str1 +='</div>';
                                    str1 +='</li>';
                                    index++;
                                }
                            } 
                        }
                        if (str1 != "") {
                            $(".activity_timeline_log_ul_"+date).append(str1);
                        }
                    }
                    old_date = date;
                }
                if (str != "" || str1 != '') {
                   
                    startlimit = parseInt(startlimit) + parseInt(endlimit);
                    /* console.log(startlimit);*/
                    $('#startlimit').val(startlimit);
                }
                $("#timeline").append(str);
                if(endlimit > index){
                    $("#load-more-row").hide();
                }else
                    $("#load-more-row").show();
                
            } else {
                str +='<div class="alert alert-danger mb-2" role="alert">';
                        str +='<strong>Sorry!</strong> No records found.';
                str +='</div>';
                $("#alert-row").show();
                $("#load-more-row").hide();
            }
        }
    });
}
function resetSearchData(){
    $("#timeline").html('');
    $('#startlimit').val('0');
    $('.form-control').not('input[name="username"]').val('');
    $('.selectize-select')[0].selectize.clear();
    loadActivityLog();
}