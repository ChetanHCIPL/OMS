@extends('layouts.admin')
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!--Start Datatable-->
<link href="{{ asset('/assets/vendors/css/tables/datatable/datatables.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" /><!--End Datatable-->
<!-- Start Selectize-->
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.default.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/plugins/forms/selectize/selectize.css')}}" rel="stylesheet" type="text/css" />
<!--End Selectize-->
<!-- END PAGE LEVEL PLUGINS -->
@php $mode = isset($mode)?$mode:"Add"; @endphp
@endsection 
@section('content')
<div class="content-wrapper listing-innerpage">
    <div class="content-body">
		<section class="content-header clearfix">
			<h3>Zone</h3>
			<ol class="breadcrumb">
				<li><a href="javascript:void(0);">Dashboard</a></li>
                <li><a href="javascript:void(0);">Master Mgmt</a></li>
				<li class="active">Zone</li>
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
                            <h4 class="card-title"><i class="material-icons">edit_location</i> Zone</h4>
                            <div class="heading-elements">
                                <div class="filter mr-1 float-left">
                                    <button type="button" id="show_filter" class="btn btn-info btn-sm"><i class="material-icons"> search </i>Show Filter</button>
                                    <button type="button" id="hide_filter" class="btn btn-info btn-sm btn-warning d-none"><i class="material-icons"> close </i>Hide Filter</button>
                                </div>
                                <div class="btn-group">
                                   @if (per_hasModuleAccess('Zone', 'Edit'))
                                    <button type="button" class="btn btn-secondary dropdown-toggle btn-sm waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings"></i> Actions</button>
                                    <div class="dropdown-menu">
                                       @if(per_hasModuleAccess('Zone', 'Delete'))
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Active', 'zone');">Active</a>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Inactive', 'zone');">Inactive</a>
                                            {{-- <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Delete', 'zone');">Delete</a> --}}
                                        @endif
                                    </div>
                                    @endif
                                    @if(per_hasModuleAccess('Zone', 'Add'))
                                    &nbsp;&nbsp;
                                    <a href="{{route('zone',['mode' => 'add', 'id' => ''])}}" class="btn btn-primary btn-sm waves-effect waves-light" title="Create Zone"><i class="ft-plus "></i>
                                        <span>Create Zone</span>
                                    </a>
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
                                                        <input type="text" class="column_filter form-control" name="zone_name" id="col0_filter" placeholder="Name"> 
                                                    </div>
                                                    <div class="col-md-2 col-sm-12" data-column="1">
                                                        <input type="text" class="column_filter form-control" name="zone_code" id="col1_filter" placeholder="Zone Code">
                                                    </div>
                                                    <div class="col-md-2 col-sm-12" data-column="2">
                                                        <input type="text" class="column_filter form-control" name="country_name" id="col2_filter" placeholder="Country">
                                                    </div>
                                                    <div class="col-md-2 col-sm-12" data-column="3">
                                                        <input type="text" class="column_filter form-control" name="state_name" id="col3_filter" placeholder="State">
                                                    </div>
                                                    <div class="col-md-2 col-sm-12" data-column="1">
                                                        <select class="column_filter selectize-select" name="status" id="col4_filter" placeholder="Select Status">
                                                            <option value="">Select Status</option>
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
                                <table class="table table-striped table-bordered table-hover" id="datatable_list" width="100%">
                                <input type="hidden" id="token" class="form-control form-filter" name="_token" value="{{csrf_token()}}">
                                <thead>
                                <tr role="row" class="heading">
                                    <th width="1%"><input type="checkbox" class="group-checkable"></th>
                                    <th width="20%">Zone</th>
                                    <th width="20%">Zone Code</th>
                                    <th width="20%">Country</th>
                                    <th width="10%">Created At</th>
                                    <th width="10%">Status</th>
                                    <th width="9%">Actions</th>
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
<button type="button" class="btn btn-outline-info block btn-lg d-none" id="add_modal_box" data-toggle="modal" data-target="#add_zone">Launch Modal</button>
<div class="modal fade text-left" id="add_zone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-focus-on="input:first" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success white">
                <h3 class="modal-title white" id="myModalLabel"><span id="mode_title"></span> Zone </h3>
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
                        <label for="ip">Name <span class="required">*</span></label>
                        <div class="controls">
                            <div class="form-group">
                                <input type="text" placeholder="Name" id="zone" name="name" class="form-control" maxlength="150" required>
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
<!--Start Datatable-->
<script src="{{ asset('/assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<!--End Datatable-->
<!-- Start Selectize -->
<script src="{{ asset('/assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
<!-- End Selectize -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('assets/pages/scripts/admin/master/zone_list.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection