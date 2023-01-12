@extends('layouts.admin')
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('/assets/vendors/css/tables/datatable/datatables.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/core/colors/palette-switch.css')}}" rel="stylesheet" type="text/css" />
<!-- Start Bootstrap Switch -->
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/switchery.min.css')}}">
<!-- End Bootstrap Switch -->
<!--Start Selectize--->
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.default.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/plugins/forms/selectize/selectize.css')}}" rel="stylesheet" type="text/css" />
<!--End Selectize--->
<!-- END PAGE LEVEL PLUGINS -->
@php $mode = isset($mode)?$mode:"Add"; @endphp
@endsection 
@section('content')
<div class="content-wrapper listing-innerpage">
   <div class="content-body">
      <section class="content-header clearfix">
         <h3>Product Head</h3>
         <ol class="breadcrumb">
            <li><a href="javascript:void(0);">Dashboard</a></li>
            <li><a href="javascript:void(0);">Product Mgmt</a></li>
            <li class="active">Product Head</li>
         </ol>
      </section>
      <div class="alert alert-dismissible mb-2 d-none" role="alert" id="list-alert">
         <span id="list-msg">Change a <a href="#" class="alert-link">few things up</a> and submit again.</span>
      </div>
      <section class="row">
         <div class="col-12">
            <div class="card">
               <div class="card-head">
                  <div class="card-header border-bottom search-filter-header">
                     <h4 class="card-title"><i class="material-icons">category</i> Product Head</h4>
                     <div class="heading-elements">
                        <div class="filter mr-1 float-left">
                           <button type="button" id="show_filter" class="btn btn-info btn-sm"><i class="material-icons"> search </i>Show Filter</button>
                           <button type="button" id="hide_filter" class="btn btn-info btn-sm btn-warning d-none"><i class="material-icons"> close </i>Hide Filter</button>
                        </div>
                        <div class="btn-group">
                           @if (per_hasModuleAccess('ProductHead', 'Edit'))
                           <button type="button" class="btn btn-secondary dropdown-toggle btn-sm waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings"></i> Actions</button>
                           <div class="dropdown-menu">
                              @if(per_hasModuleAccess('ProductHead', 'Delete'))
                              <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Active', 'producthead');">Active</a>
                              <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Inactive', 'producthead');">Inactive</a>
                              <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Delete', 'producthead');">Delete</a>
                              @endif
                           </div>
                           @endif
                           @if (per_hasModuleAccess('ProductHead', 'Add'))
                           &nbsp;&nbsp;<a href="{{route('producthead',['mode' => 'add', 'id' => ''])}}" class="btn btn-primary btn-sm waves-effect waves-light" title="Create Product Head"><i class="ft-plus "></i> Create Product Head </a>@endif
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card-content">
                  <div class="card-body">
                     <form id="frmlist" name="frmlist" action="" class="form-horizontal" method="post">
                        <div class="position-relative search-filter-hide">
                           <div class="tab-content px-121 filter-box d-none" id="filter_div">
                              <div class="row border p-1 m-1 bg-advance-filter">
                                 <div class="col-md-12">
                                    <div class="row">
                                       <div class="col-md-2 col-sm-12" data-column="0">
                                          <input type="text" class="column_filter form-control" name="name" id="col0_filter" placeholder="Product Head"> 
                                       </div>
                                       <div class="col-md-2 col-sm-12" data-column="1">
                                          <select class="column_filter selectize-select" name="status" id="col1_filter" placeholder="Select Status">
                                             <option value="">Select Status</option>
                                             <option value="1">Active</option>
                                             <option value="2">Inactive</option>
                                          </select>
                                       </div>
                                       <div class="col-md-3 col-sm-12">
                                          <a href="javascript:void(0)" onclick="return datatable_search_filter();" title="Search"><span class="btn btn-icon btn-info waves-effect waves-light"><i class="ft-search"></i></span></a>&nbsp;
                                          <a href="javascript:void(0)" onclick="return datatable_reset_filter();"  id="reset" title="Reset"><span class="btn btn-icon btn-warning waves-effect waves-light"><i class="ft-x"></i></span></a>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-md-12 pt-1">
                                    <div class="row">
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <table class="table table-striped table-bordered table-hover" id="datatable_list" width="100%">
                           <input type="hidden" id="token" class="form-control form-filter" name="_token" value="{{csrf_token()}}">
                           <thead>
                              <tr role="row" class="heading">
                                 <th width="1%"><input type="checkbox" class="group-checkable"></th>
                                 <th width="80%">Product Head</th>
                                 <th width="9%">Status</th>
                                 <th width="10%">Actions</th>
                              </tr>
                           </thead>
                           <tbody></tbody>
                        </table>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </section>
   </div>
</div>
</div>
@endsection
@section('scripts')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{ asset('/assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/additional-methods.min.js')}}" type="text/javascript"></script>
<!-- Start Bootstrap Switch -->
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/toggle/bootstrap-switch.min.js') }}"></script>
<!-- End Bootstrap Switch -->
<!--Start Selectize-->
<script src="{{ asset('/assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
<!--End Selectize-->
<script src="{{ asset('assets/pages/scripts/admin/master/product_head_list.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection