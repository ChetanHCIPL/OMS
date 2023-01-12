@extends('layouts.admin')
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.default.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/plugins/forms/selectize/selectize.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/vendors/css/forms/toggle/switchery.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/core/colors/palette-switch.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/core/colors/palette-callout.css')}}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->

<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/ui/prism.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/forms/tags/tagging.css')}}">
@endsection
@section('content')
@php
$mime_arr = Config::get('constants.email_template_mime'); 
$section_arr = Config::get('constants.email_template_section');
$email_variable_arr =Config::get('constants.email_template');

$cc_email = (isset($cc))?$cc:"";
$from =(isset($data[0]['from'])?$data[0]['from']:'');
$replyto =(isset($data[0]['reply_to'])?$data[0]['reply_to']:'');@endphp
<script type="text/javascript">
  

  var cc = <?php echo json_encode($cc_email)?>;
  var varfrom = <?php echo json_encode($from)?>;
  var replyto = <?php echo json_encode($replyto)?>;

</script>
<div class="content-wrapper">
<div class="content-body">
  <section class="content-header clearfix">

    <h3>{{$mode}} Email Template <strong><span class="text-muted accent-3">{{(isset($data[0]['type']))?' - '.$data[0]['type']:''}}</span></strong></h3>

    <ol class="breadcrumb">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li><a href="javascript:void(0);">Settings</a></li>
      <li><a href="{{route('email-template/grid')}}">Email Template</a></li>
      <li><a href="javascript:void(0);">{{(isset($mode)?$mode:'') }} Email Template</a></li>
    </ol>
  </section>
  <section class="horizontal-grid" id="horizontal-grid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          @if(isset($mode) && $mode == "Update")
              <div class="card-head ">
                <div class="card-header">
                  <div class="float-right">
                     @if (per_hasModuleAccess('EmailTemplate', 'View')) 
                      <a href="{{route('email-template',['mode' => 'view', 'id' => base64_encode($id)])}}" title="View"><span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-eye"></i></span></a>
                  @endif
                  </div>
                </div>
            </div>
          @endif
          <div class="card-content collapse show">
            <div class="card-body">
              <form class="form form-horizontal"  id="frmadd" name="frmadd"  action="">
                  <div class="row">
                    <div class="col-md-12 ">
                      <div class="alert alert-danger d-none">
                        <span id="error-msg">You have some form errors. Please check below.</span>
                        <button class="close" data-close="alert"></button>
                      </div>
                      <div class="alert alert-success d-none">
                        <span id="success-msg">Your form validation is successful!</span>
                        <button class="close" data-close="alert"></button>
                      </div>
                    </div>
                  </div>
                <input type="hidden" name="_token" value="{{csrf_token()}}" />
                <input type="hidden" name="customActionType" value="group_action" />
                <input type="hidden" id="customActionName" name="customActionName" value="{{(isset($mode)?$mode:'') }}" />
                <input type="hidden" id="id" name="id" value="{{(isset($id)?$id:'')}}" />
                <div class="row">
                  <div class="col-xl-2 col-lg-3 col-md-12 col-12">
                    <div class="sidebar-left site-setting">
                      <div id="accordionWrap5" role="tablist" aria-multiselectable="true">
                        <div class="card collapse-icon accordion-icon-rotate">
                          <div id="heading51" class="card-header">
                            <a data-toggle="collapse" href="#accordion51" aria-expanded="true" aria-controls="accordion51" class="card-title lead">Email Tempalte</a>
                          </div>
                          <div id="accordion51" role="tabpanel" data-parent="#accordionWrap5" aria-labelledby="heading51" class="card-collapse collapse show" aria-expanded="true">
                              <div class="card-body">
                                <ul class="nav nav-tabs m-0">
                                  <li class="nav-item">
                                    <a class="nav-link active" id="base-tab_1" data-toggle="tab" aria-controls="tab_1" href="#tab_1" aria-expanded="true">
                                    General Information </a>
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
                            <h3 class="tab-content-title">General Information</h3>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-xl-8">
                          <div class="row">
                            <div class="form-body">
                              <div class="form-group row mx-auto">
                                <label class="col-md-3 label-control">Type <span class="required"></span></label>
                                <div class="col-md-9">
                                 @if($mode == 'Add')                                 
                                      <input class="form-control" placeholder="Type" type="text" id="type" name="type" value="" >
                                  @elseif($mode == 'Update')                                  
                                    <input class="form-control" placeholder="Type" type="text" id="type" name="type" value="{{(isset($data[0]['type'])?$data[0]['type']:'')}}" readonly>
                                  @endif
                                </div>
                              </div>

                              <div class="form-group row mx-auto">
                                <label class="col-md-3 label-control">Section <span class="required"></span></label>
                                <div class="col-md-9">
                                
                                  @if($mode == 'Add')
                                      <select class="selectize-select" id="sectionName" name="sectionName" placeholder="Select Section">
                                          <option value="">Select Section</option> 
                                          @if(!empty($section_arr))
                                            @for($i=1 ;$i<=count($section_arr);$i++) 
                                              <option value="{{ $i }}">{{ $section_arr[$i] }}</option>
                                            @endfor
                                          @endif
                                      </select>
                                  @elseif($mode == 'Update')
                                      <input type="hidden" name="section" id="section" class="form-control" value="{{isset($data[0]['section'])?$data[0]['section']:''}}">
                                      <input class="form-control" placeholder="Section " type="text" id="sectionName" name="sectionName" value="{{(isset($section_arr[$data[0]['section']])?$section_arr[$data[0]['section']]:'')}}" readonly>
                                  @endif
                                </div>
                              </div>
                            
                              <div class="form-group row mx-auto">
                                <label class="col-md-3 label-control">From <span class="required">*</span></label>
                                <div class="col-md-9">
                                   <!--  <input class="form-control" placeholder="From " type="text" id="from"  value="{{$from}}" readonly> -->
                                    <div data-tags-input-name="from" class="from_email" id="from_email"></div>
                                </div>
                              </div>
                              <div class="form-group row mx-auto">
                                <label class="col-md-3 label-control">CC <span class="required">*</span></label>
                                <div class="col-md-9">
                               <!--  <input class="form-control" placeholder="From " type="text" id="cc" name="cc" value="{{implode(', ', $cc_email)}}" readonly> -->
                                  <div data-tags-input-name="cc" class="cc_email" id="cc_email"  value=""></div>
                                </div>
                              </div>
                              <div class="form-group row mx-auto">
                                <label class="col-md-3 label-control">Reply To <span class="required">*</span></label>
                                <div class="col-md-9">
                                <!-- <input class="form-control" placeholder="From " type="text" id="reply_to" name="reply_to" value="{{$replyto}}" readonly> -->
                                <div data-tags-input-name="reply_to" class="reply_email" id="reply_to"></div>

                                </div> 
                              </div>
                              <div class="form-group row mx-auto">
                                <label class="col-md-3 label-control">Content Type <span class="required">*</span></label>
                                <div class="col-md-9">
                                  <select class="selectize-select"  name="mime" placeholder="Mime" id="mime" onchange="changeContent();">
                                    <option value="">Select</option>
                                  
                                      @foreach($mime_arr as $k=>$v)
                                      <option  value="{{$k}}" {{(isset($data[0]['mime']) && $data[0]['mime'] == $k )?'selected' : '' }}>{{$v}}</option>
                                  
                                      @endforeach
                                  </select>
                                </div>
                              </div>
                               <div class="form-group row mx-auto">
                                <label class="col-md-3 label-control">Content <span class="required">*</span></label>
                                <div class="col-md-9">
                                  <textarea class="form-control" placeholder="Content " type="textarea" id="content" name="content" value="">{{(isset($data[0]['content'])?$data[0]['content']:'')}}</textarea>
                                </div>
                              </div>

                              <div class="form-group row mx-auto">
                                <label class="col-md-3 label-control">Status</label>
                                <div class="col-md-9">
                                  <input type="checkbox" class="switchBootstrap" id="status" name="status" data-on-text="Active" data-off-text="Inactive" data-on-color="{{Config::get('constants.switch_on_color')}}" data-off-color="{{Config::get('constants.switch_off_color')}}" value="1"
                                   {{((isset($data[0]['status']) && $data[0]['status'] == 1 )?'checked':($mode == 'Add')?'checked':'')}}/>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-xl-4 col-12">
                           @if(isset($mode) && $mode == 'Update')
                               @php $created_at = (isset($data[0]['created_at']))? date_getFormattedDateTime($data[0]['created_at']): '---';
                                $updated_at = (isset($data[0]['updated_at']))? date_getFormattedDateTime($data[0]['updated_at']): '---'; @endphp
                          
                              <div class="form-group row mx-auto">
                                  <table class="table table-bordered">
                                    <tr>
                                      <td><label class="label-view-control">Created </label></td>
                                      <td class="table-view-control">{{$created_at}}</td>
                                    </tr>
                                    <tr>
                                      <td><label class="label-view-control">Updated </label></td>
                                      <td class="table-view-control">{{$updated_at}}</td>
                                    </tr>
                                  </table>
                              </div>
                          @endif
                        </div>
                      </div>
                    </div>
                    
        						<div class="form-actions text-right">
        						  <a href="{{route('email-template/grid')}}">
        						  <button type="button" class="btn mr-1">
        						  <i class="ft-x"></i> Cancel
        						  </button></a>
        						  <button type="submit" class="btn btn-success" id="save_record">
        						  <i class="la la-check-square-o"></i> Save Changes
        						  </button>
        						</div>
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
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- BEGIN FORM VALIDATION -->
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/additional-methods.min.js')}}" type="text/javascript"></script>
<!-- END FORM VALIDATION -->
<script src="{{ asset('/assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/forms/toggle/bootstrap-switch.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/forms/toggle/switchery.min.js') }}" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<script src="{{ asset('/assets/vendors/js/editors/ckeditor/ckeditor.js') }}" type="text/javascript"></script>
<script type="text/javascript">
  var custom_toolbar = [
    { 'name': 'clipboard', 'items': [  'Undo', 'Redo', '-' ] }, 
    { 'name': 'basicstyles',  'items': [ 'Bold', 'Italic', 'Underline', '-'] },
    { 'name': 'paragraph', 'groups': [ 'list', 'indent', 'blocks', 'align' ], 'items': [ 'BulletedList', 'NumberedList',  '-', 'Outdent', 'Indent', 'Blockquote','-', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
    '/',
    { 'name': 'styles', 'items': [  'Format', 'Font', 'FontSize', 'Source' ] },
    { 'name': 'bidi' , 'items': [ '-', 'BidiLtr', 'BidiRtl']},
    {name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ]}
  ];
</script>
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('/assets/pages/scripts/admin/tools/email_template_add.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script src="{{ asset('/assets/vendors/js/tagging.js') }}" type="text/javascript"></script>
<!-- <script src="{{ asset('assets/pages/scripts/admin/common/select2.full.min.js') }}"></script> -->
<!--Start Toastr-->
@endsection