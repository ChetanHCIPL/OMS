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

<!-- END PAGE LEVEL PLUGINS -->
@php $mode = isset($mode)?$mode:"Add"; @endphp
@endsection 
@section('content')
<div class="content-wrapper listing-innerpage">
    <div class="content-body">
		<section class="content-header clearfix">
			<h3>Medium</h3>
			<ol class="breadcrumb">
				<li><a href="javascript:void(0);">Dashboard</a></li>
                <li><a href="javascript:void(0);">Master Mgmt</a></li>
				<li class="active">Medium</li>
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
                            <h4 class="card-title"><i class="material-icons">language</i> Medium</h4>
                            <div class="heading-elements">
                                <div class="filter mr-1 float-left">
                                    <button type="button" id="show_filter" class="btn btn-info btn-sm"><i class="material-icons"> search </i>Show Filter</button>
                                    <button type="button" id="hide_filter" class="btn btn-info btn-sm btn-warning d-none"><i class="material-icons"> close </i>Hide Filter</button>
                                </div>
                                <div class="btn-group">
                                   @if (per_hasModuleAccess('Medium', 'Edit'))
                                    <button type="button" class="btn btn-secondary dropdown-toggle btn-sm waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings"></i> Actions</button>
                                    <div class="dropdown-menu">
                                       @if(per_hasModuleAccess('Medium', 'Delete'))
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Active', 'medium');">Active</a>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Inactive', 'medium');">Inactive</a>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Delete', 'medium');">Delete</a>
                                        @endif
                                    </div>
                                    @endif
                                    @if(per_hasModuleAccess('Medium', 'Add'))
                                    &nbsp;&nbsp;<a href="javascript:void(0)" id="add_edit_modal" onclick="add_edit_modal('', 'Add')" class="btn btn-primary btn-sm waves-effect waves-light" title="Create Medium"><i class="ft-plus "></i> Create Medium</a>
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
                                                        <input type="text" class="column_filter form-control" name="name" id="col0_filter" placeholder="Medium"> 
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
                                <input type="hidden" name="product_board" id="product_board" value="<?php echo isset($board_id) && $board_id != '' ? $board_id : '' ;?>">
                                <thead>
                                <tr role="row" class="heading">
                                    <th width="1%"><input type="checkbox" class="group-checkable"></th>
                                    <th width="60%">Medium</th>
                                    <th width="20%">Board</th>
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
<button type="button" class="btn btn-outline-info block btn-lg d-none" id="add_modal_box" data-toggle="modal" data-target="#add_medium">Launch Modal</button>
<div class="modal fade text-left" id="add_medium" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-focus-on="input:first" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success white">
                <h3 class="modal-title white" id="myModalLabel"><span id="mode_title"></span> Medium </h3>
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
                        <label for="medium">Board <span class="required">*</span></label>
                        <div class="controls">
                            <div class="form-group">
                                <select class="form-control" id="board_id" name="board_id" placeholder="Select Board" data-placeholder="Select Board"> 
                                    @if(!empty($medium))
                                        @php $cnt_cou = count($medium); @endphp
                                        @for($i=0;$i<$cnt_cou;$i++)
                                            @if(isset($data[0]['medium_id']) && $data[0]['medium_id'] == $medium[$i]['id'])
                                                @php $selected = "selected"; @endphp
                                            @else
                                                @php $selected = ""; @endphp
                                            @endif
                                       <option value="{{$medium[$i]['id']}}" {{$selected}}>{{$medium[$i]['name']}} </option>
                                       @endfor
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="medium">Medium <span class="required">*</span></label>
                        <div class="controls">
                            <div class="form-group">
                                <input type="text" placeholder="Medium" id="medium" name="name" class="form-control" maxlength="150" required>
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
<!-- Start FORM VALIDATION -->
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/additional-methods.min.js')}}" type="text/javascript"></script>
<!-- End FORM VALIDATION -->
<!-- Start Selectize -->
<script src="{{ asset('/assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/js/scripts/forms/select/form-select2.js') }}" type="text/javascript"></script>

<!-- End Selectize -->
<!--Start Bootstrap Switch--->
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/toggle/bootstrap-switch.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/toggle/switchery.min.js') }}"></script>
<!--End Bootstrap Switch--->
<script src="{{ asset('/assets/js/scripts/forms/custom-file-input.js') }}"></script>
<!-- END PAGE LEVEL PLUGINS -->

<script type="text/javascript" src="{{ asset('/assets/vendors/js/fancybox/dist/jquery.fancybox.js')}}"></script>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{ asset('/assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/additional-methods.min.js')}}" type="text/javascript"></script>
<!-- Start Bootstrap Switch -->
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/toggle/bootstrap-switch.min.js') }}"></script>
<!-- End Bootstrap Switch -->
<script src="{{ asset('assets/pages/scripts/admin/master/medium_list.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->

@endsection