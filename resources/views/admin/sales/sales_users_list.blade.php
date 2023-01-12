@extends('layouts.admin')
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('/assets/vendors/css/tables/datatable/datatables.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.default.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/plugins/forms/selectize/selectize.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/extensions/datedropper.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/fancybox/dist/jquery.fancybox.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('/assets/vendors/css/fancybox/dist/jquery.fancybox.min.css')}}" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN: Vendor CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/js/gallery/photo-swipe/photoswipe.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/js/gallery/photo-swipe/default-skin/default-skin.css')}}">
<!-- END: Vendor CSS-->
@endsection
@section('content')
<div class="content-wrapper listing-innerpage">
   <div class="content-body">
      <section class="content-header clearfix">
         <h3>Sales User</h3>
         <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="javascript:void(0);">Sales Users Mgmt</a></li>
            <li class="active">Sales User</li>
         </ol>
      </section>
      <div class="alert alert-dismissible mb-2 alert-danger d-none" role="alert" id="list-alert">
         <span id="list-msg">
         @if(session()->get('success')) 
         {{ session()->get('success') }}   
         @endif
         </span>
      </div>
      <section class="row">
         <div class="col-12">
            <div class="card">
               <div class="card-head">
                  <div class="card-header  border-bottom search-filter-header">
                     <h4 class="card-title"><i class="material-icons">person_outline</i> Sales User List</h4>
                     <div class="heading-elements">
                        <div class="filter mr-1 float-left">
                           <button type="button" id="show_filter" class="btn btn-info btn-sm waves-effect waves-light"><i class="material-icons"> search </i>Show Filter</button>
                           <button type="button" id="hide_filter" class="btn btn-info btn-sm btn-warning d-none waves-effect waves-light"><i class="material-icons"> close </i>Hide Filter</button>
                        </div>
                        <div class="btn-group">
                           @if (per_hasModuleAccess('SalesUsers', 'Edit') || per_hasModuleAccess('SalesUsers', 'Delete'))                                 
                           <button type="button" class="btn btn-secondary dropdown-toggle btn-sm waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings"></i> Actions</button>
                           <div class="dropdown-menu">
                              @if(per_hasModuleAccess('SalesUsers', 'Edit'))
                              <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Active', 'salesuser','updatestatus');">Active</a>
                              <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Inactive', 'salesuser','updatestatus');">Inactive</a>
                              @endif 
                              @if(per_hasModuleAccess('SalesUsers', 'Delete'))
                              <a id="userMultiDelete"  class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('delete', 'salesuser','multipledelete');">Delete</a>
                              @endif
                              @endif
                           </div>
                           @if(per_hasModuleAccess('SalesUsers', 'Add'))&nbsp;&nbsp;
                           <a href="{{ route('salesuser',['mode'=>'add']) }}" class="btn btn-primary btn-sm waves-effect waves-light" title="Create User"><i class="ft-plus "></i> Create Sales User</a>
                           @endif
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card-content">
                  <div class="card-body">
                     <form action="{{ route('admin.user.muldelete') }}" method="POST" class="reset-form" id="userForm" name="frmlist">
                        <div class="position-relative search-filter-hide">
                           <div class="tab-content px-121 filter-box d-none" id="filter_div">
                              <div class="row border p-1 m-1 bg-advance-filter">
                                 <div class="col-md-12">
                                    <div class="row">
                                       <div class="col-md-2 col-sm-12" data-column="0">
                                          <input type="text" class="column_filter form-control" name="UserName" id="col0_filter" placeholder="User Name">
                                       </div>
                                       <div class="col-md-2 col-sm-12" data-column="1">
                                          <input type="text" class="column_filter form-control" name="Name" id="col1_filter" placeholder="Name">
                                       </div>
                                       <div class="col-md-2 col-sm-12" data-column="2">
                                          <input type="text" class="column_filter form-control" name="Email" id="col2_filter" placeholder="Email">
                                       </div>
                                       <div class="col-md-2 col-sm-12" data-column="5">
                                          <select class="column_filter selectize-select" name="Status" id="col5_filter" placeholder="Status">
                                             <option value="">Select</option>
                                             <option value="1">Active</option>
                                             <option value="2">Inactive</option>
                                          </select>
                                       </div>
                                       <div class="col-md-2 col-sm-12">
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
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <table class="table table-striped table-bordered table-hover access-group-table" id="datatable_list" width="100%">
                           <input type="hidden" id="token" class="form-control form-filter" name="_token" value="{{csrf_token()}}">
                           <input type="hidden" class="user_role" name="user_role" id="user_role"   value="<?php echo isset($acess_groupid) && $acess_groupid != '' ? $acess_groupid : '' ;?>">
                           <thead>
                              <tr role="row" class="heading">
                                 <th width="2%">
                                    <input type="checkbox" name="row_1" class="group-checkable">
                                 </th>
                                 <th width="8%"> Image </th>
                                 <th width="15%"> Name </th>
                                 <th width="15%"> Email </th>
                                 <th width="10%"> User Name </th>
                                 <th width="10%"> Role Name </th>
                                 <th width="13%"> Last Login </th>
                                 <th width="7%"> Login </th>
                                 <th width="10%"> Status </th>
                                 <th width="10%"> Actions </th>
                              </tr>
                           </thead>
                           <tbody></tbody>
                        </table>
                     </form>
                  </div>
               </div>
               <!-- ajax -->
               <div id="ajax-modal" class="modal fade" tabindex="-1" data-width="1000">
               </div>
            </div>
         </div>
      </section>
   </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
   var userajaxlist = "{{ route('sales.userajaxlist') }}"; 
   var DATE_PICKER_FORMAT = "{{ $dateFormate }}";
   var today = "{{date_getSystemDateTime()}}";
   var userrole = "{{date_getSystemDateTime()}}";
</script>
<script src="{{ asset('/assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{ asset('assets/pages/scripts/admin/common/jquery.validate.js') }}"></script>
<script src="{{ asset('assets/pages/scripts/admin/extensions/datedropper.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/pages/scripts/admin/user/user.js?ver=1.5') }}"></script>
<script type="text/javascript" src="{{ asset('assets\pages\scripts\admin\fancybox\dist\jquery.fancybox.js')}}"></script>
@endsection