@extends('layouts.admin')
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!--Start Datatable--->
<link href="{{ asset('/assets/vendors/css/tables/datatable/datatables.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" /><!--End Datatable--->
<!--Start Selectize--->
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/extensions/datedropper.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.default.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/plugins/forms/selectize/selectize.css')}}" rel="stylesheet" type="text/css" />
<!--End Selectize--->
<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
@php $question_type = config('constants.contact_us_question_type');@endphp
<div class="content-wrapper listing-innerpage">
    <div class="content-body">
		<section class="content-header clearfix">
			<h3>Contact Us</h3>
			<ol class="breadcrumb">
				<li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
				<li><a href="javascript:void(0);">Contact Us</a></li>
				<li class="active">Contact Us</li>
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
                            <h4 class="card-title"><i class="material-icons">list_alt</i> Contact Us Listing</h4>

                            <div class="heading-elements">
                                <div class="filter mr-1 float-left">
                                    <button type="button" id="show_filter" class="btn btn-info btn-sm"><i class="material-icons"> search </i>Show Filter</button>
                                    <button type="button" id="hide_filter" class="btn btn-info btn-sm btn-warning d-none"><i class="material-icons"> close </i>Hide Filter</button>
                                </div>
                                <div class="btn-group">
                                    @if (per_hasModuleAccess('ContactUs', 'Edit') || per_hasModuleAccess('ContactUs', 'Delete')) 
                                    <button type="button" class="btn btn-secondary dropdown-toggle btn-sm waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings"></i> Actions</button>
                                    <div class="dropdown-menu">
                                        @if (per_hasModuleAccess('ContactUs', 'Delete')) 
                                           <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Delete', 'contact-us');">Delete</a>
                                       @endif
                                    </div> 
                                    @endif    
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
                                                    <div class="col-md-3 col-sm-12" data-column="3">
                                                        <select class="column_filter selectize-select" name="member" id="member" placeholder="Select Member">
                                                            <option value="">Select Member</option>
                                                            @foreach($member_data as $member)
                                                            <option  value="{{$member['id']}}">{{$member['first_name'].' '.$member['last_name'].' ('. $member['mobile'].')'}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 col-sm-12" data-column="1">
                                                        <select class="column_filter selectize-select" name="question_type" id="question_type" placeholder="Select Question Type">
                                                            <option value="">Select Question Type</option>
                                                            @foreach($question_type as $key => $que_type)
                                                            <option value="{{$key}}">{{$que_type}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>                                                    
                                                    <div class="col-md-3 col-sm-12" data-column="0">
                                                        <input class="column_filter form-control" placeholder="Created Date" type="text" id="created_at" name="created_at" readonly="true"/>
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
                                                    <th width="25%"> Member Name </th>
                                                    <th width="10%"> Question Type</th>
                                                    <th width="34%"> Message </th>
                                                    <th width="20%"> Created Date </th>
                                                    <th width="20%"> Status </th>
                                                    <th width="10%"> Actions </th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
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
<script type="text/javascript">
    var DATE_PICKER_FORMAT = "{{ $dateFormate }}";
</script> 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!--Start Datatable-->
<script src="{{ asset('/assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<!--End Datatable-->
<!--Start Selectize-->
<script src="{{ asset('/assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
<!--End Selectize-->
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('/assets/vendors/js/extensions/datedropper.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/pages/scripts/admin/contact_us/contact_us_list.js?ver=1.5')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection