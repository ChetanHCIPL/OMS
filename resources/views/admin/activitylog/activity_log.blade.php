@extends('layouts.admin')
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->

<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.default.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/plugins/forms/selectize/selectize.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/extensions/datedropper.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/css/pages/timeline.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/css/plugins/loaders/loaders.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/css/plugins/core/colors/palette-loader.css')}}" rel="stylesheet" type="text/css" />

<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
@php
$language_path = Config::get('path.language_path');
@endphp
<div class="content-wrapper">
    <div class="content-body">
		<section class="content-header clearfix">
			<h3>Activity Log</h3>
			<ol class="breadcrumb">
				<li><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
				<li><a href="javascript:void(0);">Admins</a></li>
				<li class="active">Activity Log</li>
			</ol>
		</section>
        <section class="horizontal-grid" id="horizontal-grid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card activitylog">
                        <div class="card-header">
                            <h4 class="card-title"><i class="material-icons">swap_horiz</i> Activity Log</h4>
                        </div>
                        <div class="card-content collapse show">
                            <div class="card-body">
                                <input type="hidden" id="token" name="_token" value="{{ csrf_token()}}">
                                <input type="hidden" id="startlimit" name="startlimit" value="0">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <input type="text" class="form-control input-md" id="first_name" placeholder="First Name" name="first_name">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <input type="text" class="form-control input-md" id="last_name" placeholder="Last Name" name="last_name">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                               @php  $username = ((isset(Auth::guard('admin')->user()->username))?Auth::guard('admin')->user()->username : "");
                                               @endphp
                                                    <input type="text" class="form-control input-md" id="username" placeholder="Username" name="username" value="{{$username}}">
                                                </div>
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
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    @if(!empty($par_am_array))
                                    <div class="col-md-3">
                                        <select class="selectize-select" name="access_module_id" id="access_module_id" Placeholder="All...">
                                            <option value="">All...</option>
                                            @for($e = 0, $n = count($par_am_array); $e < $n; $e++) 
                                                @if ($par_am_array[$e]['loop'] == 1 && !empty($par_am_array[$e + 1]) && $par_am_array[$e + 1]['loop'] == 2)
                                                    <optgroup label="{{($par_am_array[$e]['path']) }}"> 
                                                @else 
                                                    <option value="{{($par_am_array[$e]['access_module_id'])}}">
                                                    {{ $par_am_array[$e]['path'] }}</option>
                                                @endif
                                            @endfor 
                                        </select>
                                    </div>
                                    @endif
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <button class="btn btn-info waves-effect waves-light filter-submit" onclick="loadActivityLogSingleOrUser()" title="Search"><i class="fa fa-search"></i> Search</button>
                                            <button class="btn btn-warning waves-effect waves-light filter-submit" onclick="resetSearchData()" title="Reset"> Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 timeline-left timeline-wrapper" id="timeline"></div>
            </div>
            <div class="row activitylog-alert-row" id="alert-row" style="display: none;">
                <div class="col-md-12">
                    <div class="alert alert-danger mb-2" role="alert"><strong>Sorry!</strong> No records found.</div>
                </div>
            </div>
            <div class="row" id="load-more-row" style="display: none;">
                <div class="col-md-12 text-right">
                    <button class="btn btn-success btn-min-width mr-1 mb-1 waves-effect waves-light" id="load_more" onclick="loadActivityLog()" type="button">Load More...</button>
                </div>
            </div>
        </section>
        <div class="loader-wrapper" style="display: none;">
            <div class="loader-container">
                <div class="ball-spin-fade-loader loader-blue">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
    </div>
</div>
<button type="button" class="btn btn-outline-info block btn-lg waves-effect waves-light d-none" data-toggle="modal" data-backdrop="false" data-target="#info" id="activity-box">Launch Modal</button>
<div class="modal fade text-left" id="info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info white">
                <h4 class="modal-title white"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body" id="data">               
            </div>
            <div class="modal-footer">
               <button type="button" class="btn grey btn-outline-secondary waves-effect waves-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    var DATE_PICKER_FORMAT = "{{ Config::get('constants.DATE_PICKER_FORMAT')}}";
    var today = "{{date_getSystemDateTime()}}"
</script>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{ asset('/assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/extensions/datedropper.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/pages/scripts/admin/activitylog/activity_log.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection