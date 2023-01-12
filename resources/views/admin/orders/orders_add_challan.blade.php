@extends('layouts.admin')
@section('styles')
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
         <h3>Add challan <strong><span class="text-muted accent-3">{{((isset($data[0]['country_name'])?' - '.reduceTitleName($data[0]['country_name']):''))}}</span></strong></h3>
         <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="javascript:void(0);">Orders Mgmt</a></li>
            <li><a href="{{ route('orders/list') }}">Order</a></li>
            <li><a href="javascript:void(0);">{{(isset($mode)?$mode:'') }} Challan</a></li>
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
                           <input type="hidden" id="id" name="id" value="{{(isset($id)?$id:'51')}}" />
                           <div class="row">
                              <div class="col-xl-12 col-lg-12 col-md-6 col-12">
                                 <div class="container-fluid">
                                    <h3>Client Info</h3>
                                    <hr>
                                    <div class="row form-box m-0">
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <label for="order_date" class="label-control col-md-3">Order no <span class="required">*</span></label>
                                             <div class="col-md-8" style="padding-top:8px;">
                                             {{$data[0]['order_no']}}
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="row form-box m-0">
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <label for="order_date" class="label-control col-md-3">Order Date <span class="required">*</span></label>
                                             <div class="col-md-8" style="padding-top:8px;">
                                             {{date('d-M-Y',strtotime($data[0]['order_date']))}}
                                             </div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <input type="hidden" name="client_id" id="client_id" value="{{$data[0]['client_id']}}">
                                             <input type="hidden" name="d_client_id" id="d_client_id" value="{{$data[0]['client_id']}}">
                                             <label class="col-md-3 label-control">Client Name <span class="required">*</span></label>
                                             <div class="col-md-8">
                                                {{$data[0]['client_name']}}
                                             </div>
                                             <div class="col-md-1">
                                             </div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3 label-control">Billing Address <span class="required">*</span></label>
                                             <div class="col-md-8">
                                                @php $selectedAddress = isset($data[0]['client_address_id']) ? $data[0]['client_address_id'] : ''; @endphp
                                                <input type="hidden" id="selected_address_id" value="<?php echo $selectedAddress ?>">
                                                <select class="selectize-select" id="client_address_id" name="client_address_id" placeholder="Select Billing Address">
                                                   <option value="">Select address</option>
                                                   @if(!empty($addresses))
                                                      @foreach($addresses as $key=>$a)
                                                         <option value="{{$a['id']}}" {{ ($key==1)?'selected':'';}}>{{$a['title']}} {{$a['address1']}} {{$a['address2']}}</option>
                                                      @endforeach
                                                   @endif
                                                </select>
                                             </div>
                                             <div class="col-md-1">
                                                
                                             </div>
                                          </div>
                                          <div class="form-group mx-auto" id="billing_address">
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
                                                   @if(!empty($contacts))
                                                      @foreach($contacts as $key=>$a)
                                                         <option value="{{$a['id']}}" {{ ($key==1)?'selected':'';}}>{{$a['full_name']}} {{$a['mobile_number']}}</option>
                                                      @endforeach
                                                   @endif
                                                </select>
                                             </div>
                                             <div class="col-md-1">
                                                
                                             </div>
                                          </div>
                                       </div>
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto d-none">
                                             <label class="col-md-3 label-control">Shipping Address <span class="required">*</span></label>
                                             <div class="col-md-9">
                                                <fieldset class="checkboxsas">
                                                   <label>
                                                   <input id="same_as_billing" type="checkbox" value="">
                                                   Same As Billing
                                                   </label>
                                                </fieldset>
                                             </div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                          <label class="col-md-3 label-control">Shipping Address <span class="required">*</span></label>
                                             <div class="col-md-8">
                                                @php $selectedShipAddress = isset($data[0]['client_ship_address_id']) ? $data[0]['client_ship_address_id'] : ''; @endphp
                                                <input type="hidden" id="selected_ship_address_id" value="<?php echo $selectedShipAddress ?>">
                                                <select class="selectize-select" id="client_ship_address_id" name="client_ship_address_id" placeholder="Select Shipping Address">
                                                   <option value="">Select Billing Address</option>
                                                   @if(!empty($addresses))
                                                      @foreach($addresses as $key=>$a)
                                                         <option value="{{$a['id']}}" {{ ($key==1)?'selected':'';}}>{{$a['title']}} {{$a['address1']}} {{$a['address2']}}</option>
                                                      @endforeach
                                                   @endif
                                                </select>
                                             </div>
                                             <div class="col-md-1">
                                                
                                             </div>
                                          </div>
                                          <div class="form-group mx-auto" id="shipping_address">
                                          <div class="form-group row mx-auto shipping-address">
                                             <small class="col-md-3 label-control">Shipping Name :</small>
                                                <div class="col-md-9">
                                                   <small class="label-control billing-name"></small>
                                                </div>
                                                <small class="col-md-3 label-control">Address :</small>
                                                <div class="col-md-9">
                                                   <small class="label-control full-address"></small>
                                                </div>
                                                <?php /*
                                             <div class="form-group row mx-auto">
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
                                             <label class="col-md-3 label-control">Sales Name <span class="required">*</span></label>
                                             <div class="col-md-8">
                                                <select class="selectize-select" id="sales_user_id" name="sales_user_id" placeholder="Sales Name ">
                                                   <option value="">Sales Name</option>
                                                   @if(!empty($salesUsers))
                                                      @foreach($salesUsers as $key=>$sArry)
                                                          <option value="{{ $sArry['id'] }}" {{ ($key==1)?'selected':'';}}>{{ $sArry['name'] }}</option>
                                                      @endforeach
                                                   @endif
                                                </select>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="row mt-1">
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
                                                <select class="selectize-select" id="product_head" name="product_head" placeholder=" Select Product Head ">
                                                   <option value="" disabled="disabled">Select Product Head</option>
                                                   @if(!empty($productHeads))
                                                      @foreach($productHeads as $pArry)
                                                         <option value="{{ $pArry['id'] }}"> {{ $pArry['name'] }}</option>
                                                      @endforeach
                                                   @endif
                                                </select>
                                             </div>
                                          </div>
                                          
                                          <div id="product_filters" style="display:none">
                                             <div class="form-group row mx-auto">
                                                <label class="label-control col-md-3">Board Medium </label>
                                                <div class="col-md-9">
                                                   <select class="selectize-select " multiple id="medium_id" name="medium_id" placeholder="Select Medium">
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
                                                   <select class="selectize-select" multiple id="segment" name="segment" placeholder="Select Std / Segment">
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
                                                      <option value="" disabled="disabled">Select Series</option>

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
                                                   <button type="submit" id="btn-label" class="btn btn-success">
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
                                                   @if(!empty($products))
                                                      @foreach($products as $pArry)
                                                      <?php /*{{ (in_array($pArry->id,['15','19','36']))? 'selected':'' }}*/?>
                                                          <option value="{{ $pArry->id }}" >{{ $pArry->name }}</option>
                                                      @endforeach
                                                   @endif
                                                </select>
                                             </div>
                                             <div class="col-md-1">
                                                
                                             </div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3 label-control" for="quantity">Default Quantity <span class="required">*</span></label>
                                             <div class="col-md-8">
                                                <input type="number" min=0 class="form-control" placeholder="Default Quantity." id="default_quantity">
                                             </div>
                                          </div>
                                          <div class="row text-right">
                                             <div class="col-md-12">
                                                <a href="javascript:void(0)" onclick="deleteLastRow()" data-repeater-create="" class="btn btn-danger remove-last round btn-min-width mr-1 mb-1">
                                                <i class="ft-minus-square"></i> Delete Last Product(s)
                                                </a>
                                                <a href="javascript:void(0);" data-repeater-create="" class="btn btn-success add round btn-min-width mr-1 mb-1 add-products-btn">
                                                <i class="ft-plus"></i> Add Products
                                                </a>
                                             </div>
                                          </div>
                                       </div>
                                    </div>

                                    <div class="row" id="order_products">
                                       <div class="table-responsive container-fluid">
                                          <table class="table table-striped">
                                             <thead>
                                                <tr>
                                                   <th width="5%" scope="col">#</th>
                                                   <th class="text-center" width="5%" scope="col">Remove</th>
                                                   <th width="30%" scope="col">Product Name</th>
                                                   <th width="10%" scope="col">SKU</th>
                                                   <th width="10%" scope="col" class="text-center"> Virtual Stock</th>
                                                   <th width="10%" scope="col" class="text-center"> Actual Stock</th>
                                                   <th width="10%" scope="col">Quantity</th>
                                                   <th class="text-right" width="10%" scope="col">Rate</th>
                                                   <th width="10%" scope="col">Discount (%)</th>
                                                   <th class="text-right" width="10%" scope="col">Amount</th>
                                                </tr>
                                             </thead>
                                             <tbody>
                                                <?php $total_price=0;$discount=0;$tqty=0;?>
                                                @if(!empty($order_product_data))
                                                @php $weight = $tqty =  $index = 0; @endphp
                                                   @foreach($order_product_data as $key=>$p)
                                                   @php 
                                                      ## Calculate Weight
                                                      $single = $p['order_qty'] * $p['weight']; 
                                                      $weight = $weight + $single;

                                                      ## Calclate Price
                                                      $price = $p['price'] * $p['order_qty'];

                                                      ## Calculate Quantity
                                                      
                                                      $tqty += $p['order_qty']
                                                   @endphp
                                                   <?php //echo '<pre>'; print_r($p); echo '</pre>'; exit(); ?>
                                                   @php $index++; @endphp
                                                   <tr>
                                                      <th scope="row">{{$index}}
                                                         <Input type="hidden" name="discount[]" value="{{$p['discount']}}">
                                                         <Input type="hidden" name="max_discount[]" class="max_discount" value="{{$p['max_discount']}}">
                                                         <Input type="hidden" name="final_amount[]" class="final_amount" value="{{$p['final_amount']}}">
                                                      </th>
                                                      <td class="text-center"><a href="#!" onclick="delete_row($(this))" class="btn btn-danger"><i class="ft-trash-2"></i></a></td>
                                                      <!-- <td>{{$p['product_name']}}</td> -->
                                                      <td class="weight" data-weight="{{$p['weight']}}">{{$p['product_name']}}</td>
                                                      <td><input type="hidden" class="form-control prd_id" value="{{$p['product_id']}}" name="prd_id[{{$index}}][product_id]" id="prd_id_{{$index}}" />{{$p['product_sku']}}</td>
                                                      <td class="text-center">{{$p['stock']}}</td>
                                                      <td class="text-center">{{$p['stock']}}</td>

                                                      <!-- <td><input type="number" class="form-control" value="{{$p['order_qty']}}" name="quantity"></td> -->

                                                      <td><input type="hidden" class="form-control prd_weight" value="" name="prd_weight[{{$index}}][weight]" id="prd_weight_{{$index}}" /><input type="number" max="{{$p['max_order_qty']}}" class="form-control prd_quantity" value="{{$p['order_qty']}}" onchange="calculateProductAmount(this);" name="prd_quantity[{{$index}}][qty]" id="prd_quantity_{{$index}}" /></td>


                                                      <!-- <td class="text-right">₹ {{number_format($p['price'],2)}}</td> -->

                                                      <td class="text-right"> <input type="hidden" class="form-control prd_price" value="{{number_format($p['price'],2)}}" name="prd_price[{{$index}}][price]" id="prd_price_{{$index}}" />{{number_format($p['price'],2)}}</td>


                                                      <!-- <td><input type="text" class="form-control enter_discount" maxlength="5" value="{{$p['max_discount']}}" max="{{$p['max_discount']}}" name="discount"></td> -->

                                                      <td><input type="hidden" class="form-control prd_dis_amount" value="{{$p['max_discount']}}" name="prd_dis_amount[{{$index}}][dis_price]" id="prd_dis_amount_{{$index}}" /><input onchange="calculateProductAmount(this);" type="number" class="form-control prd_discount" value="{{$p['max_discount']}}" name="prd_discount[{{$index}}][dic_percentage]" id="prd_discount_{{$index}}" /></td>

                                                      <!-- <td  class="text-right">₹&nbsp;{{number_format($p['final_amount'],2)}}</td> -->
                                                      
                                                      <td id="counter" data-counter="{{$index}}" class="text-right prd_include_discount_{{$index}}"><input type="hidden" class="form-control prd_include_discount" value="{{$price}}" name="prd_include_discount[{{$index}}][product_total]" id="prd_include_discount_{{$index}}" /><input type="hidden" class="form-control prd_amount" value="{{number_format($p['final_amount'],2)}}" name="prd_amount[{{$index}}][prd_amount]" id="prd_amount_{{$index}}" /><label class="prd_amt">{{number_format($p['final_amount'],2)}}</label></td>
                                                   </tr>
                                                   <?php $total_price=$total_price+$p['final_amount'];
                                                   $discount=$discount+$p['discount'];
                                                   $tqty=$tqty+$p['bill_qty'];?>
                                                   @endforeach
                                                @endif           
                                                </tbody>
                                          </table>
                                          <div class=row>
                                             <div class="col-md-4">
                                                <table class="table table-bordered table-striped">
                                                   <tbody>
                                                      <tr>
                                                         <td class="text-right" width="60%">Total Weight (in Grams)</td>
                                                         <td><b class="total-weight">{{$weight}}</b></td>
                                                      </tr>
                                                      <tr>
                                                         <td class="text-right">Total Quantity</td>
                                                         <td><b class="total-quantity">{{$tqty}}</b></td>
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
                                                         <b id="sub_total">₹&nbsp;{{number_format(($total_price+$discount),2)}}</b>
                                                      </td>
                                                   </tr>
                                                   <tr>
                                                      <td class="text-right">Total Discount</td>
                                                      <td>
                                                         <input type="hidden" name="dis_total_value" id="dis_total_value" />
                                                         <b id="dis_total">(-) ₹&nbsp;{{number_format($discount,2)}}</b>
                                                      </td>
                                                   </tr>
                                                   <tr>
                                                      <td class="text-right">Less Adv./Adj (-)</td>
                                                      <td><b id="less_adj">₹&nbsp;0.00</b></td>
                                                   </tr>
                                                   <tr>
                                                      <td class="text-right">Net Total</td>
                                                      <td><b id="net_total">₹&nbsp;{{number_format($total_price,2)}}</b></td>
                                                   </tr>
                                                   
                                                </tbody>
                                             </table>
                                             </div>
                                          </div>                                          
                                       </div>
                                    </div>
                                    <div class="tab-content">
                                       <div class="form-actions text-right">
                                          <a href="{{ url()->previous() }}">
                                          <button type="button" class="btn mr-1">
                                          <i class="ft-x"></i> Cancel
                                          </button></a>
                                          <button type="button" class="btn btn-success" id="challan_generate">
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
@endsection
@section('scripts')
<script type="text/javascript"> 
   var addresslist = "{{ route('client/addresslist') }}";
    var addressDetail = "{{ route('client/addressDetail') }}";
    var clientContacts = "{{ route('client/clientContacts') }}";
    var filterProducts = "{{ route('orders/filterProducts') }}";
   var challanGenerate = "{{ route('orders/challan/add') }}";
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
<script src="{{ asset('/assets/pages/scripts/admin/orders/orders_add_challan.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection