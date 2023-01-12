@extends('layouts.admin')
@section('content')																		 
@php 
$status = (isset($data[0]['status']) && $data[0]['status'] == 1) ? "Active" : "Inactive";
$status_color = Config::get('constants.status_color.' . $status);
$status_btn = \App\GlobalClass\Design::blade('status',$status,$status_color);
@endphp
<div class="content-wrapper">
    <div class="content-body">
		<section class="content-header clearfix">
			<h3>State<strong><span class="text-muted accent-3">{{(isset($data[0]['state_name'])?' - '.$data[0]['state_name']:'')}} </span></strong></h3>

			<ol class="breadcrumb">
				<li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
				<li><a href="javascript:void(0);">Master Mgmt</a></li>
				<li><a href="{{route('state/grid')}}">State</a></li>
				<li><a href="javascript:void(0);">{{(isset($mode)?$mode:'') }} State</a></li>
			</ol>
		</section>
        <section class="horizontal-grid" id="horizontal-grid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                    	<div class="card-head ">
			                 <div class="card-header">
								<div class="float-right">
									<a href="{{route('state/grid')}}">
					                  <button type="button" class="btn mr-1 back_btn">
					                  <span class="material-icons">arrow_back_ios</span> Back  </button>
					                </a>
									 @if (per_hasModuleAccess('States', 'Edit')) 
										<a href="{{route('state',['mode' => 'edit', 'id' => isset($data[0]['id'])?base64_encode($data[0]['id']):''])}}" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light"><i class="la la-edit"></i></span></a>@endif
								</div>
							 </div>
						  </div>
                        <div class="card-content collapse show">
                            <div class="card-body language-input">
                                <form class="form form-horizontal" name="frmadd">
                                	<input type="hidden" id="token" class="form-control form-filter" name="_token" value="{{csrf_token()}}">
                                    <div class="row">
                                    	<div class="col-xl-2 col-lg-3 col-md-12 col-12">
                                    		<div class="sidebar-left site-setting">
                                    			<div id="accordionWrap5" role="tablist" aria-multiselectable="true">
                                    				<div class="card collapse-icon accordion-icon-rotate">
                                    					<div id="heading51" class="card-header">
                                                            <a data-toggle="collapse" href="#accordion51" aria-expanded="true" aria-controls="accordion51" class="card-title lead">State</a>
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
													<div class="row">
                                                    	<div class="col-xl-8">
                                                    		<div class="row">
                                                    			<div class="form-body">
                                                    				<div class="form-group row mx-auto">
																		<label class="col-md-3 ">Country </label>
																		<div class="col-md-9 label-control">
																			{{(isset($data[0]['country_name'])?$data[0]['country_name']:'---')}}
																		</div>
																	</div>
																	<div class="form-group row mx-auto">
																		<label class="col-md-3">State Name </label>
																		<div class="col-md-9 label-control">
																			{{(isset($data[0]['state_name'])?$data[0]['state_name']:'---')}}
																		</div>
																	</div>
																	<div class="form-group row mx-auto">
																		<label class="col-md-3">State Code </label>
																		<div class="col-md-9 label-control">
																			{{(isset($data[0]['state_code'])?$data[0]['state_code']:'---')}}
																		</div>
																	</div>
																	<div class="form-group row mx-auto">
																		<label class="col-md-3">Display Order</label>
																		<div class="col-md-9 label-control">
																			{{(isset($data[0]['display_order'])?$data[0]['display_order']:'---')}}
																		</div>
																	</div>
																	<div class="form-group row mx-auto">
																		<label class="col-md-3">Status</label>
																		<div class="col-md-9 label-control">
										                                    <?php echo $status_btn; ?>
																		</div>
																	</div>
                                                    			</div>
                                                    		</div>
                                                    	</div>
														<div class="col-xl-4 col-12">
															<div class="form-group row mx-auto">
																	@php $created_at = (isset($data[0]['created_at']))? date_getFormattedDateTime($data[0]['created_at']): '---';
																		$updated_at = (isset($data[0]['updated_at']))? date_getFormattedDateTime($data[0]['updated_at']): '---';
																	@endphp
																	<table class="table table-bordered">
																		<tr>
																			<td><label class="label-view-control">Created </label></td>
																			<td class="table-view-control">{{$created_at}}</td>
																		</tr>
																		<tr>
																			<td><label class="label-view-control">Updated </label></td>
																			<td class="table-view-control">{{$updated_at}}</td>
																		</tr>
																	</table>
															</div>

	                                                    </div>
                                                    </div>
                                    			</div>
                                    			
                                                
                                                <div class="form-actions text-right"><a href="{{route('state/grid')}}">
				                                    <button type="button" class="btn mr-1">
				                                        <i class="material-icons">chevron_left</i>Back
				                                    </button></a>
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
