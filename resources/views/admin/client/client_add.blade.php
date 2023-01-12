@extends('layouts.admin')
@section('styles')
<style>
div.col-sm-3.label-control{
   padding-top:8px;
}
.selectize-dropdown{
   z-index: 9999;
}
.tab-pane .table thead th {
    line-height: 1.4;
    height: auto;
    padding: 10px !important;
}
.div_is_default_billing,.div_is_default_shipping{
   margin-top: 10px;
}
</style>
@endsection
@section('content')
@php

if(!empty($address)) $taddress=count($address); else $taddress=0;
if(!empty($contact)) $tcontact=count($contact); else $tcontact=0;

$country=config('settings.country');
@endphp
@section('content')
<div class="content-wrapper">
<div class="content-body">
<section class="content-header clearfix">
   <h3>Client <strong><span class="text-muted accent-3">{{((isset($userData[0]['name'])?' - '.reduceTitleName($userData[0]['name']):''))}}</span></strong></h3>
   <ol class="breadcrumb">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li><a href="javascript:void(0);">Client Mgmt</a></li>
      <li><a href="{{ route('client/grid') }}">Client</a></li>
      <li><a href="javascript:void(0);">{{(isset($mode)?$mode:'') }} Client</a></li>
   </ol>
</section>
<section class="horizontal-grid" id="horizontal-grid">
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            @if(isset($mode) && $mode == "Update")
            <div class="card-head ">
               <div class="card-header">
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
                     <input type="hidden" name="flag_old" id="flag_old" value="{{(isset($userData['image'])?$userData['image']:'')}}">
                     <input type="hidden" id="id" name="id" value="{{(isset($userData['id'])?$userData['id']:'')}}" />
                     <input type="hidden" id="verified_date" name="verified_date" value="{{(isset($userData['verified_date'])?$userData['verified_date']:'')}}" />
                     <input type="hidden" id="verified_date_old" name="verified_date_old" value="{{(isset($userData['verified_date'])?$userData['verified_date']:'')}}" />
                     <input type="hidden" id="country" name="country" value="{{(isset($country)?$country:'')}}" />
                     <div class="row">
                        <div class="col-xl-2 col-lg-3 col-md-12 col-12">
                           <div class="sidebar-left site-setting">
                              <div id="accordionWrap5" role="tablist" aria-multiselectable="true">
                                 <div class="card collapse-icon accordion-icon-rotate">
                                    <div id="heading51" class="card-header">
                                       <a data-toggle="collapse" href="#accordion51" aria-expanded="true" aria-controls="accordion51" class="card-title lead">Client</a>
                                    </div>
                                    <div id="accordion51" role="tabpanel" data-parent="#accordionWrap5" aria-labelledby="heading51" class="card-collapse collapse show" aria-expanded="true">
                                       <div class="card-body">
                                          <ul class="nav nav-tabs m-0">
                                             <li class="nav-item">
                                                <a class="nav-link active" id="base-tab_1" data-toggle="tab" aria-controls="tab_1" href="#tab_1" aria-expanded="true">
                                                General Information </a>
                                             </li>
                                             <li class="nav-item">
                                                <a class="nav-link " id="base-tab_3" data-toggle="tab" aria-controls="tab_3" href="#tab_3" aria-expanded="true">
                                                Addresses&nbsp;<strong>{{ ($taddress>0)? "($taddress)" :'' }}</strong></span></a>
                                             </li>
                                             <li class="nav-item">
                                                <a class="nav-link " id="base-tab_2" data-toggle="tab" aria-controls="tab_2" href="#tab_2" aria-expanded="true">
                                                Contacts&nbsp;<strong>{{ ($tcontact>0)? "($tcontact)" :'' }}</strong></a>
                                             </li>
                                             <li class="nav-item">
                                                <a class="nav-link " id="base-tab_4" data-toggle="tab" aria-controls="tab_4" href="#tab_4" aria-expanded="true">
                                                Settings&nbsp;<strong></strong></a>
                                             </li>
                                             <li class="nav-item">
                                                <a class="nav-link " id="base-tab_4" data-toggle="tab" aria-controls="tab_5" href="#tab_5" aria-expanded="true">Status Logs</a>
                                             </li>
                                             <li class="nav-item">
                                                <a class="nav-link" id="base-tab_5" data-toggle="tab" aria-controls="tab_6" href="#tab_6" aria-expanded="False">Login Information</a>
                                             </li> 
                                             @if(isset($mode) && $mode == "Update")
                                                <li class="nav-item">
                                                   <a class="nav-link" id="base-tab_7" data-toggle="tab" aria-controls="tab_7" href="#tab_7" aria-expanded="False">Documents ({{$countVerifyDocument}}/{{$countDocument}})</a>
                                                </li> 
                                             @endif                                             
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
                                 <div class="row form-box">
                                    <div class="col-xl-8">
                                       <div class="">
                                          <div class="form-body">
                                             <div class="form-group row mx-auto {{ ($mode == 'Update')?'':'d-none'}}">
                                                <label class="col-md-3 label-control" for="client_code">Code </label>
                                                <div class="col-md-6">
                                                   <input type="text"  readonly="true" id="client_code" class="form-control " placeholder="Code" name="client_code" value="{{ isset($userData['client_code']) ? $userData['client_code'] : rand(1000,9999) }}" >
                                                </div>
                                             </div>
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control" for="client_name">Client Name <span class="required">*</span></label>
                                                <div class="col-md-9">
                                                   <input type="text" id="client_name" class="form-control " placeholder="Client Name" name="client_name" value="{{ isset($userData['client_name']) ? $userData['client_name'] : '' }}" maxlength="100" >
                                                </div>
                                             </div>
                                             {{-- 
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control" for="billing_name">Billing Name <span class="required">*</span></label>
                                                <div class="col-md-9">
                                                   <input type="text" id="billing_name" class="form-control " placeholder="Billing Name" name="billing_name" value="{{ isset($userData['billing_name']) ? $userData['billing_name'] : ''}}" maxlength="100">
                                                </div>
                                             </div>
                                              --}}
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control"  for="userinput2">Tally Client Name <span class="required" aria-required="true">*</span></label>
                                                <div class="col-md-9">
                                                <input type="text" id="tally_client_name" class="form-control " placeholder="Tally Client Name" name="tally_client_name" value="{{ isset($userData['tally_client_name']) ? $userData['tally_client_name'] : old('tally_client_name') }}" > 
                                                </div> 
                                             </div>
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control" for="email">Registered Email <span class="required" aria-required="true">*</span></label>
                                                <div class="col-md-9 position-relative has-icon-left">
                                                   <input type="text" id="email" class="form-control" placeholder="Registered Email" name="email" value="{{ isset($userData['email']) ? $userData['email'] : old('email') }}" >
                                                   <div class="form-control-position"><i class="material-icons info font-medium-4">email</i></div>
                                                </div>
                                             </div>
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control"  for="mobile_number">Registered Mobile <span class="required" aria-required="true">*</span></label>
                                                <div class="col-md-6 has-icon-left">
                                                   <input type="text" id="mobile_number" class="form-control " placeholder="Registered Mobile" name="mobile_number" value="{{ isset($userData['mobile_number']) ? $userData['mobile_number'] : old('mobile_number') }}" >
                                                   <div class="form-control-position">
                                                      <i class="material-icons info font-medium-4">call</i>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control"  for="whatsapp_number">WhatsApp No. <span class="required" aria-required="true">*</span></label>
                                                <div class="col-md-6 has-icon-left">
                                                   <input type="text" id="whatsapp_number" class="form-control " placeholder="WhatsApp No." name="whatsapp_number" value="{{ isset($userData['whatsapp_number']) ? $userData['whatsapp_number'] : old('whatsapp_number') }}" >
                                                   <div class="form-control-position">
                                                      <i class="la la-whatsapp info font-medium-4"></i>
                                                   </div>
                                                </div>
                                             </div>
                                            
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control" for="sales_user_id"> Sales User <span class="required" aria-required="true">*</span></label>
                                                <div class="col-md-6 ">
                                                   <select class="selectize" id="sales_user_id" name="sales_user_id" placeholder="Select Sales User" data-placeholder="Select Sales User">
                                                      <option value="">Select Sales User</option>
                                                      @if(!empty($salesUsers))
                                                         @foreach($salesUsers as $user)
                                                            @php $selected="";
                                                            $texthtml="";
                                                            if(isset($userData['sales_user_id']) && $user['id']==$userData['sales_user_id']) $selected='selected';
                                                            if($user['sales_structure_id']==1){
                                                               if(!empty($SUS['rsm'][$user['id']])){
                                                                  $texthtml=implode(", ",$SUS['rsm'][$user['id']]);
                                                               }
                                                            }elseif($user['sales_structure_id']==2 || $user['sales_structure_id']==3){
                                                               if(!empty($SUS['zsm'][$user['id']])){
                                                                  $texthtml=$SUS['zsm'][$user['id']];
                                                               }
                                                            }elseif($user['sales_structure_id']==4){
                                                               if(!empty($SUS['asm'][$user['id']])){
                                                                  $texthtml=implode(",",$SUS['asm'][$user['id']]);
                                                               }
                                                            }elseif($user['sales_structure_id']==5){
                                                               if(!empty($SUS['aso'][$user['id']])){
                                                                  $texthtml=implode(",",$SUS['aso'][$user['id']]);
                                                               }
                                                            }
                                                            
                                                            @endphp
                                                            <option value="{{ $user['id'] }}" {{$selected}}>{{ $user['name']." (".$user['short_name'].")" }} {{$texthtml}}</option>
                                                         @endforeach
                                                      @endif
                                                   </select>
                                                </div>
                                             </div>

                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control"  for="discount_category"> Discount Category <span class="required" aria-required="true">*</span></label>
                                                <div class="col-md-6 ">
                                                   <select class="selectize-discount_category" id="discount_category" name="discount_category" placeholder="Select Discount Category" data-placeholder="Select Discount Category">
                                                      <option value="">Select Discount Category</option>
                                                      
                                                      @if(!empty($discountcat))
                                                         @foreach($discountcat as $d)
                                                            @php $selected="";
                                                            if(isset($userData['discount_category']) && $d['id']==$userData['discount_category']) $selected='selected';
                                                            @endphp
                                                            <option value="{{ $d['id'] }}" {{$selected}}>{{ $d['name'] }}</option>
                                                         @endforeach
                                                      @endif
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control"  for="client_type">Type <span class="required" aria-required="true">*</span></label>
                                                <div class="col-md-6 ">
                                                   <select class="selectize-client_type" id="client_type" name="client_type" placeholder="Select Type" data-placeholder="Select Type">
                                                      <option value="">Select Type</option>
                                                      @if(!empty($school_type))
                                                         @foreach($school_type as $s)
                                                         @php $selected="";
                                                            if(isset($userData['client_type']) && $s['id']==$userData['client_type']) $selected='selected';
                                                            @endphp
                                                            <option value="{{ $s['id'] }}" {{$selected}}>{{ $s['name'] }}</option>
                                                         @endforeach
                                                      @endif
                                                   </select>
                                                </div>
                                             </div>
                                             <div id="show_school_data">
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control" for="principal_contact_name">Principal Contact Name</label>
                                                <div class="col-md-3 ">
                                                   <input type="text" id="principal_contact_name" class="form-control " placeholder="Principal Contact Name" name="principal_contact_name" value="{{$userData['principal_contact_name'] ?? ''  }}" >
                                                </div>
                                                <label class="col-md-3 label-control" for="principal_contact_no">Principal Contact Number</label>
                                                <div class="col-md-3 ">
                                                   <input type="text" id="principal_contact_no" class="form-control " placeholder="Principal Contact Number" name="principal_contact_no" value="{{$userData['principal_contact_no'] ?? ''  }}" >
                                                </div>
                                             </div>
                                              <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control" for="account_contact_name">Account Contact Name</label>
                                                <div class="col-md-3 ">
                                                   <input type="text" id="account_contact_name" class="form-control " placeholder="Account Contact Name" name="account_contact_name" value="{{$userData['account_contact_name'] ?? ''  }}" >
                                                </div>
                                                <label class="col-md-3 label-control" for="account_contact_no">Account Contact Number</label>
                                                <div class="col-md-3 ">
                                                   <input type="text" id="account_contact_no" class="form-control " placeholder="Account Contact Number" name="account_contact_no" value="{{$userData['account_contact_no'] ?? ''  }}" >
                                                </div>
                                             </div>
                                              <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control" for="latitude">Latitude</label>
                                                <div class="col-md-3 ">
                                                   <input type="text" id="latitude" class="form-control " placeholder="Latitude" name="latitude" value="{{$userData['latitude'] ?? ''  }}" >
                                                </div>
                                                <label class="col-md-3 label-control" for="longitude">Longitude</label>
                                                <div class="col-md-3 ">
                                                   <input type="text" id="longitude" class="form-control " placeholder="Longitude" name="longitude" value="{{$userData['longitude'] ?? ''  }}" >
                                                </div>
                                             </div>
                                              <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control" for="section_id">Section</label>
                                                <div class="col-md-3">
                                                   <select class="select2 form-control" multiple id="section_id" name="section_id[]" placeholder="Select Section" data-placeholder="Select Section">
                                                      <option value="">Select Section</option>
                                                       @if(!empty($Section))
                                                         @foreach($Section as $s)
                                                            @php 
                                                            $selected = "";
                                                            if( isset($userData['section_id']) && in_array($s['id'], $userData['section_id'])) {
                                                                $selected = 'selected';
                                                            }
                                                            @endphp
                                                            <option value="{{ $s['id'] }}" {{$selected}}>{{ $s['name'] }}</option>
                                                         @endforeach
                                                       @endif
                                                   </select>
                                                </div>
                                                <label class="col-md-3 label-control"  for="school_type_id">School Type </label>
                                                <div class="col-md-3 ">
                                                   <select class="select2 form-control" multiple id="school_type_id" name="school_type_id[]" placeholder="Select School Type" data-placeholder="Select School Type">
                                                      <option value="">Select School Type </option>
                                                         @if(!empty($SchoolType))
                                                         @foreach($SchoolType as $s)
                                                            @php 
                                                            $selected = "";
                                                            if(isset($userData['school_type_id']) && in_array($s['id'], $userData['school_type_id'])) {
                                                                $selected = 'selected';
                                                            }
                                                            @endphp
                                                            <option value="{{ $s['id'] }}" {{$selected}}>{{ $s['name'] }}</option>
                                                         @endforeach
                                                         @endif
                                                   </select>
                                                </div>
                                             </div>
                                               <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control"  for="board">Board</label>
                                                <div class="col-md-3 ">
                                                   <select class="select2 form-control" multiple id="board_id" name="board_id[]" placeholder="Select Board" data-placeholder="Select Board">
                                                      <option value="">Select Board</option>
                                                       @if(!empty($Board))
                                                         @foreach($Board as $s)
                                                            @php 
                                                            $selected = "";
                                                            if(isset($userData['board_id']) && in_array($s['id'], $userData['board_id'])) {
                                                                $selected = 'selected';
                                                            }
                                                            @endphp
                                                            <option value="{{ $s['id'] }}" {{$selected}}>{{ $s['name'] }}</option>
                                                         @endforeach
                                                         @endif
                                                   </select>
                                                </div>
                                                <label class="col-md-3 label-control" for="medium">School Medium </label>
                                                <div class="col-md-3">
                                                   <select class="selectize-medium" id="medium_id" name="medium_id[]" placeholder="Select Medium" multiple data-placeholder="Select Medium">
                                                      <option value="">Select Medium </option>
                                                   </select>
                                                </div>
                                             </div>
                                          </div>
                                              <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control" for="adhar_no">Adhar No.</label>
                                                <div class="col-md-6 ">
                                                   <input type="text" id="adhar_no" class="form-control " placeholder="Adhar No." name="adhar_no" value="{{$userData['adhar_no'] ?? '' }}" >
                                                </div>
                                              </div>
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control"  for="pan_no">PAN No.</label>
                                                <div class="col-md-6 ">
                                                   <input type="text" id="pan_no" class="form-control " placeholder="PAN No." name="pan_no" value="{{ isset($userData['pan_no']) ? $userData['pan_no'] : old('pan_no') }}" >
                                                </div>
                                             </div>
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control"  for="userinput2">GST No. </label>
                                                <div class="col-md-6 ">
                                                   <input type="text" id="gst_no" class="form-control " placeholder="GST No." name="gst_no" value="{{ isset($userData['gst_no']) ? $userData['gst_no'] : old('gst_no') }}" >
                                                   <span class="text-danger"></span>
                                                </div>
                                             </div>
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control"  for="state">State <span class="required" aria-required="true">*</span></label>
                                                <div class="col-md-6 ">
                                                   <select class="selectize-state" id="state" name="state" placeholder="Select State" data-placeholder="Select State">
                                                      <option value="">Select State</option>
                                                      @if(!empty($state))
                                                         @foreach($state as $key=>$d)
                                                         @php $selected="";
                                                            if(isset($userData['state_id']) && $key==$userData['state_id']) $selected='selected';
                                                            @endphp
                                                            <option value="{{ $key }}" {{$selected}}>{{ $d}}</option>
                                                         @endforeach
                                                      @endif
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control"  for="district">District <span class="required" aria-required="true">*</span></label>
                                                <div class="col-md-6 ">
                                                   <select class="selectize-district" id="district" name="district" placeholder="Select District" data-placeholder="Select District">
                                                      <option value="">Select District</option>
                                                      @if(!empty($district))
                                                         @foreach($district as $d)
                                                         @php $selected="";
                                                            if(isset($userData['district_id']) && $d['id']==$userData['district_id']) $selected='selected';
                                                            @endphp
                                                            <option value="{{ $d['id'] }}" {{$selected}}>{{ $d['district_name'] }}</option>
                                                         @endforeach
                                                      @endif
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control"  for="taluka">Taluka <span class="required" aria-required="true">*</span></label>
                                                <div class="col-md-6 ">
                                                   <select class="selectize-taluka" id="taluka" name="taluka" data-seleted="{{ isset($userData['taluka_id']) ? $userData['taluka_id'] :'' }}" placeholder="Select Taluka" data-placeholder="Select Taluka">
                                                      <option value="">Select Taluka</option>
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control"  for="zip_code">Zip <span class="required" aria-required="true">*</span></label>
                                                <div class="col-md-6 ">
                                                   <input type="number" id="zip_code" class="form-control " placeholder="Zip" name="zip_code" value="{{ isset($userData['zip_code']) ? $userData['zip_code'] :'' }}" >
                                                </div>
                                             </div>
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control"  for="userinput2">Image </label>
                                                <div class="col-md-6">
                                                   <div class="col-md-12 custom-file m-0">
                                                   <input type="file" class="custom-file-input form-control" id="image" name="image">
                                                   <label class="custom-file-label" for="image" aria-describedby="imageAddon">Choose Image</label>
                                                   </div>
                                                </div>
                                                 @if(isset($mode) && $mode == 'Update')
                                                   @if(isset($checkImgArr['img_url']) && $checkImgArr['img_url'] != '' &&  $userData['image']!="")
                                                       <div class="col-md-3">
                                                           <a class="fancybox" rel="gallery1" href="{{$checkImgArr['fancy_box_url']}}" title="">
                                                              <img src="{{$checkImgArr['img_url']}}" alt="" class="img-fluid rounded-circle width-50" id="show-image" onerror="isImageExist(this)" noimage="80x80.jpg" />
                                                            </a>
                                                           <a href="javascript:void(0);" class="btn btn-icon ml-1 btn-danger waves-effect waves-light" onclick='deleteUploadedImage();' id="delete-image">
                                                            <i class="icon-close"></i>
                                                            </a>
                                                         </div>
                                                     @endif
                                               @endif
                                             </div>
                                             <div class="form-group row mt-1 align-items-center">
                                             <label class="col-md-3"> </label>
                                               <div class="col-md-5 ml-1">
                                                     <p class="danger">[ Valid extentions: <code>jpeg, png, jpg, gif</code>]</p>
                                               </div>
                                             </div>
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control"  for="status">Status </label>
                                                <div class="col-md-3 ">
                                                <?php $stats1="";if(isset($userData['status'])) $stats1=$userData['status'];?>
                                                   <select class="selectize-status" oldval="<?php if($stats1==1){ echo 'Active';}else if($stats1==2){echo 'Verified';}else if($stats1==3){echo 'Inactive';}?>" id="status" name="status" placeholder="Select Status" data-placeholder="Select Status">
                                                      <option value="1" {{($stats1==1)? 'selected':''}}>Active </option>
                                                      @if(isset($mode) && $mode == 'Update')
                                                      <option value="2" {{ ($stats1==2)? 'selected':''}}>Verified</option>
                                                      @endif
                                                      <option value="3" {{ ($stats1==3)? 'selected':''}}>Inactive</option>
                                                   </select>
                                                </div>
                                             </div>

                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control"  for="kyc_status">KYC Status </label>
                                                <div class="col-md-3 ">
                                                <?php $kyc_status=0;if(isset($userData['kyc_status'])) $kyc_status=$userData['kyc_status'];?>
                                                   <select class="selectize-status" oldval="<?php if($kyc_status==0){ echo 'Pending';}else if($kyc_status==1){echo 'Approved';}else if($kyc_status==2){echo 'Disapproved';}?>" id="kyc_status" name="kyc_status" placeholder="Select KYC Status" data-placeholder="Select KYC Status">
                                                      @foreach($kyc_sataus_array as $kyv_key => $kyc)
                                                      @php $kyc_selected = $kyc_status == $kyc ? 'selected' : ''; @endphp
                                                      <option value="{{ $kyc }}" {{$kyc_selected}}>{{ $kyv_key }}</option>
                                                      @endforeach
                                                   </select>
                                                </div>
                                             </div>

                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-xl-4 col-12">
                                       <div class="form-group row mx-auto">
                                       @if(isset($mode) && $mode == 'Update')
                                          <table class="table table-bordered">
                                             <tbody>
                                                <tr>
                                                   <td><label class="label-view-control">Created</label></td>
                                                   <td class="table-view-control">{{ isset($userData['created_at']) && $userData['created_at'] != ""  ? date('d-M-Y H:i:s',strtotime($userData['created_at'])) : '--' }}</td>
                                                </tr>
                                                <tr>
                                                   <td><label class="label-view-control">Updated</label></td>
                                                   <td class="table-view-control">{{ isset($userData['updated_at']) && $userData['updated_at'] != ""  ? date('d-M-Y H:i:s',strtotime($userData['updated_at'])) : '--' }}</td>
                                                </tr>
                                                @if(!empty($userData['verified_date']) && strtotime($userData['verified_date']) > 0)
                                                <tr>
                                                   <td class=""><label class="label-view-control">Verified</label></td>
                                                   <td class="table-view-control">{{ isset($userData['verified_date']) && $userData['verified_date'] != ""  ? date('d-M-Y',strtotime($userData['verified_date'])) : '--' }}</td>
                                                </tr>
                                                @endif
                                             </tbody>
                                          </table>
                                       @endif
                                       </div>

                                       <div class="form-group">
                                             <h3 class="tab-content-title">KYC Status</h3>
                                             <table class="table table-bordered">                                               
                                                <tbody>
                                                   <tr>                                                      
                                                      <td>Status</td>
                                                      <td><?php if($kyc_status==0){ echo 'Pending';}else if($kyc_status==1){echo 'Approved';}else if($kyc_status==2){echo 'Disapproved';}?></td>
                                                   </tr>
                                                   <tr>
                                                      <td>Approved Date</td>
                                                      <td>{{ !empty($userData['kyc_approved_date']) ? date_getFormattedDateTime($userData['kyc_approved_date']) : '---'}}</td>
                                                   </tr>
                                                </tbody>
                                             </table>
                                          </div>
                                    </div>
                                    <div class="col-xl-4 col-12 d-none">
                                       @if(isset($mode) && $mode == 'Update')
                                       @php  $created_at = (isset($userData[0]['created_at'])) ? date_getFormattedDateTime($userData[0]['created_at']): '---'; @endphp
                                       @php $updated_at = (isset($userData[0]['updated_at']))? date_getFormattedDateTime($userData[0]['updated_at']): '---';
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
                              <div class="tab-pane" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="tab_4"  aria-labelledby="base-tab_4">
                                <div class="row">
                                    <div class="col-xl-12">
                                       <h3 class="tab-content-title">Settings</h3>
                                    </div>
                                 </div>
                                    <div class="row">
                                       <div class="col-xl-8">
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control" for="">Cash Discount Period 1</label>
                                                <div class="col-xl-8">
                                                   <div class="row">
                                                      <div class="col-md-8">
                                                      <div class="form-group">
                                                         <input type="checkbox" class="switchBootstrap" id="cash_discount1" name="cash_discount1" data-on-text="Enable" data-off-text="Disable" data-on-color="{{Config::get('constants.switch_on_color')}}" data-off-color="{{Config::get('constants.switch_off_color')}}" value="1"
                                                         {{ (isset($userData['cash_discount1']) &&($userData['cash_discount1']==1)) ? 'checked' :'' }}/>
                                                      </div>
                                                      </div> 
                                                   </div>
                                                </div>
                                             </div>
                                             
                                             <div class="form-group row cash_discount_1 mx-auto {{ (isset($userData['cash_discount1']) &&($userData['cash_discount1']==1)) ? '' :'d-none' }}">
                                                <label class="col-md-3 label-control" for="">Cash Discount 1 Percentage</label>
                                                <div class="col-xl-8">
                                                   <div class="row">
                                                      <div class="col-md-5">
                                                         <div class="form-group">
                                                            <input type="number" min="0" max="100" maxlength="5" name="cash_discount1_amt" id="cash_discount1_amt" class="form-control" value="{{ isset($userData['cash_discount1_amt']) ? $userData['cash_discount1_amt'] :'' }}">
                                                         </div>
                                                      </div> 
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control" for="">Cash Discount Period 2</label>
                                                <div class="col-xl-8">
                                                   <div class="row">
                                                      <div class="col-md-8">
                                                         <div class="form-group">
                                                         <input type="checkbox" class="switchBootstrap" id="cash_discount2" name="cash_discount2"  data-on-text="Enable" data-off-text="Disable" data-on-color="{{Config::get('constants.switch_on_color')}}" data-off-color="{{Config::get('constants.switch_off_color')}}" value="1" {{ (isset($userData['cash_discount2']) &&($userData['cash_discount2']==1)) ? 'checked' :'' }}/>
                                                         </div>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="form-group row mx-auto cash_discount_2 {{ (isset($userData['cash_discount2']) &&($userData['cash_discount2']==1)) ? '' :'d-none' }}">
                                                <label class="col-md-3 label-control" for="">Cash Discount 2 Percentage</label>
                                                <div class="col-xl-8">
                                                   <div class="row">
                                                      <div class="col-md-5">
                                                      <div class="form-group">
                                                         <input type="number" min="0" max="100" maxlength="5" name="cash_discount2_amt" id="cash_discount2_amt" class="form-control" value="{{ isset($userData['cash_discount2_amt']) ? $userData['cash_discount2_amt'] :'' }}">
                                                      </div>
                                                      </div> 
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control" for="">Total Credit Limit</label>
                                                <div class="col-xl-8">
                                                   <div class="row">
                                                      <div class="col-md-8">
                                                         <input type="number" id="total_credit_limit" class="form-control" placeholder="Total Credit Limit" name="total_credit_limit" value="{{ isset($userData['total_credit_limit']) ? $userData['total_credit_limit'] : old('total_credit_limit') }}" >
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control" for="first_name">Order Approval Limit</label>
                                                <div class="col-xl-8">
                                                   <div class="row">
                                                      <div class="col-md-8">
                                                         <input type="number" id="order_approval_limit" class="form-control " placeholder="Order Approval Limit" name="order_approval_limit" value="{{ isset($userData['order_approval_limit']) ? $userData['order_approval_limit'] : old('order_approval_limit') }}" >
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control" for="grade">Grade <span class="required" aria-required="true">*</span></label>
                                                <div class="col-xl-8">
                                                   <div class="row">
                                                      <div class="col-md-8">
                                                         <select class="selectize-grade" id="grade_id" name="grade_id" placeholder="Select Grade" data-placeholder="Select Grade">
                                                            <option value="">Select Grade</option>
                                                            @if(!empty($gradearray))
                                                               @foreach($gradearray as $v)
                                                               @php $selected="";
                                                               if(isset($userData['grade_id']) && $v['id']==$userData['grade_id']) $selected='selected';
                                                               @endphp
                                                                  <option value="{{$v['id']}}" {{$selected}}>{{$v['name']}}</option>
                                                               @endforeach
                                                            @endif
                                                         </select>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="form-group row mx-auto">
                                                <label class="col-md-3 label-control" for="payment_term_id">Payment Terms <span class="required" aria-required="true">*</span></label>
                                                <div class="col-xl-8">
                                                   <div class="row">
                                                      <div class="col-md-8">
                                                         <select class="selectize-payment_terms" id="payment_term_id" name="payment_term_id" placeholder="Select Payment Terms" data-placeholder="Select Payment Terms">
                                                            <option value="">Select Payment Terms</option>
                                                              @if(!empty($payment_list))
                                                                  @foreach($payment_list as $v)
                                                                  @php $selected="";
                                                                  if(isset($userData['payment_term_id']) && $v['id']==$userData['payment_term_id']) $selected='selected';
                                                                  @endphp
                                                                   <option value="{{$v['id']}}" {{$selected}}>{{$v['term']}}</option>
                                                                  @endforeach
                                                              @endif
                                                         </select>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                             
                                       </div>
                                    </div>
                              </div>
                              <div class="tab-pane" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="tab_5"  aria-labelledby="base-tab_5">
                                <div class="row">
                                    <div class="col-xl-12">
                                       <h3 class="tab-content-title">Status Logs</h3>
                                    </div>
                                    <div class="col-xl-12">
                                    <table id="" class="table table-bordered table-striped" width="100%" style="">
                                    <thead><tr><th>Old Status</th><th>New Status</th><th>Date</th></tr></thead>
                                    <tbody>
                                    @if(empty($LogHistory))
                                       <tr><td colspan="4" class="text-center">No data available</td></tr>
                                    @endif
                                       @if(!empty($LogHistory))
                                          @foreach($LogHistory as $h)
                                          <tr><td>{{$h['old_status']}}</td><td>{{$h['new_status']}}</td><td>{{ isset($h['created_at']) && $h['created_at'] != ""  ? date('d-M-Y H:i:s',strtotime($h['created_at'])) : '--' }}</td></tr>
                                          @endforeach
                                       @endif
                                    </tbody>
                                    </table>
                                    </div>
                                 </div>
                              </div>
                              <div class="tab-pane" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="tab_2"  aria-labelledby="base-tab_2">
                                 <div class="row">
                                    <div class="col-xl-12">
                                       <h3 class="tab-content-title">Contacts</h3>
                                    </div>
                                 </div>
                                 <div class="row">
                                    <div class="col-xl-12">

                                    <div class="text-right"><a href="javascript:void(0)" data-repeater-create="" class="btn btn-success add round btn-min-width mr-1 mb-1" id="add_modal_box_add_contact" data-toggle="modal" data-target="#add_contact">
                                             <i class="ft-plus"></i> Add Contact
                                             </a></div>
                                      <div class="table-responsive">
                                      <div class="deleted_contact"></div>
                                         <table class="table table-hover table-bordered">
                                            <thead>
                                            <tr>
                                                  <th width="25%">Name</th>
                                                  <th width="12%">Mobile No</th>
                                                  <!--<th width="10%">Country</th>
                                                     <th width="10%">State</th>
                                                     <th width="10%">City</th>
                                                     <th width="10%">Zip</th>-->
                                                  <!-- <th width="15%">Zip</th> -->
                                                 <!--  <th width="21%">Contact Person Details</th> -->
                                                  <th width="12%">WhatsApp No</th>
                                                  <th class="text-center" width="11%">Designation</th>
                                                  <th class="text-center" width="11%">Department</th>
                                                  <th class="text-center" width="8%">Is Default?</th>
                                                  <th class="text-center" width="8%">Status</th>
                                                  <th class="text-center" width="13%">Action</th>
                                               </tr>
                                            </thead>
                                            <tbody id="con_data_contact">
                                            @if($tcontact>0)
                                                @foreach($contact as $c)
                                                   <tr>
                                                      <td>
                                                      <input type="hidden" name="contact_editid[]" class="contact_editid" value="{{ $c['id'] }}"><input type="hidden" name="contact_full_name[]" class="contact_full_name" value="{{ $c['full_name'] }}"><input type="hidden" name="contact_mobile_number[]" class="contact_mobile_number" value="{{ $c['mobile_number'] }}"><input type="hidden" name="contact_whatsapp_number[]" class="contact_whatsapp_number" value="{{ $c['whatsapp_number'] }}"><input type="hidden" name="contact_designation_id[]" dhtml="{{ isset($designation[$c['designation_id']]) ? $designation[$c['designation_id']] : '' }}" class="contact_designation_id" value="{{ $c['designation_id'] }}"><input type="hidden" name="contact_dob[]" class="contact_dob" value="{{ $c['dob'] }}"><input type="hidden" name="contact_department[]" class="contact_department" value="{{ $c['department'] }}"><input type="hidden" name="contact_is_default[]" class="contact_is_default" value="{{ $c['is_default'] }}"><input type="hidden" name="contact_status[]" class="contact_status" value="{{ $c['status'] }}">  
                                                       
                                                      {{ $c['full_name'] }}</td>
                                                      <td>{{ $c['mobile_number'] }}</td>
                                                      <td>{{ $c['whatsapp_number'] }}</td>
                                                      <td class="text-center">{{ isset($designation[$c['designation_id']]) ? $designation[$c['designation_id']] : '' }}</td>
                                                      <td class="text-center">{{ $c['department'] }}</td>
                                                      @if($c['is_default']==1)
                                                      <td class="text-center div_is_default"><span class="badge badge-border success round badge-success">Yes</span></td>
                                                      @else
                                                      <td class="text-center div_is_default"><span class="badge badge-border danger round badge-danger">No</span></td>
                                                      @endif
                                                      @if($c['status']==1)
                                                      <td class="text-center"><span class="badge badge-border success round badge-success">Active</span></td>
                                                      @else
                                                      <td class="text-center"><span class="badge badge-border danger round badge-danger">InActive</span></td>
                                                      @endif
                                                     <td class="text-center"><a href="javascript:void(0);" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light editrow"><i class="la la-edit"></i></span></a>
                                                    <a href="javascript:void(0);" class="btn btn-danger deleterow"><i class="ft-trash-2"></i></a></td>
                                                   </tr>
                                                @endforeach
                                             @else
                                             <tr><td valign="top" colspan="9" class="dataTables_empty text-center">No data available</td></tr>
                                               @endif
                                               <!-- <script type="tex/javascript">addr_index++;</script> -->
                                            </tbody>
                                         </table>
                                      </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="tab-pane" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="tab_3"  aria-labelledby="base-tab_3">
                                 <div class="row">
                                    <div class="col-xl-12">
                                       <h3 class="tab-content-title">Addresses</h3>
                                    </div>
                                 </div>
                                 <div class="row">
                                    <div class="col-xl-12">
                                      <div class="text-right"><a href="javascript:void(0)" data-repeater-create="" class="btn btn-success add round btn-min-width mr-1 mb-1" id="add_modal_box_add_address" data-toggle="modal" data-target="#add_address">
                                             <i class="ft-plus"></i> Add Address
                                             </a></div>
                                      <div class="table-responsive">
                                      <div class="deleted_address"></div>
                                         <table class="table table-hover table-bordered">
                                            <thead>
                                               <tr>
                                                  <th width="18%">Name</th>
                                                  <th width="32%">Address</th>
                                                  <!--<th width="10%">Country</th>
                                                     <th width="10%">State</th>
                                                     <th width="10%">City</th>
                                                     <th width="10%">Zip</th>-->
                                                  <!-- <th width="15%">Zip</th> -->
                                                 <!--  <th width="21%">Contact Person Details</th> -->
                                                  <th class="" width="20%">Mobile No./Email</th>
                                                  <th class="text-center" width="8%"><span>Use for Billing?</span> / <br>
                                                  <span>Billing Is Default?</span></th>
                                                  <th class="text-center" width="8%"><span>Use for Shipping?</span> / <br><span>Shipping Is Default?</span></th>
                                                  <th class="text-center" width="4%">Status</th>
                                                  <th class="text-center" width="10%">Action</th>
                                               </tr>
                                            </thead>
                                            <tbody id="con_data_address">
                                            @if($taddress>0)
                                                @foreach($address as $a)
                                                   <tr>
                                                      <td>
                                                      <input type="hidden" name="address_editid[]" class="address_editid" value="{{$a['id']}}">
                                                      <input type="hidden" name="address_title[]"  class="address_title" value="{{$a['title']}}">
                                                      <input type="hidden" name="address_mobile_number[]" class="address_mobile_number" value="{{$a['mobile_number']}}">
                                                      <input type="hidden" name="address_address1[]" class="address_address1" value="{{$a['address1']}}">
                                                      <input type="hidden" class="address_address2" name="address_address2[]" value="{{$a['address2']}}">
                                                      <input type="hidden" name="address_email[]" class="address_email" value="{{$a['email']}}">
                                                      <input type="hidden" name="address_state1[]" dhtml="{{ isset($state[$a['state_id']]) ? $state[$a['state_id']] : ''}}" class="address_state1" value="{{$a['state_id']}}">
                                                      <input type="hidden" name="address_district1[]" dhtml="{{ isset($districta[$a['district_id']]) ? $districta[$a['district_id']] : ''}}" class="address_district1" value="{{$a['district_id']}}">
                                                      <input type="hidden" class="address_taluka1" dhtml="{{$talukae[$a['taluka_id']]}}" name="address_taluka1[]" value="{{$a['taluka_id']}}">
                                                      <input type="hidden" name="address_zip_code[]" class="address_zip_code" value="{{$a['zip_code']}}">
                                                      <input type="hidden" name="address_used_for_billing[]" class="address_used_for_billing" value="{{$a['use_for_billing']}}">
                                                      <input type="hidden" name="address_used_for_shipping[]" class="address_used_for_shipping" value="{{$a['use_for_shipping']}}">
                                                      <input type="hidden" name="address_is_default_billing[]" class="address_is_default_billing" value="{{$a['is_default_billing']}}">
                                                      <input type="hidden" name="address_is_default_shipping[]" class="address_is_default_shipping" value="{{$a['is_default_shipping']}}">
                                                      <input type="hidden" name="address_status[]" class="address_status" value="{{$a['status']}}">   
                                                      {{ $a['title'] }}</td>
                                                      <td>{{ $a['address1'] }}, {{ $a['address2'] }} <br>{{ isset($state[$a['state_id']]) ? $state[$a['state_id']] : "" }}, {{ isset($districta[$a['district_id']]) ? $districta[$a['district_id']] : "" }}, {{ isset($talukae[$a['taluka_id']]) ? $talukae[$a['taluka_id']] : "" }}, {{ $a['zip_code'] }}</td>
                                                      <td><i class="material-icons info font-medium-4">phone</i>{{ $a['mobile_number']}}<br><i class="material-icons info font-medium-4">email</i> {{ $a['email']}}</td>
                                                      <td class="text-center">
                                                         @if($a['use_for_billing']==1)
                                                            <span class="badge badge-border success round badge-success">Yes</span><br>
                                                         @else
                                                            <span class="badge badge-border danger round badge-danger">No</span><br>
                                                         @endif
                                                         @if($a['is_default_billing']==1)
                                                         <div class="div_is_default_billing">
                                                            <span class="badge badge-border success round badge-success">Yes</span></div>
                                                         @else
                                                         <div class="div_is_default_billing">
                                                            <span class="badge badge-border danger round badge-danger">No</span></div>
                                                         @endif
                                                      </td>
                                                      <td class="text-center">
                                                         @if($a['use_for_shipping']==1)
                                                         <span class="badge badge-border success round badge-success">Yes</span><br>
                                                         @else
                                                         <span class="badge badge-border danger round badge-danger">No</span><br>
                                                         @endif
                                                         @if($a['is_default_shipping']==1)
                                                         <div class="div_is_default_shipping">
                                                            <span class="badge badge-border success round badge-success">Yes</span></div>
                                                         @else
                                                         <div class="div_is_default_shipping">
                                                            <span class="badge badge-border danger round badge-danger">No</span></div>
                                                         @endif
                                                         </td>
                                                      <td class="text-center">
                                                      @if($a['status']==1)
                                                      <span class="badge badge-border success round badge-success">Active</span>
                                                      @else
                                                      <span class="badge badge-border danger round badge-danger">InActive</span>
                                                      @endif
                                                      </td>
                                                      <td class="text-center"><a href="javascript:void(0);" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light editrow"><i class="la la-edit"></i></span></a><a href="javascript:void(0);" class="btn btn-danger deleterow"><i class="ft-trash-2"></i></a></td>
                                                   </tr>
                                                @endforeach
                                               @else
                                               <tr><td valign="top" colspan="8" class="dataTables_empty text-center">No data available</td></tr>
                                                @endif
                                               <!-- <script type="tex/javascript">addr_index++;</script> -->
                                               <!-- <script type="tex/javascript">addr_index++;</script> -->
                                            </tbody>
                                         </table>
                                      </div>
                                    </div>
                                 </div>
                              </div>

                               <div class="tab-pane " aria-expanded="true" role="tabpanel" id="tab_6" aria-labelledby="base-tab_5">
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
                                               <label class="col-md-3 label-control">Username <span class="required" aria-required="true">*</span> </label>
                                               <div class="col-md-9">
                                                   <input type="text" id="username" class="form-control" placeholder="Username" name="username" value="{{ isset($userData['username']) ? $userData['username'] : old('username') }}" >
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
                                               <label class="col-md-3 label-control" for="userinput2">Password <span class="required">*</span></label>
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
                                       </div>
                                       </div>
                                       </div>
                                 </div>
                             </div>
                              
                             <div class="tab-pane" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="tab_7"  aria-labelledby="base-tab_7">
                                <div class="row">
                                    <div class="col-xl-12">
                                       <h3 class="tab-content-title">Documents</h3>
                                    </div>
                                    <div class="col-xl-12">
                                    <table id="" class="table table-bordered table-striped" width="100%" style="">
                                    <thead>
                                       <tr>
                                          <th class="text-center" style="width: 1%;">#</th>
                                          <th>Document</th>
                                          <th style="width: 40%;">Notes</th>
                                          <th style="width: 40%;">File @if(isset($client_document_ext_array))
                                                      <label class="danger">[ Valid extentions: <code>
                                                         @foreach ($client_document_ext_array as $value)
                                                                {{ $loop->first ? '' : ', ' }}
                                                                <span class="nice">.{{ $value }}</span>
                                                            @endforeach
                                                      </code>]</label>
                                                   @endif    </th>
                                          <th class="text-center">Uploaded By</th>
                                          <th class="text-center">Verified?</th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                                                 
                                    @if(empty($ClientDocuments) || !isset($ClientDocuments))
                                       <tr><td colspan="6" class="text-center">No data available</td></tr>
                                    @endif
                                       @if(!empty($ClientDocuments))
                                          @foreach($ClientDocuments as $key => $document)
                                          <tr>
                                             <input type="hidden" name="document[{{$document['id']}}][id]" value="{{$document['id']}}">
                                             <input type="hidden" id="old_doc_file_{{$document['client_document_id']}}" name="document[{{$document['id']}}][old_doc_file]" value="{{$document['file_name']}}">
                                             <td>{{$key+1}}</td>
                                             <td>{{$document['title']}}</td>
                                             <td>
                                                <textarea class="form-control" rows="1" placeholder="Notes" name="document[{{$document['id']}}][notes]">{{$document['notes']}}</textarea>
                                             </td>
                                             <td class="text-center">
                                                <div class="form-group row mx-auto">
                                                   <div class=" col-md-6 custom-file m-0">
                                                    <input type="file" class="custom-file-input form-control" accept="{{$client_document_ext}}" id="document_file_{{$document['id']}}" name="document_file[{{$document['id']}}]">
                                                   <label class="custom-file-label" for="document_file_{{$document['id']}}" aria-describedby="imageAddon">Choose Document</label>                                                   
                                                  </div>
                                                @if(!empty($document['file_name']))
                                                <div class=" col-md-6 custom-file m-0" id="document_link_{{$document['client_document_id']}}">
                                                   <div class="btn-group" role="group" aria-label="Basic example">
                                                        <a  href="{{$document['document_link']}}" download title="Download" class="btn btn-icon btn-primary"><i class="la la-cloud-download"></i></a>
                                                        <a href="{{$document['document_link']}}" target="_blank" title="View" class="btn btn-icon btn-info"><i class="la la-eye"></i></a>
                                                        <a  href="javascript:void(0)" onclick="deleteDocumentUploadedFile(this.id)" id="{{$document['client_document_id']}}" class="btn btn-icon btn-danger"><i class="icon-close"></i></a>
                                                    </div>                                                
                                                </div>
                                                @endif
                                                </div>
                                             </td>
                                             <td class="text-center">
                                                {{$document['updated_by']}}
                                             </td>  
                                             <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                   <input type="checkbox" {{ $document['is_verified'] ? 'checked' : '' }} value="1" name="document[{{$document['id']}}][is_verified]" class="custom-control-input" id="is_verified{{$document['id']}}">
                                                   <label class="custom-control-label" for="is_verified{{$document['id']}}"></label>
                                                </div>
                                             </td>                                           
                                          </tr>
                                          @endforeach
                                       @endif
                                    
                                    </tbody>
                                    </table>
                                    </div>
                                 </div>
                              </div>

                              <div class="form-actions text-right">
                                 <a href="{{route('client/grid')}}">
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
{{----- Start Popup for add address -----}}
<div class="modal fade text-left" id="add_address" tabindex="-1" role="dialog" aria-labelledby="myModaladdAddress" aria-hidden="true" data-focus-on="input:first" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-success white">
          <h3 class="modal-title white" id="myModaladdAddress"><span id="mode_title"></span> Address </h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <form name="frmaddaddress" id="frmaddaddress" action="">
        <input type="hidden" name="address_editid" class="" id="editid" value="">
        <input type="hidden" name="address_addid" class="" id="addid" value="1">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control" for="title">Name&nbsp;<span class="required" aria-required="true">*</span></div>
                  <div class="col-sm-9">
                    <input type="text" id="title" class="form-control " placeholder="Name" name="title" value="" maxlength="100">
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control" for="address1">Address&nbsp;<span class="required" aria-required="true">*</span></div>
                     <div class="col-sm-9">
                        <input type="text" id="address1" class="form-control clearfix" placeholder="Address 1" name="address1" id="address1" value="" maxlength="200">
                     </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control" for="address1"></div>
                     <div class="col-sm-9">
                        <input type="text" id="address2" class="form-control " placeholder="Address 2" name="address2" id="address2" value="" maxlength="200">
                     </div>   
                  </div>   
               </div>   
            </div>   
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control" for="mobile_number">Mobile No.&nbsp;<span class="required" aria-required="true">*</span></div>
                  <div class="col-sm-5 has-icon-left">
                    <input type="number" id="mobile_number" class="form-control " placeholder="Mobile No." name="mobile_number" value="" maxlength="12">
                    <div class="form-control-position">
                        <i class="material-icons info font-medium-4">call</i>
                     </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control" for="address2">Email</div>
                  <div class="col-sm-9 has-icon-left">
                    <input type="email" id="email" class="form-control" placeholder="Email" name="email" value="" maxlength="200">
                     <div class="form-control-position">
                        <i class="material-icons info font-medium-4">email</i>
                     </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control" for="state">State <span class="required" aria-required="true">*</span></div>
                  <div class="col-sm-5">
                  <select class="selectize-state1" id="state1" name="state1" placeholder="Select state" data-placeholder="Select state">
                      <option value="">Select State</option>
                        @if(!empty($state))
                           @foreach($state as $key=>$d)
                              <option value="{{ $key }}">{{ $d }}</option>
                           @endforeach
                        @endif
                  </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control" for="state">District <span class="required" aria-required="true">*</span></div>
                  <div class="col-sm-5">
                  <select class="selectize-district1" id="district1" name="district1" placeholder="Select District" data-placeholder="Select District">
                      <option value="">Select District</option>
                  </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control" for="state">Taluka <span class="required" aria-required="true">*</span></div>
                  <div class="col-sm-5">
                     <select class="selectize-taluka1" id="taluka1" name="taluka1" placeholder="Select Taluka" data-placeholder="Select Taluka">
                        <option value="">Select Taluka</option>
                     </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control" for="zip_code">Zip Code <span class="required" aria-required="true">*</span></div>
                  <div class="col-sm-5">
                    <input type="number" id="zip_code" class="form-control " placeholder="Zip Code" name="zip_code" value="" maxlength="12">
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control" for=""></div>
                     <div class="col-sm-4">
                        <div class="form-group">
                           <div class="custom-control custom-checkbox">
                              <input type="checkbox" value="1" name="used_for_billing" class="custom-control-input" id="used_for_billing">
                              <label class="custom-control-label" for="used_for_billing"></label>Used for Billing
                           </div>
                        </div>
                     </div>
                     <div class="col-sm-4">
                        <div class="form-group">
                           <div class="custom-control custom-checkbox d-none">
                              <input type="checkbox" value="1" name="is_default_billing" class="custom-control-input" id="is_default_billing">
                              <label class="custom-control-label" for="is_default_billing"></label>Is Default ?
                           </div>
                        </div>
                     </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control"></div>
                     <div class="col-sm-4">
                        <div class="form-group">
                           <div class="custom-control custom-checkbox">
                              <input type="checkbox" value="1" name="used_for_shipping" class="custom-control-input" id="used_for_shipping">
                              <label class="custom-control-label" for="used_for_shipping"></label>Used for Shipping
                           </div>
                        </div>
                     </div>
                     <div class="col-sm-4">
                        <div class="form-group">
                           <div class="custom-control custom-checkbox  d-none">
                              <input type="checkbox" value="1" name="is_default_shipping" class="custom-control-input" id="is_default_shipping">
                              <label class="custom-control-label" for="is_default_shipping"></label>Is Default ?
                           </div>
                        </div>
                     </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control"></div>
                     <div class="col-sm-4">
                        <div class="form-group">
                           <div class="custom-control custom-checkbox">
                              <input type="checkbox" value="1" name="is_approved" class="custom-control-input" id="is_approved">
                              <label class="custom-control-label" for="is_approved"></label>IS Approved ?
                           </div>
                        </div>
                     </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                <div for="status" class="col-sm-3 label-control">Status <span class="required"></span></div>
                <div class="controls col-sm-9">
                  <div class="form-group">
                    <input type="checkbox" class="switchBootstrap" id="status" name="status" data-on-text="Active" data-off-text="Inactive" data-on-color="{{Config::get('constants.switch_on_color')}}" data-off-color="{{Config::get('constants.switch_off_color')}}" value="1" checked/>
                  </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
               <button type="button" id="btn_close_modal" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
               <button type="submit" class="btn btn-success" id="save_record">Save changes</button>
            </div>
          </div>
      </form>
      </div>
    </div>
