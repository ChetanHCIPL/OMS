@extends('layouts.admin')
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS --> 
<link href="{{ asset('/assets/vendors/css/vendors.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/pickers/daterange/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/css/bootstrap.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/css/bootstrap-extended.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/css/colors.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/css/components.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/css/core/menu/menu-types/horizontal-menu.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/css/core/colors/palette-gradient.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/css/plugins/forms/wizard.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/css/plugins/pickers/daterange/daterange.css')}}" rel="stylesheet" type="text/css" />
<!--Start Selectize-->
<link href="{{ asset('/assets/vendors/css/forms/selects/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.default.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/plugins/forms/selectize/selectize.css')}}" rel="stylesheet" type="text/css" />
<!--End Selectize-->
<!--Start Bootstrap Switch-->
<!-- <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
   <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/switchery.min.css')}}">
   <link href="{{ asset('assets/css/core/colors/palette-switch.css')}}" rel="stylesheet" type="text/css" />
   <link href="{{ asset('/assets/vendors/css/fancybox/dist/jquery.fancybox.css')}}" rel="stylesheet" type="text/css"/>
   <link href="{{ asset('/assets/vendors/css/fancybox/dist/jquery.fancybox.min.css')}}" rel="stylesheet" type="text/css"/> -->
<!--End Bootstrap Switch-->
<!-- END PAGE LEVEL PLUGINS -->
@endsection
<?php $mode = 'Add'; ?>
@section('content')

      <section class="content-header clearfix">
         <h3>Order <strong><span class="text-muted accent-3">{{((isset($data[0]['name'])?' - '.reduceTitleName($data[0]['name']):''))}}</span></strong></h3>
         <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="javascript:void(0);">Orders Mgmt</a></li>
            <li><a href="javascript:void(0);">Orders</a></li>
            <li><a href="javascript:void(0);">{{(isset($mode)?$mode:'') }} Order</a></li>
         </ol>
      </section>
      <!-- Form wizard with number tabs section start -->
      <section id="horizontal-grid">
         <div class="row">
            <div class="col-12">
               <div class="card">
                  <div class="card-header">
                     <h4 class="card-title">Add Order</h4>
                     <a class="heading-elements-toggle"><i class="la la-ellipsis-h font-medium-3"></i></a>
                  </div>
                  <div class="card-content collapse show">
                     <div class="card-body">
                        <form action="#" class="number-tab-steps wizard-circle">
                           <!-- Step 1 -->
                           <h6>Customer Info</h6>
                           <fieldset>
                              <div class="row">
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label class="label-control">Client Name <span class="required">*</span></label>
                                       <select class="custom-select form-control" id="client_name" name="client_name" placeholder="Select Client Name">
                                          <option value="">Select Client</option>
                                          <option value="c1">Sumit patel</option>
                                          <option value="c2">Mr. Ramesh Jain</option>
                                          <option value="c3">Kapil Dev</option>
                                       </select>
                                    </div>
                                    <div class="form-group">
                                       <label class="label-control">Billing Name <span class="required">*</span></label>
                                       <select class="custom-select form-control" id="billing_name" name="billing_name" placeholder="Select Client Name">
                                          <option value="">Select address</option>
                                          <option value="a1">301 Shikhar Complex...</option>
                                          <option value="a2">203, 2nd Floor, 3 rd Eye- II...</option>
                                          <option value="a3">Ideal Office, Ahmedabad... </option>
                                       </select>
                                    </div>
                                    <div class="form-group row" style="display:none;" id="address">
                                       <div class="col-md-9 label-control">
                                          <label class="col-md-3">Address1 :</label> 301 Shikhar Complex
                                       </div>
                                       <div class="col-md-9 label-control">
                                          <label class="col-md-3">Address2 :</label>   Nr. Adani House
                                       </div>
                                       <div class="col-md-9 label-control">
                                          <label class="col-md-3">Address3 :</label>Mithakhali Six Road
                                       </div>
                                       <div class="col-md-9 label-control">
                                          <label class="col-md-3">City :</label>Ahmedabad
                                       </div>
                                       <div class="col-md-9 label-control">
                                          <label class="col-md-3">State :</label>Gujarat
                                       </div>
                                       <div class="col-md-9 label-control">
                                          <label class="col-md-3">Country :</label>India
                                       </div>
                                    </div>
                                    <div class="form-group">
                                       <label class="label-control">Contact Name & Number <span class="required">*</span></label>
                                       <select class="custom-select form-control" id="c_n_n" name="c_n_n" placeholder="Select Contact Name & Number">
                                          <option value="">Contact Name & Number</option>
                                          <option value="c1">Rahul Sharma - (9996654545)</option>
                                          <option value="c2">Atul Kulkarni - (9874454545)</option>
                                          <option value="c3">Depak jain - (9447744747)</option>
                                       </select>
                                    </div>
									<div class="form-group">
                                       <label class="label-control">Name <span class="required">*</span></label>
                                       <select class="custom-select form-control" id="Name" name="Name" placeholder="Name">
                                          <option value="">Name</option>
                                          <option value="n1">John Doe</option>
                                          <option value="n2">Atul Kulkarni</option>
                                          <option value="n3">Jack Sparrow</option>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label class="label-control">Same as Billing <span class="required">*</span></label>
                                       <fieldset class="checkboxsas">
                                          <label>
                                          <input id="same-address" type="checkbox" value="">
                                          Same As Billing
                                          </label>
                                       </fieldset>
                                    </div>
                                    <div class="form-group">
                                       <label class="label-control">Billing Address </label>
                                       <select class="custom-select form-control" id="shipping_address" name="shipping_address" placeholder="Select Shipping Address">
                                          <option value="">Select Billing Address</option>
                                          <option value="a1">301 Shikhar Complex...</option>
                                          <option value="a2">203, 2nd Floor, 3 rd Eye- II...</option>
                                          <option value="a3">Ideal Office, Ahmedabad... </option>
                                       </select>
                                    </div>
                                    <div class="form-group row" style="display:none;" id="address2">
                                       <div class="col-md-9 label-control">
                                          <label class="col-md-3">Address1 :</label> 301 Shikhar Complex
                                       </div>
                                       <div class="col-md-9 label-control">
                                          <label class="col-md-3">Address2 :</label>   Nr. Adani House
                                       </div>
                                       <div class="col-md-9 label-control">
                                          <label class="col-md-3">Address3 :</label>Mithakhali Six Road
                                       </div>
                                       <div class="col-md-9 label-control">
                                          <label class="col-md-3">City :</label>Ahmedabad
                                       </div>
                                       <div class="col-md-9 label-control">
                                          <label class="col-md-3">State :</label>Gujarat
                                       </div>
                                       <div class="col-md-9 label-control">
                                          <label class="col-md-3">Country :</label>India
                                       </div>
                                    </div>
                                    <div class="form-group row mx-auto">
                                       <label class="label-control">Sales Name <span class="required">*</span></label>
                                       <select class="custom-select form-control" id="sales_name" name="sales_name" placeholder="Sales Name ">
                                          <option value="">Sales Name</option>
                                          <option value="s1">Hetesh Sharma</option>
                                          <option value="s2">Kuldeep Patel</option>
                                          <option value="s3">Rakesh Varma</option>
                                       </select>
                                    </div>
									<div class="form-group">
                                       <label for="contact-number">Contact Number<span class="required">*</span></label>
                                       <input type="number" min=0 class="form-control" placeholder="Contact Number" id="contact-number">
                                    </div>
                                 </div>
                              </div>
                           </fieldset>
                           <!-- Step 2 -->
                           <h6>Products</h6>
                           <fieldset>
                              <div class="row">
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label for="proposalTitle1">Upload Order Form :</label>
                                       <input type="file" class="form-control-file" id="exampleInputFile">
                                    </div>
                                    <div class="form-group">
                                       <label>Medium <span class="required">*</span></label>
                                       <select class="select2 form-control " multiple id="medium" name="medium" placeholder=" Select Medium ">
                                          <option value="">Select Medium</option>
                                          <option value="eng">English</option>
                                          <option value="hin">Hindi</option>
                                          <option value="guj">Gujrati</option>
                                       </select>
                                    </div>
                                    <div class="form-group">
                                       <label >Product Head <span class="required">*</span></label>
                                       <select class="custom-select form-control" id="product_head" name="product_head" placeholder=" Select Product Head ">
                                          <option value="">Select Product Head</option>
                                          <option value="pe">Popkorn English</option>
                                          <option value="pg">Popkorn Gujrati</option>
                                          <option value="inb">INoteBook</option>
                                          <option value="is">Ideal Student</option>
                                       </select>
                                    </div>
                                    <div class="form-group">
                                       <label >Series (Mentor) <span class="required">*</span></label>
                                       <select class="custom-select form-control" id="series" name="series" placeholder="Select Series ">
                                          <option value="">Select Series</option>
                                          <option value="s1">PopKorn </option>
                                          <option value="s2">INoteBook</option>
                                          <option value="s3">Ideal Student</option>
                                       </select>
                                    </div>

                                 </div>
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label >Std / Segment <span class="required">*</span></label>
                                       <select class="select2 form-control " multiple id="series" name="series" placeholder="Select Std / Segment ">
                                          <option value="">Select Std/Segment</option>
                                          <option value="std1">Std 1</option>
                                          <option value="std2">Std 2</option>
                                          <option value="std3">Std 3</option>
                                          <option value="std4">Std 4</option>
                                       </select>
                                    </div>
                                    <div class="form-group">
                                       <label for="quantity">Default Quantity<span class="required">*</span></label>
                                       <input type="number" min=0 class="form-control" placeholder="Default Quantity." id="quantity">
                                    </div>
                                    <div class="form-group">
                                       <label for="discount">Default Discount (%)<span class="required">*</span></label>
                                       <input type="number" min=0 class="form-control" placeholder="Default Discount" id="discount">
                                    </div>
                                    <div class="form-group">
                                       <label >Products <span class="required">*</span></label>
                                       <select class="select2 form-control " multiple id="product" name="product" placeholder="Select Products ">
                                          <option value="">Select Products</option>
                                          <option value="p1">Pricture book</option>
                                          <option value="p2">Kakko</option>
                                          <option value="p3">English</option>
                                          <option value="p4">Gujarati poem</option>
                                          <option value="p5">Math</option>
                                       </select>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <a href="#!" data-repeater-create="" class="btn btn-success add round btn-min-width mr-1 mb-1">
                                 <i class="ft-plus"></i> Add Products
                                 </a>
                                 <a href="#!" data-repeater-create="" class="btn btn-info add round btn-min-width mr-1 mb-1">
                                 <i class="ft-plus-square"></i> Add Single Product
                                 </a>
                                 <a href="#!" data-repeater-create="" class="btn btn-secondary remove round btn-min-width mr-1 mb-1">
                                 <i class="ft-minus-square"></i> Delete Last Product(s)
                                 </a>
                                 <a href="#!" data-repeater-create="" class="btn btn-dark remove round btn-min-width mr-1 mb-1">
                                 <i class="ft-minus"></i> Remove All Products
                                 </a>	
                              </div>
                              <div class="row" id="list-product" style="display:none;">
                                 <div class="table-responsive">
                                    <table class="table table-striped">
                                       <thead>
                                          <tr>
                                             <th width="5%" scope="col">#</th>
                                             <th width="50%" scope="col">Product Name</th>
                                             <th width="10%" scope="col">Quantity</th>
                                             <th class="text-right" width="10%" scope="col">Rate</th>
                                             <th width="10%" scope="col">Discount (%)</th>
                                             <th class="text-right" width="10%" scope="col">Amount</th>
                                             <th class="text-center" width="5%" scope="col">Remove</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          <tr>
                                             <th scope="row">1</th>
                                             <td>Product 1</td>
                                             <td><input type="number" class="form-control" value ="15" name="quantity" /></td>
                                             <td class="text-right" >80</td>
                                             <td><input type="number" class="form-control" value ="50" name="discount" /></td>
                                             <td class="text-right" >4000</td>
                                             <td class="text-center"><a href="#" class="btn btn-danger"><i class="ft-trash-2"></i></a></td>
                                          </tr>
                                          <tr>
                                             <th scope="row">2</th>
                                             <td>Product 2</td>
                                             <td><input type="number" class="form-control" value ="20" name="quantity" /></td>
                                             <td class="text-right" >60</td>
                                             <td><input type="number" class="form-control" value ="60" name="discount" /></td>
                                             <td class="text-right" >3600</td>
                                             <td class="text-center"><a href="#" class="btn btn-danger"><i class="ft-trash-2"></i></a></td>
                                          </tr>
                                          <tr>
                                             <th scope="row">3</th>
                                             <td>Product 3</td>
                                             <td><input type="number" class="form-control" value ="50	" name="quantity" /></td>
                                             <td class="text-right" >50</td>
                                             <td><input type="number" class="form-control" value ="100" name="discount" /></td>
                                             <td class="text-right" >5000</td>
                                             <td class="text-center"><a href="#" class="btn btn-danger"><i class="ft-trash-2"></i></a></td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </fieldset>
                           <!-- Step 3 -->
                           <h6>Dispatch</h6>
                           <fieldset>
                              <div class="row">
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label for="date1">Dispatch Date<span class="required">*</span></label>
                                       <input type="date" class="form-control" id="date1">
                                    </div>
                                    <div class="form-group">
                                       <label for="transporter">Select Prefered Transporter (If any) <span class="required">*</span></label>
                                       <select class="custom-select form-control" id="transporter" data-placeholder="Select Transporter" name="transporter">
                                          <option value="t1">Patel Transporter</option>
                                          <option value="t2">Shree Ganesh</option>
                                          <option value="t3">Mahashager</option>
                                          <option value="t4">Geeta Transporter</option>
                                       </select>
                                    </div>
                                    <div class="form-group">
                                       <label for="route">Select Roue Area <span class="required">*</span></label>
                                       <select class="custom-select form-control" id="route_area" data-placeholder="Select Route Area" name="route_area">
                                          <option value="r1">Ahmedabad-morbi</option>
                                          <option value="r2">Ahmedabad-rajkot-jamnager</option>
                                          <option value="r3">Jetpur-shomnath</option>
                                          <option value="r4">Bhuj</option>
                                       </select>
                                    </div>
									
                                 </div>
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label for="payment_due_days">Payment Due Days</label>
                                       <select class="custom-select form-control" id="payment_due_days" name="payment_due_days">
                                          <option value="">Select Payment Terms.</option>
                                          <option value="term1">45 Day</option>
                                          <option value="term2">30 Day</option>
                                          <option value="term3">15 Day</option>
                                       </select>
                                    </div>
                                    <div class="form-group">
                                       <label for="date2">Expected Delivery Date<span class="required">*</span></label>
                                       <input type="date" class="form-control" id="due-days">
                                    </div>
									         <div class="form-group">
                                       <label for="order-remark">Order Remark<span class="required">*</span></label>
                                       <input type="text" class="form-control" id="order-remark">
                                    </div>
                                 </div>
                              </div>
                           </fieldset>
                        </form>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <!-- Form wizard with number tabs section end -->

@endsection
@section('scripts')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- Start FORM VALIDATION -->
<script src="{{ asset('/assets/vendors/js/vendors.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/ui/jquery.sticky.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/charts/jquery.sparkline.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/extensions/jquery.steps.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/pickers/dateTime/moment-with-locales.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/pickers/daterange/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/js/core/app-menu.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/js/core/app.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/js/scripts/ui/breadcrumbs-with-stats.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/js/scripts/forms/wizard-steps.js')}}" type="text/javascript"></script>
<!-- Start Selectize -->
<script src="{{ asset('/assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/js/scripts/forms/select/form-select2.js') }}" type="text/javascript"></script>
<!-- End Selectize -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('/assets/pages/scripts/admin/catalog/order_add.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection