<style type="text/css">
	
</style>@extends('layouts.admin')
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
         <h3>Add Order <strong><span class="text-muted accent-3">{{((isset($data[0]['country_name'])?' - '.reduceTitleName($data[0]['country_name']):''))}}</span></strong></h3>
         <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="javascript:void(0);">Orders Mgmt</a></li>
            <li><a href="javascript:void(0);">Order</a></li>
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
									 <div class="form-box m-0">
										<div class="col-md-12">
											<div class="row">
											   <div class="col-md-6">
												  <div class="form-group row mx-auto">
													 <label for="date1" class="label-control col-md-3">Order Date<span class="required">*</span></label>
													 <div class="col-md-8">
														<input type="date" class="form-control" id="date1">
													 </div>
												  </div>
											   </div>
											</div>
											<div class="row">
											   <div class="col-md-6">
												  <div class="form-group row mx-auto">
													 <label class="col-md-3 label-control">Client Name <span class="required">*</span></label>
													 <div class="col-md-8">
														<select class="custom-select form-control" id="client_name" name="client_name" placeholder="Select Client Name">
														   <option value="">Select Client</option>
												  <option value="s1">Hetesh Sharma</option>
												  <option value="s2">Kuldeep Patel</option>
												  <option value="s3">Rakesh Varma</option>
														</select>
													 </div>
													 <div class="col-md-1">
														<a href="#" class="btn btn-success"><i class="ft-plus"></i></a>
													 </div>
												  </div>
												  <div class="form-group row mx-auto">
													 <label class="col-md-3 label-control">Billing Address <span class="required">*</span></label>
													 <div class="col-md-8">
														<select class="custom-select form-control" id="billing_name" name="billing_name" placeholder="Select Client Name">
														   <option value="">Select address</option>
														   <option value="a1">301 Shikhar Complex...</option>
														   <option value="a2">203, 2nd Floor, 3 rd Eye- II...</option>
														   <option value="a3">Ideal Office, Ahmedabad... </option>
														</select>
													 </div>
													 <div class="col-md-1">
														<a href="#" class="btn btn-success"><i class="ft-plus"></i></a>
													 </div>
												  </div>
												  <div class="form-group mx-auto" style="display:none;" id="address">
													 <div class="form-group row mx-auto">
														<small class="col-md-3 label-control">Address1 :</small>
														<div class="col-md-9">
														   <small class="label-control">301 Shikhar Complex</small>
														</div>
														<small class="col-md-3 label-control">Address2 :</small>
														<div class="col-md-9">
														   <small class="label-control">Nr. Adani House</small>
														</div>
														<small class="col-md-3 label-control">Country :</small>
														<div class="col-md-9">
														   <small class="label-control">India</small>
														</div>
														<small class="col-md-3 label-control">State :</small>
														<div class="col-md-9">
														   <small class="label-control">Gujarat</small>
														</div>
														<small class="col-md-3 label-control">District :</small>
														<div class="col-md-9">
														   <small class="label-control">Ahmedabad</small>
														</div>
														<small class="col-md-3 label-control">Taluka :</small>
														<div class="col-md-9">
														   <small class="label-control">Bavla</small>
														</div>
														<small class="col-md-3 label-control">Pincode :</small>
														<div class="col-md-9">
														   <small class="label-control">382220</small>
														</div>
													 </div>
												  </div>
												  <div class="form-group row mx-auto">
													 <label class="label-control col-md-3">Contact Name <span class="required">*</span></label>
													 <div class="col-md-8">
														<select class="custom-select form-control" id="c_n_n" name="c_n_n" placeholder="Select Contact Name & Number">
														   <option value="">Contact Name</option>
														   <option value="c1">Jack Sparrow - (9996654545)</option>
														   <option value="c2">John Doe     - (9874454545)</option>
														   <option value="c3">Lorem Ipsum  - (9447744747)</option>
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
													 <div class="col-md-9">
														<fieldset class="checkboxsas">
														   <label>
														   <input id="same-address" type="checkbox" value="">
														   Same As Billing
														   </label>
														</fieldset>
													 </div>
												  </div>
												  <div class="form-group row mx-auto">
													 <label class="col-md-3 label-control">&nbsp;</label>
													 <div class="col-md-8">
														<select class="custom-select form-control" id="shipping_address" name="shipping_address" placeholder="Select Shipping Address">
														   <option value="">Select Billing Address</option>
														   <option value="a1">301 Shikhar Complex...</option>
														   <option value="a2">203, 2nd Floor, 3 rd Eye- II...</option>
														   <option value="a3">Ideal Office, Ahmedabad... </option>
														</select>
													 </div>
													 <div class="col-md-1">
														<a href="#" class="btn btn-success"><i class="ft-plus"></i></a>
													 </div>
												  </div>
												  <div class="form-group mx-auto" style="display:none;" id="address2">
													 <div class="form-group row mx-auto">
													 <small class="col-md-3 label-control">Address1 :</small>
														<div class="col-md-9">
														   <small class="label-control">301 Shikhar Complex</small>
														</div>
														<small class="col-md-3 label-control">Address2 :</small>
														<div class="col-md-9">
														   <small class="label-control">Nr. Adani House</small>
														</div>
														<small class="col-md-3 label-control">Country :</small>
														<div class="col-md-9">
														   <small class="label-control">India</small>
														</div>
														<small class="col-md-3 label-control">State :</small>
														<div class="col-md-9">
														   <small class="label-control">Gujarat</small>
														</div>
														<small class="col-md-3 label-control">District :</small>
														<div class="col-md-9">
														   <small class="label-control">Ahmedabad</small>
														</div>
														<small class="col-md-3 label-control">Taluka :</small>
														<div class="col-md-9">
														   <small class="label-control">Bavla</small>
														</div>
														<small class="col-md-3 label-control">Pincode :</small>
														<div class="col-md-9">
														   <small class="label-control">382220</small>
														</div>
													 </div>
												  </div>
												  <div class="form-group row mx-auto">
													 <label class="col-md-3 label-control">Sales Name <span class="required">*</span></label>
													 <div class="col-md-8">
														<select class="custom-select form-control" id="sales_name" name="sales_name" placeholder="Sales Name ">
														   <option value="">Sales Name</option>
												  <option value="s1">Hetesh Sharma</option>
												  <option value="s2">Kuldeep Patel</option>
												  <option value="s3">Rakesh Varma</option>
														</select>
													 </div>
												  </div>
											  </div>
										  </div>
                                       </div>
                                    </div>
                                    <div class="row mt-1">
                                       <div class="col-md-12">
                                            <h3>Products</h3>
                                       </div>
									         </div>
                                    <div class="row form-box m-0">
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <label class="label-control col-md-3">Product Head <span class="required">*</span></label>
                                             <div class="col-md-9">
                                                <select class="custom-select form-control" id="product_head" name="product_head" placeholder=" Select Product Head ">
                                                   <option value="">Select Product Head</option>
                                                   <option value="pe" selected >Popkorn English</option>
                                                   <option value="pg">Popkorn Gujrati</option>
                                                   <option value="inb">INoteBook</option>
                                                   <option value="is">Ideal Student</option>
                                                </select>
                                             </div>
                                          </div>
                                          <div class="form-group row mx-auto d-none">
                                             <div class ="col-md-12">
                                                <a  href="#!" id="filters1">
                                                   <button type="button" id="btn-label" class="btn btn-primary">
                                                      <i class="ft-filter"></i> Show Filter
                                                   </button>
                                                   
                                                </a>
                                             </div>
                                          </div>
                                          
                                          
                                          <div id="ftr" style = "display:none">
                                             <div class="form-group row mx-auto">
                                                <label class="label-control col-md-3">Board Medium </label>
                                                <div class="col-md-9">
                                                   <select class="select2 form-control " multiple id="medium" name="medium" placeholder=" Select Medium ">
                                                      <option value="">Select Medium</option>
                                                      <option value="ceng">CBSE - English </option>
                                                      <option value="geng">GBSE - English </option>
                                                      <option value="chin">CBSE - Hindi</option>
                                                      <option value="ghin">GBSE - Hindi</option>
                                                      <option value="cguj">CBSE - Gujrati</option>
                                                      <option value="gguj">GBSE - Gujrati</option>
                                                   </select>
                                                </div>
                                             </div>                                             
                                             <div class="form-group row mx-auto">
                                                <label class="label-control col-md-3">Std / Segment </label>
                                                <div class="col-md-9">
                                                   <select class="select2 form-control " multiple id="segment" name="segment" placeholder="Select Std / Segment ">
                                                      <option value="">Select Std/Segment</option>
                                                      <option value="std1">Std 1</option>
                                                      <option value="std2">Std 2</option>
                                                      <option value="std3">Std 3</option>
                                                      <option value="std4">Std 4</option>
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="form-group row mx-auto">
                                                <label class="label-control col-md-3">Series (Mentor) </label>
                                                <div class="col-md-9">
                                                   <select class="custom-select form-control" id="series" name="series" placeholder="Select Series ">
                                                      <option value="">Select Series</option>
                                          <option value="s1">PopKorn </option>
                                          <option value="s2">INoteBook</option>
                                          <option value="s3">Ideal Student</option>
                                                   </select>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="form-group row mx-auto text-right">
                                             <div class ="col-md-12">
                                             <a  href="#!" id="filters">
                                                   <button type="button" id="btn-label" class="btn btn-primary">
                                                      <i class="ft-filter"></i> Show Filter
                                                   </button>
                                                   
                                                </a>
                                                <a href="#!" >
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
                                                <select class="select2 form-control " multiple id="product" name="product" placeholder="Select Products ">
                                                   <option value="">Select Products</option>
                                                   <option value="p1">BIM-001 Ideal I-Mentor Part 1</option>
                                                   <option value="p2">DIM-002 Ideal I-Mentor Part 2</option>
                                                   <option value="p3">QIM-052 Ideal I-Mentor Part 8</option>
                                                   <option value="p4">LIM-175 Ideal I-Mentor Part 5</option>
                                                   <option value="p5">BIM-006 Ideal I-Mentor Part 1</option>
                                                </select>
                                             </div>
                                             <div class="col-md-1">
                                                <a href="#" class="btn btn-success"><i class="ft-plus"></i></a>
                                             </div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="col-md-3 label-control" for="quantity">Default Quantity<span class="required">*</span></label>
                                             <div class="col-md-8">
                                                <input type="number" min=0 class="form-control" placeholder="Default Quantity." id="quantity">
                                             </div>
                                          </div>
                                          <div class="row">
                                             <div class="col text-right">
                                                
                                                <!-- <a href="#!" data-repeater-create="" class="btn btn-info add round btn-min-width mr-1 mb-1">
                                                <i class="ft-plus-square"></i> Add Single Product
                                                </a> -->
                                                <a href="#!" data-repeater-create="" class="btn btn-danger remove round btn-min-width mr-1 mb-1">
                                                <i class="ft-minus-square"></i> Delete Last Product(s)
                                                </a>
                                                <a href="#!" data-repeater-create="" class="btn btn-success add round btn-min-width mr-1 mb-1">
                                                <i class="ft-plus"></i> Add Products
                                                </a>
                                                <!--<a href="#!" data-repeater-create="" class="btn btn-danger remove round btn-min-width mr-1 mb-1">
                                                <i class="ft-minus"></i> Remove All Products
                                                </a>	 -->
                                             </div>
                                          </div>
                                       </div>
                                    

                                       <div class="col-12" id="list-product" style="display:none;">
                                          <div class="table-responsive container-fluid">
                                             <table class="table table-striped">
                                                <thead>
                                                   <tr>
                                                      <th width="5%" scope="col">#</th>
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
                                                   <tr>
                                                      <th scope="row">1</th>
                                                      <td class="text-center"><a href="#" class="btn btn-danger"><i class="ft-trash-2"></i></a></td>
                                                      <td>Popkorn English</td>
                                                      <td>Popkorn-English</td>
                                                      <td><input type="number" class="form-control" value ="15" name="quantity" /></td>
                                                      <td class="text-right" >80</td>
                                                      <td><input type="number" class="form-control" value ="50" name="discount" /></td>
                                                      <td class="text-right" >4000</td>
                                                   </tr>
                                                   <tr>
                                                      <th scope="row">2</th>
                                                      <td class="text-center"><a href="#" class="btn btn-danger"><i class="ft-trash-2"></i></a></td>
                                                      <td>Popkorn Gujrati</td>
                                                      <td>Popkorn-Gujrati</td>
                                                      <td><input type="number" class="form-control" value ="20" name="quantity" /></td>
                                                      <td class="text-right" >60</td>
                                                      <td><input type="number" class="form-control" value ="60" name="discount" /></td>
                                                      <td class="text-right" >3600</td>
                                                   </tr>
                                                   <tr>
                                                      <th scope="row">3</th>
                                                      <td class="text-center"><a href="#" class="btn btn-danger"><i class="ft-trash-2"></i></a></td>
                                                      <td>INoteBook</td>
                                                      <td>INoteBook</td>
                                                      <td><input type="number" class="form-control" value ="50" name="quantity" /></td>
                                                      <td class="text-right" >50</td>
                                                      <td><input type="number" class="form-control" value ="100" name="discount" /></td>
                                                      <td class="text-right" >5000</td>
                                                   </tr>
                                                   
                                                </tbody>
                                             </table>
                                             <div class=row>
                                                <div class="col-md-4">
                                                   <table class="table table-bordered table-striped">
                                                      <tbody>
                                                         <tr>
                                                            <td class="text-right" width="60%">Total Weight (in KGs)</td>
                                                            <td><b>550</b></td>
                                                         </tr>
                                                         <tr>
                                                            <td class="text-right">Total Quantity</td>
                                                            <td><b> 85</b></td>
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
                                                         <td><b id="Dis_Subtotal">Rs. 12600.00</b></td>
                                                      </tr>
                                                      <tr>
                                                         <td class="text-right">Total Discount</td>
                                                         <td><b id="Dis_TotalDis">(-) Rs. 1260.00</b></td>
                                                      </tr>
                                                      <tr>
                                                         <td class="text-right">Less Adv./Adj (-)</td>
                                                         <td><b id="Dis_NetTotal">Rs. 0.00</b></td>
                                                      </tr>
                                                      <tr>
                                                         <td class="text-right">Net Total</td>
                                                         <td><b id="Dis_NetTotal">Rs. 11340.00</b></td>
                                                      </tr>
                                                      
                                                   </tbody>
                                                </table>
                                                </div>
                                             </div>                                          
                                          </div>
                                       </div>
									         </div>
                                    <div class="row mt-1">
                                       <div class="col-md-12">
                                          <h3>Order Form</h3>
                                       </div>
                                    </div>
                                    <div class="row form-box m-0">
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <label class="label-control col-md-3" for="proposalTitle1">Upload Order Form :</label>
                                             <div class="col-md-9">
                                                <input type="file" class="form-control-file" id="exampleInputFile">
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="row mt-1">
                                       <div class="col-md-12">
                                          <h3>Dispatch</h3>
                                       </div>
                                    </div>
                                    <div class="row form-box m-0">
                                       <div class="col-md-6">
                                          <div class="form-group row mx-auto">
                                             <label class="label-control col-md-3" for="date1">Dispatch Date<span class="required">*</span></label>
                                             <div class="col-md-9">
                                                <input type="date" class="form-control" id="date1">
                                             </div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="label-control col-md-3" for="transporter">Prefered Transporter<span class="required">*</span></label>
                                             <div class="col-md-9">
                                                <select class="custom-select form-control" id="transporter" data-placeholder="Select Transporter" name="transporter">
                                          <option value="t1">Patel Transporter</option>
                                          <option value="t2">Shree Ganesh</option>
                                          <option value="t3">Mahashager</option>
                                          <option value="t4">Geeta Transporter</option>
                                                </select>
                                             </div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="label-control col-md-3" for="route">Route Area <span class="required">*</span></label>
                                             <div class="col-md-9">
                                                <select class="custom-select form-control" id="route_area" data-placeholder="Select Route Area" name="route_area">
                                                   
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
                                                <select class="custom-select form-control" id="payment_due_days" name="payment_due_days">
                                                   <option value="">Payment Terms</option>
                                                   <option value="term1">1 Net</option>
                                                   <option value="term2">15 Net</option>
                                                   <option value="term3">45 Net</option>
                                                   <option value="term3">45 Net</option>
                                                </select>
                                             </div>
                                             <label class="label-control col-md-3" for="date2">Due Date<span class="required">*</span></label>
                                             <div class="col-md-3">
                                                <input type="date" class="form-control" id="due-days">
                                             </div>
                                          </div>
                                          <div class="form-group row mx-auto">
                                             <label class="label-control col-md-3" for="order-remark">Order Remark<span class="required">*</span></label>
                                             <div class="col-md-9">
                                                <fieldset class="form-group">
                                                   <textarea class="form-control" id="order-remark" rows="3"></textarea>
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
<script src="{{ asset('/assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/js/scripts/forms/select/form-select2.js') }}" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="{{ asset('/assets/vendors/js/fancybox/dist/jquery.fancybox.js')}}"></script>
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('/assets/pages/scripts/admin/catalog/order_add.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection