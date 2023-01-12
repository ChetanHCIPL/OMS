@extends('layouts.admin')
@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/css/bootstrap.css')}}">
@endsection 
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 col-sm-12 col-lg-6 mb-3">
            <div class="card text-white bg-info h-100 mb-0" style="background-color:#0DA6E7">
                <div class="card-content">
                    <div class="position-relative">
                        <div class="chart-title position-absolute mt-2 ml-2 white">
                            <h1 class="display-4 text-white">174630</h1>
                            <span>Today Order</span>
                        </div>
                        <canvas id="emp-satisfaction" class="height-450 block"></canvas>
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 col-lg-6 mb-3">
            <div class="card custom-card-box h-100 mb-0" style="background-color:#0fcc93;">
                <div class="card-content">
                    <div class="earning-chart position-relative">
                        <div class="chart-title position-absolute mt-2 ml-2">
                            <h1 class="display-4">0</h1>
                            <span class="text-muted">Inward Quantity</span>
                        </div>
                        <canvas id="earning-chart" class="height-450 block d-none"></canvas>
                        <div id="area-chart"></div>
                       
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 col-lg-6 mb-3">
            <div class="card custom-card-box h-100 mb-0 bg-danger bg-lighten-4">
                <div class="card-content">
                    <div class="card-body sales-growth-chart">
                        <div id="monthly-sales" ></div>
                    </div>
                </div>
                <!-- <div class="card-footer">
                    <div class="chart-title mb-1 text-center">
                        <span class="text-muted">Total monthly Sales.</span>
                    </div>
                    <div class="chart-stats text-center">
                        <a href="#" class="btn btn-sm btn-info mr-1">Statistics <i class="ft-bar-chart"></i></a> <span class="text-muted">for the last year.</span>
                    </div>
                </div> -->
            </div>
        </div>
        <div class="col-md-6 col-sm-12 col-lg-6 mb-3">
            <div class="card custom-card-box h-100 mb-0">
                <div class="card-content">
                    <div class="card-body text-white bg-white">
                        <h4 class="card-title mb-0">Visitors Sessions</h4>
                        <div class="row">
                            <div class="col-12">
                                <p class="pb-1">Sessions by Browser</p>
                                <div id="donut-rotated" ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-12 mb-3">
            <div class="card h-100 mb-0 custom-card-box p-1" style="background-color:#0fcc93;">
                <div class="card-header bg-transparent  border-0 pb-0">
                    <h4 class="card-title text-white"><i class="material-icons mr-1">shopping_cart</i>Order</h4>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <div class="table-responsive">
                            <!--  -->
                            <table class="table table-transparent ">
                                <thead>
                                    <tr>
                                        <th>Detail</th>
                                        <th class="text-right">Amount</th>
                                        <th class="text-center" >Orders</th>
                                        <th class="text-center">Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row">Today</th>
                                        <td class="text-right">₹  500</td>
                                        <td class="text-center">37</td>
                                        <td class="text-center">500</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Yesterday</th>
                                        <td class="text-right">₹  500</td>
                                        <td class="text-center">37</td>
                                        <td class="text-center">500</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">This Month</th>
                                        <td class="text-right"> ₹  500</td>
                                        <td class="text-center">50</td>
                                        <td class="text-center">1000</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Last Month</th>
                                        <td class="text-right">₹  500</td>
                                        <td class="text-center">50</td>
                                        <td class="text-center">1000</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">This Year</th>
                                        <td class="text-right">₹  500</td>
                                        <td class="text-center">50</td>
                                        <td class="text-center">1000</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Pending Confirmation</th>
                                        <td class="text-right">0</td>
                                        <td class="text-center">0</td>
                                        <td class="text-center">0</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Pending Challan</th>
                                        <td class="text-right">0</td>
                                        <td class="text-center">39</td>
                                        <td class="text-center">1000</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Specimen Order</th>
                                        <td class="text-right">0</td>
                                        <td class="text-center">15</td>
                                        <td class="text-center">560</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12 mb-3">
            <div class="card h-100 mb-0 custom-card-box p-1" style="background-color:#0DA6E7;">
                <div class="card-header bg-transparent  border-0 pb-0">
                    <h4 class="card-title text-white"><i class="material-icons mr-1">list_alt</i>Delivery Challan</h4>
                    
                    
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-transparent">
                                <thead>
                                    <tr>
                                        <th>Detail</th>
                                        <th class="text-right">Amount</th>
                                        <th class="text-right">DC</th>
                                        <th class="text-center">Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row">Today</th>
                                        <td class="text-right">0</td>
                                        <td class="text-right">0</td>
                                        <td class="text-center">0</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Yesterday</th>
                                        <td class="text-right">₹  12800</td>
                                        <td class="text-right">62</td>
                                        <td class="text-center">15622</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">This Month</th>
                                        <td class="text-right">₹  25000</td>
                                        <td class="text-right">62</td>
                                        <td class="text-center">260</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">This Year</th>
                                        <td class="text-right">₹  36500</td>
                                        <td class="text-right">90</td>
                                        <td class="text-center">@twitter</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Pending QC</th>
                                        <td class="text-right">₹  45000</td>
                                        <td class="text-right">15</td>
                                        <td class="text-center">@twitter</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Pending Bilty</th>
                                        <td class="text-right">₹  85000</td>
                                        <td class="text-right">30</td>
                                        <td class="text-center">@twitter</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-12 mb-3">
            <div class="card h-100 mb-0 custom-card-box p-1" style="background-color:#0fcc93;">
                <div class="card-header bg-transparent  border-0 pb-0">
                    <h4 class="card-title text-white"><i class="material-icons mr-1">receipt</i>Purchase Order</h4>
                    
                    
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-transparent">
                                <thead>
                                    <tr>
                                        <th>Detail</th>
                                        <th class="text-right">PO</th>
                                        <th class="text-right">PO Qty</th>
                                        <th class="text-right">Inward</th>
                                        <th class="text-center">Inward Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row">Today</th>
                                        <td class="text-right">0</td>
                                        <td class="text-right">0</td>
                                        <td class="text-right">0</td>
                                        <td class="text-center">0</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Yesterday</th>
                                        <td class="text-right">0</td>
                                        <td class="text-right">0</td>
                                        <td class="text-right">0</td>
                                        <td class="text-center">50,000</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">This Month</th>
                                        <td class="text-right">59</td>
                                        <td class="text-right">650</td>
                                        <td class="text-right">650</td>
                                        <td class="text-center">1,00,000</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">This Year</th>
                                        <td class="text-right">80</td>
                                        <td class="text-right">200</td>
                                        <td class="text-right">650</td>
                                        <td class="text-center">50,000</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Open PO</th>
                                        <td class="text-right">30</td>
                                        <td class="text-right">650</td>
                                        <td class="text-right">10</td>
                                        <td class="text-center">51,845</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Closed PO</th>
                                        <td class="text-right">41</td>
                                        <td class="text-right">650</td>
                                        <td class="text-right">650</td>
                                        <td class="text-center">1,00,000</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Pending Inward</th>
                                        <td class="text-right">41</td>
                                        <td class="text-right">9000</td>
                                        <td class="text-right">0</td>
                                        <td class="text-center">0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12 mb-3">
            <div class="card h-100 mb-0 custom-card-box p-1 " style="background-color:#0DA6E7;">
                <div class="card-header bg-transparent  border-0 pb-0">
                    <h4 class="card-title text-white"><i class="material-icons mr-1">event_note</i>CN</h4>
                    
                    
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-transparent">
                                <thead>
                                    <tr>
                                        <th>Detail</th>
                                        <th class="text-right">Request</th>
                                        <th class="text-right">Request Qty.</th>
                                        <th class="text-center">Inward (CN)</th>
                                        <th class="text-center">Inward Qty.(CN)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row">Today</th>
                                        <td class="text-right">0(0)</td>
                                        <td class="text-right">0</td>
                                        <td class="text-center">0(0)</td>
                                        <td class="text-center">0</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Yesterday</th>
                                        <td class="text-right">500</td>
                                        <td class="text-right">840</td>
                                        <td class="text-center">1260</td>
                                        <td class="text-center">10</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">This Month</th>
                                        <td class="text-right">200</td>
                                        <td class="text-right">140</td>
                                        <td class="text-center">1500</td>
                                        <td class="text-center">90</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">This F.Y.</th>
                                        <td class="text-right">200</td>
                                        <td class="text-right">140</td>
                                        <td class="text-center">1500</td>
                                        <td class="text-center">90</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">This Year</th>
                                        <td class="text-right">2700500(250)</td>
                                        <td class="text-right">36000</td>
                                        <td class="text-center">260555(836)</td>
                                        <td class="text-center">566</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Pending Inward / CN</th>
                                        <td class="text-right">1605806(530)</td>
                                        <td class="text-right">30278</td>
                                        <td class="text-center">0(0)</td>
                                        <td class="text-center">0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card mb-0 custom-card-box p-1" style="background-color:#0fcc93;">
                <div class="card-header bg-transparent  border-0 pb-0">
                    <h4 class="card-title text-white"><i class="material-icons mr-1">book</i>Product Sales Detail</h4>
                    
                    
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-transparent">
                                <thead>
                                    <tr>
                                        <th>Detail</th>
                                        <th class="text-right">Order</th>
                                        <th class="text-right">Pending Dispatched</th>
                                        <th class="text-right">Dispatched</th>
                                        <th class="text-right">Specimen</th>
                                        <th class="text-center">CN Request</th>
                                        <th class="text-right">Pending Inward</th>
                                        <th class="text-right">Inward</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row">Popkorn Eng</th>
                                        <td class="text-right">5600652</td>
                                        <td class="text-right">97155</td>
                                        <td class="text-right">46000</td>
                                        <td class="text-right">8992754</td>
                                        <td class="text-center">2169658</td>
                                        <td class="text-right">243894</td>
                                        <td class="text-right">658025</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Popkorn Guj</th>
                                        <td class="text-right">5600652</td>
                                        <td class="text-right">97155</td>
                                        <td class="text-right">46000</td>
                                        <td class="text-right">8992754</td>
                                        <td class="text-center">2169658</td>
                                        <td class="text-right">243894</td>
                                        <td class="text-right">658025</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">INB Eng</th>
                                        <td class="text-right">5600652</td>
                                        <td class="text-right">97155</td>
                                        <td class="text-right">46000</td>
                                        <td class="text-right">8992754</td>
                                        <td class="text-center">2169658</td>
                                        <td class="text-right">243894</td>
                                        <td class="text-right">658025</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">INB Guj</th>
                                        <td class="text-right">5600652</td>
                                        <td class="text-right">97155</td>
                                        <td class="text-right">46000</td>
                                        <td class="text-right">8992754</td>
                                        <td class="text-center">2169658</td>
                                        <td class="text-right">243894</td>
                                        <td class="text-right">658025</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">INB 9th & 10th</th>
                                        <td class="text-right">5600652</td>
                                        <td class="text-right">97155</td>
                                        <td class="text-right">46000</td>
                                        <td class="text-right">8992754</td>
                                        <td class="text-center">2169658</td>
                                        <td class="text-right">243894</td>
                                        <td class="text-right">658025</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Revise Series Std. 10</th>
                                        <td class="text-right">5600652</td>
                                        <td class="text-right">97155</td>
                                        <td class="text-right">46000</td>
                                        <td class="text-right">8992754</td>
                                        <td class="text-center">2169658</td>
                                        <td class="text-right">243894</td>
                                        <td class="text-right">658025</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Total</th>
                                        <td class="text-right">80000000000</td>
                                        <td class="text-right">97155</td>
                                        <td class="text-right">46000</td>
                                        <td class="text-right">8992754</td>
                                        <td class="text-center">2169658</td>
                                        <td class="text-right">243894</td>
                                        <td class="text-right">658025</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 custom-card-box p-1" style="background-color:#0DA6E7;">
                <div class="card-header bg-transparent  border-0 pb-0">
                    <h4 class="card-title text-white"><i class="material-icons mr-1">info</i>Stock Status</h4>
                    
                    
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-transparent">
                                <thead>
                                    <tr>
                                        <th>Detail</th>
                                        <th class="text-right">Opening</th>
                                        <th class="text-right">Inward</th>
                                        <th class="text-right">Outward</th>
                                        <th class="text-center">CN (Qty)</th>
                                        <th class="text-center">Current (Qty)</th>
                                        <th class="text-right">Value (Amt)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row">Popkorn Eng</th>
                                        <td class="text-right">73</td>
                                        <td class="text-right">331944</td>
                                        <td class="text-right">426948</td>
                                        <td class="text-center">24268</td>
                                        <td class="text-center">196236</td>
                                        <td class="text-right">8006568568</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Popkorn Guj</th>
                                        <td class="text-right">3300</td>
                                        <td class="text-right">159494</td>
                                        <td class="text-right">215589</td>
                                        <td class="text-center">22070</td>
                                        <td class="text-center">96730</td>
                                        <td class="text-right">8006568568</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">INB Guj</th>
                                        <td class="text-right">296098</td>
                                        <td class="text-right">2181367</td>
                                        <td class="text-right">2829293</td>
                                        <td class="text-center">287142</td>
                                        <td class="text-center">789552</td>
                                        <td class="text-right">8006568568</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Popkorn Guj</th>
                                        <td class="text-right">2400</td>
                                        <td class="text-right">491371</td>
                                        <td class="text-right">481823</td>
                                        <td class="text-center">60473</td>
                                        <td class="text-center">196546</td>
                                        <td class="text-right">8006568568</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">INB Guj</th>
                                        <td class="text-right">0</td>
                                        <td class="text-right">248820</td>
                                        <td class="text-right">167226</td>
                                        <td class="text-center">19303</td>
                                        <td class="text-center">99904</td>
                                        <td class="text-right">8006568568</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">INB Eng</th>
                                        <td class="text-right">0</td>
                                        <td class="text-right">12075</td>
                                        <td class="text-right">5813</td>
                                        <td class="text-center">0</td>
                                        <td class="text-center">6262</td>
                                        <td class="text-right">8006568568</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Total</th>
                                        <td class="text-right">301871</td>
                                        <td class="text-right">3425071</td>
                                        <td class="text-right">4126690</td>
                                        <td class="text-center">413256</td>
                                        <td class="text-center">1385230</td>
                                        <td class="text-right">8006568568</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://www.google.com/jsapi"></script>
<script src="{{ asset('/assets/vendors/js/vendors.min.js')}}" type="text/javascript"></script>

<script src="{{ asset('/assets/vendors/js/ui/charts/jquery.sticky.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/charts/jquery.sparkline.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/charts/raphael-min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/charts/morris.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/charts/chartist.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/charts/chartist-plugin-tooltip.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/charts/chart.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/js/scripts/cards/card-charts.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/js/scripts/charts/google/pie/donut-rotated.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/js/scripts/charts/google/line/area.js')}}" type="text/javascript"></script>
@endsection