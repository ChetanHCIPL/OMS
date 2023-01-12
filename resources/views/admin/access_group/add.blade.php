@extends('layouts.admin')
@section('styles')
<!--Start Bootstrap Switch-->
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/switchery.min.css')}}">
<link href="{{ asset('assets/css/core/colors/palette-switch.css')}}" rel="stylesheet" type="text/css" />
<!--End Bootstrap Switch-->
<!--Start Datatable-->
<link href="{{ asset('/assets/vendors/css/tables/datatable/datatables.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
<style type="text/css">
	.access-role .custom-checkbox{
		text-align: center;
	}
	.text-center{
		text-align: center;
	}
	.table td:first-child {
	    text-align: center;
	}

</style>
<!--End Datatable-->
@endsection
@section('content')
<div class="content-wrapper">
    <div class="content-body">
		<section class="content-header clearfix">
			<h3>Roles<strong><span class="text-muted accent-3">{{isset($data[0]['access_group'])?' - '.$data[0]['access_group']:''}}</span></strong></h3>
			<ol class="breadcrumb">
				<li><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
				<li><a href="javascript:void(0);">Admin</a></li>
				<li><a href="javascript:void(0);">User Mgmt</a></li>
				<li><a href="{{route('access-role/grid')}}">Roles</a></li>
				<li class="active">{{(isset($mode)?$mode:'') }} Role</li>
			</ol>
		</section>
        <section class="horizontal-grid" id="horizontal-grid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                    	@if(isset($mode) && $mode == "Update")
	                    	<div class="card-head ">
				              	<div class="card-header">
				                    <div class="float-right">
				                       @if(per_hasModuleAccess('Roles', 'View'))
				                        <a href="{{route('access-role',['mode' => 'view', 'id' => isset($id)?base64_encode($id):''])}}" title="View"><span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-eye"></i></span></a>
				                      @endif
				                       @if(per_hasModuleAccess('Roles', 'Delete'))
				                        <a onclick="deleteSingleRecord({{isset($id)?$id:''}},'access-role');" title="Delete "><span class="btn btn-icon btn-danger waves-effect waves-light"><i class="la la-trash"></i></span></a>
				                      @endif
				                    </div>
				                </div>
				            </div>
						 @endif
                        <div class="card-content collapse show">
                            <div class="card-body language-input">
                            	<form class="form form-horizontal" id="frmadd" name="frmadd" >
									<div class="row">
										<div class="col-md-12 ">
											<div class="alert alert-danger d-none">
												<span id="error-msg">You have some form errors. Please check below.</span>
												<button class="close" data-close="alert"></button>
											</div>
											<div class="alert alert-success d-none">
												<span id="success-msg">Your form validation is successful!</span>
												<button class="close" data-close="alert"></button>
											</div>
										</div>
										<div class="col-xl-2 col-lg-3 col-md-12 col-12">
											<div class="sidebar-left site-setting">
												<div id="accordionWrap5" role="tablist" aria-multiselectable="true">
													<div class="card collapse-icon accordion-icon-rotate">
														<div id="heading51" class="card-header">
															<a data-toggle="collapse" href="#accordion51" aria-expanded="true" aria-controls="accordion51" class="card-title lead" id="roles_tabs">Roles</a>
														</div>
														<div id="accordion51" role="tabpanel" data-parent="#accordionWrap5" aria-labelledby="heading51" class="card-collapse collapse show" aria-expanded="true">
															<div class="card-body">
																<ul class="nav nav-tabs m-0">
																	<li class="nav-item">
																		<a class="nav-link active" id="base-tab_1" data-toggle="tab" aria-controls="tab_1" href="#tab_1" aria-expanded="true">
																		General Information </a>
																	</li>

																</ul>
															</div>
														</div>
												  <?php //if (isset($mode) && $mode=='Update') {
													  ?><div id="headingper" class="card-header">
															<a data-toggle="collapse" id="per_tab" href="#accordionper" aria-expanded="false" aria-controls="accordionper" class="card-title lead collapsed">Permission</a>
														</div>
														<div id="accordionper" role="tabpanel" data-parent="#accordionWrap5" aria-labelledby="headingper" class="card-collapse collapse" aria-expanded="f">
															<div class="card-body">
																<ul class="nav nav-tabs m-0">
																	<li class="nav-item">
																		<a class="nav-link active" id="module-tab" data-toggle="tab" aria-controls="module" href="#module" aria-expanded="true">
																		Module </a>
																	</li>
																	{{-- <li class="nav-item">
																		<a class="nav-link" id="report-tab" data-toggle="tab" aria-controls="report" href="#report" aria-expanded="false">
																		Report </a>
																	</li> --}}
																	 
																</ul>
															</div>
														</div><?php //}
												  ?>
												  </div>
												</div>
											</div>
										</div>
										<div class="col-xl-10 col-lg-9 col-md-12 col-12">
											<div class="tab-content">
												<div class="tab-pane active" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="tab_1"  aria-labelledby="base-tab_1">
													<div class="row">
														<div class="col-xl-12">
															<h3 class="tab-content-title">General Information</h3>
														</div>
													</div>
														<input type="hidden" name="_token" value="{{csrf_token()}}" />
														<input type="hidden" name="customActionType" value="group_action" />
														<input type="hidden" id="customActionName" name="customActionName" value="{{(isset($mode)?$mode:'') }}" />
														<input type="hidden" name="id" value="{{isset($id)?$id:''}}" >
														<div class="row ">
															<div class="col-xl-8">
																<div class="row">
																	<div class="form-body">
																		<div class="form-group row mx-auto">
																			<label class="col-md-3 label-control">Role <span class="required">*</span></label>
																			<div class="col-md-9">
																				<input type="text" placeholder="Role Name" id="access_group" name="access_group" class="form-control" value="{{isset($data[0]['access_group'])?$data[0]['access_group']:''}}">
																			</div>
																		</div>
																		<div class="form-group row mx-auto">
																			<label class="col-md-3 label-control">Status</label>
																			<div class="col-md-9">
																				<input type="checkbox" class="switchBootstrap form-control" id="status" name="status" data-on-text="Active" data-off-text="Inactive" data-on-color="{{Config::get('constants.switch_on_color')}}"  data-off-color="{{Config::get('constants.switch_off_color')}}"  value="1" {{((isset($data[0]['status']) && $data[0]['status'] == 1 )?'checked':($mode == 'Add')?'checked':'')}}/>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
												</div>
												<div class="tab-pane" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="module"  aria-labelledby="module-tab">
													<div class="row">
														<div class="col-xl-12">
															<h3 class="tab-content-title">Module</h3>
														</div>
													  </div>
													<div class="row">
														<div class="col-xl-11">
														   <div class="row">
															<table class="table table-striped table-bordered table-hover access-role" id="datatable_module">
																<thead>
																<tr role="row" class="heading">
																	<th width="1%" class="text-center"> # </th> 
																	<th width="1%"><div class="custom-control custom-checkbox"><input id="selectall" type="checkbox" class="custom-control-input group-checkable"><label class="custom-control-label" for="selectall"></label></div></th>
																	<th width="30%"> Access Module </th>
																	<th width="5%" class="text-center"> Listing <br/><div class="custom-control custom-checkbox"><input id="selectall_list" type="checkbox" class="custom-control-input group-checkable" onclick="selectAllList();"><label class="custom-control-label" for="selectall_list"></label></div></th>
																	<th width="5%" class="text-center"> View <br/><div class="custom-control custom-checkbox"><input id="selectall_view" type="checkbox" class="custom-control-input group-checkable"  onclick="selectAllView();"><label class="custom-control-label" for="selectall_view"></label></div></th>
																	<th width="5%" class="text-center"> Add <br/><div class="custom-control custom-checkbox"><input id="selectall_add" type="checkbox" class="custom-control-input group-checkable" onclick="selectAllAdd();"><label class="custom-control-label" for="selectall_add"></label></div></th>
																	<th width="5%" class="text-center"> Edit <br/><div class="custom-control custom-checkbox"><input id="selectall_edit" type="checkbox" class="custom-control-input group-checkable" onclick="selectAllEdit();"><label class="custom-control-label" for="selectall_edit"></label></div></th>
																	<th width="5%" class="text-center"> Delete <br/><div class="custom-control custom-checkbox"><input id="selectall_delete" type="checkbox" class="custom-control-input group-checkable" onclick="selectAllDelete();"><label class="custom-control-label" for="selectall_delete"></label></div></th>
																</tr>
																</thead>
																<tbody>
																	 @if( !empty($permission_arr['module']) && isset($permission_arr['module']))
																			<?php $module =$permission_arr['module'];
																			echo $module; ?>
																		@else
																		    echo "<td colspan='8'  class='text-center'>No records found</td>"; 
																		@endif
																</tbody>
															</table>
														   </div>
														</div>
													</div>
												</div>
												{{-- <div class="tab-pane" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="report"  aria-labelledby="report-tab">
													<div class="row">
														<div class="col-xl-12">
															<h3 class="tab-content-title">Report</h3>
														</div>
													</div>
													<div class="row">
														<div class="col-xl-11">
														   <div class="row">
															<table class="table table-striped table-bordered table-hover access-role" id="datatable_report">
																<thead>
																<tr role="row" class="heading">
																	<th width="1%"> # </th>
																	<th width="1%"><div class="custom-control custom-checkbox"><input id="selectall_report" type="checkbox" class="custom-control-input group-checkable"><label class="custom-control-label" for="selectall_report"></label></div></th></th>
																	<th width="50%"> Access Module </th>
																	<th width="30%" class="text-center"> Date Period </th>
																	<th width="9%" class="text-center"> Export <br/><div class="custom-control custom-checkbox"><input id="selectall_export" type="checkbox" class="custom-control-input group-checkable" onclick="selectAllExport();"><label class="custom-control-label" for="selectall_export" ></label></div></th></th>
																	<th width="9%" class="text-center"> Print <br/><div class="custom-control custom-checkbox"><input id="selectall_print" type="checkbox" class="custom-control-input group-checkable" onclick="selectAllPrint();"><label class="custom-control-label" for="selectall_print"></label></div></th></th>
																</tr>
																</thead>
																<tbody>
																	@if(isset($permission_arr['report']) && $permission_arr['report'] != ""):
																			echo $permission_arr['report']; 
																		@else
																		    echo "<td colspan='6' class='text-center'>No records found</td>"; 
																	@endif
																</tbody>
															</table>
														   </div>
														</div>
													</div>
												</div> --}}
												 
												<div class="form-actions text-right"><a href="{{route('access-role/grid')}}">
					                                <a href="{{route('access-role/grid')}}">
														<button type="button" class="btn mr-1">
															<i class="ft-x"></i> Cancel
														</button>
													</a>
													<button type="submit" class="btn btn-success" id="save_record">
														<i class="la la-check-square-o"></i> Save Changes
													</button>
					                            </div>
											</div>
										</div>
									</div>
								</form>
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
@section('scripts')
<!-- Start FORM VALIDATION -->
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/additional-methods.min.js')}}" type="text/javascript"></script>
<!--Start Bootstrap Switch-->
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/toggle/bootstrap-switch.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/toggle/switchery.min.js') }}"></script>
<!--End Bootstrap Switch-->
<!--Start Datatable-->
<script src="{{ asset('/assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<!--End Datatable-->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('/assets/pages/scripts/admin/accessgroup/access_group_add.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/pages/scripts/admin/accessgroup/access_group_role.js')}}" type="text/javascript"></script>

<!-- END PAGE LEVEL SCRIPTS -->
@endsection