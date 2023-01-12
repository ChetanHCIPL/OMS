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
<link href="{{ asset('/assets/pages/style.css')}}" rel="stylesheet" type="text/css" />
<!--End Bootstrap Switch-->
<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
<div class="content-wrapper">
<div class="content-body">
<section class="content-header clearfix">
   <h3>Transporter <strong><span class="text-muted accent-3">{{((isset($data[0]['name'])?' - '.reduceTitleName($data[0]['name']):''))}}</span></strong></h3>
   <ol class="breadcrumb">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li><a href="javascript:void(0);">Master Mgmt</a></li>
      <li><a href="{{route('transporter/grid')}}">Transporter</a></li>
      <li><a href="javascript:void(0);">{{(isset($mode)?$mode:'') }} Transporter</a></li>
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
                     <a href="{{route('transporter/grid')}}">
                     <button type="button" class="btn mr-1 back_btn">
                     <span class="material-icons">arrow_back_ios</span> Back  </button>
                     </a>
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
                     <input type="hidden" id="id" name="id" value="{{(isset($id)?$id:'')}}" />
                     <div class="row">
                        <div class="col-xl-2 col-lg-3 col-md-12 col-12">
                           <div class="sidebar-left site-setting">
                              <div id="accordionWrap5" role="tablist" aria-multiselectable="true">
                                 <div class="card collapse-icon accordion-icon-rotate">
                                    <div id="heading51" class="card-header">
                                       <a data-toggle="collapse" href="#accordion51" aria-expanded="true" aria-controls="accordion51" class="card-title lead">Transporter</a>
                                    </div>
                                    <div id="accordion51" role="tabpanel" data-parent="#accordionWrap5" aria-labelledby="heading51" class="card-collapse collapse show" aria-expanded="true">
                                       <div class="card-body">
                                          <ul class="nav nav-tabs m-0">
                                             <li class="nav-item">
                                                <a class="nav-link active" id="base-tab_1" data-toggle="tab" aria-controls="tab_1" href="#tab_1" aria-expanded="true">
                                                General Information </a>
                                             </li>
                                            <!--  <li class="nav-item">
                                                <a class="nav-link" id="base-tab_2" data-toggle="tab" aria-controls="tab_2" href="#tab_2" aria-expanded="true">
                                                Transporter Route Mapping </a>
                                             </li> -->
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
                                 <div class="row ">
                                    <div class="col-xl-8">
                                       <div class="row form-box">
                                          <div class="form-body">
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control">Name <span class="required">*</span></label>
                                                <div class="col-md-9">
                                                   <input class="form-control" placeholder="Name" type="text" id="name" name="name" value="{{(isset($data[0]['name'])?$data[0]['name']:'')}}">
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
                              <div class="tab-pane" aria-expanded="false" role="tabpanel" aria-expanded="false"  id="tab_2"  aria-labelledby="base-tab_2">
                                 <div class="row">
                                    <div class="col-xl-12">
                                       <h3 class="tab-content-title">Transporter Route Mapping</h3>
                                    </div>
                                 </div>
                                 <div class="row">
                                    <div class="col-xl-8">
                                       <div class="row">
                                          <div class="form-body form-box">
                                             <div class="table-responsive">
                                                <table class="table table-bordered mb-0" id="transport" data-num="1">
                                                   <thead>
                                                      <tr>
                                                         <th>#</th>
                                                         <th>State</th>
                                                         <th>Zone</th>
                                                         <th>District</th>
                                                         <th>Taluka</th>
                                                         <th>Area</th>
                                                         <th>Action</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody class="dataajax">
                                                   </tbody>
                                                   <tbody id="optn_details" class="repeater d-none">
                                                      <tr>
                                                         <td class="text-center">1</td>
                                                         <td>
                                                         <!-- selectize-select -->
                                                            <select class="form-control" id="state" name="Medium2[]" placeholder="Select Medium" data-placeholder="Select Medium" >
                                                               <option value="">Select State </option>
                                                               <option value="1"> Gujarat </option>
                                                               <option value="2"> Maharashtra </option>
                                                               <option value="3"> Rajesthan </option>
                                                               <option value="4"> Uttrakhand </option>
                                                            </select>
                                                         </td>
                                                         <td>
                                                            <select class="form-control" id="zone" name="segment1[]" placeholder="Select Segment" data-placeholder="Select Segment">
                                                               <option value="">Select Zone </option>
                                                               <option value="c">Center </option>
                                                               <option value="e">East zone </option>
                                                               <option value="w">West zone </option>
                                                               <option value="n">North zone </option>
                                                               <option value="s">South zone </option>
                                                            </select>
                                                         </td>
                                                         <td>
                                                            <select class="form-control" id="district" name="semester2[]" placeholder="Select Semester" data-placeholder="Select Semester" >
                                                               <option value="">Select District</option>
                                                               <option value="ah"> Ahmedabad</option>
                                                               <option value="ra"> Rajkot</option>
                                                               <option value="ba"> Banaskatha</option>
                                                               <option value="mu"> Mumbai</option>
                                                               <option value="jo"> Jodhpur</option>
                                                               <option value="aj"> Ajmer</option>
                                                               <option value="pu"> Pune</option>
                                                               <option value="de"> Dehradun</option>
                                                            </select>
                                                         </td>
                                                         <td>
                                                            <select class="form-control" id="taluka" name="semester2[]" placeholder="Select Semester" data-placeholder="Select Semester" >
                                                               <option value="">Select Semester</option>
                                                               <option value="">Vastral</option>
                                                               <option value="">Sanand</option>
                                                               <option value="">Nadiad</option>
                                                               <option value="">Anand</option>
                                                               <option value="">Dakor</option>
                                                               <option value="">Dahanu</option>
                                                               <option value="">Valsad</option>
                                                            </select>
                                                         </td>
                                                         <td>
                                                            <select class="form-control" id="area" name="semester2[]" placeholder="Select Semester" data-placeholder="Select Semester" >
                                                               <option value="">Select Area</option>
                                                               <option value="k">Kandiwali</option>
                                                               <option value="b">Boriwali</option>
                                                               <option value="a">Andheri</option>
                                                               <option value="V">Vastrapur</option>
                                                               <option value="bo">Bopal</option>
                                                               <option value="g">Ghuma</option>
                                                               <option value="is">Iscon</option>
                                                               <option value="is">Maninagar</option>
                                                               <option value="ba">Bapunagar</option>
                                                            </select>
                                                         </td>
                                                         <td>  
                                                         <a href="javascript:void(0);" class="btn btn-danger deletesegme"><i class="ft-trash-2"></i></a>
                                                         </td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </div>
                                             <button type="button" class="btn btn-primary waves-effect waves-light addplusclick pull-right" id="add-question">
                                                <i class="ft-plus"></i> Add more
                                             </button>
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
                                 <a href="{{route('transporter/grid')}}">
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

<script type="text/javascript" src="{{ asset('/assets/js/scripts/tooltip/tooltip.js')}}"></script>

<script src="{{ asset('/assets/pages/scripts/admin/master/transporter_add.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection