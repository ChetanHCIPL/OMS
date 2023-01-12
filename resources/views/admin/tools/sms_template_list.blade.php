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
@endsection
@section('content')
<div class="content-wrapper  listing-innerpage">
    <div class="content-body">
        <section class="content-header clearfix">
            <h3>SMS Templates</h3>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="javascript:void(0);">Tools</a></li>
                <li class="active">SMS Tempaltes</li>
            </ol>
        </section>
        <div class="alert alert-dismissible mb-2 d-none" role="alert" id="list-alert">
            <span id="list-msg">Change a <a href="#" class="alert-link">few things up</a> and submit again.</span>
        </div>
        <section class="row">
            <div class="col-12">   
                <div class="card">
                    <div class="card-head">
                        <div class="card-header  border-bottom search-filter-header">
                            <h4 class="card-title"><i class="material-icons">sms</i> SMS Template Listing</h4>
                           <div class="heading-elements">
                                <div class="btn-group">
	                                <div class="filter mr-1 float-left">
	                                    <button type="button" id="show_filter" class="btn btn-info btn-sm"><i class="material-icons"> search </i>Show Filter</button>
	                                    <button type="button" id="hide_filter" class="btn btn-info btn-sm btn-warning d-none"><i class="material-icons"> close </i>Hide Filter</button>
	                                </div>
	                                <div class="btn-group"> 
                                        @if (per_hasModuleAccess('SMSTemplate', 'Edit')) 
                                        <button type="button" class="btn btn-secondary dropdown-toggle btn-sm waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="ft-settings"></i>
                                            Actions
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Active', 'sms-template');">
                                                <span>Active</span>
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Inactive', 'sms-template');">
                                                <span>Inactive</span>
                                            </a>
                                        </div>
                                        @endif
                        			</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body ">
                            <ul class="nav nav-tabs nav-top-border no-hover-bg" id="secttab">
                                    @if(isset($section_arr))
                                        @foreach($section_arr as $k=>$v)
		                                    <li class="nav-item">
		                                       <a aria-selected="true" class="nav-link" data-toggle="tab" href="#tab_{{$k}}" id="{{$k}}" role="tab">{{$v}}</a>
		                                    </li>
	                                    @endforeach
	                                 @endif
                            </ul>
                         <form id="frmlist" name="frmlist" action="" class="form-horizontal" method="post">
                           <div class="position-relative search-filter-hide">
                                    <div class="tab-content px-121 filter-box d-none" id="filter_div">
                                        <div class="row border p-1 m-1 bg-advance-filter">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-2 col-sm-12" data-column="0">
                                                        <input type="text" class="column_filter form-control"  name="search_type" id="col0_filter" placeholder="Type">
                                                    </div>
                                                    <div class="col-md-2 col-sm-12" data-column="1">
                                                        <select class="column_filter selectize-select" name="search_status" id="col1_filter" placeholder="Select Status">
                                                            <option value="">Select Status</option>
                                                            <option value="1">Active</option>
                                                            <option value="2">Inactive</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 col-sm-12">
                                                        <a href="javascript:void(0)" onclick="return search_filter();" title="Search"><span class="btn btn-icon btn-info waves-effect waves-light"><i class="ft-search"></i></span></a>&nbsp;
                                                        <a href="javascript:void(0)" onclick="return reset_filter();"  id="reset" title="Reset"><span class="btn btn-icon btn-warning waves-effect waves-light"><i class="ft-x"></i></span></a>
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
                             <input type="hidden" id="token" class="form-control form-filter" name="_token" value="{{csrf_token()}}">
                            <div class="tab-content">
                            @if(isset($section_arr)) 
                                @foreach($section_arr as $k=>$v) 
                                <div aria-labelledby="module-tab" class="tab-pane fade  show" id="tab_{{$k}}" role="tabpanel">
                                    <table class="table table-striped table-bordered table-hover datatable_list" id="datatable_{{$k}}" width="100%"> 
                                        <thead>
                                            <tr role="row" class="heading">
                                                <th width="5%"><input type="checkbox" class="group-checkable"></th>
                                                <th width="55%">Type</th>
                                                <th width="20%">Status</th>
                                                <th width="20%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                 @endforeach
                                @endif
                            </div>
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
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{ asset('/assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>

<script src="{{ asset('/assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('/assets/pages/scripts/admin/tools/sms_template_list.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection