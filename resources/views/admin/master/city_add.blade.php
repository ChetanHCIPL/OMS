@extends('layouts.admin')
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!--Start Selectize-->
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.default.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/plugins/forms/selectize/selectize.css')}}" rel="stylesheet" type="text/css" />
<!--End Selectize-->
<!-- Start Bootstrap Switch -->
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/switchery.min.css')}}">
<link href="{{ asset('assets/css/core/colors/palette-switch.css')}}" rel="stylesheet" type="text/css" />
<!-- End Bootstrap Switch -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
@php
$temp_country_code = (isset($data[0]['country_code'])?$data[0]['country_code']:'');
@endphp
<div class="content-wrapper">
    <div class="content-body">
		<section class="content-header clearfix">

			<h3>{{-- City --}}District<strong><span class="text-muted accent-3">{{(isset($data[0]['city_name'])?' - '.$data[0]['city_name']:'')}} </span></strong></h3>

 
			<ol class="breadcrumb">
				<li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
				<li><a href="javascript:void(0);">Master Mgmt</a></li>
				<li><a href="{{route('city/grid')}}">{{-- City --}}District</a></li>
				<li><a href="javascript:void(0);">{{(isset($mode)?$mode:'') }} {{-- City --}}District</a></li>
			</ol>
		</section>
        <section class="horizontal-grid city-edit-page" id="horizontal-grid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                    	@if(isset($mode) && $mode == "Update")
                    	<div class="card-head ">
			                <div class="card-header">
								<div class="float-right">
										<a href="{{route('city/grid')}}">
						                  <button type="button" class="btn mr-1 back_btn">
						                  <span class="material-icons">arrow_back_ios</span> Back  </button>
						                </a>
									 @if(per_hasModuleAccess('Cities','View'))
										<a href="{{route('city',['mode' => 'view', 'id' => isset($data[0]['id'])?base64_encode($data[0]['id']):''])}}" title="View"><span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-eye"></i></span></a>
									@endif
									<?php  /*
									 if(per_hasModuleAccess('Cities', 'Delete')): ?>
										<a onclick="deleteSingleRecord({{isset($data[0]['id'])?$data[0]['id']:''}},'city');" title="Delete "><span class="btn btn-icon btn-danger waves-effect waves-light"><i class="la la-trash"></i></span></a>
									<?php endif;*/ ?>
										
								</div>
							</div>
						</div>
						@endif
                        <div class="card-content collapse show">
                            <div class="card-body language-input">
                                <form class="form form-horizontal" id="frmadd" name="frmadd" enctype="multipart/form-data" action="">
                                    <input type="hidden" name="_token" value="{{csrf_token()}}" />
                                    <input type="hidden" name="customActionType" value="group_action" />
                                    <input type="hidden" id="customActionName" name="customActionName" value="{{(isset($mode)?$mode:'') }}" />
                                    <input type="hidden" name="flag_old" id="flag_old" value="{{(isset($data[0]['flag'])?$data[0]['flag']:'')}}">
                                    <input type="hidden" id="id" name="id" value="{{(isset($id)?$id:'')}}" />

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
                                                            <a data-toggle="collapse" href="#accordion51" aria-expanded="true" aria-controls="accordion51" class="card-title lead">{{-- City --}}District</a>
                                                        </div>
                                                        <div id="accordion51" role="tabpanel" data-parent="#accordionWrap5" aria-labelledby="heading51" class="card-collapse collapse show" aria-expanded="true">
                                                            <div class="card-body">
                                                                <ul class="nav nav-tabs m-0">
                                                                    <li class="nav-item">
                                                                        <a class="nav-link active" id="base-tab_1" data-toggle="tab" aria-controls="tab_1" href="#tab_1" aria-expanded="true">
                                                                        General Information </a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="base-tab_2" data-toggle="tab" aria-controls="tab_2" href="#tab_2" aria-expanded="False">Language</a>
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
																		<label class="col-md-3 label-control">{{-- City --}}District Name <span class="required">*</span></label>
																		<div class="col-md-9">
																			<input class="form-control" placeholder="{{-- City --}}District Name" type="text" id="city_name" name="city_name" value="{{(isset($data[0]['city_name'])?$data[0]['city_name']:'')}}">
																		</div>
																	</div>
																	<div class="form-group row mx-auto">
																		<label class="col-md-3 label-control">{{-- City --}}District Code <span class="required">*</span></label>
																		<div class="col-md-9">
																			<input class="form-control" placeholder="{{-- City --}}District Code" type="text" id="city_code" name="city_code" value="{{(isset($data[0]['city_code'])?$data[0]['city_code']:'')}}">
																		</div>
																	</div>
																	<div class="form-group row mx-auto">
																		<label class="col-md-3 label-control">Country <span class="required">*</span></label>
																		<div class="col-md-9">
																			<select class="selectize-select" id="country_code" name="country_code" placeholder="Select Country">
																			  	<option value="">Select Country</option>
																			  	@if(!empty($country))
																				@php $cnt_cou = count($country);
																				@endphp
																					@for($i=0;$i<$cnt_cou;$i++)
																						@if(isset($data[0]['country_code']) && $data[0]['country_code'] == $country[$i]['country_code'])
																							@php $selected = "selected"; @endphp
																						 @else
																							@php $selected = ""; @endphp
																						 @endif
																						<option value="{{$country[$i]['country_code']}}" {{$selected}}>{{$country[$i]['country_name']}}</option>
																					@endfor
																				@endif
																			</select>
																		</div>
																	</div>
																	<div class="form-group row mx-auto">
																		<label class="col-md-3 label-control">State <span class="required">*</span></label>
																		<div class="col-md-9">
																			@php $selectedState = isset($data[0]['state_code']) ? $data[0]['state_code'] : ''; @endphp
																			<input type="hidden" id="selected_state_code" value="<?php echo $selectedState ?>">
																			<select class="selectize-select" id="state_code" name="state_code" placeholder="Select State">
																			@if($mode == 'Update')
                                                                               @if(!empty($stateData))
                                                                                    @foreach($stateData as $sArry)
                                                                                        <option value="{{ $sArry->state_code }}" {{ $sArry->state_code == $data[0]['state_code'] ? 'selected' : '' }}>{{ $sArry->state_name }}</option>
                                                                                    @endforeach
                                                                                @endif 
                                                                            @endif
																			</select>
																		</div>
																	</div>
																	<div class="form-group row mx-auto">
																		<label class="col-md-3 label-control">Display Order</label>
																		<div class="col-md-9">
																			<input class="form-control" placeholder="Display Order" type="text" id="display_order" name="display_order" value="{{(isset($data[0]['display_order'])?$data[0]['display_order']:'')}}">
																		</div>
																	</div>
																	<div class="form-group row mx-auto">
																		<label class="col-md-3 label-control">Status</label>
																		<div class="col-md-9">
																			
																			@if($mode == 'Add')
																				@php $checked = 'checked'; @endphp
																			@else
																				@php $checked = ((isset($data[0]['status']) && $data[0]['status'] == $currentClass->_cityModel::ACTIVE )?'checked':''); @endphp
																			@endif
																			<input type="checkbox" class="switchBootstrap" id="status" name="status" data-on-text="Active" data-off-text="Inactive" data-on-color="{{Config::get('constants.switch_on_color')}}" data-off-color="{{Config::get('constants.switch_off_color')}}" value="1" {{$checked}}/>
																		</div>
																	</div>

										
																</div>
															</div>
                                                    	</div>
																<div class="col-xl-4 col-12">
																@if(isset($mode) && $mode == 'Update')
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
																						<td><label class="label-view-control">Updated </strong></td>
																						<td class="table-view-control">{{$updated_at}}</td>
																					</tr>
																				</table>
																			</div>
																	@endif
																	</div>

                                                    </div>
                                                </div>



                                                <div class="tab-pane" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="tab_2"  aria-labelledby="base-tab_2">
                                                   <div class="row">
														<div class="col-xl-12">
															<h3 class="tab-content-title">Language</h3>
														</div>
													  </div>


													<div class="row">
                                                        <div class="col-xl-8">
                                                            <div class="row">
                                                            	<div class="form-body">
                                                            		@if(!empty($language))
						                                              	@php $cnt = count($language);
						                                              	@endphp
						                                              	 @for($i = 0; $i < $cnt; $i++)
							                                                @php $direction  = ($language[$i]['direction'] == "2")?"rtl":"ltr"; @endphp
																			<div class="row">
																				<div class="col-md-12">
																					<div class="form-group row mx-auto">
																						<label class="col-md-6 control-label">{{-- City --}}District Name</label>
																						<label class="col-md-6 control-label text-right">
																							<img class="flag" id="show-image" src="{{$language[$i]['Image']}}"  onerror="isImageExist(this)" noimage="80x80.jpg" alt=""/>&nbsp;&nbsp;{{$language[$i]['lang_name']}}
																						</label>
																						<div class="col-md-12">
																							<input type="hidden" name="lang_code[]" value="{{$language[$i]['lang_code']}}">
																							<input type="text" class="form-control" name="coulan_name[]" placeholder="{{-- City --}}District Name" dir="{{$direction}}" value="{{(isset($city_language[$language[$i]['lang_code']])?$city_language[$language[$i]['lang_code']]:"")}}">
																						</div>
																				   	</div>
																				</div>
																			</div>
																		@endfor
						                                            @endif 
                                                            	</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-actions text-right"><a href="{{route('city/grid')}}">
				                                    <button type="button" class="btn mr-1">
				                                        <i class="ft-x"></i> Cancel
				                                    </button></a>
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
<button type="button" class="btn btn-outline-danger block btn-lg" data-toggle="modal" data-target="#del_image" style="display: none;" id="delete-image-box">Launch Modal</button>
<div class="modal fade text-left show" id="del_image" tabindex="-1" role="dialog" aria-labelledby="myModalLabel10" aria-modal="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="alert alert-danger" role="alert">
                   <i class="la la-question"></i> Are you sure want to delete image?
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal"  id="delete-image-box_btn_close">Close</button>
                <button type="button" class="btn btn-outline-danger" onclick="deleteUploadedImage()">Ok</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript"> 
    var statelist = "{{ route('admin/master/statelist') }}";
    var temp_country_code = '<?php echo $temp_country_code?>';
    var temp_state_code = '';
</script> 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- Start FORM VALIDATION -->
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/additional-methods.min.js')}}" type="text/javascript"></script>
<!-- End FORM VALIDATION -->
<!-- Start Selectize -->
<script src="{{ asset('/assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
<!-- Start Bootstrap Switch -->
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/toggle/bootstrap-switch.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/toggle/switchery.min.js') }}"></script>
<!-- End Bootstrap Switch --> 
<!-- End Selectize -->
<!-- <script src="{{ asset('/assets/js/scripts/forms/custom-file-input.js') }}"></script> -->
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('/assets/pages/scripts/admin/master/city_add.js')}}" type="text/javascript"></script>
<!-- <script src="{{ asset('assets/pages/scripts/admin/common/<!-- select2 -->.full.min.js') }}"></script> -->
<!-- END PAGE LEVEL SCRIPTS -->
@endsection