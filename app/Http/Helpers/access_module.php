<?php
/**
* Get Child Modules of any Module
*/
function am_getChildAccessModuleList($iParent = 0, $old_cat = "", $iCatIdNot = "0", $loop = 1, $maxloop = 5, $acc_mod_assoc_arr = array()) {
    global $par_am_array;

    if ($loop <= $maxloop && isset($acc_mod_assoc_arr[$iParent]) && is_array($acc_mod_assoc_arr[$iParent])) {
        foreach ($acc_mod_assoc_arr[$iParent] as $Pid => $db_amodule_rs) {
            if ($iCatIdNot != $db_amodule_rs['access_module_id']) {
                $par_am_array[] = array('access_module_id' => $db_amodule_rs['access_module_id'], 'path' => html_entity_decode($old_cat . "--|" . $loop . "|&nbsp;&nbsp;" . $db_amodule_rs['access_module']), 'loop' => $loop);
                am_getChildAccessModuleList($db_amodule_rs['access_module_id'], $old_cat . "&nbsp;&nbsp;&nbsp;&nbsp;", $iCatIdNot, $loop + 1, $maxloop, $acc_mod_assoc_arr);
            }
        }
    }
    $old_cat = "";
    return $par_am_array;
}