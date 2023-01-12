jQuery(document).ready(function(){
	$("#roles_tabs").click(function(){
		$("#tab_1").addClass('active');
        $("#module").removeClass('active');
        $("#report").removeClass('active');
		$("#general").removeClass('active');
	});

	$("#per_tab").click(function(){
        $("#tab_1").removeClass('active');
        $("#report-tab").removeClass('active');
        $("#general-tab").removeClass('active');
        
        $("#module").addClass('active');
        $("#module-tab").addClass('active');
        showAccessModuleData('module');
    });
	
});

$(".nav-tabs a").click(function(){
	 
    var id = $(this).attr("id");

    if(id == 'module-tab'){
        showAccessModuleData('module');
    }
    else if(id == 'report-tab'){
        showAccessModuleData('report');
    }
    else if(id == 'general-tab'){
        showAccessModuleData('general');
    }
});

function showAccessModuleData(type){
	var columns = new Array;
	var id='';
    if(type=='module'){
		id='datatable_module';
		columns = [
			{"orderable": false, "class": "text-center"},
			{"orderable": false},
			{"orderable": false, "class": "text-center"},
			{"orderable": false, "class": "text-center"},
			{"orderable": false, "class": "text-center"},
			{"orderable": false, "class": "text-center"},
		];
	}else if(type=='report'){
		id='datatable_report';
		columns = [
			{"orderable": false, "class": "text-center"},
			{"orderable": false},
			{"orderable": false, "class": "text-center"},
			{"orderable": false, "class": "text-center"},
			{"orderable": false, "class": "text-center"}
		];
	}else if(type=='general'){
		id='datatable_general';
		columns = [
			{"orderable": false, "class": "text-center"},
			{"orderable": false, "class": "text-center"},
			{"orderable": false}
		];
	}
	$('#'+id).DataTable({
		"processing": true,
		"serverSide": true,
		"searching":false, // Hide Search Box
		"bPaginate": false,// Hide Pagination
		"bInfo": false,//Hide pagination entries
		"ajax": {
			"type": "POST",
			"url": assets + "/" +panel_text + "/access-group/role-view", // ajax source
			"data": function (data) { 
				data['_token'] = $('input[name="_token"]').val();
				data['iAGroupId'] = $('input[name="id"]').val();
				data['tab'] = type;
			},
		},
		"columns": columns,
		"bDestroy": true
	});
}
