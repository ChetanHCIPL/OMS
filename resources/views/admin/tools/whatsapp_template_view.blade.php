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
@endsection
@section('content')
@php
  $section_arr = Config::get('constants.whatsapp_template_section');
@endphp
<div class="content-wrapper">
	<div class="content-body">
		<section class="content-header clearfix">
		  <h3>{{$mode}} WhatsApp Template<strong><span class="text-muted accent-3">{{isset($data[0]['type'])?' - '.$data[0]['type']:''}}</span></strong></h3>
		  <ol class="breadcrumb">
			<li><a href="javascript:void(0);">Dashboard</a></li>
			<li><a href="javascript:void(0);">Tools</a></li>
			<li><a href="{{route('whatsapp-template/grid')}}">WhatsApp Template</a></li>
      <li class="active">{{(isset($mode)?$mode:'') }} WhatsApp Template</li>
		  </ol>
		</section>
		<section class="horizontal-grid" id="horizontal-grid">
		  <div class="row">
			<div class="col-md-12">
			  <div class="card">
          <div class="card-head ">
              <div class="card-header">
                <div class="float-right">
                 @if(per_hasModuleAccess('WhatsappTemplate','Edit'))
                    <a href="{{route('whatsapp-template',['mode' => 'edit', 'id' => isset($data[0]['id'])?gen_generate_encoded_str($data[0]['id'], '3', '3', ''):''])}}" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light"><i class="la la-edit"></i></span></a>
                  @endif
                </div>
              </div>
            </div>
				<div class="card-content collapse show">
				  <div class="card-body  language-input">
					<form class="form form-horizontal"  id="frmadd" name="frmadd"  action="">
					  <input type="hidden" name="_token" value="{{csrf_token()}}" />
					  <input type="hidden" name="customActionType" value="group_action" />
					  <input type="hidden" id="customActionName" name="customActionName" value="{{(isset($mode)?$mode:'') }}" />
					  <input type="hidden" id="type" name="type" value="{{(isset($data[0]['type'])?$data[0]['type']:'')}}" />
					  <input type="hidden" id="id" name="id" value="{{(isset($data[0]['id'])?$data[0]['id']:'')}}" />
					  <input type="hidden" id="section" name="section" value="{{(isset($section)?$section:'')}}" />
						<div class="row">
                  <div class="col-xl-2 col-lg-3 col-md-12 col-12">
                    <div class="sidebar-left site-setting">
                      <div id="accordionWrap5" role="tablist" aria-multiselectable="true">
                        <div class="card collapse-icon accordion-icon-rotate">
                          <div id="heading51" class="card-header">
                            <a data-toggle="collapse" href="#accordion51" aria-expanded="true" aria-controls="accordion51" class="card-title lead">WhatsApp Template</a>
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
                                <label class="col-md-3">Type </label>
                                <div class="col-md-9  label-control">
                                  {{(isset($data[0]['type'])?$data[0]['type']:'---')}}
                                </div>
                              </div>
                              <div class="form-group row mx-auto">
                                <label class="col-md-3">Section </label>
                                <div class="col-md-9  label-control">
                                  <input type="hidden" name="section" id="section" class="form-control" value="{{isset($data[0]['section'])?$data[0]['section']:''}}">
                                  {{(isset($section_arr[$data[0]['section']])?$section_arr[$data[0]['section']]:'---')}}
                                </div>
                              </div>
                                <div class="form-group row mx-auto">
                                <label class="col-md-3">Content </label>
                                <div class="col-md-9  label-control">
                                 {{(isset($data[0]['content'])?$data[0]['content']:'')}}
                                </div>
                              </div>
                                <div class="form-group row mx-auto">
                                	<label class="col-md-3">Status</label>
	                                <div class="col-md-9  label-control">
                                    @php
                                      $status = (isset($data[0]['status']) && $data[0]['status'] == 1) ? "Active" : "Inactive";
                                      $status_color = Config::get('constants.status_color.' . $status);
                                      $status_btn = \App\GlobalClass\Design::blade('status',$status,$status_color);
                                      echo $status_btn;
                                    @endphp
	                                </div>
                              	</div>
                            </div>
                          </div>
                        </div>
                        <div class="col-xl-4 col-12">
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
                        </div>
                      </div>
                    </div>
          					<div class="form-actions text-right">
                      <a href="{{route('whatsapp-template/grid')}}"><button type="button" class="btn mr-1"><i class="material-icons">chevron_left</i>Back</button></a>
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
	</div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
  var tabId = '<?php echo (isset($data[0]["section"]) ?$data[0]["section"] : "1") ?>';
  window.localStorage.setItem('tabId', tabId);
</script>
@endsection
