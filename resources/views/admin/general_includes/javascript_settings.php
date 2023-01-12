<?php
// use Cache;
?>
<?php if (Config::get('settings.ADMIN_PAGING_TOP') == 'Y' && Config::get('settings.ADMIN_PAGING_BOTTOM') == 'N') { ?>
    <script type="text/javascript">
        var PAGING_DOM = "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t";
    </script>
<?php } elseif (Config::get('settings.ADMIN_PAGING_TOP') == 'N' && Config::get('settings.ADMIN_PAGING_BOTTOM') == 'Y') { ?>
    <script type="text/javascript">
        var PAGING_DOM = "t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>";
    </script>
<?php } else { ?>
    <script type="text/javascript">
        var PAGING_DOM = "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>";
    </script>
<?php }
?>
<?php if (Config::get('settings.REC_LIMIT') != "" && Config::get('settings.REC_LIMIT') != "0") { ?>
    <script type="text/javascript">
        var REC_LIMIT = parseInt('<?php echo Config::get('settings.REC_LIMIT'); ?>');
    </script>
<?php }else{ ?>
    <script type="text/javascript">
        var REC_LIMIT = parseInt(10);
    </script>
<?php } ?>
<script type="text/javascript">
    var DATE_DISPLAY_FORMAT = '<?php echo Config::get("constants.DATE_PICKER_FORMAT"); ?>';
    var DATE_PICKER_SEP = '<?php echo Config::get("constants.DATE_PICKER_SEP"); ?>';
    var assets = '<?php echo url("/"); ?>';
    var public_path = '<?php echo public_path(); ?>';
    var panel_text = '<?php echo Config::get("constants.LN_PANEL_PREFIX"); ?>';
	var route_for_popup = '<?php echo Route::currentRouteName(); ?>';
    var mode = '<?php echo isset($mode)?$mode:'Add' ?>';
</script>