</div>
  {{----- End Popup for add address -----}}
  {{----- Start add Contact poup  -----}}
  <div class="modal fade text-left" id="add_contact" tabindex="-1" role="dialog" aria-labelledby="myModaladdContact" aria-hidden="true" data-focus-on="input:first" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-success white">
          <h3 class="modal-title white" id="myModaladdContact"><span id="mode_title"></span> Contact </h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <form name="frmaddcontact" id="frmaddcontact" action="">
        <input type="hidden" name="contact_editid" class="" id="editid" value="">
        <input type="hidden" name="contact_addid" class="" id="addid" value="1">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control" for="full_name">Full Name <span class="required" aria-required="true">*</span></div>
                  <div class="col-sm-9">
                    <input type="text" id="full_name" class="form-control " placeholder="Full Name" name="full_name" value="" maxlength="150">
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control" for="mobile_number">Mobile No. <span class="required" aria-required="true">*</span></div>
                  <div class="col-sm-5 has-icon-left">
                    <input type="number" id="mobile_number" class="form-control " placeholder="Mobile No." name="mobile_number" value="" maxlength="12">
                     <div class="form-control-position">
                        <i class="material-icons info font-medium-4">call</i>
                     </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control" for="whatsapp_number">WhatsApp No. <span class="required" aria-required="true">*</span></div>
                  <div class="col-sm-5 has-icon-left">
                    <input type="number" id="whatsapp_number" class="form-control " placeholder="WhatsApp No." name="whatsapp_number" value="" maxlength="10">
                    <div class="form-control-position">
                        <i class="la la-whatsapp info font-medium-4"></i>
                     </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control" for="designation_id">Designation</div>
                  <div class="col-sm-5">
                  <select class="selectize-designation_id" id="designation_id" name="designation_id" placeholder="Select Designation" data-placeholder="Select Designation"><option value="" seleted>Select Designation</option>
                    @if(!empty($designation))
                     @foreach($designation as $key=>$d)
                        <option value="{{ $key }}">{{ $d }}</option>
                     @endforeach
                    @endif
                  </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control" for="department">Department</div>
                  <div class="col-sm-5">
                    <input type="text" id="department" class="form-control " placeholder="Department" name="department" value="" maxlength="50">
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control" for="department">Birth Date <span class="required" aria-required="true">*</span></div>
                  <div class="col-sm-5">
                     <input type="date" class="form-control" id="date1" name="date1">
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control" for="department"></div>
                  <div class="col-sm-9">
                     <div class="form-group"><!-- is_default --->
                           <div class="custom-control custom-checkbox">
                              <input type="checkbox" value="1" name="is_default" class="custom-control-input" id="is_default">
                              <label class="custom-control-label" for="is_default"></label>Is Default ?
                           </div>
                     </div>
                  </div>
                </div>
              </div>
            </div>              
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                <div for="status" class="col-sm-3 label-control">Status <span class="required"></span></div>
                <div class="controls col-sm-9">
                  <div class="form-group">
                    <input type="checkbox" class="switchBootstrap" id="status" name="status" data-on-text="Active" data-off-text="Inactive" data-on-color="{{Config::get('constants.switch_on_color')}}" data-off-color="{{Config::get('constants.switch_off_color')}}" value="1" checked/>
                  </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
               <button type="button" id="btn_close_modal" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
               <button type="submit" class="btn btn-success" id="save_record">Save changes</button>
            </div>
          </div>
      </div>
      </form>
    </div>
  </div>
  </div>
  
  {{----- End add Contact poup  -----}}
  {{----- Start verified popup ------}}
  <div class="modal fade modal-sm m-auto" id="verifiedpopup" tabindex="-1" role="dialog" aria-labelledby="myModaladdVerify" aria-hidden="true" data-focus-on="input:first" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header bg-success white">
            <h3 class="modal-title white" id="myModaladdContact"><span id="mode_title"></span> Verification Date </h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
         </div>
         <form name="frmaddVerify" id="frmaddVerify" action="">
            <div class="modal-body">
               <div class="row">
               <div class="col-md-12">
                  <div class="form-group row mx-auto">
                        <div class="col-sm-5 label-control" for="vdate">Verified Date</div>
                        <div class="col-sm-7">
                           <input type="date" class="form-control" id="vdate" name="vdate" value="{{ (isset($userData['verified_date']) && strtotime($userData['verified_date']) > 0 ) ? date('d-m-Y',strtotime($userData['verified_date'])) : ''}}" >
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" id="close_verified" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
               <button type="submit" class="btn btn-success" id="save_verified">Save changes</button>
            </div>
         </form> 
         </div>
      </div>
   </div>
{{----- End verified popup ------}}
@endsection
@section('scripts')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script>
   var csrf_token = "{{ csrf_token() }}";
   var districtlist = "{{ route('admin/master/districts/districtslist') }}";
   var stateurl = "{{ route('admin/master/districtslist') }}";
   var talukaurl = "{{ route('taluka/talukalist') }}";
   var mediumlist = "{{ route('admin/master/mediumlist') }}";
   var board_id= <?php echo json_encode($board_id_arr)?>;
   var medium_id = <?php echo isset($userData['medium_id']) ? json_encode($userData['medium_id']) : json_encode(array());?>;
   var PASSWORD_MIN    = "{{config('constants.PASSWORD_MIN')}}";
   var PASSWORD_MAX    = "{{config('constants.PASSWORD_MAX')}}";
   var PASSWORD_FORMAT = {{config('constants.PASSWORD_FORMAT')}} 
</script>
<!-- Start FORM VALIDATION -->
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/additional-methods.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('assets/pages/scripts/admin/common/select2.full.min.js') }}"></script>
<script src="{{ asset('/assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/js/scripts/forms/select/form-select2.js') }}" type="text/javascript"></script>
<!-- End FORM VALIDATION -->
<!-- End Selectize -->
<!--Start Bootstrap Switch--->
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/toggle/bootstrap-switch.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/toggle/switchery.min.js') }}"></script>
<!--End Bootstrap Switch--->
<script src="{{ asset('/assets/js/scripts/forms/custom-file-input.js') }}"></script>
<!-- END PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="{{ asset('/assets/vendors/js/fancybox/dist/jquery.fancybox.js')}}"></script>
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('/assets/pages/scripts/admin/client/client_add.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection