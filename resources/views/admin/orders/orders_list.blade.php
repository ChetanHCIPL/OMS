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
@php
$usertype=Config::get('constants.usertype');
@endphp 
@section('content')
<div class="content-wrapper listing-innerpage">
    <div class="content-body">
		<section class="content-header clearfix">
			<h3>Orders</h3>
			<ol class="breadcrumb">
				<li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
				<li><a href="javascript:void(0);">Orders Mgmt</a></li>
				<li class="active">Orders</li>
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
                            <h4 class="card-title"><i class="material-icons">local_mall</i> Orders Listing</h4>

                            <div class="heading-elements">
                                <div class="filter mr-1 float-left">
                                    <button type="button" id="show_filter" class="btn btn-info btn-sm"><i class="material-icons"> search </i>Show Filter</button>
                                    <button type="button" id="hide_filter" class="btn btn-info btn-sm btn-warning d-none"><i class="material-icons"> close </i>Hide Filter</button>
                                </div>
                                <div class="btn-group">
                                    @if (1==2)
                                    {{-- @if (per_hasModuleAccess('Orders', 'Edit') || per_hasModuleAccess('Orders', 'Delete')) --}}
                                    <button type="button" class="btn btn-secondary dropdown-toggle btn-sm waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings"></i> Actions</button>
                                    <div class="dropdown-menu">
                                        @if (per_hasModuleAccess('Orders', 'Edit')) 
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Active', 'orders');">Active</a>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Inactive', 'orders');">Inactive</a>
                                        @endif
                                        @if (per_hasModuleAccess('Orders', 'Delete'))
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="return checkAction('Delete', 'orders');">Delete</a>
                                        @endif
                                    </div>
                                    @endif
									@if (per_hasModuleAccess('Orders', 'Add'))
                                    &nbsp;&nbsp;<a href="{{route('ordersm',['mode' => 'add', 'id' => ''])}}" class="btn btn-primary btn-sm waves-effect waves-light" title="Create Order"><i class="ft-plus "></i> Create Order</a>@endif
                                     </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="orderstatus-wrap">
                                <ul class="nav nav-tabs nav-underline no-hover-bg orderstatus ">
                                    <li class="nav-item ">
                                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" aria-controls="home" aria-expanded="true">All ({{$ordercount[0]}})</a>
                                    </li>
                                    @foreach($OrderStatus as $key=>$val)
                                    <li class="nav-item ">
                                        <a class="nav-link" id="home-tab{{$val['id']}}" data-toggle="tab" href="#home{{$val['id']}}" aria-controls="home{{$val['id']}}" data-id="{{$val['id']}}" aria-expanded="true">{{$val['name']}} ({{ (!empty($ordercount[$val['id']]))?$ordercount[$val['id']]:'0'; }})</a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>  
                            <form id="frmlist" name="frmlist" action="" class="form-horizontal reset-form" method="post">
                                <div class="position-relative search-filter-hide">
                                    <div class="tab-content px-121 filter-box d-none" id="filter_div">
                                        <div class="row border p-1 m-1 bg-advance-filter">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-sm-12" data-column="0">
                                                        <h3>Date Filter</h3>
                                                    </div>
                                                    <div class="col-sm-12" data-column="0">
                                                        <div class="row">
                                                            <div class="col-sm-6 p-0" data-column="0">
                                                                <label class="label-control col-md-3"><input type="radio" name="ordersearch" class="order_date_wise column_filter" checked value="order_date"> Order Date Wise &nbsp; &nbsp; &nbsp;</label>
                                                                <label class="label-control col-md-3"><input type="radio" name="ordersearch" class="challan_date_wise" value="order_date"> Challan Date Wise &nbsp; &nbsp; &nbsp;</label>
                                                                <label class="label-control col-md-3"><input type="radio" name="ordersearch" class="qc_date_wise" value="order_date"> QC Date Wise &nbsp; &nbsp; &nbsp;</label>
                                                                <label class="label-control col-md-3"> <input type="radio" name="ordersearch" class="bitly_date" value="order_date"> Bitly Date Wise &nbsp; &nbsp; &nbsp;</label>
                                                            </div>
                                                         </div>
                                                    </div>
                                                </div>
                                                <div class="row OrderDateWise">
                                                <div class="col-md-10 col-sm-12">
                                                <div class="row">
                                                    <div class="col-md-2 col-sm-12" data-column="0">
                                                        <input type="date" class="column_filter form-control pr-1" name="fromDate" id="col4_filter" placeholder="From Date">
                                                    </div>
                                                    <div class="col-md-2 col-sm-12" data-column="0">
                                                        <input type="date" class="column_filter form-control pr-1" name="toDate" id="col5_filter" placeholder="To Date">
                                                    </div>
                                                    <div class="col-md-2 col-sm-12" data-column="0">
                                                        <input type="text" class="column_filter form-control" name="orderNo" id="col0_filter" placeholder="Order No">
                                                    </div>
                                                    <div class="col-md-2 col-sm-12 client_name_search" data-column="0">
                                                        <select class="selectize-select column_filter" name="clientName" id="col1_filter" placeholder="Search Client" data-placeholder="Search Client">
                                                            <option value="">Select Client</option>
                                                            @if(!empty($clientData))
                                                                @foreach($clientData as $s)
                                                                    <option value="{{$s['id']}}">{{$s['client_name']}}</option>    
                                                                @endforeach
                                                            @endif
                                                        </select>    
                                                    </div>
                                                    <div class="col-md-2 col-sm-12" data-column="0">
                                                        <select class="select2 form-control column_filter" name="salesUsers" id="col2_filter">
                                                        <option value="">Sales Users </option>
                                                            @if(!empty($SalesUsers))
                                                                @foreach($SalesUsers as $s)
                                                                    <option value="{{$s['id']}}">{{$s['name']}}</option>    
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2 col-sm-12" data-column="3">
                                                        <select class="column_filter form-control" name="couStatus" id="col3_filter" placeholder="Select Status">
                                                            <option value="">Select Status</option>
                                                            @if(!empty($OrderStatus))
                                                                @foreach($OrderStatus as $os)
                                                                <option value="{{$os['id']}}">{{$os['name']}}</option>    
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <input type="hidden" name="orderstatus" id="orderstatus" value="" class="column_filter">
                                                    <div class="col-md-2 col-sm-12">
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
                                                    <th width="5%"> Order Number </th>
                                                    <th width="8%"> Order Date </th>
                                                    <th width="8%"> Order Photo </th>
                                                    <th width="17%">Client Name</th>
                                                    <th width="17%">Sales User</th>
                                                   <th width="8%"> Order Amount</th>
                                                   <th width="8%"> Weight (kg)</th>
                                                   <th width="8%"> Dispatch Date</th>
                                                    <th width="10%"> Status </th>
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
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!--Start Datatable-->
<script>
    var orderstatus=0;
    var clientFilter="{{ route('orders/clientdata') }}";
</script>
<script src="{{ asset('/assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<!--End Datatable-->
<!--Start Selectize-->
<script src="{{ asset('/assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
<!--End Selectize-->
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('/assets/pages/scripts/admin/orders/orders_list.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- <script>
    $(document).on('click', '.orderstatus li', function(){
        $('.orderstatus li').removeClass('active');
        $('ul.orderstatus').toggleClass('expanded');
        $(this).addClass('active');
        var tab_id = $(this).attr('data-tab');
        $('.tab-content').removeClass('current');
        $(this).addClass('current');
        $('#'+tab_id).addClass('current');
    });


</script> -->
@endsection