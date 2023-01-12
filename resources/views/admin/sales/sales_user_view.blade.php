@extends('layouts.admin')
@section('styles')
@php 
$status = (isset($userData[0]['status']) && $userData[0]['status'] == 1) ? "Active" : "Inactive";
$status_color = Config::get('constants.status_color.' . $status);
$status_btn = \App\GlobalClass\Design::blade('status',$status,$status_color);
@endphp
<!--Start Bootstrap Switch-->
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/switchery.min.css')}}">
<link href="{{ asset('assets/css/core/colors/palette-switch.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.default.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/plugins/forms/selectize/selectize.css')}}" rel="stylesheet" type="text/css" />
<!--End Bootstrap Switch-->
<link href="{{ asset('css/admin/fancybox/dist/jquery.fancybox.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
@php
$temp_country_code = (isset($userData[0]['country_code'])?$userData[0]['country_code']:'');
@endphp
<div class="content-wrapper">
   <div class="content-body">
      <section class="content-header clearfix">
         <h3>Sales User<strong><span class="text-muted accent-3">{{(isset($userData[0]['first_name'])?' - '.$userData[0]['first_name']:'')}}{{(isset($userData[0]['last_name'])?' '.$userData[0]['last_name']:'')}}</span></strong></h3>
         <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="javascript:void(0);">Users Mgmt</a></li>
            <li><a href="{{ route('admin.user') }}">User</a></li>
            <li class="active">{{(isset($mode)?$mode:'') }} Sales User</li>
         </ol>
      </section>
      <section class="horizontal-grid full-page-content-section" id="horizontal-grid">
         <div class="row">
            <div class="col-12">
               <div class="card">
                  <div class="card-head">
                     <div class="card-header">
                        <div class="float-right">
                           @if (per_hasModuleAccess('SalesUsers', 'Edit')) 
                           <a href="{{ route('salesuser',['mode'=>'edit','id'=>isset($userData[0]['id'])?base64_encode($userData[0]['id']):'']) }}" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light"><i class="la la-edit"></i></span></a> 
                           @endif
                           {{--@if (per_hasModuleAccess('SalesUsers', 'Delete')) 
                           <a onclick="deleteSingleRecord({{isset($userData[0]['id'])?$userData[0]['id']:''}},'user');" title="Delete"><span class="btn btn-icon btn-danger waves-effect waves-light"><i class="la la-trash"></i></span></a>
                           @endif--}}
                        </div>
                     </div>
                  </div>
                  <div class="card-content collapse show">
                     <div class="card-body language-input">
                        <form class="form form-horizontal"   method="post"  enctype="multipart/form-data"  id="useraddForm" >
                           <input type="hidden" name="mode"  id="mode" value="{{ $mode }}">
                           <input type="hidden" name="id"  value="{{ isset($userData[0]['id']) ? $userData[0]['id'] : old('id') }}" > 
                           <div class="row">
                              <div class="col-xl-2 col-lg-3 col-md-12 col-12">
                                 <div class="sidebar-left site-setting">
                                    <div id="accordionWrap5" role="tablist" aria-multiselectable="true">
                                       <div class="card collapse-icon accordion-icon-rotate">
                                          <div id="heading51" class="card-header">
                                             <a data-toggle="collapse" href="#accordion51" aria-expanded="true" aria-controls="accordion51" class="card-title lead">Sales User</a>
                                          </div>
                                          <div id="accordion51" role="tabpanel" data-parent="#accordionWrap5" aria-labelledby="heading51" class="card-collapse collapse show" aria-expanded="true">
                                             <div class="card-body">
                                                <ul class="nav nav-tabs m-0">
                                                   <li class="nav-item">
                                                      <a class="nav-link active" id="base-tab_1" data-toggle="tab" aria-controls="tab_1" href="#tab_1" aria-expanded="true">
                                                      Personal Info</a>
                                                   </li>
                                                   <li class="nav-item">
                                                      <a class="nav-link" id="base-tab_2" data-toggle="tab" aria-controls="tab_2" href="#tab_2" aria-expanded="False">Address</a>
                                                   </li>
                                                   <li class="nav-item">
                                                      <a class="nav-link" id="base-tab_3" data-toggle="tab" aria-controls="tab_3" href="#tab_3" aria-expanded="False">Login Information</a>
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
                                             <h3 class="tab-content-title"><i class="ft-user"></i> Personal Info</h3>
                                          </div>
                                       </div>
                                       <div class="row">
                                          <div class="col-xl-8">
                                             <div class="row m-0">
                                                <div class="form-body">
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3" for="first_name">First Name</label>
                                                      <div class="col-md-9 label-control" >{{ isset($userData[0]['first_name']) ? $userData[0]['first_name'] : "--" }}
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 "  for="userinput2">Last Name</label>
                                                      <div class="col-md-9 label-control" >{{ isset($userData[0]['last_name']) ? $userData[0]['last_name'] : "--" }}
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3" >Email Address </label>
                                                      <div class="col-md-9 label-control" >{{ isset($userData[0]['email']) ? $userData[0]['email'] : "--" }}
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 "  for="userinput2">Image</label>
                                                      <div class="col-md-6 label-control">
                                                         @if(!empty($userImage))
                                                         <a class="fancybox" rel="gallery1" href="{{$userImage['fancy_box_url']}}" title=""><img src="{{(isset($userImage['img_url']) && $userImage['img_url'] != '')?$userImage['img_url']:''}}" alt="" class="img-fluid rounded-circle width-50" id="show-image" onerror="isImageExist(this)" noimage="80x80.jpg" /></a>
                                                         @endif
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 "  for="userinput2">Sales Structure </label>
                                                      <div class="col-md-9  label-control">
                                                         {{ isset($sales_structure) ? $sales_structure : old('sales_strucutre') }} 
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 "  for="userinput2">Aadhar No. </label>
                                                      <div class="col-md-9  label-control">
                                                         {{ isset($userData[0]['adhar_no']) ? $userData[0]['adhar_no'] : old('adhar_no') }} 
                                                      </div>
                                                   </div>
                                                   {{-- <div class="form-group row mx-auto">
                                                      <label class="col-md-3" for="userinput2">Designation </label>
                                                      <div class="col-md-9 label-control">
                                                         {{ isset($designation) ? $designation : old('designation') }} 
                                                      </div>
                                                   </div> --}}
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 "  for="userinput2">Mobile </label>
                                                      <div class="col-md-9  label-control">
                                                         {{ isset($userData[0]['mobile_isd'])?$userData[0]['mobile_isd']:""}}  {{ isset($userData[0]['mobile']) ? $userData[0]['mobile'] : old('mobile') }} 
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 "  for="userinput2">WhatsApp No. </label>
                                                      <div class="col-md-9  label-control">
                                                         {{ isset($userData[0]['whatsapp_number']) ? $userData[0]['whatsapp_number'] : old('whatsapp_number') }} 
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 "  for="userinput2">Remark </label>
                                                      <div class="col-md-9  label-control">
                                                         {{ isset($userData[0]['remark']) ? $userData[0]['remark'] : old('remark') }} 
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto d-none">
                                                      <label class="col-md-3 " for="acess_groupid">Access Group </label> 
                                                      <div class="col-md-9 label-control"> 
                                                         @if(isset($access_group_data) && !empty($access_group_data))
                                                         @foreach($access_group_data as $key => $value)
                                                         {{ $value['access_group'] }}
                                                         @endforeach                  
                                                         @endif
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="col-xl-4 col-12">
                                             <div class="form-group row mx-auto">
                                                <table class="table table-bordered table-striped">
                                                   <tbody>
                                                      <tr>
                                                         <td><label class="label-view-control">Total Login</label></td>
                                                         <td class="table-view-control">{{ isset($userData[0]['tot_login']) && $userData[0]['tot_login'] != ""  ? $userData[0]['tot_login'] : '0' }}</td>
                                                      </tr>
                                                      <tr>
                                                         <td><label class="label-view-control">Created</label></td>
                                                         <td class="table-view-control">{{ isset($userData[0]['created_at']) && $userData[0]['created_at'] != ""  ? date('d-m-Y H:i:s',strtotime($userData[0]['created_at'])) : '--' }}</td>
                                                      </tr>
                                                      <tr>
                                                         <td><label class="label-view-control">Updated</label></td>
                                                         <td class="table-view-control">{{ isset($userData[0]['updated_at']) && $userData[0]['updated_at'] != ""  ? date('d-m-Y H:i:s',strtotime($userData[0]['updated_at'])) : '--' }}</td>
                                                      </tr>
                                                      <tr>
                                                         <td><label class="label-view-control">Last Logged Date</label></td>
                                                         <td class="table-view-control">{{ isset($userData[0]['last_access']) && $userData[0]['last_access'] != ""  ? date('d-m-Y H:i:s',strtotime($userData[0]['last_access'])) : '--' }}</td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="tab-pane " aria-expanded="true" role="tabpanel" id="tab_2" aria-labelledby="base-tab_2">
                                       <div class="row">
                                          <div class="col-xl-12">
                                             <h3 class="tab-content-title"><i class="la la-paperclip"></i> Address</h3>
                                          </div>
                                       </div>
                                       <div class="row">
                                          <div class="col-xl-8">
                                             <div class="row">
                                                <div class="form-body">
                                                   <div class="form-group row mx-auto d-none">
                                                      <label class="col-md-3 ">Country</label>
                                                      <div class="col-md-9 label-control">
                                                         {{ isset($countryData['country_name']) && $countryData['country_name'] != '' ? $countryData['country_name'] : '--' }}
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 ">State</label>
                                                      <div class="col-md-9 label-control">
                                                         {{ isset($stateData['state_name']) && $stateData['state_name'] != '' ? $stateData['state_name'] : '--'  }}
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 ">Districts</label>
                                                      <div class="col-md-9 label-control">
                                                         {{isset($districtsData['district_name']) && $districtsData['district_name'] != '' ? $districtsData['district_name'] : '--' }}
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 "  for="userinput2">Taluka</label>
                                                      <div class="col-md-9 label-control">
                                                         {{ (isset($taluka[0]['taluka_name']) && $taluka[0]['taluka_name'] != '') ? $taluka[0]['taluka_name']  : '--'}}
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 "  for="userinput2">Zip</label>
                                                      <div class="col-md-9 label-control">
                                                         {{ (isset($userData[0]['zip']) &&  $userData[0]['zip'] != '' )? $userData[0]['zip'] : '--' }}
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 "  for="userinput2">Address</label>
                                                      <div class="col-md-9 label-control">
                                                         {{ (isset($userData[0]['address']) &&  $userData[0]['address'] != '' ) ? $userData[0]['address'] : '--' }}
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="tab-pane " aria-expanded="true" role="tabpanel" id="tab_3" aria-labelledby="base-tab_3">
                                       <div class="row">
                                          <div class="col-xl-12">
                                             <h3 class="tab-content-title"><i class="la la-lock"></i> Login Information</h3>
                                          </div>
                                       </div>
                                       <div class="row">
                                          <div class="col-xl-8">
                                             <div class="row">
                                                <div class="form-body">
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 "  for="userinput2">User Name</label>
                                                      <div class="col-md-9 label-control">
                                                         {{isset($userData[0]['username'])?$userData[0]['username']:"---" }}
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control">Status</label>
                                                      <div class="col-md-9 label-control">
                                                         <?php echo $status_btn;?>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="form-actions text-right">
                                    <a href="{{ route('admin.user') }}">
                                    <button type="button" class="btn mr-1 waves-effect waves-light">
                                    <i class="ft-chevron-left"></i> Back
                                    </button>
                                    </a>
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
<script type="text/javascript">
   var csrf_token = "{{ csrf_token() }}";
   var statelist = "{{ route('admin/master/statelist') }}";
   /*    var route_user_add ="{{ route('admin.usersave') }}";
   var adduser = "{{ route('user',['mode'=>'add']) }}";
   var list = "{{ route('admin.user') }}";*/
   var removeimage = "{{ route('admin.user.removeimage') }}";
   var country = <?php echo json_encode($country); ?>;
    var temp_country_code = '<?php echo $temp_country_code?>'; 
</script>
<script src="{{ asset('assets/pages/scripts/admin/common/select2.full.min.js') }}"></script>
<!--Start Bootstrap Switch-->
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/toggle/bootstrap-switch.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/toggle/switchery.min.js') }}"></script>
<!--End Bootstrap Switch-->
<!-- Start FORM VALIDATION -->
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/additional-methods.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script> 
<!-- End FORM VALIDATION -->
<script type="text/javascript" src="{{ asset('assets\pages\scripts\admin\fancybox\dist\jquery.fancybox.js')}}"></script>
<script src="{{ asset('assets/pages/scripts/admin/user/userview.js') }}"></script>
@endsection