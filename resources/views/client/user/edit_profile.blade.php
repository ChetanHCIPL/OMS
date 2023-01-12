@extends('layouts.admin')
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('/assets/vendors/css/fancybox/dist/jquery.fancybox.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('/assets/vendors/css/fancybox/dist/jquery.fancybox.min.css')}}" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
@php
$valid_ext = implode(", ", Config::get('constants.image_ext_array'));
@endphp
<div class="content-wrapper">
    <div class="content-body">
		<section class="content-header clearfix">
			<h3>Edit Profile</h3>
		</section>
        <section class="horizontal-grid" id="horizontal-grid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-content collapse show">
                            <div class="card-body language-input">
                                <form class="form form-horizontal" id="frmadd" name="frmadd" enctype="multipart/form-data" action="">
                                    <input type="hidden" id="token" name="_token" value="{{ csrf_token() }}" />
                                    <input type="hidden" id="id" name="id" value="{{ (isset($id) ? $id : "") }}" />
                                    <input type="hidden" name="image_old" id="image_old" value="{{ ((isset($data[0]['image']) && $data[0]['image'] != "") ? $data[0]['image'] : "")}}" />
                                    <input type="hidden" name="customActionType" value="group_action" />
                                    <input type="hidden" name="groupActionName" value="Update" />
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
                                                            <a data-toggle="collapse" href="#accordion51" aria-expanded="true" aria-controls="accordion51" class="card-title lead">Edit Profile</a>
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
                                    			<div class="tab-pane active" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="tab_1" aria-labelledby="base-tab_1">
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
																		<label class="col-md-3 label-control">Name <span class="required">*</span></label>
																		<div class="col-md-9">
																			<input class="form-control" placeholder="Name" type="text" id="client_name" name="client_name" value="{{ (isset($data[0]['client_name']) ? $data[0]['client_name'] : old('client_name'))}}">
																		</div>
																	</div>
																	<div class="form-group row mx-auto">
																		<label class="col-md-3 label-control">Email <span class="required">*</span></label>
																		<div class="col-md-9 position-relative has-icon-left">
																		  <input type="email" class="form-control" name="email" id="email" value="{{ (isset($data[0]['email']) ? $data[0]['email'] : old('email'))}}" placeholder="Email" />
																		  <div class="form-control-position">
																			<i class="material-icons info font-medium-4">email</i>
																		  </div>
																		</div>
																	</div>
																	<div class="form-group row mx-auto">
																		<label class="col-md-3 label-control">Mobile <span class="required">*</span></label>
																		<div class="col-md-9 position-relative has-icon-left">
																		  <input class="form-control" placeholder="Mobile" type="text" id="mobile_number" name="mobile_number" value="{{ (isset($data[0]['mobile_number']) ? $data[0]['mobile_number'] : old('mobile_number'))}}" maxlength="10">
																		  <div class="form-control-position">
																			<i class="material-icons info font-medium-4">phone_iphone</i>
																		  </div>
																		</div>
																	</div>
																	<div class="form-group row mx-auto">
																		<label class="col-md-3 label-control">Image</label>
																		<div class="col-md-4 custom-file">
																		  <input type="file" class="custom-file-input form-control" id="image" name="image">
																		  <label class="custom-file-label" for="image" aria-describedby="imageAddon">Choose Image</label>
																		</div>
																			@if(isset($checkImgArr['img_url']) && $checkImgArr['img_url'] != '')
																				  
																				  <div class="col-md-3">
																					 <a class="fancybox" rel="gallery1" href="{{$checkImgArr['fancy_box_url']}}" title=""><img src="{{(isset($checkImgArr['img_url']) && $checkImgArr['img_url'] != '')?$checkImgArr['img_url']:''}}" alt="" class="img-fluid rounded-circle width-50" id="show-image" onerror="isImageExist(this)" noimage="50x50.jpg" /></a> 
																					  <a href="javascript:void(0);" class="btn btn-icon ml-1 btn-danger waves-effect waves-light" onclick='deleteUploadedImage();' id="delete-image"><i class="icon-close"></i></a>
																				</div> 
																			@endif
																	 </div>
																	 <div class="form-group row mx-auto">
																		<label class="col-md-3 label-control">User Name  </label>
																		<div class="col-md-9 label-control">
																			{{ (isset($data[0]['username']) ? $data[0]['username'] : old('username'))}}
																			<input class="form-control" placeholder="User Name" type="hidden" id="username" name="username" value="{{ (isset($data[0]['username']) ? $data[0]['username'] : old('username')) }}" readonly="">
																		</div>
																	 </div>
																	 <div class="form-group row mx-auto">
																		<label class="col-md-3 label-control">Check for password change</label>
																	   <div class="col-md-9 label-control">
																		  <div class="custom-control custom-checkbox">
																			  <input type="checkbox" value="0" name="changePasswordChk" class="custom-control-input" id="changePasswordChk">
																			  <label class="custom-control-label" for="changePasswordChk"></label>
																		  </div>
																	  </div>   
																	</div>
																	<div class="d-none" id="changePassDiv">
																	<div class="form-group row mx-auto">
																		<label class="col-md-3 label-control">Current Password <span class="required">*</span></label>
																		<div class="col-md-9 position-relative has-icon-right">
																			<input name="current_password" type="password" class="form-control" id="current_password" placeholder="Current Password" value=""  autocomplete="new-password" >
                                                                            <a href="javascript:void(0)" title="show password" class="form-control-position" onclick="showhidePassword(this);"><span class="btn btn-icon btn-secondary btn-light  waves-effect waves-light"><i class="la la-eye"></i></span></a>
																		</div>
																	</div>
																	<div class="form-group row mx-auto">
																		<label class="col-md-3 label-control">New Password <span class="required">*</span></label>
																		<div class="col-md-9 position-relative has-icon-right">
																			<input name="password" type="password" class="form-control" id="new_password" placeholder="New Password" value=""  autocomplete="new-password" >
                                                                            <a href="javascript:void(0)" title="show password" class="form-control-position" onclick="showhidePassword(this);"><span class="btn btn-icon btn-secondary btn-light  waves-effect waves-light"><i class="la la-eye"></i></span></a>
																		</div>
																	</div>
																	<div class="form-group row mx-auto">
																		<label class="col-md-3 label-control">Confirm Password <span class="required">*</span></label>
																		<div class="col-md-9 position-relative has-icon-right">
																			<input name="password_confirmation" type="password" class="form-control" id="password_confirmation" placeholder="Confirm Password" value=""  autocomplete="new-password" >
																			<a class="form-control-position" href="javascript:void(0)" title="show password" onclick="showhidePassword(this);"><span class="btn btn-icon btn-secondary btn-light  waves-effect waves-light"><i class="la la-eye"></i></span></a>
																		</div>
																	</div>
																	</div>
																</div>
                                                    		</div>
                                                    	</div>
                                                    </div>
                                    			</div>
                                                <div class="form-actions text-right">
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
	var PASSWORD_MIN    = "{{config('constants.PASSWORD_MIN')}}";
    var PASSWORD_MAX    = "{{config('constants.PASSWORD_MAX')}}";
    var PASSWORD_FORMAT = {{config('constants.PASSWORD_FORMAT')}};
    
    var panel_text = '<?php echo Config::get("constants.CLIENT_PANEL_PREFIX"); ?>'; 
</script>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- BEGIN FORM VALIDATION -->
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/additional-methods.min.js')}}" type="text/javascript"></script>
<!-- END FORM VALIDATION -->
<script type="text/javascript" src="{{ asset('/assets/vendors/js/fancybox/dist/jquery.fancybox.js')}}"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('/assets/pages/scripts/client/user/edit_profile.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection