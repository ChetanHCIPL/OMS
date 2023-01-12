$("#per_tab").click(function(){
    $("#tab_1").removeClass('active');
    $("#report-tab").removeClass('active');
    $("#general-tab").removeClass('active');
    
    $("#module").addClass('active');
    $("#module-tab").addClass('active');
});

$("#selectall").click(function () {
    var class_nm = (this.checked) ? "checked" : "";
    $('.case').closest('span').attr("class", class_nm);
    $('.case').prop('checked', this.checked);
    $("#selectall_list").closest('span').attr("class", class_nm);
    $("#selectall_list").prop('checked', this.checked);

    $("#selectall_view").closest('span').attr("class", class_nm);
    $("#selectall_view").prop('checked', this.checked);

    $("#selectall_add").closest('span').attr("class", class_nm);
    $("#selectall_add").prop('checked', this.checked);

    $("#selectall_edit").closest('span').attr("class", class_nm);
    $("#selectall_edit").prop('checked', this.checked);

    $("#selectall_delete").closest('span').attr("class", class_nm);
    $("#selectall_delete").prop('checked', this.checked);

    $("#selectall_status").closest('span').attr("class", class_nm);
    $("#selectall_status").prop('checked', this.checked);

    selectAllList();
    selectAllView();
    selectAllAdd();
    selectAllEdit();
    selectAllDelete();
    selectAllStatus();

});
$("#selectall_report").click(function () {
    var class_nm = (this.checked) ? "checked" : "";
    $('.case_report').closest('span').attr("class", class_nm);
    $('.case_report').prop('checked', this.checked);
    $("#selectall_export").prop('checked' ,this.checked );
    $("#selectall_print").prop('checked' , this.checked);
    selectAllExport();
    selectAllPrint();

});
$("#selectall_general").click(function () {
    var class_nm = (this.checked) ? "checked" : "";
    $('.case-general').closest('span').attr("class", class_nm);
    $('.case-general').prop('checked', this.checked);
});

function selectAllList() {
    var class_nm = ($("#selectall_list").prop('checked'))? "checked" : "";
    $('.case_list').closest('span').attr("class", class_nm);
    $('.case_list').prop('checked', $("#selectall_list").prop('checked'));
};
function selectAllView() {
    var class_nm = ($("#selectall_view").prop('checked'))? "checked" : "";
    $('.case_view').closest('span').attr("class", class_nm);
    $('.case_view').prop('checked', $("#selectall_view").prop('checked'));
};
function selectAllAdd() {
    var class_nm = ($("#selectall_add").prop('checked'))? "checked" : "";
    $('.case_add').closest('span').attr("class", class_nm);
    $('.case_add').prop('checked', $("#selectall_add").prop('checked'));
};

function selectAllEdit() {
    var class_nm = ($("#selectall_edit").prop('checked'))? "checked" : "";
    $('.case_edit').closest('span').attr("class", class_nm);
    $('.case_edit').prop('checked', $("#selectall_edit").prop('checked'));
};

function selectAllDelete() {
    var class_nm = ($("#selectall_delete").prop('checked'))? "checked" : "";
    $('.case_delete').closest('span').attr("class", class_nm);
    $('.case_delete').prop('checked', $("#selectall_delete").prop('checked'));
};

function selectAllStatus() {
    var class_nm = ($("#selectall_status").prop('checked'))? "checked" : "";
    $('.case_status').closest('span').attr("class", class_nm);
    $('.case_status').prop('checked', $("#selectall_status").prop('checked'));
};
$("#selectall_status").click(function () {
    var class_nm = (this.checked) ? "checked" : "";
    $('.case_status').closest('span').attr("class", class_nm);
    $('.case_status').prop('checked', this.checked);
});
function selectAllExport() {
    var class_nm = ($("#selectall_export").prop('checked'))? "checked" : "";
    $('.case_export').closest('span').attr("class", class_nm);
    $('.case_export').prop('checked', $("#selectall_export").prop('checked'));
};
$("#selectall_export").click(function () {
    var class_nm = (this.checked) ? "checked" : "";
    $('.case_export').closest('span').attr("class", class_nm);
    $('.case_export').prop('checked', this.checked);
});
function selectAllPrint() {
    var class_nm = ($("#selectall_print").prop('checked'))? "checked" : "";
    $('.case_print').closest('span').attr("class", class_nm);
    $('.case_print').prop('checked', $("#selectall_print").prop('checked'));
};

function checkAllRow(rowObj) {
    var rowEleName = rowObj.id;
    var tmpArr1 = rowEleName.split("_");
    var class_nm = (rowObj.checked) ? "checked" : "";
    var actIndex = tmpArr1[1];

    // List checkbox of Row is checked
    $('#list_' + actIndex ).closest('span').attr("class", class_nm);
    $('#list_' + actIndex).prop('checked', rowObj.checked);

    $('#view_' + actIndex ).closest('span').attr("class", class_nm);
    $('#view_' + actIndex).prop('checked', rowObj.checked);

    $('#add_' + actIndex ).closest('span').attr("class", class_nm);
    $('#add_' + actIndex).prop('checked', rowObj.checked);

    $('#edit_' + actIndex ).closest('span').attr("class", class_nm);
    $('#edit_' + actIndex).prop('checked', rowObj.checked);

    $('#delete_' + actIndex ).closest('span').attr("class", class_nm);
    $('#delete_' + actIndex).prop('checked', rowObj.checked);

    $('#status_'+actIndex).closest('span').attr("class", class_nm);
    $('#status_' + actIndex ).prop('checked', rowObj.checked);

}
function checkAllRowReport(rowObj){
	var rowEleName = rowObj.id;
    var tmpArr1 = rowEleName.split("_");
    var class_nm = (rowObj.checked) ? "checked" : "";
    var actIndex = tmpArr1[1];

	$('#export_' + actIndex).closest('span').attr("class", class_nm);
    $('#export_' + actIndex).prop('checked', rowObj.checked);

    $('#print_' + actIndex).closest('span').attr("class", class_nm);
    $('#print_' + actIndex).prop('checked', rowObj.checked);


}

function chekRowchk(chkObj){
    var rowEleName = chkObj.id;
    var tmpArr1 = rowEleName.split("_");
    var actIndex = tmpArr1[1];
    $("#selectrow_"+actIndex).closest('span').attr("class", "");
    $("#selectrow_"+actIndex).prop('checked',false);

}
