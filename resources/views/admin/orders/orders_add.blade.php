@extends('layouts.admin')
@section('styles')

<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.default.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/plugins/forms/selectize/selectize.css')}}" rel="stylesheet" type="text/css" />
<!--End Selectize-->
<!--Start Bootstrap Switch-->
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/switchery.min.css')}}">
<link href="{{ asset('assets/css/core/colors/palette-switch.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/fancybox/dist/jquery.fancybox.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('/assets/vendors/css/fancybox/dist/jquery.fancybox.min.css')}}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
@php
$country_path = Config::get('path.country_path'); 
$image = isset($data[0]['flag'])?$data[0]['flag']:"";
$mode = 'Add';
@endphp
<div class="content-wrapper">
   <div class="content-body">
      <section class="content-header clearfix">
         <h3>Add Order <strong><span class="text-muted accent-3">{{((isset($data[0]['country_name'])?' - '.reduceTitleName($data[0]['country_name']):''))}}</span></strong></h3>
         <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="javascript:void(0);">Orders Mgmt</a></li>
            <li><a href="{{route('orders/list')}}">Order</a></li>
            <li><a href="javascript:void(0);">{{(isset($mode)?$mode:'') }} Order</a></li>
         </ol>
      </section>
      <section class="horizontal-grid" id="horizontal-grid">
         <div class="row">
            <div class="col-md-12">
               <div class="card">
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
                              <div class="col-xl-12 col-lg-12 col-md-6 col-12">
                                 <div class="container-fluid">
                                    <h3>Client Info</h3>
                                    <hr>
                                    <div class="row form-box m-0">
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <label for="order_date" class="label-control col-md-3">Order Date <span class="required">*</span></label>
                                             <div class="col-md-8">
                                                <input type="date" class="form-control h_date" id="order_date" name="order_date" value="<?php echo date('Y-m-d') ?>">                                                
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="row form-box m-0">
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3 label-control">Client Name <span class="required">*</span></label>
                                             <div class="col-md-8">
                                             <select id="client_id" name="client_id" placeholder="Select Client Name">
                                                   {{-- <option value="">Select Client</option>
                                                   @if(!empty($ClientList))
                                                      @foreach($ClientList as $cArry)
                                                          <option data-data='{"d_category_id": {{$cArry["discount_category"]}} }' value="{{ $cArry['id']}}">{{ $cArry['client_name'] }}</option>
                                                      @endforeach
                                                   @endif --}}
                                                </select>                                                
                                                <div><span id= "select_cliect_name_error"></span></div>
                                                <input type="hidden" value="" name="client_discount_category_id" id="client_discount_category_id">
                                             </div>
                                             @if(per_hasModuleAccess('Client', 'Add'))
                                             <div class="col-md-1">
                                                <a href="{{route('client',['mode' => 'add', 'id' => ''])}}" class="btn btn-success"><i class="ft-plus"></i></a>
                                             </div>
                                             @endif
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3 label-control">Billing Address <span class="required">*</span></label>
                                             <div class="col-md-8">
                                                @php $selectedAddress = isset($data[0]['client_address_id']) ? $data[0]['client_address_id'] : ''; @endphp
                                                <input type="hidden" id="selected_address_id" value="<?php echo $selectedAddress ?>">
                                                <select class="selectize-select" id="client_address_id" name="client_address_id" placeholder="Select Billing Address">
                                                   <option value="">Select address</option>
                                                </select>
                                             </div>
                                             <div class="col-md-1">
                                             <button  type="button" class="btn btn-success" id="add_modal_box_address"><i class="ft-plus"></i></button>
                                             </div>
                                          </div>
                                          <div class="form-group mx-auto" style="display:none;" id="billing_address">
                                             <div class="form-group row mx-auto billing-address">
                                                <small class="col-md-3 label-control">Billing Name :</small>
                                                <div class="col-md-9">
                                                   <small class="label-control billing-name"></small>
                                                </div>
                                                <small class="col-md-3 label-control">Address :</small>
                                                <div class="col-md-9">
                                                   <small class="label-control full-address"></small>
                                                </div>
                                                <?php /*
                                                <small class="col-md-3 label-control">Address1 :</small>
                                                <div class="col-md-9">
                                                   <small class="label-control address1"></small>
                                                </div>
                                                <small class="col-md-3 label-control">Address2 :</small>
                                                <div class="col-md-9">
                                                   <small class="label-control address2"></small>
                                                </div>
                                                <small class="col-md-3 label-control">Country :</small>
                                                <div class="col-md-9">
                                                   <small class="label-control country"></small>
                                                </div>
                                                <small class="col-md-3 label-control">State :</small>
                                                <div class="col-md-9">
                                                   <small class="label-control state"></small>
                                                </div>
                                                <small class="col-md-3 label-control">District :</small>
                                                <div class="col-md-9">
                                                   <small class="label-control district"></small>
                                                </div>
                                                <small class="col-md-3 label-control">Taluka :</small>
                                                <div class="col-md-9">
                                                   <small class="label-control taluka"></small>
                                                </div>
                                                <small class="col-md-3 label-control">Pincode :</small>
                                                <div class="col-md-9">
                                                   <small class="label-control pincode"></small>
                                                </div>*/?>
                                             </div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="label-control col-md-3">Contact Name <span class="required">*</span></label>
                                             <div class="col-md-8">
                                                 @php $selectedContact = isset($data[0]['client_contact_id']) ? $data[0]['client_contact_id'] : ''; @endphp
                                                <input type="hidden" id="selected_contact_id" value="<?php echo $selectedContact ?>">
                                                <select class="selectize-select" id="client_contact_id" name="client_contact_id" placeholder="Select Contact Name & Number">
                                                   <option value="">Contact Name</option>
                                                </select>
                                             </div>
                                             <div class="col-md-1">
                                                <a href="#" class="btn btn-success"><i class="ft-plus"></i></a>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3 label-control">Shipping Address <span class="required">*</span></label>
                                             <div class="col-md-8">
                                                @php $selectedShipAddress = isset($data[0]['client_ship_address_id']) ? $data[0]['client_ship_address_id'] : ''; @endphp
                                                <input type="hidden" id="selected_ship_address_id" value="<?php echo $selectedShipAddress ?>">
                                                <select class="selectize-select" id="client_ship_address_id" name="client_ship_address_id" placeholder="Select Shipping Address">
                                                   <option value="">Select Shipping Address</option>
                                                </select>
                                             </div>
                                             <div class="col-md-1">
                                                <a href="#" class="btn btn-success"><i class="ft-plus"></i></a>
                                             </div>
                                          </div>
                                          <div class="form-group mx-auto" style="display:none;" id="shipping_address">
                                          <small class="col-md-3 label-control">Shipping Name :</small>
                                                <div class="col-md-9">
                                                   <small class="label-control shipping-name"></small>
                                                </div>
                                             <div class="form-group row mx-auto">
                                                <small class="col-md-3 label-control">Address :</small>
                                                <div class="col-md-9">
                                                   <small class="label-control full-address"></small>
                                                </div><?php /*
                                                <small class="col-md-3 label-control">Address2 :</small>
                                                <div class="col-md-9">
                                                   <small class="label-control address2"></small>
                                                </div>
                                                <small class="col-md-3 label-control">Country :</small>
                                                <div class="col-md-9">
                                                   <small class="label-control country"></small>
                                                </div>
                                                <small class="col-md-3 label-control">State :</small>
                                                <div class="col-md-9">
                                                   <small class="label-control state"></small>
                                                </div>
                                                <small class="col-md-3 label-control">District :</small>
                                                <div class="col-md-9">
                                                   <small class="label-control district"></small>
                                                </div>
                                                <small class="col-md-3 label-control">Taluka :</small>
                                                <div class="col-md-9">
                                                   <small class="label-control taluka"></small>
                                                </div>
                                                <small class="col-md-3 label-control">Pincode :</small>
                                                <div class="col-md-9">
                                                   <small class="label-control pincode"></small>
                                                </div>*/?>
                                             </div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3 label-control">Sales Name <span class="required">*</span></label>
                                             <div class="col-md-8">
                                                <select class="selectize-select" id="sales_user_id" name="sales_user_id" placeholder="Sales Name ">
                                                   <option value="">Sales Name</option>
                                                   @if(!empty($salesUsers))
                                                      @foreach($salesUsers as $user)
                                                      @php
                                                      $texthtml="";
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
                                                          <option value="{{ $user['id'] }}">{{ $user['name'] }} ({{$texthtml}})</option>
                                                      @endforeach
                                                   @endif
                                                </select>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="row mt-1 products-container">
                                       <div class="col-md-12">
                                            <h3>Products</h3>
                                       </div>
									         </div>
                                    <hr>
                                    <div class="row form-box m-0">
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <label class="label-control col-md-3">Product Head <span class="required">*</span></label>
                                             <div class="col-md-9">
                                                <select class="selectize-select" id="product_head" name="product_head" placeholder="Select Product Head">
                                                   @if(!empty($productHeads))
                                                      @foreach($productHeads as $pArry)
                                                         <option value="{{ $pArry['id'] }}">{{ $pArry['name'] }}</option>
                                                      @endforeach
                                                   @endif
                                                </select>
                                             </div>
                                          </div>
                                          
                                          <div id="product_filters" style="display:none">
                                             <div class="form-group row mx-auto">
                                                <label class="label-control col-md-3">Board Medium </label>
                                                <div class="col-md-9">
                                                   <select class="select2 form-control " multiple id="medium_id" name="medium_id" placeholder="Select Medium">
                                                      <option value="" disabled="disabled">Select Board Medium</option>
                                                      @if(!empty($mediums))
                                                         @foreach($mediums as $mArry)
                                                            <option value="{{ $mArry['id'] }}">{{ $mArry['board_name'].' - '.$mArry['name'] }}</option>
                                                         @endforeach
                                                      @endif
                                                   </select>
                                                </div>
                                             </div>                                             
                                             <div class="form-group row mx-auto">
                                                <label class="label-control col-md-3">Std / Segment </label>
                                                <div class="col-md-9">
                                                   <select class="select2 form-control" multiple id="segment" name="segment" placeholder="Select Std / Segment ">
                                                      <option value="" disabled="disabled">Select Std/Segment</option>
                                                      @if(!empty($segment))
                                                         @foreach($segment as $sArry)
                                                             <option value="{{ $sArry['id'] }}">{{ $sArry['name'] }}</option>
                                                         @endforeach
                                                      @endif
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="form-group row mx-auto">
                                                <label class="label-control col-md-3">Series (Mentor) </label>
                                                <div class="col-md-9">
                                                   <select class="selectize-select" id="series" name="series" placeholder="Select Series ">
                                                      <option value="">Select Series</option>
                                                      @if(!empty($series))
                                                         @foreach($series as $sArry)
                                                             <option value="{{ $sArry['id'] }}">{{ $sArry['name'] }}</option>
                                                         @endforeach
                                                      @endif
                                                   </select>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="form-group row text-right">
                                             <div class="col-md-12">
												            <a href="javascript:void(0)" id="filter_btn">
                                                   <button type="button" id="btn-label" class="btn btn-primary">
                                                      <i class="ft-filter"></i> Show Filter</button>
                                                </a>
                                                <a href="javascript:void(0)" id="apply_filter">
                                                   <button type="button" id="btn-label" class="btn btn-success">
                                                      Apply Filter
                                                   </button>
                                                </a>
                                             </div>                                             
                                          </div>
                                       </div>
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3 label-control">Products <span class="required">*</span></label>
                                             <div class="col-md-8">
                                                <select class="selectize-select" multiple id="product" name="product" placeholder="Select Products ">
                                                   <option value="">Select Products</option>
                                                   <!-- @if(!empty($products))
                                                      @foreach($products as $pArry)
                                                          <option value="{{ $pArry->id }}">{{ $pArry->name }}</option>
                                                      @endforeach
                                                   @endif -->
                                                </select>
                                             </div>
                                             @if(per_hasModuleAccess('Products', 'Add'))
                                             <div class="col-md-1">
                                                <a href= "{{route('products',['mode' => 'add', 'id' => ''])}}" class="btn btn-success"><i class="ft-plus"></i></a>
                                             </div>
                                             @endif
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3 label-control" for="quantity">Default Quantity</label>
                                             <div class="col-md-8">
                                                <input type="number" min=0 class="form-control" placeholder="Default Quantity." id="default_quantity">
                                             </div>
                                          </div>
                                          <div class="row text-right">
                                             <div class="col-md-12">
                                                <a href="#!" data-repeater-create="" class="btn btn-danger remove round btn-min-width mr-1 mb-1">
                                                <i class="ft-minus-square"></i> Delete Last Product(s)
                                                </a>
                                                <a href="javascript:void(0);" data-repeater-create="" class="btn btn-success add round btn-min-width mr-1 mb-1 add-products-btn">
                                                <i class="ft-plus"></i> Add Products
                                                </a>
                                             </div>
                                          </div>
                                          <div class="row text-right order-error-container">
                                             <div class="col-md-12">
                                                <span id="order_error" class="error">Please add product(s) with quantity.</span>
                                             </div>
                                          </div>
                                       </div>
                                    </div>

                                    <div class="row" id="order_products" style="display:none;">
                                       <div class="table-responsive container-fluid">
                                          <table class="table table-striped">
                                             <thead>
                                                <tr>
                                                   <th class="text-center" width="5%" scope="col">#</th>
                                                   <th class="text-center" width="5%" scope="col">Remove</th>
                                                   <th width="40%" scope="col">Product Name</th>
                                                   <th width="10%" scope="col">SKU</th>
                                                   <th width="10%" scope="col">Quantity</th>
                                                   <th class="text-right" width="10%" scope="col">Rate</th>
                                                   <th width="10%" scope="col">Discount (%)</th>
                                                   <th class="text-right" width="10%" scope="col">Amount</th>
                                                </tr>
                                             </thead>
                                             <tbody>
                                             </tbody>
                                          </table>
                                          <div class=row>
                                             <div class="col-md-4">
                                                <table class="table table-bordered table-striped">
                                                   <tbody>
                                                      <tr>
                                                         <td class="text-right" width="60%">Total Weight (in Grams)</td>
                                                         <td><b class="total-weight"></b></td>
                                                      </tr>
                                                      <tr>
                                                         <td class="text-right">Total Quantity</td>
                                                         <td><b class="total-quantity"></b></td>
                                                      </tr>
                                                   </tbody>
                                                </table>   
                                             </div>
                                             <div class="col-md-4"></div>
                                             <div class="col-md-4">
                                             <table class="table table-bordered table-striped">
                                                <tbody>
                                                   <tr>
                                                      <td class="text-right" width="60%">Sub Total</td>
                                                      <td>
                                                         <input type="hidden" name="sub_total_value" id="sub_total_value" />
                                                         <b id="sub_total">Rs. 0.00</b>
                                                      </td>
                                                   </tr>
                                                   <tr>
                                                      <td class="text-right">Total Discount</td>
                                                      <td>
                                                         <input type="hidden" name="dis_total_value" id="dis_total_value" />
                                                         <b id="dis_total">(-) Rs. 0.00</b>
                                                      </td>
                                                   </tr>
                                                   <tr>
                                                      <td class="text-right">Less Adv./Adj (-)</td>
                                                      <td><b id="less_adj">Rs. 0.00</b></td>
                                                   </tr>
                                                   <tr>
                                                      <td class="text-right">Net Total</td>
                                                      <td>
                                                         <input type="hidden" name="order_total" id="order_total" />
                                                         <b id="net_total">Rs. 0.00</b>
                                                      </td>
                                                   </tr>
                                                   
                                                </tbody>
                                             </table>
                                             </div>
                                          </div>                                          
                                       </div>
                                    </div>
                                    <div class="row mt-1">
                                       <div class="col-md-12">
                                          <h3>Order Form</h3>
                                       </div>
                                    </div>
                                    <hr>
                                    <div class="row form-box m-0">
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <label class="label-control col-md-3" for="proposalTitle1">Upload Order Form :</label>
                                             <div class="col-md-9">
                                                <input type="file" class="form-control-file" name="order_form_photo" id="exampleInputFile">
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="row">
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <h3 class="mt-1">Dispatch</h3>
                                             <hr>
                                          </div>
                                       </div>
                                    </div>
                                    <hr>
                                    <div class="row form-box m-0 mt-2">
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <label class="label-control col-md-3" for="date1">Dispatch Date <span class="required">*</span></label>
                                             <div class="col-md-9">
                                                <input type="date" class="form-control h_date" id="dispatch_date" name="dispatch_date" value="<?php echo date('Y-m-d') ?>">
                                             </div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="label-control col-md-3" for="transporter">Prefered Transporter<span class="required">*</span></label>
                                             <div class="col-md-9">
                                                <select class="selectize-select" id="transporter" data-placeholder="Select Transporter" name="transporter">
                                                   <option value="" >Select Transporter</option>
                                                   @if($transporters)
                                                      @foreach($transporters as $transporter)
                                                         <option value="{{$transporter['id']}}" >{{$transporter['name']}}</option>
                                                      @endforeach                                                      
                                                   @endif
                                                </select>
                                             </div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="label-control col-md-3" for="route">Route Area <span class="required">*</span></label>
                                             <div class="col-md-9">
                                                <select class="selectize-select" id="route_area" data-placeholder="Select Route Area" name="route_area">
                                                   <option value="r1">Ahmedabad-morbi</option>
                                                   <option value="r2">Ahmedabad-rajkot-jamnager</option>
                                                   <option value="r3">Jetpur-shomnath</option>
                                                   <option value="r4">Bhuj</option>
                                                </select>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <label class="label-control col-md-3" for="payment_due_days">Payment Due Days <span class="required">*</span></label>
                                             <div class="col-md-3">
                                                <select class="selectize-select" id="payment_due_days" name="payment_due_days">
                                                   <option value="" disabled="disabled">Payment Terms</option>
                                                   @if($paymentTerms)
                                                      @foreach($paymentTerms as $paymentTerm)
                                                         <option data-data='{"days": {{$paymentTerm["due_type_value"]}} }' value="{{$paymentTerm['id']}}">{{$paymentTerm['term']}}</option>
                                                      @endforeach
                                                   @endif
                                                </select>
                                             </div>
                                             <label class="label-control col-md-3" for="date2">Due Date <span class="required">*</span></label>
                                             <div class="col-md-3">
                                                <input type="date" class="form-control h_date" id="due_date" name="due_date" value="">
                                             </div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="label-control col-md-3" for="order-remark">Order Remark</label>
                                             <div class="col-md-9">
                                                <fieldset class="form-group">
                                                   <textarea class="form-control" id="order-remark" name="order_remark" rows="3"></textarea>
                                                </fieldset>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="tab-content">
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
{{----- Start Popup for add address -----}}
<button type="button" class="btn btn-outline-info block btn-lg d-none" data-backdrop="static" data-keyboard="false" id="add_address" data-toggle="modal" data-target="#add_client_address">Launch Modal</button>
<div class="modal fade text-left" id="add_client_address" tabindex="-1" role="dialog" aria-labelledby="myModaladdAddress" aria-hidden="true" data-focus-on="input:first" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-success white">
          <h3 class="modal-title white" id="myModaladdAddress"><span id="mode_title"></span> Address </h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <form name="frmaddaddress" id="frmaddaddress" action="">
        <input type="hidden" name="address_editid" class="" id="editid" value="">
        <input type="hidden" name="address_addid" class="" id="addid" value="1">
         <input type="hidden" name="_token" value="{{csrf_token()}}" />
         <input type="hidden" name="customActionName" value="CustomerAddressAdd" />
          <input type="hidden" id="client_ids" class="client_id" name="client_ids" value=""/>
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
                  <div class="col-sm-5">
                    <input type="number" id="mobile_number" class="form-control " placeholder="Mobile No." name="mobile_number" value="" maxlength="12">
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group row mx-auto">
                  <div class="col-sm-3 label-control" for="address2">Email</div>
                  <div class="col-sm-9">
                    <input type="email" id="email" class="form-control" placeholder="Email" name="email" value="" maxlength="200">
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
                           <div class="custom-control custom-checkbox">
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
                           <div class="custom-control custom-checkbox approve">
                              <input type="checkbox" value="1" name="is_approved" class="custom-control-input" id="is_approved">
                              <label class="custom-control-label" for="is_approved"></label>IS Approved ?
                           </div>
                        </div>
                     </div>
                </div>
              </div>
            </div>
              <div class="row">
                  <label class="col-md-3 label-control">Status</label>
                  <div class="col-md-9">
                    <input type="checkbox" class="switchBootstrap" id="status" name="status" data-on-text="Active" data-off-text="Inactive" data-on-color="{{Config::get('constants.switch_on_color')}}" data-off-color="{{Config::get('constants.switch_off_color')}}" value="1"
                     {{((isset($data[0]['status']) && $data[0]['status'] == 1 )?'checked':($mode == 'Add')?'checked':'')}}/>
                  </div>
               </div>
            <div class="modal-footer">
               <button type="button" id="btn_close_modal" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
               <button type="submit" class="btn btn-success" id="submit_record">Save changes</button>
            </div>
          </div>
      </form>
      </div>
    </div>
</div>
  {{----- End Popup for add address -----}}
@endsection
@section('scripts')
<script type="text/javascript"> 
    var csrf_token = "{{ csrf_token() }}";
    var addresslist = "{{ route('client/addresslist') }}";
    var addressDetail = "{{ route('client/addressDetail') }}";
    var clientContacts = "{{ route('client/clientContacts') }}";
    var filterProducts = "{{ route('orders/filterProducts') }}";
    var productsData = "{{ route('orders/productsData') }}";
    var districtlist = "{{ route('admin/master/districts/districtslist') }}";
    var stateurl = "{{ route('admin/master/districtslist') }}";
    var talukaurl = "{{ route('taluka/talukalist') }}";
    var route_for_popup = "orders";
    var clientFilter="{{ route('orders/clientdata') }}";
</script> 
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
<script src="{{ asset('/assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/js/scripts/forms/select/form-select2.js') }}" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="{{ asset('/assets/vendors/js/fancybox/dist/jquery.fancybox.js')}}"></script>
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('/assets/pages/scripts/admin/orders/orders_add.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection