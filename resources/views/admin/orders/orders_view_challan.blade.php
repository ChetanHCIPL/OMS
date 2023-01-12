@extends('layouts.admin')
@section('content')
@php
$country_path = Config::get('path.country_path'); 
$image = isset($data[0]['flag'])?$data[0]['flag']:"";
$mode = 'Add';
@endphp
<div class="content-wrapper">
   <div class="content-body">
      <section class="content-header clearfix">
         <h3>Create Challan <strong></strong></h3>
         <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="javascript:void(0);">Orders Mgmt</a></li>
            <li><a href="{{route('orders/createChallan')}}">Order</a></li>
            <li><a href="javascript:void(0);">Create Challan</a></li>
         </ol>
      </section>
      <section class="horizontal-grid" id="horizontal-grid">
         <div class="row">
            <div class="col-md-12">
               <div class="card">
                  <div class="card-content collapse show">
                     <div class="card-body language-input">
                        <form class="form form-horizontal" id="frmadd" name="frmadd" enctype="multipart/form-data" action="">
                           <input type="hidden" name="_token" value="{{csrf_token()}}" />
                           <div class="row">
                              <div class="col-xl-12 col-lg-12 col-md-6 col-12">
                                 <div class="container-fluid">
                                    <h3>Order Details</h3>
                                    <hr>
                                    <div class="row form-box m-0">
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3">Order Id </label>
                                             <div class="col-md-9 label-control">{{$data[0]['id']}}</div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3">Order Number </label>
                                             <div class="col-md-9 label-control">{{$data[0]['order_no']}}</div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="label-control col-md-3">Order Amount </label>
                                             <div class="col-md-9 label-control">{{$data[0]['order_total']}}</div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="label-control col-md-3">Order Form Link </label>
                                             <div class="col-md-9 label-control"><a href="{{ URL::asset('images/product/'.$data[0]['order_form_photo']) }}" target="_blank">Open Order Form</a></div>
                                          </div>
                                       </div>
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3">Order Date </label>
                                             @php 
                                                $created_at = (isset($data[0]['created_at']))? date_getMyDateFormat1($data[0]['created_at']): '---';
                                                $order_payment_due_date = (isset($data[0]['order_payment_due_date']))? date_getMyDateFormat1($data[0]['order_payment_due_date']): '---';
                                             @endphp
                                             <div class="col-md-9 label-control">{{$created_at}}</div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3">Payment Due Date </label>
                                             <div class="col-md-9 label-control">{{$order_payment_due_date}}</div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3">Order Remark </label>
                                             <div class="col-md-9 label-control">{{$data[0]['order_remark']}}</div>
                                          </div>
                                       </div>
                                    </div>

                                    <div class="row mt-1">
                                       <div class="col-md-12">
                                            <h3>Client Info</h3>
                                       </div>
                                    </div>
                                    <hr>
                                    <div class="row form-box m-0">
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3">Client Name </label>
                                             <div class="col-md-9 label-control">{{$data[0]['client_name']}}</div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3">Client Number </label>
                                             <div class="col-md-9 label-control">{{$data[0]['client_number']}}</div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3">Client Address </label>
                                             <div class="col-md-9 label-control">{{$data[0]['billing_address']}}</div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="label-control col-md-3">Contact Person </label>
                                             <div class="col-md-9 label-control">{{$data[0]['order_responsible_person_name']}}</div>
                                          </div>
                                       </div>
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3">Contact Person Number </label>
                                             <div class="col-md-9 label-control">{{$data[0]['order_responsible_person_number']}}
                                             </div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3">Transport </label>
                                             <div class="col-md-9 label-control">{{$data[0]['transporter']}}</div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3">Sales Name </label>
                                             <div class="col-md-9 label-control">{{$data[0]['sales_user_name']}}</div>
                                          </div>
                                       </div>
                                    </div>

                                    <div class="row mt-1">
                                       <div class="col-md-12">
                                            <h3>Dispatch Through</h3>
                                       </div>
                                    </div>
                                    <hr>
                                    <div class="row form-box m-0">
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3">Dispatch Through </label>
                                             <div class="col-md-9 label-control">Direct To School</div>
                                          </div>
                                       </div>
                                    </div>

                                    <div class="row mt-1">
                                       <div class="col-md-12">
                                            <h3>Billing Details</h3>
                                       </div>
                                    </div>
                                    <hr>
                                    <div class="row form-box m-0">
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3">Bill Number </label>
                                             <div class="col-md-9 label-control">{{$data[0]['bill_number']}}</div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3">Billing Name </label>
                                             <div class="col-md-9 label-control">{{$data[0]['client_name']}}</div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             @php 
                                                $order_date = (isset($data[0]['order_date']))? date_getMyDateFormat1($data[0]['order_date']): '---';
                                             @endphp
                                             <label class="col-md-3">Bill Date </label>
                                             <div class="col-md-9 label-control">{{$order_date}}</div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3">Bill Amount </label>
                                             <div class="col-md-9 label-control">{{$data[0]['order_total']}}</div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3">Bill Remarks </label>
                                             <div class="col-md-9 label-control">{{$data[0]['order_remark']}}</div>
                                          </div>
                                       </div>
                                    </div>

                                    <div class="row mt-1">
                                       <div class="col-md-12">
                                            <h3>Product Details</h3>
                                       </div>
                                    </div>
                                    <hr>
                                    <div class="row" id="order_products">
                                       <div class="table-responsive container-fluid">
                                          <table class="table table-striped">
                                             <thead>
                                                <tr>
                                                   <th class="text-center" width="5%" scope="col">SR No.</th>
                                                   <th width="30%" scope="col">Product Name</th>
                                                   <th width="20%" scope="col">SKU</th>
                                                   <th class="text-right" width="10%" scope="col">Order Quantity</th>
                                                   <th class="text-right" width="10%" scope="col">Bill Quantity</th>
                                                   <th class="text-right" width="10%" scope="col">Dispatch Quantity</th>
                                                   <th class="text-right" width="10%" scope="col">MRP</th>
                                                   <th class="text-right" width="10%" scope="col">Available Quantity</th>
                                                </tr>
                                             </thead>
                                             <tbody>
                                                @if(!empty($order_product_data))
                                                   @foreach($order_product_data as $key => $product)
                                                   <tr>
                                                      <td class="text-center">{{$key+1}}</td>
                                                      <td>{{$product['product_name']}}</td>
                                                      <td>{{$product['product_sku']}}</td>
                                                      <td class="text-right">{{$product['order_qty']}}</td>
                                                      <td class="text-right">{{$product['bill_qty']}}</td>
                                                      <td class="text-right">{{$product['bill_qty']}}</td>
                                                      <td class="text-right">{{$product['final_amount']}}</td>
                                                      <td class="text-right">{{$product['stock']}}</td>
                                                   </tr>
                                                   @endforeach
                                                @endif
                                             </tbody>
                                          </table>
                                          <div class="row">
                                             <div class="col-md-4">
                                                <table class="table table-bordered table-striped">
                                                   <tbody>
                                                      <tr>
                                                         <td class="text-right" width="60%">Total Billing Weight</td>
                                                         <td><b class="total-weight"></b></td>
                                                      </tr>
                                                      <tr>
                                                         <td class="text-right">Total Dispatch Weight</td>
                                                         <td><b class="total-quantity"></b></td>
                                                      </tr>
                                                   </tbody>
                                                </table>   
                                             </div>
                                          </div>                                          
                                       </div>
                                    </div>

                                    <div class="row mt-1">
                                       <div class="col-md-12">
                                            <h3>Bilty Details</h3>
                                       </div>
                                    </div>
                                    <hr>
                                    <div class="row form-box m-0">
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <div class="col-md-3">
                                                <a class="fancybox" rel="gallery1" href="http://192.168.32.160/ideal_oms/images/user/2_1646981948_Screenshot_3.png" title=""><img src="http://192.168.32.160/ideal_oms/images/user/1_1646981948_Screenshot_3.png" alt="" class="img-fluid rounded-circle width-100" id="show-image" noimage="80x80.jpg" /></a>
                                              <!-- <img src="{{ URL::asset('images/product/Lighthouse.jpg') }}"> -->
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
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('assets\pages\scripts\admin\fancybox\dist\jquery.fancybox.js')}}"></script>
<script type="text/javascript">
   $(document).ready(function() {  
      $(".fancybox").fancybox({
         openEffect  : 'none',
         closeEffect : 'none'
      });
   });
</script>
@endsection