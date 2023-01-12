@extends('layouts.admin')
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS --> 
<!--Start Selectize-->
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.default.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/plugins/forms/selectize/selectize.css')}}" rel="stylesheet" type="text/css" />
<!--End Selectize-->
<!--Start Bootstrap Switch-->
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/switchery.min.css')}}">
<link href="{{ asset('assets/css/core/colors/palette-switch.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/fancybox/dist/jquery.fancybox.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('/assets/vendors/css/fancybox/dist/jquery.fancybox.min.css')}}" rel="stylesheet" type="text/css"/>
<!--End Bootstrap Switch-->
<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
@php
  $country_path = Config::get('path.country_path'); 
  $image = isset($data[0]['flag'])?$data[0]['flag']:"";
@endphp
<div class="content-wrapper">
  <div class="content-body">

	<section class="content-header clearfix">
		<h3>Country <strong><span class="text-muted accent-3">{{((isset($data[0]['country_name'])?' - '.reduceTitleName($data[0]['country_name']):''))}}</span></strong></h3>
		<ol class="breadcrumb">
			<li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
			<li><a href="javascript:void(0);">Master Mgmt</a></li>
			<li><a href="{{route('country/grid')}}">Country</a></li>
			<li><a href="javascript:void(0);">{{(isset($mode)?$mode:'') }} Country</a></li>
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
                    <a href="{{route('country/grid')}}">
                        <button type="button" class="btn mr-1 back_btn">
                        <span class="material-icons">arrow_back_ios</span> Back  </button>
                    </a>
                    @if (per_hasModuleAccess('Country', 'View'))
                    <a href="{{route('country',['mode' => 'view', 'id' => isset($data[0]['id'])?gen_generate_encoded_str($data[0]['id'], '3', '3', ''):''])}}" title="View"><span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-eye"></i></span></a>@endif
                  </div>
                </div>
              </div>
            @endif
            <div class="card-content collapse show">
              <div class="card-body language-input">
                <form class="form form-horizontal" id="frmadd" name="frmadd" enctype="multipart/form-data" action="">
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
				  </div>
                  <input type="hidden" name="_token" value="{{csrf_token()}}" />
                  <input type="hidden" name="customActionType" value="group_action" />
                  <input type="hidden" id="customActionName" name="customActionName" value="{{(isset($mode)?$mode:'') }}" />
                  <input type="hidden" name="flag_old" id="flag_old" value="{{(isset($data[0]['flag'])?$data[0]['flag']:'')}}">
                  <input type="hidden" id="id" name="id" value="{{(isset($id)?$id:'')}}" />
				  
                  <div class="row">

                    <div class="col-xl-2 col-lg-3 col-md-12 col-12">
                      <div class="sidebar-left site-setting">
                        <div id="accordionWrap5" role="tablist" aria-multiselectable="true">
                          <div class="card collapse-icon accordion-icon-rotate">
                            <div id="heading51" class="card-header">
                              <a data-toggle="collapse" href="#accordion51" aria-expanded="true" aria-controls="accordion51" class="card-title lead">Country</a>
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
                                    <label class="col-md-3 label-control">Country Name <span class="required">*</span></label>
                                    <div class="col-md-9">
                                      <input class="form-control" placeholder="Country Name" type="text" id="country_name" name="country_name" value="{{(isset($data[0]['country_name'])?$data[0]['country_name']:'')}}">
                                    </div>
                                  </div>
                                  <div class="form-group row mx-auto">
                                    <label class="col-md-3 label-control">Country Code <span class="required">*</span></label>
                                    <div class="col-md-9">
                                      <input class="form-control" placeholder="Country Code" type="text" id="country_code" name="country_code" value="{{(isset($data[0]['country_code'])?$data[0]['country_code']:'')}}">
                                    </div>
                                  </div>
                                  <div class="form-group row mx-auto">
                                    <label class="col-md-3 label-control">ISD Code <span class="required">*</span></label>
                                    <div class="col-md-9">
                                      <input class="form-control" placeholder="ISD Code" type="text" id="isd_code" name="isd_code" value="{{(isset($data[0]['isd_code'])?$data[0]['isd_code']:'')}}">
                                    </div>
                                  </div>
                                  <div class="form-group row mx-auto">
                                    <label class="col-md-3 label-control">Display Order</label>
                                    <div class="col-md-9">
                                      <input class="form-control" placeholder="Display Order" type="text" id="display_order" name="display_order" value="{{(isset($data[0]['display_order'])?$data[0]['display_order']:'')}}">
                                    </div>
                                  </div>

                                  <div class="form-group row mx-auto">
                                    <label class="col-md-3 label-control">Flag</label>
                                    <div class="col-md-4 custom-file">
                                      <input type="file" class="custom-file-input form-control" id="flag" name="flag">
                                      <label class="custom-file-label" for="flag" aria-describedby="imageAddon">Choose flag</label>
                                    </div>
                                        @if(isset($mode) && $mode == 'Update')
                                          @if(isset($checkImgArr['img_url']) && $checkImgArr['img_url'] != '' && $image != "")
                                              <div class="col-md-3">
                                                  <a class="fancybox" rel="gallery1" href="{{$checkImgArr['fancy_box_url']}}" title=""><img src="{{$checkImgArr['img_url']}}" alt="" class="img-fluid rounded-circle width-50" id="show-image" onerror="isImageExist(this)" noimage="80x80.jpg" /></a>
                                                  <a href="javascript:void(0);" class="btn btn-icon ml-1 btn-danger waves-effect waves-light" onclick='deleteUploadedImage();' id="delete-image"><i class="icon-close"></i></a>
                                                </div>
                                            @endif
                                      @endif
                                  </div>
                                   <div class="form-group row mx-auto">
                                    <label class="col-md-3 label-control"> </label>
                                      <div class="col-md-9">
                                            <p class="danger">[ Valid extentions: <code>{{!empty($img_ext_array)?$img_ext_array:''}}</code>]</p>
                                      </div>
                                  </div> 
                                  <div class="form-group row mx-auto">
                                    <label class="col-md-3 label-control">Status</label>
                                    <div class="col-md-9">
                                      <input type="checkbox" class="switchBootstrap" id="status" name="status" data-on-text="Active" data-off-text="Inactive" data-on-color="{{Config::get('constants.switch_on_color')}}" data-off-color="{{Config::get('constants.switch_off_color')}}" value="1"
                                       {{((isset($data[0]['status']) && $data[0]['status'] == 1 )?'checked':($mode == 'Add')?'checked':'')}}/>
                                    </div>
                                  </div>

                                </div>
                              </div>
                            </div>

								<div class="col-xl-4 col-12">
								  @if(isset($mode) && $mode == 'Update')
                                   @php  $created_at = (isset($data[0]['created_at'])) ? date_getFormattedDateTime($data[0]['created_at']): '---'; @endphp
                                    
                                   @php $updated_at = (isset($data[0]['updated_at']))? date_getFormattedDateTime($data[0]['updated_at']): '---';
                                   @endphp
                                  <div class="form-group row mx-auto">
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
                                  @endif
	                          </div>

                          </div>
                        </div>

                        <div class="form-actions text-right">
                          <a href="{{route('country/grid')}}">
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
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- Start FORM VALIDATION -->
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/additional-methods.min.js')}}" type="text/javascript"></script>
<!-- End FORM VALIDATION -->
<!-- Start Selectize -->
<script src="{{ asset('/assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
<!-- End Selectize -->
<!--Start Bootstrap Switch--->
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/toggle/bootstrap-switch.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/toggle/switchery.min.js') }}"></script>
<!--End Bootstrap Switch--->
<script src="{{ asset('/assets/js/scripts/forms/custom-file-input.js') }}"></script>
<!-- END PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="{{ asset('/assets/vendors/js/fancybox/dist/jquery.fancybox.js')}}"></script>
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('/assets/pages/scripts/admin/master/country_add.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection