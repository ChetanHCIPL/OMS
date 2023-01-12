@extends('layouts.admin')
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/vendors/css/forms/selects/selectize.default.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/plugins/forms/selectize/selectize.css')}}" rel="stylesheet" type="text/css" />


<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')


<div class="content-wrapper clearfix">
  <div class="content-body clearfix">

    <section class="content-header clearfix">
      <h4 class="card-title"><i class="material-icons">settings_brightness</i> Settings</h4>
    

      <ol class="breadcrumb">
         <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li><a href="javascript:void(0);">Tools</a></li>
          <li class="active">Settings</li>
      </ol>
    </section>
    <section class="horizontal-grid" id="horizontal-grid">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-content collapse show">
              <div class="card-body">

                <form class="form form-horizontal" id="frmadd" name="frmadd" enctype="multipart/form-data" action="">
                  <input type="hidden" name="_token" value="{{csrf_token()}}" />
                  <input type="hidden" name="customActionType" value="group_action" />
                  <input type="hidden" name="groupActionName" value="Update" />
                  <div class="row">
                    <div class="col-xl-2 col-lg-3 col-md-12 col-12">
                      <div class="sidebar-left site-setting">
                        <div id="accordionWrap5" role="tablist" aria-multiselectable="true">
                        <div class="card collapse-icon accordion-icon-rotate">
                        <?php foreach($setting_config_type as $key=>$value): ?>
                          <div id="{{$key}}" class="card-header">
                            <a id="{{$key}}tab" data-toggle="collapse" href="#accordion{{$key}}" <?php if ($key != '2') { ?> aria-expanded="false" <?php } else { ?> aria-expanded="true" <?php } ?> aria-controls="{{$key}}" class="card-title lead <?php if ($key != '2') { ?> collapsed <?php } ?>" >{{$value['name']}}</a>
                          </div>
                          <div id="accordion{{$key}}" role="tabpanel" data-parent="#accordionWrap5"  aria-labelledby="{{$key}}" class="card-collapse collapse <?php if ($key == '2') { ?> show <?php } ?>" <?php if ($key != '2') { ?> aria-expanded="false" <?php } else { ?> aria-expanded="true" <?php } ?>>
                            <div class="card-body">
                              <ul class="nav nav-tabs m-0">
                                  <?php if(!empty($value['sub_config'])) :
                                          foreach ($value['sub_config'] as $subkey => $subvalue): ?>
                                            <li class="nav-item">
                                                <a class="nav-link tab_nav <?php if ($key == 2) { ?> active <?php } ?>"  id="subtab{{$key}}_{{$subkey}}" data-toggle="tab" href="#tab{{$key}}_{{$subkey}}"  <?php if ($key == '2') { ?>aria-expanded="true" <?php } else { ?> aria-expanded="false" <?php } ?> aria-controls="subtab{{$key}}_{{$subkey}}" >{{$subvalue}}</a>
                                            </li>
                                  <?php   endforeach; 
                                        endif;
                                      if(!empty($setting_lang_data[$key])):  ?>
                                        <li class="nav-item">
                                        <a class="nav-link" id="lang{{$key}}" data-toggle="tab" aria-controls="language_{{$key}}" href="#language{{$key}}" aria-expanded="False">Language</a>
                                        </li>
                                  <?php endif;
                                  ?>
                              </ul>
                            </div>
                          </div><?php endforeach;
                        ?></div>
                      </div>
                      </div>
                    </div>
                    <div class="col-xl-10 col-lg-9 col-md-12 col-12">
                      <div class="tab-content">
                        <?php
                          foreach($setting_config_type as $key => $value):
                              $cnt = count($fields);
                              if(!empty($value['sub_config'])):
                                foreach ($value['sub_config'] as $subkey => $subvalue):
                        ?>
                        <div class="tab-pane <?php if ($key == 2) { ?> active <?php } ?>" role="tabpanel" <?php if ($key == 2) { ?> aria-expanded="true"<?php } ?>   id="tab{{$key}}_{{$subkey}}"  aria-labelledby="subtab{{$key}}_{{$subkey}}" >
                                <h3 class="tab-content-title"><?php echo ($value['name']) ?></h3>
                                <div class="row">
                                  <div class="col-xl-8">
                                    <div class="row">
                                      <div class="form-body">
                                        <?php if ($cnt > 0): 
                                                for ($i = 0; $i < $cnt; $i++): 
                                                  if($fields[$i]['config_type'] == $key):
                                                    if($fields[$i]['sub_config_type'] == $subkey):
                                        ?>
                                            <div class="form-group row mx-auto">
                                                <label class="col-md-4 label-control" for="userinput1"><?php echo $fields[$i]["desc"]; ?></label>
                                                <div class="col-md-8">
                                                  <?php if ($setting_display_type[$fields[$i]["display_type"]] == 'text') { ?>
                                                  <input type="text" class="form-control" name="<?php echo $fields[$i]["name"] ?>" value="<?php echo $fields[$i]["value"] ?>">
                                                  <?php }
                                                    if ($setting_display_type[$fields[$i]["display_type"]] == 'selectbox') {
                                                      if ($fields[$i]["source"] == '1') {
                                                        $Source_Arr = explode("|", $fields[$i]["source_value"]);
                                                        $nSource_List = count($Source_Arr);     
                                                     ?>
                                                        <?php if ($fields[$i]["select_type"] == '1') { ?>
                                                              <select class="selectize-select" name="<?php echo $fields[$i]["name"] ?>" id="sel">
                                                                <option value="-9"><< Select <?php echo $fields[$i]["desc"] ?> >></option>
                                                                <?php for ($j = 0; $j < $nSource_List; $j++) {
                                                                    $list_arr = explode("::", $Source_Arr[$j]);
                                                                    if ($list_arr[1] == "")
                                                                      $list_arr[1] = $list_arr[0];
                                                                    $selected = "";
                                                                    if ($list_arr[0] == $fields[$i]["value"])
                                                                        $selected = "selected";
                                                                    ?>  
                                                                    <option value="<?php echo $list_arr[0]; ?>" <?php echo $selected ?> ><?php echo $list_arr[1]; ?></option>
                                                                <?php } ?>  
                                                              </select>
                                                        <?php } else { ?>
                                                            <select class="selectize-select" name="<?php echo $fields[$i]["name"] ?>[]" id="sel" multiple>
                                                                <option value="-9"><< Select <?php echo $fields[$i]["desc"] ?> >></option>
                                                                <?php  for ($j = 0; $j < $nSource_List; $j++) {
                                                                    $list_arr = explode("::", $Source_Arr[$j]);
                                                                    if ($list_arr[1] == "")
                                                                      $list_arr[1] = $list_arr[0];
                                                                    $selected = "";
                                                                    $value_arr = explode("|", $fields[$i]["value"]);
                                                                      if (in_array($list_arr[0], $value_arr))
                                                                        $selected = "selected";
                                                                    ?>  
                                                                    <option <?php echo $selected ?> value="<?php echo $list_arr[0]; ?>"><?php echo $list_arr[1]; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        <?php } ?> 
                                                    <?php } else {
                                                        $Source_Arr = DB::select($fields[$i]["source_value"]);
                                                        $nSource_List = count($Source_Arr);
                                    
                                                        if ($fields[$i]["select_type"] == '1') { ?>
                                                          <select class="selectize-select" name="<?php echo $fields[$i]["name"] ?>" id="sel">
                                                               <option value="-9"><< Select <?php echo $fields[$i]["desc"] ?> >></option>
                                                              <?php for ($j = 0; $j < $nSource_List; $j++) {
                                                                  $selected = "";
                                                                  if ($Source_Arr[$j]->country_code == $fields[$i]["value"]){
                                                                    $selected = "selected";
                                                                  }
                                                                  if ($Source_Arr[$j]->isd_code == $fields[$i]["value"]){
                                                                    $selected = "selected";
                                                                  }
                                                                  if($fields[$i]["name"] == 'DEFAULT_ISD'){ ?>
                                                                    <option <?php echo $selected ?> value="<?php echo $Source_Arr[$j]->isd_code; ?>"><?php echo $Source_Arr[$j]->isd_code; ?></option>
                                                                <?php  }else{
                                                                ?>  
                                                                 <option <?php echo $selected ?> value="<?php echo $Source_Arr[$j]->country_code; ?>"><?php echo $Source_Arr[$j]->country_name; ?></option>
                                                              <?php  } } ?>  
                                                          </select>
                                                        <?php } else { ?>
                                                            <select class="selectize-select" name="<?php echo $fields[$i]["name"] ?>[]" id="sel" multiple>
                                                              <option value="-9"><< Select <?php echo $fields[$i]["desc"] ?> >></option>
                                                              <?php
                                                                for ($j = 0; $j < $nSource_List; $j++) {
                                                                  $selected = "";
                                                                  $value_arr = explode("|", $fields[$i]["value"]);
                                                                    if (in_array($Source_Arr[$j]->country_code, $value_arr))
                                                                      $selected = "selected";
                                                                  ?>  
                                                              <option <?php echo $selected ?> value="<?php echo $Source_Arr[$j]->country_code; ?>"><?php echo $Source_Arr[$j]->country_name; ?></option>
                                                              <?php } ?>           
                                                            </select>
                                                          <?php } ?>
                                                  <?php }
                                                    }
                                                    if ($setting_display_type[$fields[$i]["display_type"]] == 'textarea') {  ?>
                                                        <textarea class="form-control input-xlarge" name="<?php echo $fields[$i]["name"] ?>"><?php echo stripslashes($fields[$i]["value"]) ?></textarea>
                                                  <?php }
                                                    if ($setting_display_type[$fields[$i]["display_type"]] == 'checkbox') { ?>
                                                        <div class="checkboxsas"><input type="checkbox" name="<?php echo $fields[$i]["name"] ?>" id="<?php echo $fields[$i]["name"] ?>" value="Y" <?php echo ($fields[$i]["value"] == 'Y') ? "checked" : "" ?>><label for="<?php echo $fields[$i]["name"] ?>"></label></div>
                                                  <?php
                                                    } ?>
                                                </div>
                                            </div>
                                        <?php
                                                  endif;
                                                endif;
                                              endfor;
                                            endif;
                                        ?>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                        </div>
                        <?php  if(!empty($setting_lang_data[$key])): ?>
                          <div class="tab-pane" aria-expanded="true" role="tabpanel" aria-expanded="true"  id="language{{$key}}"  aria-labelledby="language_{{$key}}">
                              <div class="row">
                                <div class="col-xl-12">
                                  <h3 class="tab-content-title">Language</h3>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-xl-8">
                                  <div class="row">
                                   <div class="col-xl-12">
                                    <div class="form-body">
                                    <?php
                                      if (!empty($language)):
                                         $lang_fields = array();
                                         $cnt_lang = 0;
                                         $cnt = count($language);
                                         for ($j = 0; $j < $cnt; $j++):
                                          $direction  = ($language[$j]['direction'] == "2")?"rtl":"ltr";

                                          if(isset($setting_lang_data[$key]) && !empty($setting_lang_data[$key])):
                                            $lang_fields = $setting_lang_data[$key];
                                            $cnt_lang = count($lang_fields);
                                        
                                      ?>
                                      <h4 class="form-section"><img class="flag" onerror="isImageExist(this)" noimage="80x80.jpg" id="show-image" src="{{$language[$j]['Image']}}" alt=""/> {{$language[$j]['lang_name']}}</h4>
                                      <div class="row">
                                        <div class="col-xl-8">
                                          <div class="row">
                                          <div class="form-body">
                                            <?php  if ($cnt_lang > 0): ?>
                                            <?php for ($i = 0; $i < $cnt_lang; $i++): 
                                                if(isset($settings_language[$value['name']][$lang_fields[$i]['name']][$language[$j]['lang_code']])){
                                                    $set_value = $settings_language[$value['name']][$lang_fields[$i]['name']][$language[$j]['lang_code']];
                                                 } else {
                                                   $set_value='';
                                                 }  
                                            ?>
                                              <div class="form-group row mx-auto">
                                                <label class="col-md-4 label-control" for="userinput1"><?php echo $lang_fields[$i]["desc"]; ?></label>
                                                <div class="col-md-8">
                                                  <?php if ($setting_display_type[$lang_fields[$i]["display_type"]] == 'text') { ?>
                                                  <input type="text" class="form-control" name="{{$key}}[<?php echo $lang_fields[$i]["name"] ?>][{{$language[$j]['lang_code']}}]" value="<?php echo isset($set_value)?$set_value:'' ?>" dir="{{$direction}}">
                                                  <?php
                                                  }
                                                  if ($setting_display_type[$lang_fields[$i]["display_type"]] == 'selectbox') {
                                                    if ($lang_fields[$i]["source"] == '1') {
                                                      $Source_Arr = explode(",", $lang_fields[$i]["source_value"]);
                                                      $nSource_List = count($Source_Arr);
                                                  
                                                    ?>
                                                      <?php
                                                      if ($lang_fields[$i]["select_type"] == '1') {
                                                      ?>
                                                          <select class="selectize-select" name="{{$key}}[<?php echo $lang_fields[$i]["name"] ?>][{{$language[$j]['lang_code']}}]" id="sel">
                                                          <option value="-9"><< Select <?php echo $lang_fields[$i]["desc"] ?> >></option>
                                                          <?php
                                                            for ($j = 0; $j < $nSource_List; $j++) {
                                                              $list_arr = explode("::", $Source_Arr[$j]);
                                                              if ($list_arr[1] == ""){
                                                                $list_arr[1] = $list_arr[0];
                                                              }
                                                              $selected = "";
                                                              if ($list_arr[0] == $lang_fields[$i]["value"]){
                                                                $selected = "selected";
                                                              }
                                                            ?>  
                                                            <option <?php echo $selected ?> value="<?php echo $list_arr[0]; ?>"><?php echo $list_arr[1]; ?></option>
                                                          <?php } ?>  
                                                          </select>
                                                      <?php } else { ?>
                                                          <select class="selectize-select" name="{{$key}}[<?php echo $lang_fields[$i]["name"] ?>][{{$language[$j]['lang_code']}}]" id="sel" multiple>
                                                          <?php } ?>
                                                          <option value="-9"><< Select <?php echo $lang_fields[$i]["desc"] ?> >></option>
                                                          <?php
                                                            for ($j = 0; $j < $nSource_List; $j++) {
                                                            $list_arr = explode("::", $Source_Arr[$j]);
                                                            if ($list_arr[1] == "")
                                                              $list_arr[1] = $list_arr[0];
                                                            $selected = "";
                                                            $value_arr = explode("|", $lang_fields[$i]["value"]);
                                                              if (in_array($list_arr[0], $value_arr))
                                                                $selected = "selected";
                                                            ?>  
                                                          <option <?php echo $selected ?> value="<?php echo $list_arr[0]; ?>"><?php echo $list_arr[1]; ?></option>
                                                          <?php } ?>              
                                                          </select>
                                                  <?php
                                                    }
                                                  }
                                                  if ($setting_display_type[$lang_fields[$i]["display_type"]] == 'textarea') {
                                                  ?>
                                                  <textarea dir="{{$direction}}" class="form-control input-xlarge" name="{{$key}}[<?php echo $lang_fields[$i]["name"] ?>][{{$language[$j]['lang_code']}}]"><?php echo stripslashes($set_value) ?></textarea>
                                                  <?php
                                                  }
                                                  if ($setting_display_type[$lang_fields[$i]["display_type"]] == 'checkbox') {
                                                    ?>
                                                  <div class="checkboxsas"><input type="checkbox" name="{{$key}}[<?php echo $lang_fields[$i]["name"] ?>][{{$language[$j]['lang_code']}}]" id="<?php echo $lang_fields[$i]["name"] ?>" value="Y" <?php echo ($lang_fields[$i]["value"] == 'Y') ? "checked" : "" ?>><label for="<?php echo $lang_fields[$i]["name"] ?>"></label></div>
                                                  <?php
                                                  }
                                                  ?>
                                                </div>
                                              </div>
                                            <?php
                                              endfor;
                                            endif;
                                             ?>
                                          </div>
                                          </div>
                                        </div>
                                      </div>
                                      <?php endif;
                                          endfor; 
                                        endif;
                                      ?>
                                    </div>
                                  </div>
                                  </div>
                                </div>
                              </div>
                          </div>
                        <?php endif;  ?>
                      <?php endforeach;
                        endif;
                      endforeach; ?>
                        <div class="form-actions text-right">
                          <button type="submit" class="btn btn-success waves-effect waves-light" id="save_record">
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
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/jquery-validation/js/additional-methods.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('/assets/pages/scripts/admin/tools/site_settings.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection