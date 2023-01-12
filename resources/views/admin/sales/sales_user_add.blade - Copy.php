@extends('layouts.admin')
@section('styles')
<!--Start Bootstrap Switch-->
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/switchery.min.css')}}">
<link href="{{ asset('assets/css/core/colors/palette-switch.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.default.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/plugins/forms/selectize/selectize.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/fancybox/dist/jquery.fancybox.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('/assets/vendors/css/fancybox/dist/jquery.fancybox.min.css')}}" rel="stylesheet" type="text/css"/>
<!--End Bootstrap Switch-->
@endsection
@section('content')
@php
$mode = isset($mode)?$mode:"Add";
$is_ip_auth_cls = $mobile_email_cls = 'd-none';
$checked_is_ip_auth = $checked_mobile_email_auth = '';
@endphp
@if($mode == 'Update')
@if(isset($userData['is_ip_auth']) && $userData['is_ip_auth'] == 1 ) 
@php $is_ip_auth_cls = ''; //ip auth
$checked_is_ip_auth = 'checked'; @endphp
@else
@php $is_ip_auth_cls = 'd-none';
$checked_is_ip_auth = ''; @endphp
@endif
@if(isset($userData['is_mobile_auth']) && $userData['is_mobile_auth'] == 1 )
@php $mobile_email_cls = '';  //mobile email auth
$checked_mobile_email_auth = 'checked'; @endphp
@else
@php $mobile_email_cls = 'd-none';
$checked_mobile_email_auth = ''; @endphp
@endif
@endif
@php $temp_country_id = (isset($userData['country_id'])?$userData['country_id']:''); @endphp
<div class="content-wrapper">
   <div class="content-body">
      <section class="content-header clearfix">
         <h3>Sales User<strong><span class="text-muted accent-3">{{(isset($userData['first_name'])?' - '.reduceTitleName($userData['first_name']):'')}}{{(isset($userData['last_name'])?' '.$userData['last_name']:'')}}</span></strong></h3>
         <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="javascript:void(0);">Sales Users Mgmt</a></li>
            <li><a href="{{ route('sales.user') }}">Sales User</a></li>
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
                           @if($mode == 'Update')
                           @if (per_hasModuleAccess('SalesUsers', 'View')) 
                           <a href="{{ route('salesuser',['mode'=>'view','id'=>base64_encode($userData['id'])]) }}" title="View"><span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-eye"></i></span></a> 
                           @endif
                           {{-- @if(per_hasModuleAccess('SalesUsers', 'Delete')) 
                           <a onclick="deleteSingleRecord({{isset($userData['id'])?$userData['id']:''}},'salesuser');" title="Delete"><span class="btn btn-icon btn-danger waves-effect waves-light"><i class="la la-trash"></i></span></a>  
                           @endif --}}
                           @endif
                        </div>
                     </div>
                  </div>
                  <div class="card-content collapse show">
                     <div class="card-body language-input">
                        <form class="form form-horizontal"   method="post"  enctype="multipart/form-data"  id="useraddForm">
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
                           <input type="hidden" name="_token" value="{{ csrf_token() }}">
                           <input type="hidden" name="mode"  id="mode" value="{{ $mode }}">
                           <input type="hidden" name="id"  id="id"  value="{{ isset($userData['id']) ? $userData['id'] : old('id') }}" > 
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
                                                   <li class="nav-item">
                                                      <a class="nav-link" id="base-tab_4" data-toggle="tab" aria-controls="tab_4" href="#tab_4" aria-expanded="False">Security</a>
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
                                    <div class="tab-pane active " aria-expanded="true" role="tabpanel" aria-expanded="true"  id="tab_1"  aria-labelledby="base-tab_1">
                                       <div class="row">
                                          <div class="col-xl-12">
                                             <h3 class="tab-content-title"><i class="ft-user"></i> Personal Info</h3>
                                          </div>
                                       </div>
                                       <div class="row">
                                          <div class="col-xl-8">
                                             <div class="row">
                                                <div class="form-body  col-md-12">
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control" for="first_name">First Name <span class="required">*</span></label>
                                                      <div class="col-md-9">
                                                         <input type="text" id="first_name" class="form-control " placeholder="First Name" name="first_name" value="{{ isset($userData['first_name']) ? $userData['first_name'] : old('first_name') }}" >
                                                         <span class="text-danger">{{ $errors->first('first_name') }}</span>
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control"  for="userinput2">Last Name <span class="required" aria-required="true">*</span></label>
                                                      <div class="col-md-9">
                                                         <input type="text" id="last_name" class="form-control " placeholder="Last Name" name="last_name" value="{{ isset($userData['last_name']) ? $userData['last_name'] : old('last_name') }}" >
                                                         <span class="text-danger">{{ $errors->first('last_name') }}</span>
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control"  for="userinput2">Email Address <span class="required" aria-required="true">*</span></label>
                                                      <div class="col-md-9 position-relative has-icon-left">
                                                         <input type="text" id="email" class="form-control" placeholder="Email Address" name="email" value="{{ isset($userData['email']) ? $userData['email'] : old('email') }}" >
                                                         <div class="form-control-position">
                                                            <i class="material-icons info font-medium-4">email</i>
                                                         </div>
                                                         <span class="text-danger">{{ $errors->first('email') }}</span>
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto m-1">
                                                      <label class="col-md-3 label-control"  for="userinput2">Image</label>
                                                      <div class="col-md-4 custom-file ">
                                                         <input type="file" id="image" class="custom-file-input form-control col-md-6"  name="image" value="{{ isset($userData['image']) ? $userData['image'] : old('image') }}" >
                                                         <label class="custom-file-label" for="image" aria-describedby="imageAddon">Choose file</label>
                                                         <input type="hidden" name="user_image_old" value="{{ isset($userData['image']) ? $userData['image'] : '' }}" >
                                                         <span class="text-danger">
                                                         {{ $errors->first('image') }}
                                                         </span>
                                                      </div>
                                                      @if(isset($mode) && $mode == 'Update')
                                                      @if(isset($userImage['img_url']) && $userImage['img_url'] != '')
                                                      <div class="col-md-3">
                                                         <a class="fancybox" rel="gallery1" href="{{$userImage['fancy_box_url']}}" title=""><img src="{{(isset($userImage['img_url']) && $userImage['img_url'] != '')?$userImage['img_url']:''}}" alt="" class="img-fluid rounded-circle width-50" id="show-image" onerror="isImageExist(this)" noimage="50x50.jpg" /></a> 
                                                         <a href="javascript:void(0);" title="Delete" id="removeImage" data-id="{{ isset($userData['id']) ? $userData['id'] : '' }}"><span class="btn btn-icon btn-danger waves-effect waves-light"><i class="icon-close"></i></span></a>
                                                      </div>
                                                      @endif 
                                                      @endif
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control">  </label>
                                                      @if(isset($img_ext_array) && !empty($img_ext_array))
                                                      <div class="">
                                                         <p class="danger mb-1 px-2">[ Valid extentions: <code>{{!empty($img_ext_array)?$img_ext_array:''}}</code>]</p>
                                                      </div>
                                                      @endif
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control" for="sales_structure">Sales Structure <span class="required">*</span></label>
                                                      <div class="col-md-9">
                                                         <select  id="sales_structure" class="access_control" name="sales_structure_id" >
                                                            <option value="">Select Sales Structure</option>
                                                            @if(!empty($sales_structure))
                                                            @foreach($sales_structure as $structure)
                                                            <option value="{{ $structure['id'] }}"
                                                               <?php
                                                                  if (isset($userData['sales_structure_id']) && $userData['sales_structure_id'] == $structure['id']){
                                                                    ?>
                                                               selected
                                                               <?php
                                                                  }
                                                                   ?>
                                                               >{{ $structure['short_name'] }}
                                                            </option>
                                                            @endforeach
                                                            @endif
                                                         </select>
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control"  for="aadhar_no">Aadhar No. <span class="required" aria-required="true">*</span></label>
                                                      <div class="col-md-9 position-relative has-icon-left">
                                                         <input type="number" id="aadhar_no" class="form-control " placeholder="Aadhar Number" name="adhar_no" value="{{ isset($userData['adhar_no']) ? $userData['adhar_no'] : old('adhar_no') }}" maxlength="12" >
                                                         <div class="form-control-position">
                                                            <i class="material-icons info font-medium-4">tag</i>
                                                         </div>
                                                         <span class="text-danger">{{ $errors->first('email') }}</span>
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control" for="designation">Designations <span class="required">*</span></label>
                                                      <div class="col-md-9">
                                                         <select  id="designation" class="access_control" name="designation_id" >
                                                            <option value="">Select Designation</option>
                                                            @if(!empty($designations))
                                                            @foreach($designations as $designation)
                                                            <option value="{{ $designation['id'] }}"
                                                               <?php
                                                                  if (isset($userData['designation_id']) && $userData['designation_id'] == $designation['id']){
                                                                    ?>
                                                               selected
                                                               <?php
                                                                  }
                                                                   ?>
                                                               >{{ $designation['name'] }}
                                                            </option>
                                                            @endforeach
                                                            @endif
                                                         </select>
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control"  for="userinput2">Mobile <span class="required" aria-required="true">*</span></label>
                                                      <div class="col-md-9 position-relative has-icon-left">
                                                         <input type="text" id="mobile" class="form-control " placeholder="Mobile" name="mobile" value="{{ isset($userData['mobile']) ? $userData['mobile'] : old('mobile') }}" >
                                                         <div class="form-control-position">
                                                            <i class="material-icons info font-medium-4">call</i>
                                                         </div>
                                                         <span class="text-danger">{{ $errors->first('email') }}</span>
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control"  for="whatsapp">WhatsApp Number <span class="required" aria-required="true">*</span></label>
                                                      <div class="col-md-9 position-relative has-icon-left">
                                                         <input type="text" id="whatsapp" class="form-control " placeholder="WhatsApp Number" name="whatsapp_number" value="{{ isset($userData['whatsapp_number']) ? $userData['whatsapp_number'] : old('whatsapp_number') }}" >
                                                         <div class="form-control-position">
                                                            <i class="la la-whatsapp info font-medium-4"></i>
                                                         </div>
                                                         <span class="text-danger">{{ $errors->first('email') }}</span>
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control"  for="remark">Remark <span class="required" aria-required="true">*</span></label>
                                                      <div class="col-md-9">
                                                         <textarea class="form-control" id="remark" rows="3" placeholder="Remark" name = "remark" value="{{ isset($userData['remark']) ? $userData['remark'] : old('remark') }}">{{ isset($userData['remark']) ? $userData['remark'] : old('remark') }}</textarea>
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto d-none">
                                                      <label class="col-md-3 label-control" for="acess_groupid">Access Group <span class="required">*</span></label>
                                                      @php $grp_exp = array(); @endphp
                                                      @if (isset($userData['access_group_id_arr']) && $userData['access_group_id_arr'] != "") 
                                                      @php $grp_exp = array_filter(explode(',', $userData['access_group_id_arr'])); @endphp
                                                      @endif
                                                      <div class="col-md-9">
                                                         <select  id="acess_groupid" class="access_control" name="acess_groupid[]" >
                                                            <option value="">Select Access Group</option>
                                                            @if(!empty($access_group_data))
                                                            @foreach($access_group_data as $rowGroup)
                                                            @if($mode == 'Add')
                                                            <option value="{{ $rowGroup['id'] }}"
                                                               <?php if ($rowGroup['id']==63) { ?>
                                                               selected
                                                               <?php } ?>
                                                               >{{ $rowGroup['access_group'] }}
                                                            </option>
                                                            @elseif($mode == 'Update')
                                                            @php $grp_exp = array(); @endphp
                                                            @if(isset($userData['access_group_id_arr']) && $userData['access_group_id_arr'] != "") 
                                                            @php $grp_exp = array_filter(explode(',', $userData['access_group_id_arr'])); @endphp
                                                            @endif
                                                            <option  @if (!empty($grp_exp) && in_array($rowGroup['id'], $grp_exp)) selected="selected" @endif value="{{ $rowGroup['id'] }}">{{ $rowGroup['access_group'] }}</option>
                                                            @endif 
                                                            @endforeach
                                                            @endif
                                                         </select>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                          @if($mode == 'Update')
                                          <div class="col-xl-4 col-12">
                                             <div class="form-group row mx-auto">
                                                <table class="table table-bordered table-striped">
                                                   <tbody>
                                                      <tr>
                                                         <td><label class="label-view-control">Total Login</label></td>
                                                         <td class="table-view-control">{{ isset($userData['tot_login']) && $userData['tot_login'] != ""  ? $userData['tot_login'] : '0' }}</td>
                                                      </tr>
                                                      <tr>
                                                         <td><label class="label-view-control">Created</label></td>
                                                         <td class="table-view-control">{{ isset($userData['created_at']) && $userData['created_at'] != ""  ? date('d-m-Y H:i:s',strtotime($userData['created_at'])) : '--' }}</td>
                                                      </tr>
                                                      <tr>
                                                         <td><label class="label-view-control">Updated</label></td>
                                                         <td class="table-view-control">{{ isset($userData['updated_at']) && $userData['updated_at'] != ""  ? date('d-m-Y H:i:s',strtotime($userData['updated_at'])) : '--' }}</td>
                                                      </tr>
                                                      <tr>
                                                         <td><label class="label-view-control">Last Logged Date</label></td>
                                                         <td class="table-view-control">{{ isset($userData['last_access']) && $userData['last_access'] != ""  ? date('d-m-Y H:i:s',strtotime($userData['last_access'])) : '--' }}</td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </div>
                                          </div>
                                          @endif 
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
                                                      <label class="col-md-3 label-control">Country</label>
                                                      <div class="col-md-9">
                                                         <select  id="country_id" class="  select2-placeholder " name="country_id" >
                                                            <option value="">Select Country</option>
                                                            @foreach($countryData as $key => $countryArray)
                                                            @php $def_country = (isset($default_country)?$default_country:"")@endphp
                                                            <option value="{{ $countryArray->id }}" <?php if ($countryArray->id == 1){ ?> selected <?php  }?>  {{($countryArray->id ==$def_country)?'selected':''}} >{{ $countryArray->country_name }}</option>
                                                            @endforeach
                                                         </select>
                                                         <span class="text-danger">{{ $errors->first('country_code') }}</span>
                                                      </div>
                                                   </div>
                                                   <?php
                                                      //  echo '<pre>'; print_r($stateData); echo '</pre>';
                                                      //  echo '<pre>'; print_r($userData['state_id']); echo '</pre>'; exit();
                                                        ?>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control">State</label>
                                                      <div class="col-md-9">
                                                         <select  id="state_id" class=" select2-state " name="state_id" >
                                                            <option value="">Select State</option>
                                                            @if(!empty($stateData))
                                                            @foreach($stateData as $state)
                                                            <option value="{{ $state['id'] }}" 
                                                               <?php
                                                                  if($userData['state_id'] == $state['id']){
                                                                    ?>
                                                               selected
                                                               <?php
                                                                  }
                                                                  ?>
                                                               >{{ $state['state_name']}}
                                                            </option>
                                                            @endforeach
                                                            @endif 
                                                         </select>
                                                         <span class="text-danger">{{ $errors->first('state_id') }}</span> 
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control">Districts</label>
                                                      <div class="col-md-9">
                                                         <select  id="districts_id" class=" select2-districts " name="districts_id" >
                                                            <option value="">Select Districts</option>
                                                            @if($mode == 'Update')
                                                            @foreach($districtsData as $key=>$dArry)
                                                            <option value="{{ $dArry['id'] }}" {{ $dArry['id'] == $userData['district_id'] ? 'selected' : '' }}>{{ $dArry['district_name'] }}</option>
                                                            @endforeach
                                                            @endif
                                                         </select>
                                                         <span class="text-danger">{{ $errors->first('districts_code') }}</span>
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control" for="taluka">Taluka </label>
                                                      <div class="col-md-9">
                                                         <select  id="taluka_id" class="access_control" name="taluka_id" >
                                                            <option value="">Select Taluka</option>
                                                            @if($mode == 'Update')
                                                            @if(!empty($all_taluka))
                                                               @foreach($all_taluka as $taluka)
                                                               <option value="{{ $taluka['id'] }}"
                                                                  <?php
                                                                     if ( isset($userData) && $userData['taluka_id'] == $taluka['id']){
                                                                     ?>
                                                                  selected
                                                                  <?php
                                                                     }
                                                                     ?>
                                                                  >{{ $taluka['taluka_name'] }}
                                                               </option>
                                                               @endforeach
                                                            @endif
                                                            @endif
                                                         </select>
                                                      </div>
                                                   </div>
                                                   {{--<div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control" for="area">Area <span class="required">*</span></label>
                                                      <div class="col-md-9">
                                                         <select  id="area" class="access_control" name="area" >
                                                            <option value="">Select Area</option>
                                                            @if(!empty($areas))
                                                            @foreach($areas as $area)
                                                            <option value="{{ $area['id'] }}"
                                                               <?php
                                                                  if ( isset($userData) && $userData['area'] == $area['id']){
                                                                    ?>
                                                               selected
                                                               <?php
                                                                  }
                                                                  ?>
                                                               >{{ $area['name'] }}
                                                            </option>
                                                            @endforeach
                                                            @endif
                                                         </select>
                                                      </div>
                                                   </div>--}}
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control"  for="userinput2">Zip</label>
                                                      <div class="col-md-9">
                                                         <input type="text" id="zip" class="form-control " placeholder="Zip" name="zip" value="{{ isset($userData['zip']) ? $userData['zip'] : old('zip') }}" >
                                                         <span class="text-danger">{{ $errors->first('zip') }}</span>
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control"  for="userinput2">Address</label>
                                                      <div class="col-md-9">
                                                         <textarea name="address"  id="address"  class="form-control "  > {{ isset($userData['address']) ? $userData['address'] : old('address') }}</textarea>
                                                         <span class="text-danger">{{ $errors->first('address') }}</span>
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
                                                      <label class="col-md-3 label-control"  for="userinput2">User Name <span class="required" aria-required="true">*</span> </label>
                                                      <div class="col-md-9">
                                                         @if(isset($mode) && $mode == 'Update')
                                                         @if(Auth::guard('admin')->user()->id == $userData['id'])
                                                         <span class="label-control"> {{ isset($userData['username']) ? $userData['username'] :  '--' }}</span>
                                                         <input type="hidden" id="username" class="form-control " placeholder="User Name" name="username" value="{{ isset($userData['username']) ? $userData['username'] : old('username') }}" >
                                                         @else
                                                         <input type="text" id="username" class="form-control " placeholder="User Name" name="username" value="{{ isset($userData['username']) ? $userData['username'] : old('username') }}" >
                                                         @endif
                                                         @elseif(isset($mode) && $mode == 'Add')
                                                         <input type="text" id="username" class="form-control " placeholder="User Name" name="username" value="{{ old('username') }}" >
                                                         @endif
                                                         <span class="text-danger">{{ $errors->first('username') }}</span>
                                                      </div>
                                                   </div>
                                                   @if(isset($mode) && $mode == 'Update')
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control">Check for password change</label>
                                                      <div class="card-content col-md-9">
                                                         <div class="card-body">
                                                            <div class="custom-control custom-checkbox">
                                                               <input type="checkbox" value="1" name="changePasswordChk" class="custom-control-input" id="changePasswordChk">
                                                               <label class="custom-control-label" for="changePasswordChk"></label>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto changePassDiv d-none" id="password" >
                                                      <label class="col-md-3 label-control">New Password <span class="required">*</span></label>
                                                      <div class="col-md-9 position-relative has-icon-right password-input">
                                                         <input name="password" type="password" class="form-control" placeholder="New Password" value=""  id="new_password" autocomplete="new-password" >
                                                         <a href="javascript:void(0)" title="show password" class="form-control-position" onclick="showhidePassword(this);"><span class="btn btn-icon btn-secondary btn-light  waves-effect waves-light"><i class="la la-eye"></i></span></a>
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto changePassDiv d-none" >
                                                      <label class="col-md-3 label-control"></label>
                                                      <div class="col-md-9 ">
                                                         <span class="required">[Enter at least one uppercase letter, one lowercase letter, one number and one special character for password.Minimum {{config('constants.PASSWORD_MIN')}} character and maximum {{config('constants.PASSWORD_MAX')}} character for password.]</span>
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto changePassDiv d-none" id="password_confirmation">
                                                      <label class="col-md-3 label-control">Confirm Password <span class="required">*</span></label>
                                                      <div class="col-md-9 position-relative has-icon-right password-input">
                                                         <input name="password_confirmation" type="password" id="passwordconfirmation" class="form-control" placeholder="Confirm Password" value=""  autocomplete="new-password" >
                                                         <a href="javascript:void(0)" title="show password" class="form-control-position" onclick="showhidePassword(this);"><span class="btn btn-icon btn-secondary btn-light  waves-effect waves-light"><i class="la la-eye"></i></span></a>
                                                      </div>
                                                   </div>
                                                   @else 
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control"  for="userinput2">Password <span class="required">*</span></label>
                                                      <div class="col-md-9  position-relative has-icon-right password-input">
                                                         <input type="password" id="password" class="form-control " placeholder="Password" name="password" value="{{ old('Password') }}" >
                                                         <a href="javascript:void(0)" title="show password" class="form-control-position" onclick="showhidePassword(this);"><span class="btn btn-icon btn-secondary btn-light  waves-effect waves-light"><i class="la la-eye"></i></span></a>
                                                         <span class="text-danger">{{ $errors->first('password') }}</span>
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto"  >
                                                      <label class="col-md-3 label-control"></label>
                                                      <div class="col-md-9 ">
                                                         <span class="required">[Enter at least one uppercase letter, one lowercase letter, one number and one special character for password.Minimum {{config('constants.PASSWORD_MIN')}} character and maximum {{config('constants.PASSWORD_MAX')}} character for password.]</span>
                                                      </div>
                                                   </div>
                                                   @endif
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control">Status</label>
                                                      <div class="col-md-9">
                                                         <?php
                                                            $checked = '';
                                                            if($mode == 'Add') {
                                                                $checked = 'checked';
                                                            }else {
                                                                $checked = (isset($userData['status']) && $userData['status'] == "1" ) ? ' checked ' : '';
                                                            } ?>
                                                         <input type="checkbox" class="switchBootstrap form-control" id="status" name="status" data-on-text="Active" data-off-text="Inactive" data-on-color="{{Config::get('constants.switch_on_color')}}"  data-off-color="{{Config::get('constants.switch_off_color')}}"  value="1" {{$checked}} />
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="tab-pane" aria-expanded="true" role="tabpanel" id="tab_4" aria-labelledby="base-tab_4">
                                       <div class="row">
                                          <div class="col-xl-12">
                                             <h3 class="tab-content-title"><i class="la la-lock"></i> Admin Security</h3>
                                          </div>
                                       </div>
                                       <div class="row">
                                          <div class="col-xl-8">
                                             <div class="row">
                                                <div class="form-body">
                                                   <div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control"  for="userinput2">IP Authentication   </label>
                                                      <div class="col-md-9">
                                                         <div class="card-body">
                                                            <div class="custom-control custom-checkbox">
                                                               <input type="checkbox" value="1" {{$checked_is_ip_auth}} name="is_ip_auth" class="custom-control-input" id="is_ip_auth">
                                                               <label class="custom-control-label" for="is_ip_auth"></label>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                   <div class="form-group row mx-auto ip_authDiv {{$is_ip_auth_cls}}" id="ip_authDiv">
                                                      <label class="col-md-3 label-control" for="mobile"> IP  </label>
                                                      <div class="col-md-9">
                                                         <select class="selectize-control selectize-select-multipleIp" id="ip_id" name="ip_id[]" placeholder="Select Ip" multiple>
                                                            <option value="">Select Ip</option>
                                                            @if(!empty($ip_data))
                                                            @php $selected = ""; @endphp
                                                            @foreach($ip_data as $ipkey => $ipval)
                                                            @if(isset($admin_ip_data_arr) && !empty($admin_ip_data_arr))
                                                            @if(in_array($ipval['id'],$admin_ip_data_arr))
                                                            @php $selected = "selected"; @endphp
                                                            @else
                                                            @php $selected = ""; @endphp
                                                            @endif
                                                            @endif
                                                            <option value="{{$ipval['id']}}" {{$selected}}>{{(isset($ipval['ip']))?$ipval['ip']:''}} ( {{isset($ipval['description'])?$ipval['description']:''}} )</option>
                                                            @endforeach
                                                            @endif
                                                         </select>
                                                      </div>
                                                   </div>
                                                   {{--<div class="form-group row mx-auto">
                                                      <label class="col-md-3 label-control"  for="userinput2">Mobile and Email Authentication  </label>
                                                      <div class="col-md-9">
                                                         <div class="card-body">
                                                            <div class="custom-control custom-checkbox">
                                                               <input type="checkbox" value="1" {{$checked_mobile_email_auth}} name="is_mobile_auth" class="custom-control-input" id="is_mobile_auth">
                                                               <label class="custom-control-label" for="is_mobile_auth"></label>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div> --}}
                                                   <div class="form-group row mx-auto mobile_auth_attemptDiv {{$mobile_email_cls}}" id="mobile_auth_attemptDiv">
                                                      <label class="col-md-3 label-control" for="mobile"> Total Attempts  </label>
                                                      <div class="col-md-9 position-relative has-icon-left mobile-country">
                                                         <input name="mobile_auth_attempt" type="text" class="form-control" placeholder="Total attempts" value="{{(isset($userData['mobile_auth_attempt']))?$userData['mobile_auth_attempt']:'0'}}"  id="mobile_auth_attempt" >
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="form-actions text-right">
                                    <a href="{{ route('sales.user') }}">
                                    <button type="button" class="btn mr-1 waves-effect waves-light" id="btn_close_par"><i class="ft-x"></i> Cancel
                                    </button></a>
                                    <button type="submit" class="btn btn-success waves-effect waves-light" id="save_record">
                                    <i class="la la-check-square-o"></i> Save Changes
                                    </button> 
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
   var districtlist = "{{ route('admin/master/districtslist') }}";
   var route_user_add ="{{ route('sales.usersave') }}"; 
   var adduser = "{{ route('user',['mode'=>'add']) }}";
   var list = "{{ route('admin.user') }}";
   var removeimage = "{{ route('admin.user.removeimage') }}";
   var country = <?php echo json_encode($country); ?>;
   var temp_country_code = '<?php echo $temp_country_id?>';
   var temp_state_code = '';
   var PASSWORD_MIN    = "{{config('constants.PASSWORD_MIN')}}";
   var PASSWORD_MAX    = "{{config('constants.PASSWORD_MAX')}}";
   var PASSWORD_FORMAT = {{config('constants.PASSWORD_FORMAT')}};
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
<script type="text/javascript" src="{{ asset('/assets/vendors/js/fancybox/dist/jquery.fancybox.js')}}"></script>
<!-- End FORM VALIDATION -->
<script src="{{ asset('assets/pages/scripts/admin/sales/salesuseradd.js') }}"></script>
@endsection