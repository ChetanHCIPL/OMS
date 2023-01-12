@extends('layouts.admin')
@section('styles')
<link href="{{ asset('/assets/vendors/css/fancybox/dist/jquery.fancybox.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('/assets/vendors/css/fancybox/dist/jquery.fancybox.min.css')}}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
@php
  $status = (isset($data[0]['status']) && $data[0]['status'] == 1) ? "Active" : "Inactive";
  $status_color = Config::get('constants.status_color.' . $status);
  $status_btn = \App\GlobalClass\Design::blade('status',$status,$status_color);
@endphp
<div class="content-wrapper">
  <div class="content-body">

	<section class="content-header clearfix">
		<h3>{{(isset($mode)?$mode:'') }} Segment <strong><span class="text-muted accent-3">{{((isset($data[0]['name'])?' - '.$data[0]['name']:''))}}</span></strong></h3>
		<ol class="breadcrumb">
			<li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
			<li><a href="javascript:void(0);">Master Mgmt</a></li>
			<li><a href="{{route('segment/grid')}}">Segment</a></li>
			<li><a href="javascript:void(0);">{{(isset($mode)?$mode:'') }} Segment</a></li>
		</ol>
    </section>

	<section class="horizontal-grid" id="horizontal-grid">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
			<div class="card-head ">
                 <div class="card-header">
					<div class="float-right">
            <a href="{{route('segment/grid')}}">
                <button type="button" class="btn mr-1 back_btn">
                <span class="material-icons">arrow_back_ios</span> Back  </button>
              </a>
             @if (per_hasModuleAccess('Segment', 'Edit'))
						<a href="{{route('segment',['mode' => 'edit', 'id' => isset($data[0]['id'])?gen_generate_encoded_str($data[0]['id'], '3', '3', ''):''])}}" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light"><i class="la la-edit"></i></span></a>
            @endif
					</div>
				 </div>
			  </div>
            <div class="card-content collapse show">
			  
              <div class="card-body language-input">
                <form class="form form-horizontal" id="frmadd" name="frmadd" enctype="multipart/form-data" action="">
				   <input type="hidden" id="token" class="form-control form-filter" name="_token" value="{{csrf_token()}}">
                  <div class="row">
                    <div class="col-xl-2 col-lg-3 col-md-12 col-12">
                      <div class="sidebar-left site-setting">
                        <div id="accordionWrap5" role="tablist" aria-multiselectable="true">
                          <div class="card collapse-icon accordion-icon-rotate">
                            <div id="heading51" class="card-header">
                              <a data-toggle="collapse" href="#accordion51" aria-expanded="true" aria-controls="accordion51" class="card-title lead">Segment</a>
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
                                    <label class="col-md-3 ">
                                      Segment Name 
                                    </label>
                                    <div class="col-md-9 label-control">
                                     {{(isset($data[0]['name'])?$data[0]['name']:'---')}}
                                    </div>
                                  </div>
                                  
                                  <div class="form-group row mx-auto">
                                    <label class="col-md-3 ">
                                      Medium
                                    </label>
                                    <div class="col-md-9 label-control">
                                     @if(isset($mediums) && isset($data[0]['medium_id']))
                                        @foreach($mediums as $medium)
                                            @if($medium['id'] == $data[0]['medium_id'])
                                            {{$medium['name']}}
                                            @endif
                                        @endforeach
                                     @endif
                                    </div>
                                  </div>
                                  <div class="form-group row mx-auto">
                                    <label class="col-md-3 ">
                                      Semester
                                    </label>
                                    <div class="col-md-9 label-control">
                                     @if(isset($semesters) && isset($data[0]['semester_id']))
                                        @foreach($semesters as $semester)
                                            @if($semester['id'] == $data[0]['semester_id'])
                                            {{$semester['name']}}
                                            @endif
                                        @endforeach
                                     @endif
                                    </div>
                                  </div>
                                  <div class="form-group row mx-auto">
                                    <label class="col-md-3 ">Status</label>
                                    <div class="col-md-9 label-control">
                                     <?php echo $status_btn; ?>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
        		                <div class="col-xl-4 col-12">
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
                            </div>
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
@section('scripts')
<script type="text/javascript" src="{{ asset('/assets/vendors/js/fancybox/dist/jquery.fancybox.js')}}"></script>
<script type="text/javascript">
$(".fancybox").fancybox({
    openEffect  : 'none',
    closeEffect : 'none',
});
</script>
@endsection