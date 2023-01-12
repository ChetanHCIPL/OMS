@extends('layouts.admin')
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!--Start Datatable--->
<link href="{{ asset('/assets/vendors/css/tables/datatable/datatables.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" /><!--End Datatable--->
<!--Start Selectize-->
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.default.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/plugins/forms/selectize/selectize.css')}}" rel="stylesheet" type="text/css" />
<!--End Selectize-->
<!-- Start Bootstrap Switch -->
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/switchery.min.css')}}">
<link href="{{ asset('assets/css/core/colors/palette-switch.css')}}" rel="stylesheet" type="text/css" />
<!-- End Bootstrap Switch -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
<div class="content-wrapper">
  <div class="content-body">
    <section class="content-header clearfix">
      <h3>Contact Us<strong><span class="text-muted accent-3"></span></strong></h3> 
      <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li><a href="javascript:void(0);">Contact Us Mgmt</a></li>
        <li><a href="{{route('contact-us/grid')}}">Contact Us</a></li>
        <li><a href="javascript:void(0);">{{(isset($mode)?$mode:'') }} Contact Us</a></li>
      </ol>
    </section>
    <section class="horizontal-grid" id="horizontal-grid">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-head ">
                <div class="card-header">
                  <div class="float-right">
                    <a href="{{route('contact-us/grid')}}">
                        <button type="button" class="btn mr-1 back_btn">
                        <span class="material-icons">arrow_back_ios</span> Back  </button>
                      </a>
                  </div>
                </div>
            </div>
            <div class="card-content collapse show">
              <div class="card-body language-input">
                <form class="form form-horizontal" id="frmadd" name="frmadd">
                  <input type="hidden" id="token" class="form-control form-filter" name="_token" value="{{csrf_token()}}">
                  <div class="row">
                    <div class="col-xl-2 col-lg-3 col-md-12 col-12">
                      <div class="sidebar-left site-setting">
                        <div id="accordionWrap5" role="tablist" aria-multiselectable="true">
                          <div class="card collapse-icon accordion-icon-rotate">
                            <div id="heading51" class="card-header">
                              <a data-toggle="collapse" href="#accordion51" aria-expanded="true" aria-controls="accordion51" class="card-title lead">Contact Us</a>
                            </div>
                            <div id="accordion51" role="tabpanel" data-parent="#accordionWrap5" aria-labelledby="heading51" class="card-collapse collapse show" aria-expanded="true">
                              <div class="card-body">
                                <ul class="nav nav-tabs m-0">
                                  <li class="nav-item">
                                    <a class="nav-link active" id="base-tab_1" data-toggle="tab" aria-controls="tab_1" href="#tab_1" aria-expanded="true">
                                    General information </a>
                                  </li>
                                  <li class="nav-item">
                                    <a class="nav-link" id="base-tab_2" data-toggle="tab" aria-controls="tab_2" href="#tab_2" aria-expanded="False">Reply</a>
                                  </li>
                                  <li class="nav-item">
                                    <a class="nav-link" id="base-tab_3" data-toggle="tab" aria-controls="tab_3" href="#tab_3" aria-expanded="False">Mail Log History</a>
                                  </li>
                                </ul>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-xl-10 col-lg-9 col-md-12 col-12">
                      <div class="tab-content">
                        <div class="tab-pane active" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="tab_1"  aria-labelledby="base-tab_1">
                          <div class="row">
                            <div class="col-xl-12">
                              <h3 class="tab-content-title">General information</h3>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-xl-8">
                              <div class="row">
                                <div class="form-body">
                                  <div class="form-group row mx-auto">
                                    <label class="col-md-3">Member Name </label>
                                    <div class="col-md-9 label-control">
                                      {{(isset($data[0]['member_name'])?$data[0]['member_name']:'---')}}
                                    </div>
                                  </div>
                                  <div class="form-group row mx-auto">
                                    <label class="col-md-3">Question Type </label>
                                    <div class="col-md-9 label-control">
                                      {{(isset($type)?$type:'---')}}
                                    </div>
                                  </div>
                                  <div class="form-group row mx-auto">
                                    <label class="col-md-3">Message</label>
                                     <div class="col-md-9 label-control">
                                      {{(isset($data[0]['message'])?$data[0]['message']:'---')}}
                                    </div>
                                  </div>
                                  <div class="form-group row mx-auto">
                                    <label class="col-md-3">Created Date</label>
                                     <div class="col-md-9 label-control">
                                      {{(isset($data[0]['created_at']))? date_getDateTimeAll($data[0]['created_at']): '---';}}
                                      
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            
                          </div>
                        </div>
                        <div class="tab-pane" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="tab_2"  aria-labelledby="base-tab_2">
                          <div class="row">
                            <div class="col-xl-12">
                              <h3 class="tab-content-title">Reply of an Enquiry</h3>
                            </div>
                          </div>
                          <form class="form form-horizontal" id="frmadd" name="frmadd" enctype="multipart/form-data" action="">
                            <input type="hidden" name="_token" value="{{csrf_token()}}" />
                            <input type="hidden" name="customActionType" value="group_action" />
                            <input type="hidden" id="customActionName" name="customActionName" value="SentEmailtoinquiry" />
                            <input type="hidden" id="contact_id" name="contact_id" value="{{(isset($id)?$id:'')}}" />
                            <input type="hidden" id="member_id" name="member_id" value="{{(isset($data[0]['member_id'])?$data[0]['member_id']:'---')}}" />
                            <div class="row">
                              <div class="col-xl-8">
                                <div class="row">
                                  <div class="form-body"> 
                                    <div class="form-group row mx-auto">
                                      <label class="col-md-3 label-control">To <span class="required">*</span></label>
                                      <div class="col-md-9">
                                        <input class="form-control" placeholder="To" type="text" id="email_to" name="email_to" value="{{(isset($data[0]['email_to'])?$data[0]['email_to']:'')}}">
                                      </div>
                                    </div>
                                    <div class="form-group row mx-auto">
                                      <label class="col-md-3 label-control">From <span class="required">*</span></label>
                                      <div class="col-md-9">
                                        <input class="form-control" placeholder="From" type="text" id="email_from" name="email_from" value="{{(isset($email_from)?$email_from:'')}}" readonly>
                                      </div>
                                    </div>
                                    <div class="form-group row mx-auto">
                                      <label class="col-md-3 label-control">Subject <span class="required">*</span></label>
                                      <div class="col-md-9">
                                        <input class="form-control" placeholder="Subject" type="text" id="email_subject" name="email_subject" value="{{(isset($subject)?$subject:'')}}">
                                      </div>
                                    </div>
                                    <div class="form-group row mx-auto">
                                      <label class="col-md-3 label-control">Message<span class="required">*</span></label>
                                      <div class="col-md-9">
                                        <textarea class="form-control" rows="10" cols="10" placeholder="Write Message Here" type="text" id="email_message" name="email_message">{{$message}}</textarea>
                                      </div>
                                    </div>
                                    <div class="form-group row mx-auto">
                                      <label class="col-md-3 label-control">Status <span class="required">*</span></label>
                                      <div class="col-md-9">
                                        <select class="selectize-select" id="email_status" name="email_status" placeholder="Select Status">
                                          <option value="1">Opened </option>
                                          <option value="2" selected>Closed </option>
                                          <option value="3">Cancelled </option>
                                        </select>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="form-actions text-right"><a href="{{route('contact-us/grid')}}">
                                <button type="button" class="btn mr-1">
                                    <i class="ft-x"></i> Cancel
                                </button></a>
                                <button type="submit" class="btn btn-success" id="save_record">
                                    <i class="la la-check-square-o"></i> Save Changes
                                </button>
                            </div>
                          </form>
                        </div>
                        <div class="tab-pane" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="tab_3"  aria-labelledby="base-tab_3">
                          <div class="row">
                            <div class="col-xl-12">
                              <h3 class="tab-content-title">Mail Log History</h3>
                            </div>
                          </div>
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <div class="table-responsive">
                            <table  class="table table-striped table-bordered table-hover" id="datatable_list" width="100%">
                              <thead>
                                <tr role="row" class="heading">
                                  <th width="1%">#</th>
                                  <th width="19%"> Subject </th>
                                  <th width="40%"> Message </th>
                                  <th width="10%"> From</th>
                                  <th width="10%"> To</th>
                                  <th width="10%"> Sent Time </th>
                                  <th width="10%"> Status </th>
                                </tr>
                              </thead>
                              <tbody></tbody>
                            </table>
                          </div>
                        </div>
                        {{--<div class="form-actions text-right">
                          <a href="{{route('contact-us/grid')}}">
                          <button type="button" class="btn mr-1">
                            <i class="material-icons">chevron_left</i>Back
                          </button></a>
                        </div>--}}
                      </div>
                    </div>
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
<script type="text/javascript">
  var contact_id = "{{(isset($id)?$id:'')}}"; 
  var mode = "SentEmailtoinquiry";
</script>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- Start FORM VALIDATION -->
<!--Start Datatable-->
<script src="{{ asset('/assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<!--End Datatable-->
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/additional-methods.min.js')}}" type="text/javascript"></script>
<!-- End FORM VALIDATION -->
<!-- Start Selectize -->
<script src="{{ asset('/assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
<!-- Start Bootstrap Switch -->
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/toggle/bootstrap-switch.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/assets/vendors/js/forms/toggle/switchery.min.js') }}"></script>
<!-- End Bootstrap Switch -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('/assets/pages/scripts/admin/contact_us/contact_us_add.js?ver=1.0.6')}}" type="text/javascript"></script>

<script src="{{ asset('assets/pages/scripts/admin/common/select2.full.min.js') }}"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection