@extends('layouts.admin')
@section('content')
@php
  $mime_arr = Config::get('constants.email_template_mime');
  $section_arr = Config::get('constants.email_template_section');
@endphp
<div class="content-wrapper">
<div class="content-body">
<section class="content-header clearfix">
  <h3>{{$mode}} Email Template<strong><span class="text-muted accent-3">{{isset($data[0]['type'])?' - '.$data[0]['type']:''}}</span></strong></h3>
  <ol class="breadcrumb">
    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li><a href="javascript:void(0);">Settings</a></li>
    <li><a href="{{route('email-template/grid')}}">Email Template</a></li>
    <li class="active">{{(isset($mode)?$mode:'') }} Email Template</li>
  </ol>
</section>
<section class="horizontal-grid" id="horizontal-grid">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-head ">
            <div class="card-header">
              <div class="float-right">
               @if (per_hasModuleAccess('EmailTemplate', 'Edit'))
                  <a href="{{route('email-template',['mode' => 'edit', 'id' => base64_encode($id)])}}" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light"><i class="la la-edit"></i></span></a>
                @endif
              </div>
            </div>
          </div>
        <div class="card-content collapse show">
          <div class="card-body">
            <form class="form form-horizontal"  id="frmadd" name="frmadd"  action="">
              <div class="row">
                <div class="col-xl-2 col-lg-3 col-md-12 col-12">
                  <div class="sidebar-left site-setting">
                    <div id="accordionWrap5" role="tablist" aria-multiselectable="true">
                      <div class="card collapse-icon accordion-icon-rotate">
                        <div id="heading51" class="card-header">
                          <a data-toggle="collapse" href="#accordion51" aria-expanded="true" aria-controls="accordion51" class="card-title lead">Email Template</a>
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
                                <label class="col-md-3  label-control">Type <span class="required"></span></label>
                                <div class="col-md-8 label-control">
                                 {{(isset($data[0]['type']) && $data[0]['type'] !='')?$data[0]['type']:'---'}}
                                </div>
                              </div>
                              <div class="form-group row mx-auto">
                                <label class="col-md-3  label-control">Section <span class="required"></span></label>
                                <div class="col-md-8 label-control">
                                  {{(isset($section_arr[$data[0]['section']])  && $data[0]['section'] !='')?$section_arr[$data[0]['section']]:'---'}}
                                </div>
                              </div>
                              <div class="form-group row mx-auto">
                                <label class="col-md-3  label-control">From </label>
                                <div class="col-md-8 label-control">
                                  {{(isset($data[0]['from'])  && $data[0]['from'] !='')?$data[0]['from']:'---'}}
                                </div>
                              </div>
                              <div class="form-group row mx-auto">
                                <label class="col-md-3  label-control">CC </label>
                                <div class="col-md-8 label-control">
                                  {{(isset($data[0]['cc'])  && $data[0]['cc'] !='')?$data[0]['cc']:'---'}}
                                </div>
                              </div>
                              <div class="form-group row mx-auto">
                                <label class="col-md-3  label-control">Reply To </label>
                                <div class="col-md-8 label-control">
                                  {{(isset($data[0]['reply_to'])  && $data[0]['reply_to'] !='')?$data[0]['reply_to']:'---'}}
                                </div>
                              </div>
                              <div class="form-group row mx-auto">
                                <label class="col-md-3  label-control">Content Type </label>
                                <div class="col-md-8 label-control">
                                  {{(isset($mime_arr[$data[0]['mime']]) && $data[0]['mime'] !='')?$mime_arr[$data[0]['mime']]:'---'}}
                                </div>
                              </div>
                               <div class="form-group row mx-auto">
                                <label class="col-md-3">Content </label>
                                <div class="col-md-8  label-control">
                                 {{(isset($data[0]['content'])?$data[0]['content']:'')}}
                                </div>
                              </div>
                              <div class="form-group row mx-auto">
                                <label class="col-md-3  label-control">Status</label>
                                <div class="col-md-8 label-control">
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
                           @if(isset($mode) && $mode == 'Update')
                           @php
                                $created_at = (isset($data[0]['created_at']))? date_getFormattedDateTime($data[0]['created_at']): '---';
                                $updated_at = (isset($data[0]['updated_at']))? date_getFormattedDateTime($data[0]['updated_at']): '---';
                          @endphp 
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
						 <a href="{{route('email-template/grid')}}"><button type="button" class="btn mr-1">
                          <i class="material-icons">chevron_left</i>Back</button></a>
						</div>
             </div>
    				</div>
    			  </div>
                </form>
              </div>
            </div>
          </div>
    </section>
  </div>
</div>
@endsection
