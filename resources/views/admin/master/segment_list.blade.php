@extends('layouts.admin')
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!--Start Datatable--->
<link href="{{ asset('/assets/vendors/css/tables/datatable/datatables.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" /><!--End Datatable--->
<!--Start Selectize--->
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.default.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/plugins/forms/selectize/selectize.css')}}" rel="stylesheet" type="text/css" />
<!--End Selectize--->
<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
<div class="content-wrapper listing-innerpage">
    <div class="content-body">
		<section class="content-header clearfix">
			<h3>Segment</h3>
			<ol class="breadcrumb">
				<li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
				<li><a href="javascript:void(0);">Product Mgmt</a></li>
				<li class="active">Segment</li>
			</ol>
		</section>
        <div class="alert alert-dismissible mb-2 d-none" role="alert" id="list-alert">
            <span id="list-msg">Change a <a href="#" class="alert-link">few things up</a> and submit again.</span>
        </div>
        <section class="row">
            <div class="col-12">  
                <div class="card">
                    <div class="card-head ">
                        <div class="card-header border-bottom  search-filter-header">
                            <h4 class="card-title"><i class="material-icons">supervisor_account</i> Segment Listing</h4>

                            <div class="heading-elements">
                                <div class="filter mr-1 float-left">
                                    <button type="button" id="show_filter" class="btn btn-info btn-sm"><i class="material-icons"> search </i>Show Filter</button>
                                    <button type="button" id="hide_filter" class="btn btn-info btn-sm btn-warning d-none"><i class="material-icons"> close </i>Hide Filter</button>
                                </div>
                                <div class="btn-group">
                                    @if (per_hasModuleAccess('Segment', 'Edit') || per_hasModuleAccess('Segment', 'Delete')) 
                                    <button type="button" class="btn btn-secondary dropdown-toggle btn-sm waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings"></i> Actions</button>
                                    <div class="dropdown-menu">
                                        @if (per_hasModuleAccess('Segment', 'Edit')) 
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Active', 'segment');">Active</a>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Inactive', 'segment');">Inactive</a>
                                        @endif
                                        @if (per_hasModuleAccess('Segment', 'Delete'))
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Delete', 'segment');">Delete</a>
                                        @endif
                                    </div>
                                    @endif
									@if (per_hasModuleAccess('Segment', 'Add'))
                                    &nbsp;&nbsp;<a href="{{route('segment',['mode' => 'add', 'id' => ''])}}" class="btn btn-primary btn-sm waves-effect waves-light" title="Create Segment"><i class="ft-plus "></i> Create Segment </a>@endif
                                     </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form id="frmlist" name="frmlist" action="" class="form-horizontal reset-form" method="post">
                                <div class="position-relative search-filter-hide">
                                    <div class="tab-content px-121 filter-box d-none" id="filter_div">
                                        <div class="row border p-1 m-1 bg-advance-filter">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-2 col-sm-12" data-column="0">
                                                        <input type="text" class="column_filter form-control" name="couName" id="col0_filter" placeholder="Segment Name">
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
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="token" class="form-control form-filter" name="_token" value="{{csrf_token()}}">
                                <div class="table-responsive">
                                        <table  class="table table-striped table-bordered table-hover" id="datatable_list" width="100%">
                                            <thead>
                                                <tr role="row" class="heading">
                                                    <th width="1%"><input type="checkbox" class="group-checkable"></th>
                                                    <th width="80%"> Segment Name </th>
                                                    <th width="9%"> Status </th>
                                                    <th width="10%"> Actions </th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
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
@endsection
@section('scripts')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!--Start Datatable-->
<script src="{{ asset('/assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<!--End Datatable-->
<!--Start Selectize-->
<script src="{{ asset('/assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
<!--End Selectize-->
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('/assets/pages/scripts/admin/master/segment_list.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection