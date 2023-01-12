@extends('layouts.admin')
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
				<li class="active">{{(isset($mode)?$mode:'') }} Roles</a></li>
			</ol>
		</section>
        <section class="horizontal-grid" id="horizontal-grid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">   
                    	<div class="card-head ">
			              <div class="card-header">
			                  <div class="float-right">
			                      @if(per_hasModuleAccess('Roles', 'Edit'))
			                        <a href="{{route('access-role',['mode' => 'edit', 'id' => isset($data[0]['id'])?base64_encode($data[0]['id']):''])}}" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light"><i class="la la-edit"></i></span></a>
			                      @endif
			                       @if(per_hasModuleAccess('Roles', 'Delete'))
			                        <a onclick="deleteSingleRecord({{isset($data[0]['id'])?$data[0]['id']:''}},'access-role');" title="Delete "><span class="btn btn-icon btn-danger waves-effect waves-light"><i class="la la-trash"></i></span></a>
			                      @endif
			                  </div>
			                </div>
			            </div>  
                        <div class="card-content collapse show">
                            <div class="card-body language-input">
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
													</div>@if(isset($mode) && $mode=='View')
												  <div id="headingper" class="card-header">
														<a data-toggle="collapse" id="per_tab" href="#accordionper" aria-expanded="false" aria-controls="accordionper" class="card-title lead collapsed">Permission</a>
													</div>
													<div id="accordionper" role="tabpanel" data-parent="#accordionWrap5" aria-labelledby="headingper" class="card-collapse collapse" aria-expanded="f">
														<div class="card-body">
															<ul class="nav nav-tabs m-0">
																<li class="nav-item">
																	<a class="nav-link active" id="module-tab" data-toggle="tab" aria-controls="module" href="#module" aria-expanded="true">
																	Module </a>
																</li>
																<li class="nav-item">
																	<a class="nav-link" id="report-tab" data-toggle="tab" aria-controls="report" href="#report" aria-expanded="false">
																	Report </a>
																</li>
																<li class="nav-item">
																	<a class="nav-link" id="general-tab" data-toggle="tab" aria-controls="general" href="#general" aria-expanded="false">
																	General </a>
																</li>
															</ul>
														</div>
													</div>@endif
											  </div>
											</div>
										</div>
									</div>
									<div class="col-xl-10 col-lg-10 col-md-12 col-12">
										<div class="tab-content">
											<div class="tab-pane active" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="tab_1"  aria-labelledby="base-tab_1">
												<div class="row">
													<div class="col-xl-12">
														<h3 class="tab-content-title">General Information</h3>
													</div>
												</div>
												<form class="form form-horizontal" id="frmadd" name="frmadd" enctype="multipart/form-data" action="">
													<input type="hidden" name="_token" value="{{csrf_token()}}" />
													<input type="hidden" name="customActionType" value="group_action" />
													<input type="hidden" id="customActionName" name="customActionName" value="{{(isset($mode)?$mode:'') }}" />
													<input type="hidden" id="id" name="id" value="{{(isset($id)?$id:'')}}" />
													<div class="row">
														<div class="col-xl-8">
															<div class="row">
																<div class="form-body">
																	<div class="form-group row mx-auto">
																		<label class="col-md-3 ">Role </label>
																		<div class="col-md-9 label-control">
																			{{isset($data[0]['access_group'])?$data[0]['access_group']:'---'}}
																		</div>
																	</div>
																	<div class="form-group row mx-auto">
																		<label class="col-md-3 ">Status</label>
																		<div class="col-md-9 label-control">
																		@php 
									                                        $status = (isset($data[0]['status']) && $data[0]['status'] == 1) ? "Active" : "Inactive";
																			$status_color = Config::get('constants.status_color.' . $status);
																			$status_btn = \App\GlobalClass\Design::blade('status',$status,$status_color);
																			echo $status_btn;
									                                      @endphp
									                                      
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</form>
											</div>
											<div class="tab-pane" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="module"  aria-labelledby="module-tab">
												<input type="hidden" name="tab_val" value="module"/>
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
																<th width="1%"> # </th> 
																<th width="30%"> Access Module </th>
																<th width="5%"> Listing </th>
																<th width="5%"> Add </th>
																<th width="5%"> Edit </th>
																<th width="5%"> Delete </th>
															</tr>
															</thead>
															<tbody></tbody>
														</table>
													   </div>
													</div>
												</div>
											</div>
											<div class="tab-pane" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="report"  aria-labelledby="report-tab">
												<input type="hidden" name="tab_val" value="report"/>
												<div class="row">
													<div class="col-xl-12">
														<h3 class="tab-content-title">Report</h3>
													</div>
												  </div>
												<div class="row">
													<div class="col-xl-10">
													   <div class="row">
														<table class="table table-striped table-bordered table-hover access-role" id="datatable_report">
															<thead>
															<tr role="row" class="heading">
																<th width="1%"> # </th>
																<th width="50%"> Access Module </th>
																<th width="30%"> Date Period </th>
																<th width="9%"> Export </th></th>
																<th width="9%"> Print </th>
															</tr>
															</thead>
															<tbody></tbody>
														</table>
													   </div>
													</div>
												</div>
											</div>
											<div class="tab-pane" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="general"  aria-labelledby="general-tab">
												<input type="hidden" name="tab_val" value="general"/>
												<div class="row">
													<div class="col-xl-12">
														<h3 class="tab-content-title">General</h3>
													</div>
												  </div>
												<div class="row">
													<div class="col-xl-10">
													   <div class="row">
														<table class="table table-striped table-bordered table-hover access-role" id="datatable_general">
															<thead>
															<tr role="row" class="heading">
																<th width="1%"> # </th>
																<th width="1%"></th>
																<th width="15%"> Name </th>
															</tr>
															</thead>
															<tbody></tbody>
														</table>
													   </div>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12 row">
											<div class="form-actions text-right col-xl-12">
						                    <a href="{{route('access-role/grid')}}">
						                        <button type="button" class="btn mr-1">
						                            <i class="ft-chevron-left"></i> Back
						                        </button></a>
						                    </div>
										</div>
									</div>
								</div>
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
<script src="{{ asset('/assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/pages/scripts/admin/accessgroup/access_group_role_view.js')}}" type="text/javascript"></script>
@endsection