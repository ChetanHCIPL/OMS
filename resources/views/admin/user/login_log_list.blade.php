@extends('layouts.admin')
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('/assets/vendors/css/tables/datatable/datatables.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/extensions/datedropper.min.css')}}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
<div class="content-wrapper listing-innerpage">
    <div class="content-body">
		<section class="content-header clearfix">
			<h3>Login History</h3>
			<ol class="breadcrumb">
				<li><a href="javascript:void(0);">Dashboard</a></li>
				<li><a href="javascript:void(0);">Admins</a></li>
				<li><a href="javascript:void(0);">Users Mgmt</a></li>
				<li class="active">Login History</li>
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
                            <h4 class="card-title"><i class="material-icons">person_outline</i> Login History Listing</h4>
                            <!-- <a class="heading-elements-toggle"><i class="la la-ellipsis-h font-medium-3"></i></a> -->
                            <div class="heading-elements">
                                <div class="filter mr-1 float-left">
                                    <button type="button" id="show_filter" class="btn btn-info btn-sm"><i class="material-icons"> search </i>Show Filter</button>
                                    <button type="button" id="hide_filter" class="btn btn-info btn-sm btn-warning d-none"><i class="material-icons"> close </i>Hide Filter</button>
                                </div>
                                <div class="btn-group">
                                @if (per_hasModuleAccess('LoginLog', 'Delete'))
                                    <button type="button" class="btn btn-secondary dropdown-toggle btn-sm waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings"></i> Actions</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Delete', 'login-history');">Delete</a>
                                    </div>
                                @endif
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form id="frmlist" name="frmlist" action="" class="form-horizontal  reset-form" method="post">
                                <div class="position-relative search-filter-hide">
                                    <div class="tab-content px-121 filter-box d-none" id="filter_div">
                                        <div class="row border p-1 m-1 bg-advance-filter">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-2 col-sm-12" data-column="0">
                                                        <input type="text" class="column_filter form-control" name="Name" id="col0_filter" placeholder="Name">
                                                    </div>
                                                    <div class="col-md-2 col-sm-12" data-column="1">
                                                        <input type="text" class="column_filter form-control" name="Ip" id="col1_filter" placeholder="Ip">
                                                    </div>
                                                    <div class="col-md-2 col-sm-12" data-column="2">
                                                        <input type="text" class="form-control input-md" id="col2_filter" placeholder="Login From" name="login_date_from">
                                                    </div>
                                                    <div class="col-md-2 col-sm-12" data-column="3">
                                                        <input type="text" class="form-control input-md" id="col3_filter" placeholder="Login To" name="login_date_to">
                                                    </div>
                                                    <div class="col-md-2 col-sm-12" data-column="4">
                                                        <input type="text" class="form-control input-md" id="col4_filter" placeholder="Logout From" name="logout_date_from">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pt-1">
                                                <div class="row">
                                                    <div class="col-md-2 col-sm-12" data-column="5">
                                                        <input type="text" class="form-control input-md" id="col5_filter" placeholder="Logout To" name="logout_date_from">
                                                    </div>
                                                    <div class="col-md-2 col-sm-12">
                                                        <a href="javascript:void(0)" onclick="return dt_search();" title="Search"><span class="btn btn-icon btn-info waves-effect waves-light"><i class="ft-search"></i></span></a>&nbsp;
                                                        <a href="javascript:void(0)" onclick="return reset_datatable();"  id="reset" title="Reset"><span class="btn btn-icon btn-warning waves-effect waves-light"><i class="ft-x"></i></span></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-striped table-bordered table-hover" id="datatable_list" width="100%">
                                    <input type="hidden" id="token" class="form-control form-filter" name="_token" value="{{csrf_token()}}">
                                    <thead>
                                    <tr role="row" class="heading">
                                      <th width="2%">
                                            <input type="checkbox" class="group-checkable"><input type="hidden" name="" id="" >
                                        </th>
                                        <th width="15%"> Name </th>
                                        <th width="15%"> Ip </th>
                                        <th width="22%"> Login Time </th>
                                        <th width="15%"> Logout Time </th>
                                        <th width="10%"> Duration </th>
                                        <th width="10%"> Accessed From </th>
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
    var DATE_PICKER_FORMAT = "{{ Config::get('constants.DATE_PICKER_FORMAT')}}";
    var today = "{{date_getSystemDateTime()}}";
    //alert(today);
</script>
<script src="{{ asset('/assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{ asset('/assets/vendors/js/extensions/datedropper.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/pages/scripts/admin/user/login_log_list.js')}}" type="text/javascript"></script>
@endsection