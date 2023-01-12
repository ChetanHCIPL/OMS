@extends('layouts.admin')
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('/assets/vendors/css/tables/datatable/datatables.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/core/colors/palette-switch.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/extensions/datedropper.min.css')}}" rel="stylesheet" type="text/css" />

<!-- Start Bootstrap Switch -->
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/switchery.min.css')}}">
<!-- End Bootstrap Switch -->
<!-- END PAGE LEVEL PLUGINS -->
@php $mode = isset($mode)?$mode:"Add"; @endphp
@endsection 
@section('content')
<div class="content-wrapper listing-innerpage">
   <div class="content-body">
      <section class="content-header clearfix">
         <h3>Account Year</h3>
         <ol class="breadcrumb">
            <li><a href="javascript:void(0);">Dashboard</a></li>
            <li><a href="javascript:void(0);">Master Mgmt</a></li>
            <li class="active">Account Year</li>
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
                     <h4 class="card-title"><i class="material-icons">bar_chart</i> Account Year</h4>
                     <div class="heading-elements">
                        <div class="filter mr-1 float-left">
                           <button type="button" id="show_filter" class="btn btn-info btn-sm"><i class="material-icons"> search </i>Show Filter</button>
                           <button type="button" id="hide_filter" class="btn btn-info btn-sm btn-warning d-none"><i class="material-icons"> close </i>Hide Filter</button>
                        </div>
                        <div class="btn-group">
                           @if (per_hasModuleAccess('AccountYear', 'Edit'))
                           <button type="button" class="btn btn-secondary dropdown-toggle btn-sm waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings"></i> Actions</button>
                           <div class="dropdown-menu">
                              @if(per_hasModuleAccess('AccountYear', 'Delete'))
                              <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Active', 'account_year');">Active</a>
                              <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Inactive', 'account_year');">Inactive</a>
                              <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Delete', 'account_year');">Delete</a>
                              @endif
                           </div>
                           @endif
                           @if(per_hasModuleAccess('AccountYear', 'Add'))
                           &nbsp;&nbsp;<a href="javascript:void(0)" id="add_edit_modal" onclick="add_edit_modal('', 'Add')" class="btn btn-primary btn-sm waves-effect waves-light" title="Create Account Year"><i class="ft-plus "></i> Create Account Year</a>
                           @endif
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
                                          <input type="text" class="column_filter form-control" name="name" id="col0_filter" placeholder="Account Year"> 
                                       </div>
                                       <div class="col-md-2">
                                                <div class="form-group">
                                                    <input type="text" class="form-control input-md" id="date_from" placeholder="From Date" name="date_from">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <input type="text" class="form-control input-md" id="date_to" placeholder="To Date" name="date_to">
                                                </div>
                                            </div>
                                       <div class="col-md-2 col-sm-12" data-column="1">
                                          <select class="column_filter selectize-select form-control" name="status" id="col1_filter" placeholder="Select Status">
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
                                 <th width="30%">Name</th>
                                 <th width="10%">From Date</th>
                                 <th width="10%">To Date</th>
                                 <th width="10%">Added Date</th>
                                 <th width="10%">Update Date</th>
                                 <th width="10%">Status</th>
                                 <th width="6%">Actions</th>
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
<button type="button" class="btn btn-outline-info block btn-lg d-none" id="add_modal_box" data-toggle="modal" data-target="#add_account_year">Launch Modal</button>
<div class="modal fade text-left" id="add_account_year" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-focus-on="input:first" data-backdrop="static" data-keyboard="false">
   <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
         <div class="modal-header bg-success white">
            <h3 class="modal-title white" id="myModalLabel"><span id="mode_title"></span> Account Year </h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
         </div>
         <form name="frmadd" id="frmadd" action="">
            <input type="hidden" name="_token" value="{{csrf_token()}}" />
            <input type="hidden" name="customActionType" value="group_action" />
            <input type="hidden" id="customActionName" name="customActionName" value="{{(isset($mode)?$mode:'') }}" />
            <input type="hidden" id="id" name="id" value=""/>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                     <label for="account_year">Name <span class="required">*</span></label>
                     <div class="controls">
                        <div class="form-group">
                           <input type="text" placeholder="Name" id="account_year" name="name" class="form-control" maxlength="150" required>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-12">
                     <label for="auto_approval_limit">Auto Approval Limit <span class="required">*</span></label>
                     <div class="controls">
                        <div class="form-group">
                           <input type="number" placeholder="Approval Limit" id="auto_approval_limit" name="auto_approve_limit" class="form-control" required>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-12">
                     <label for="status">Status <span class="required"></span></label>
                     <div class="controls">
                        <div class="form-group">
                           <input type="checkbox" class="switchBootstrap" id="status" name="status" data-on-text="Active" data-off-text="Inactive" data-on-color="{{Config::get('constants.switch_on_color')}}" data-off-color="{{Config::get('constants.switch_off_color')}}" value="1"
                           {{((isset($data[0]['status']) && $data[0]['status'] == 1 ) ?'checked':($mode == 'Add')?'checked':'') }}/>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" id="btn_close_modal" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
               <button type="submit" class="btn btn-success" id="save_record">Save changes</button>
            </div>
         </form>
      </div>
   </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
var DATE_PICKER_FORMAT = "{{ Config::get('constants.DATE_PICKER_FORMAT')}}";

</script>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{ asset('/assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/additional-methods.min.js')}}" type="text/javascript"></script>
<!-- Start Bootstrap Switch -->
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/toggle/bootstrap-switch.min.js') }}"></script>
<script src="{{ asset('/assets/vendors/js/extensions/datedropper.min.js') }}" type="text/javascript"></script>
<!-- End Bootstrap Switch -->
<script src="{{ asset('assets/pages/scripts/admin/master/account_year_list.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection