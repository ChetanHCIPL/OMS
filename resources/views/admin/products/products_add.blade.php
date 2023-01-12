@extends('layouts.admin')
@section('styles')
@endsection
@section('content')
@php
$products_path = Config::get('path.products_path'); 
$image = isset($data[0]['flag'])?$data[0]['flag']:"";
@endphp
<?php 
$head = 'Product';
if (Route::current()->getName() == 'kit') {
   $head = 'Kit';
}
?>
<div class="content-wrapper">
<div class="content-body">
<section class="content-header clearfix">
   <h3>{{$head}} <strong><span class="text-muted accent-3">{{((isset($data[0]['name'])?' - '.reduceTitleName($data[0]['name']):''))}}</span></strong></h3>
   <ol class="breadcrumb">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li><a href="javascript:void(0);">Product Mgmt</a></li>
      <li><a href="{{route('products/grid')}}">Product</a></li>
      <li><a href="javascript:void(0);">{{(isset($mode)?$mode:'') }} {{$head}}</a></li>
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
                     <a href="{{route('products/grid')}}">
                     <button type="button" class="btn mr-1 back_btn">
                     <span class="material-icons">arrow_back_ios</span> Back  </button>
                     </a>
                     <?php /*@if (per_hasModuleAccess('Products', 'View'))
                        <a href="{{route('products',['mode' => 'view', 'id' => isset($data[0]['id'])?gen_generate_encoded_str($data[0]['id'], '3', '3', ''):''])}}" title="View"><span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-eye"></i></span></a>@endif */?>
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
                     <input type="hidden" name="flag_old" id="flag_old" value="{{(isset($data[0]['image'])?$data[0]['image']:'')}}">
                     <input type="hidden" id="id" name="id" value="{{(isset($id)?$id:'')}}" />
                     <input type="hidden" id="type_of_product" name="type_of_product" 
                     value="<?php if ($head == 'Kit'){ echo 1; }else{ echo 0; }?>"/>
                     <div class="row">
                        <div class="col-xl-2 col-lg-3 col-md-12 col-12">
                           <div class="sidebar-left site-setting">
                              <div id="accordionWrap5" role="tablist" aria-multiselectable="true">
                                 <div class="card collapse-icon accordion-icon-rotate">
                                    <div id="heading51" class="card-header">
                                       <a data-toggle="collapse" href="#accordion51" aria-expanded="true" aria-controls="accordion51" class="card-title lead">{{$head}}</a>
                                    </div>
                                    <div id="accordion51" role="tabpanel" data-parent="#accordionWrap5" aria-labelledby="heading51" class="card-collapse collapse show" aria-expanded="true">
                                       <div class="card-body">
                                          <ul class="nav nav-tabs m-0">
                                             <li class="nav-item">
                                                <a class="nav-link active" id="base-tab_1" data-toggle="tab" aria-controls="tab_1" href="#tab_1" aria-expanded="true">
                                                General Information </a>
                                             </li>
                                             <li class="nav-item">
                                                <a class="nav-link" id="base-tab_2" data-toggle="tab" aria-controls="tab_2" href="#tab_2" aria-expanded="true">
                                                Description </a>
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
                              <div class="tab-pane" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="tab_2"  aria-labelledby="base-tab_2">
                                 <div class="row">
                                    <div class="col-xl-12">
                                       <h3 class="tab-content-title">Description</h3>
                                    </div>
                                 </div>
                                 <div class="row">
                                    <div class="col-xl-8">
                                       <div class="form-box">
                                          <div class="form-body">
                                             <div class="form-group row">
                                                <div class="col-md-3">Description</div>
                                                <div class="col-md-9">
                                                   <textarea form="frmadd" cols="30" class="form-control ckeditor" id="description" name="description" rows="15">{{(isset($data[0]['description'])?$data[0]['description']:'')}}</textarea>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="tab-pane active" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="tab_1"  aria-labelledby="base-tab_1">
                                 <div class="row">
                                    <div class="col-xl-7">
                                       <div class="row">
                                          <div class="col-xl-12">
                                             <h3 c1lass="tab-content-title">General Information</h3>
                                          </div>
                                       </div>
                                       <div class="row form-box p-1">
                                          <div class="form-body">
                                             <div class="form-group row align-items-center">
                                                <div class="col-md-3"><label class="custom-label">{{$head}} Name&nbsp;<span class="required">*</span></label></div>
                                                <div class="col-md-9">
                                                   <input class="form-control" placeholder="{{$head}} Name" type="text" id="name" name="name" value="{{(isset($data[0]['name'])?$data[0]['name']:'')}}">
                                                </div>
                                             </div>
                                             <div class="form-group row align-items-center">
                                                <div class="col-md-3"><label class="custom-label">Product Head&nbsp;<span class="required">*</span></label></div>
                                                <div class="col-md-9">
                                                   <select class="form-control" id="product_head_id" name="product_head_id" data-placeholder="Select Product Head" placeholder="Select Product Head" required>
                                                      <!---	  <option value="">Select Product Head </option>----->
                                                      <option value="">Select Product Head</option>
                                                      @if(!empty($productHead))
                                                      @php $cnt_cou = count($productHead); @endphp
                                                      @for($i=0;$i<$cnt_cou;$i++)
                                                      @if(isset($data[0]['product_head_id']) && $data[0]['product_head_id'] == $productHead[$i]['id'])
                                                      @php $selected = "selected"; @endphp
                                                      @else
                                                      @php $selected = ""; @endphp
                                                      @endif
                                                      <option value="{{$productHead[$i]['id']}}" {{$selected}}>{{$productHead[$i]['name']}} </option>
                                                      @endfor
                                                      @endif
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="form-group row align-items-center">
                                                <div class="col-md-3"><label class="custom-label">Series&nbsp;<span class="required">*</span></label></div>
                                                <div class="col-md-9">
                                                   <select class="form-control" id="series_id" name="series_id" placeholder="Select Series" data-placeholder="Select Series">
                                                      <option value="">Select Series</option>
                                                      <!----<option value="">Select Series </option>------->
                                                      @if(!empty($series))
                                                      @php $cnt_cou = count($series); @endphp
                                                      @for($i=0;$i<$cnt_cou;$i++)
                                                      @if(isset($data[0]['series_id']) && $data[0]['series_id'] == $series[$i]['id'])
                                                      @php $selected = "selected"; @endphp
                                                      @else
                                                      @php $selected = ""; @endphp
                                                      @endif
                                                      <option value="{{$series[$i]['id']}}" {{$selected}}>{{$series[$i]['name']}} </option>
                                                      @endfor
                                                      @endif
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="form-group row align-items-center">
                                                <div class="col-md-3"><label class="custom-label">{{$head}} Code&nbsp;<span class="required">*</span></label></div>
                                                <div class="col-md-6">
                                                   <input class="form-control" placeholder="{{$head}} Code" type="text" id="products_code" name="products_code" value="{{(isset($data[0]['code'])?$data[0]['code']:'')}}">
                                                </div>
                                             </div>
                                             <div class="form-group row align-items-center">
                                                <div class="col-md-3"><label class="custom-label">QR Code</label></div>
                                                <div class="col-md-9">
                                                   <input class="form-control" placeholder="QR Code" type="text" id="qrcode" name="qrcode" value="{{(isset($data[0]['qrcode'])?$data[0]['qrcode']:'')}}">
                                                </div>
                                             </div>
                                             <div class="form-group row align-items-center">
                                                <div class="col-md-3"><label class="custom-label">HSN Number&nbsp;<span class="required">*</span></label></div>
                                                <div class="col-md-6">
                                                   <input class="form-control" placeholder="HSN Number" type="text" id="hsn_number" name="hsn_number" value="{{(isset($data[0]['hsn_number'])?$data[0]['hsn_number']:'')}}">
                                                </div>
                                             </div>
                                             <div class="form-group row align-items-center">
                                                <div class="col-md-3 col-10"><label class="custom-label">Version No</label></div>
                                                <div class="col-md-6 col-auto">
                                                   <input class="form-control" placeholder="Version No" type="number" id="version_no" name="version_no" value="{{(isset($data[0]['version_no'])?$data[0]['version_no']:'')}}">
                                                </div>
                                             </div>
                                             <div class="form-group row align-items-center">
                                                <div class="col-md-3"><label class="custom-label">Max Order Qty&nbsp;<span class="required">*</span></label></div>
                                                <div class="col-md-6">
                                                   <input class="form-control" placeholder="Max Order Qty" type="number" id="max_order_qty" name="max_order_qty" value="{{(isset($data[0]['max_order_qty'])?$data[0]['max_order_qty']:'')}}">
                                                </div>
                                             </div>
                                             <div class="form-group row align-items-center">
                                                <div class="col-md-3"><label class="custom-label">Row No.</label></div>
                                                <div class="col-md-6">
                                                   <input class="form-control" placeholder="Row Stock" type="text" id="row_stock" name="row_stock" value="{{(isset($data[0]['row_stock'])?$data[0]['row_stock']:'')}}" maxlength="20">
                                                </div>
                                             </div>
                                             <div class="form-group row align-items-center">
                                                <div class="col-md-3"><label class="custom-label">Current Stock&nbsp;
                                                <?php if(isset($mode) && $mode != 'Update'){ ?>
                                                   <span class="required">*</span>
                                                <?php } ?>
                                                </label></div>
                                                <div class="col-md-6">
                                                   <?php if(isset($mode) && $mode == 'Update'){
                                                      ?>
                                                      <label>{{(isset($data[0]['stock'])?$data[0]['stock']:'')}}</label>
                                                      <?php
                                                      }else{ ?>
                                                      <input class="form-control" placeholder="Current Stock" <?php if(isset($mode) && $mode == 'Update'){ ?> disabled="disabled" <?php } ?> type="number" id="stock" name="stock" value="{{(isset($data[0]['stock'])?$data[0]['stock']:'')}}">
                                                      <?php
                                                   } ?>
                                                </div>
                                             </div>
                                             <div class="form-group row align-items-center">
                                                <div class="col-md-3"><label class="custom-label">MRP&nbsp;<span class="required">*</span></label></div>
                                                <div class="col-md-3">
                                                   <input class="form-control" placeholder="MRP" type="number" id="mrp" name="mrp" value="{{(isset($data[0]['mrp'])?$data[0]['mrp']:'')}}">
                                                </div>
                                                <div class="col-md-3"><label class="custom-label">Weight (Gram)&nbsp;<span class="required">*</span></label></div>
                                                <div class="col-md-3">
                                                   <input class="form-control" placeholder="Weight" type="number" id="weight" name="weight" value="{{(isset($data[0]['weight'])?$data[0]['weight']:'')}}">
                                                </div>
                                             </div>
                                             @if ($head=='Product')
                                             <div class="form-group row align-items-center">
                                                <div class="col-md-3"><label class="custom-label">Badho&nbsp;<span class="required">*</span></label></div>
                                                <div class="col-md-3">
                                                   <input class="form-control" placeholder="Badho" type="number" id="badho" name="badho" value="{{(isset($data[0]['badho'])?$data[0]['badho']:'')}}">
                                                </div>
                                                <div class="col-md-3"><label class="custom-label">Pages&nbsp;<span class="required">*</span></label></div>
                                                <div class="col-md-3">
                                                   <input class="form-control" placeholder="Pages" type="number" id="pages" name="pages" value="{{(isset($data[0]['pages'])?$data[0]['pages']:'')}}">
                                                </div>
                                             </div>
                                             @endif
                                             <div class="form-group row align-items-center mt-2">
                                                <div class="col-md-3 col-10"><label class="custom-label">Lock For Order</label></div>
                                                <div class="col-md-3 col-auto">
                                                   <div class="d-inline-block custom-control custom-radio">
                                                      <input class="custom-control-input " name="lock_for_order" id="lockfororderyes" type="radio" value="1" {{ ((isset($data[0]['lock_for_order']) && $data[0]['lock_for_order']==1)?'checked':'') }}>
                                                      <label class="custom-control-label" for="lockfororderyes">Yes</label>
                                                   </div>
                                                   <div class="d-inline-block custom-control custom-radio">
                                                      <input class="custom-control-input " name="lock_for_order" id="lockorno" type="radio" value="0" {{ ((isset($data[0]['lock_for_order']) && $data[0]['lock_for_order']==1)?'':'checked') }}>
                                                      <label class="custom-control-label" for="lockorno">No</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-3 col-10"><label class="custom-label">Lock For CN</label></div>
                                                <div class="col-md-3 col-auto">
                                                   <div class="d-inline-block custom-control custom-radio">
                                                      <input class="custom-control-input" name="cn_lock" id="lockforcnyes" type="radio" value="1"  {{ ((isset($data[0]['cn_lock']) && $data[0]['cn_lock']==1)?'checked':'') }}>
                                                      <label class="custom-control-label" for="lockforcnyes">Yes</label>
                                                   </div>
                                                   <div class="d-inline-block custom-control custom-radio">
                                                      <input class="custom-control-input" name="cn_lock" id="lockforcnno" type="radio" value="0" {{ ((isset($data[0]['cn_lock']) && $data[0]['cn_lock']==1)?'':'checked') }}>
                                                      <label class="custom-control-label" for="lockforcnno">No</label>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="form-group row align-items-center mt-1">
                                                <div class="col-md-3 col-10"><label class="custom-label">Stock Alert</label></div>
                                                <div class="col-md-3  position-relative has-icon-left mobile-country">
                                                   <div class="d-inline-block custom-control custom-radio">
                                                      <input class="custom-control-input stockalert" name="stock_alert" id="stockalertyes" type="radio" value="1" {{ ((isset($data[0]['stock_alert']) && $data[0]['stock_alert']==1)?'checked':'') }}>
                                                      <label class="custom-control-label" for="stockalertyes">Yes</label>
                                                   </div>
                                                   <div class="d-inline-block custom-control custom-radio">
                                                      <input class="custom-control-input stockalert" name="stock_alert" id="stockalertno" type="radio" value="0" {{ ((isset($data[0]['stock_alert']) && $data[0]['stock_alert']==1)?'':'checked') }}>
                                                      <label class="custom-control-label" for="stockalertno">No</label>
                                                   </div>
                                                </div>
                                                <div class="col-md-3 col-10 Stockq {{ ((isset($data[0]['stock_alert']) && $data[0]['stock_alert']==1)?'':'d-none') }} "><label class="custom-label">Stock Alert Qty&nbsp;<span class="required">*</span></label></div>
                                                <div class="col-md-3 col-auto Stockq {{ ((isset($data[0]['stock_alert']) && $data[0]['stock_alert']==1)?'':'d-none') }}">
                                                   <input class="form-control" placeholder="Stock Alert Qty" type="number" id="stock_alert_qty" name="stock_alert_qty" value="{{(isset($data[0]['stock_alert_qty'])?$data[0]['stock_alert_qty']:'')}}">
                                                </div>
                                             </div>
                                             <div class="form-group row align-items-center">
                                                <div class="col-md-3"><label class="custom-label">Product Image</label></div>
                                                <div class="col-md-4 custom-file">
                                                   <input type="file" class="custom-file-input form-control" id="image" name="image">
                                                   <label class="custom-file-label" for="image" aria-describedby="imageAddon">Choose Image</label>
                                                </div>
                                                @if(isset($mode) && $mode == 'Update')
                                                @if(isset($checkImgArr['img_url']) && $checkImgArr['img_url'] != '')
                                                <div class="col-md-3">
                                                   <a class="fancybox" rel="gallery1" href="{{$checkImgArr['fancy_box_url']}}" title=""><img src="{{$checkImgArr['img_url']}}" alt="" class="img-fluid rounded-circle width-50" id="show-image" onerror="isImageExist(this)" noimage="80x80.jpg" /></a>
                                                   <a href="javascript:void(0);" class="btn btn-icon ml-1 btn-danger waves-effect waves-light" onclick='deleteUploadedImage();' id="delete-image"><i class="icon-close"></i></a>
                                                </div>
                                                @endif
                                                @endif
                                             </div>
                                             <div class="form-group row mt-1 align-items-center">
                                                <label class="col-md-3"> </label>
                                                <div class="col-md-9">
                                                   <p class="danger">[ Valid extentions: <code>{{!empty($img_ext_array)?$img_ext_array:''}}</code>]</p>
                                                </div>
                                             </div>
                                             <div class="form-group row align-items-center">
                                                <div class="col-md-3 col-4"><label class="custom-label">Status</label></div>
                                                <div class="col-md-9 col-auto ml-auto">
                                                   <input type="checkbox" class="switchBootstrap" id="status" name="status" data-on-text="Active" data-off-text="Inactive" data-on-color="{{Config::get('constants.switch_on_color')}}" data-off-color="{{Config::get('constants.switch_off_color')}}" value="1"
                                                   {{((isset($data[0]['status']) && $data[0]['status'] == 1 )?'checked':($mode == 'Add')?'checked':'')}}/>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-xl-5 col-12">
                                       <div class="row">
                                          <div class="col-xl-12">
                                             <h3 class="tab-content-title">
                                                @if($head=='Kit')
                                                   Products Mapping
                                                @elseif($head=='Product')
                                                   Segment Mapping
                                                @endif
                                             </h3>
                                          </div>
                                       </div>
                                       @if($head=='Product')
                                          <div class="form-body clearfix">
                                             <div class="table-responsive list-product ">
                                                <?php 
                                                   $last_index = 1;
                                                   if(isset($mode) && $mode == 'Update'){
                                                      $last_index = count($productMapping) + 1;
                                                   }
                                                ?>
                                                <table class="table table-bordered main_awarness_test_table" id="segmentsem" data-num="<?php echo $last_index; ?>" cellpadding="0" cellspacing="0" rowspan="0">
                                                   <thead>
                                                      <th class="text-center att-option-wd-2 th_head1">#</th>
                                                      <th class="att-option-wd-40 th_head2 w-auto">Board Medium</th>
                                                      <th class="att-option-wd-10 th_head3 text-center">Segment</th>
                                                      <th class="text-center att-option-wd-10 th_head4 text-center">Semester</th>
                                                      <th class="text-center att-option-wd-10 th_head5 text-center">Action</th>
                                                   </thead>
                                                   <tbody class="dataajax">
                                                      <?php 
                                                      if(isset($mode) && $mode == 'Update'){
                                                         $i=1;
                                                         if ($productMapping) {
                                                         foreach($productMapping as $mapping){
                                                         ?>
                                                         <tr>
                                                            <td class="text-center">
                                                               {{$i}}
                                                            </td>
                                                            <td>
                                                               <select class="select2" name="medium[]" placeholder="Select Medium" data-placeholder="Select Medium" >
                                                                  @if(!empty($mediumBoard))
                                                                  @foreach($mediumBoard as $item)
                                                                  <option value="{{$item['id']}}" @if($item['id'] == $mapping['medium_board_id']) selected @endif >{{$item['name']}} - {{$item['board_name']}} </option>
                                                                  @endforeach
                                                                  @endif
                                                               </select>
                                                            </td>
                                                            <td class="text-center">
                                                               <select class="form-control" name="segment[]" placeholder="Select Segment" data-placeholder="Select Segment">
                                                                  @if(!empty($segment))
                                                                  @foreach($segment as $item)
                                                                  <option value="{{$item['id']}}" @if($item['id'] == $mapping['segment_id']) selected @endif >{{$item['name']}} </option>
                                                                  @endforeach
                                                                  @endif
                                                               </select>
                                                            </td>
                                                            <td class="text-center">
                                                               <select class="form-control" name="semester[]" placeholder="Select Semester" data-placeholder="Select Semester" >
                                                                  <option value="">Select Semester</option>
                                                                  @if(!empty($SemsList))
                                                                  @foreach($SemsList as $item)
                                                                  <option value="{{$item['id']}}" @if($item['id'] == $mapping['semester_id']) selected @endif >{{$item['name']}} </option>
                                                                  @endforeach
                                                                  @endif
                                                               </select>
                                                            </td>
                                                            <td>
                                                            <a href="javascript:void(0);" class="btn btn-danger deletesegme"><i class="ft-trash-2"></i></a> 
                                                            </td>
                                                         </tr>
                                                         <?php
                                                         $i++;
                                                         }
                                                         }
                                                      }?>
                                                   </tbody>
                                                   <tbody id="optn_details" class=" repeater">
                                                      <tr>
                                                         <td class="text-center"><?php echo $last_index; ?></td>
                                                         <td>
                                                            <select class="select2" id="Medium2" name="Medium2[]" placeholder="Select Medium" data-placeholder="Select Medium" >
                                                               <!----<option value="">Select Series </option>----->
                                                               <!----<option value="">Select Series </option>----->
                                                               @if(!empty($mediumBoard))
                                                               @foreach($mediumBoard as $item)
                                                               <option value="{{$item['id']}}">{{$item['name']}} - {{$item['board_name']}} </option>
                                                               @endforeach
                                                               @endif
                                                            </select>
                                                         </td>
                                                         <td>
                                                            <select class="form-control" id="segment1" name="segment1[]" placeholder="Select Segment" data-placeholder="Select Segment">
                                                               <!----<option value="">Select Series </option>------>
                                                               @if(!empty($segment))
                                                               @foreach($segment as $item)
                                                               <option value="{{$item['id']}}" {{$selected}}>{{$item['name']}} </option>
                                                               @endforeach
                                                               @endif
                                                            </select>
                                                         </td>
                                                         <td>
                                                            <select class="form-control" id="semester2" name="semester2[]" placeholder="Select Semester" data-placeholder="Select Semester" >
                                                               <option value="">Select Semester</option>
                                                               @if(!empty($SemsList))
                                                               @foreach($SemsList as $item)
                                                               <option value="{{$item['id']}}" {{$selected}}>{{$item['name']}} </option>
                                                               @endforeach
                                                               @endif
                                                            </select>
                                                         </td>
                                                         <td>  </td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </div>
                                             <button type="button" class="btn btn-primary waves-effect waves-light addplusclick pull-right" id="add-question">
                                             <i class="ft-plus"></i> Add more
                                             </button>
                                          </div>
                                       @elseif($head=='Kit')
                                          <div class="form-body clearfix">
                                             <div class="table-responsive list-product ">
                                                <?php 
                                                   $last_index = 1;
                                                   if(isset($mode) && $mode == 'Update'){
                                                      $last_index = count($kit_products) + 1;
                                                   }
                                                ?>
                                                <table class="table table-bordered main_awarness_test_table product-mapping-tbl" id="segmentsem" data-num="<?php echo $last_index; ?>" cellpadding="0" cellspacing="0" rowspan="0">
                                                   <thead>
                                                      <th class="text-center att-option-wd-2 th_head1">#</th>
                                                      <th class="att-option-wd-30 th_head2 w-auto">Products</th>
                                                      <th class="text-center att-option-wd-40 th_head4 text-center">Quantity</th>
                                                      <th class="text-center att-option-wd-10 th_head5 text-center">Action</th>
                                                   </thead>
                                                   <tbody class="dataajax">
                                                      <?php 
                                                      if(isset($mode) && $mode == 'Update'){
                                                         $i=1;
                                                         if ($kit_products) {
                                                            foreach($kit_products as $kit_mapping){
                                                            ?>
                                                            <tr>
                                                               <td class="text-center">
                                                                  {{$i}}
                                                               </td>
                                                               <td>
                                                                  <select class="select2" name="product_ids[]" placeholder="Select Products">
                                                                     <option value="" disabled> Select Product</option>
                                                                     @if(!empty($allProduct))
                                                                        @foreach($allProduct as $product)
                                                                           <option value="{{$product->id}}" @if($product->id == $kit_mapping['product_id']) selected @endif data-maxquantity='{{$product->max_order_qty}}'>{{$product->name}} - ₹ {{$product->mrp}}</option>
                                                                        @endforeach
                                                                     @endif
                                                                  </select>
                                                               </td>
                                                               <td class="text-center">
                                                                  <input type="number" class="quantity input-sm form-control" id="product_qty_{{$i}}" name="product_quantity[]" min="1" value="{{$kit_mapping['quantity']}}" data-attr="{{$i}}" />
                                                                  <!-- <span id="product_quantity_{{$i}}" class="help-block help-block-error product-quantity-error"></span> -->
                                                               </td>
                                                               <td>
                                                                  <a href="javascript:void(0);" class="btn btn-danger deletesegme"><i class="ft-trash-2"></i></a> 
                                                               </td>
                                                            </tr>
                                                            <?php
                                                            $i++;
                                                            }
                                                         }
                                                      }?>
                                                   </tbody>
                                                   <tbody id="optn_details" class=" repeater">
                                                      <tr>
                                                         <td class="text-center"><?php echo $last_index; ?></td>
                                                         <td>
                                                            <select class="select2" name="temp_product_id" id="product_id" placeholder="Select Products">
                                                               <option value="" disabled> Select Product</option>
                                                               @if($allProduct)
                                                                  @foreach($allProduct as $product)
                                                                     <option value="{{$product->id}}" data-maxquantity='{{$product->max_order_qty}}'>{{$product->name}} - ₹ {{$product->mrp}}</option>
                                                                  @endforeach
                                                               @endif
                                                            </select>
                                                         </td>
                                                         <td>
                                                            <fieldset>
                                                               <input type="number" class="quantity input-sm form-control" id="product_quantity" name="temp_product_q" min="1" value="1" />
                                                            </fieldset>
                                                         </td>
                                                         <td>

                                                         </td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </div>
                                             <button type="button" class="btn btn-primary waves-effect waves-light addProductclick pull-right" id="add-question">
                                             <i class="ft-plus"></i> Add more
                                             </button>
                                          </div>
                                       @endif

                                       <?php if (is_array($subProduct) && !empty($subProduct[0]['kit_product_id'])): ?>
                                          <div class="form-group">
                                             <h3 class="tab-content-title">Available in Kit</h3>
                                             <table class="table table-bordered">
                                                <thead>
                                                   <th class="text-center att-option-wd-2 th_head1">#</th>
                                                   <th class="att-option-wd-40 th_head2 w-auto">Kit Name</th>
                                                   <th class="text-center att-option-wd-10 th_head5 text-center">Action</th>
                                                </thead>
                                                <?php
                                                   $i=1;
                                                   foreach($subProduct as $kit_product) :  
                                                      $encoded_id = gen_generate_encoded_str($kit_product['kit_product_id'], '3', '3', '');
                                                      ?>
                                                      <tr>
                                                         <td>{{$i}}</td>
                                                         <td><label class="label-view-control">{{$kit_product['name']}} </label></td>
                                                         <td><label class="label-view-control"><a target="_black" href="{{route('kit',['mode' => 'edit', 'id' => $encoded_id])}}"><span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-eye"></i></span></a> </label></td>
                                                      </tr>
                                                   <?php endforeach; ?>
                                             </table>
                                          </div><hr>
                                       <?php endif; ?>                                          
                                       @if(isset($mode) && $mode == 'Update')
                                       @php  $created_at = (isset($data[0]['created_at'])) ? date_getFormattedDateTime($data[0]['created_at']): '---'; @endphp
                                       @php $updated_at = (isset($data[0]['updated_at']))? date_getFormattedDateTime($data[0]['updated_at']): '---';
                                       @endphp
                                       <div class="form-group">
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
                                 <a href="{{route('products/grid')}}">
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
<script type="text/javascript"> 
    var mediumBoard = @json($mediumBoard);
    var segment = @json($segment);
    var semester = @json($SemsList);
    var allProducts = @json($allProduct);
</script> 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- Start FORM VALIDATION -->
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/additional-methods.min.js')}}" type="text/javascript"></script>
<!-- End FORM VALIDATION -->
<!-- Start Selectize -->
<script src="{{ asset('/assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/js/scripts/forms/select/form-select2.js') }}" type="text/javascript"></script>
<!-- End Selectize -->
<!--Start Bootstrap Switch--->
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/toggle/bootstrap-switch.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/toggle/switchery.min.js') }}"></script>
<!--End Bootstrap Switch--->
<script src="{{ asset('/assets/js/scripts/forms/custom-file-input.js') }}"></script>
<!-- END PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="{{ asset('/assets/vendors/js/fancybox/dist/jquery.fancybox.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/spinner/jquery.bootstrap-touchspin.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/scripts/forms/input-groups.js')}}"></script>
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('/assets/pages/scripts/admin/products/products_add.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection