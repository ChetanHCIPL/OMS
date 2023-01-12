<?php
/**
* Get Values of enum Database Field
* @param table name $table, column name $field
* @return string
*/
function getEnumValues($table, $field) {

    $results = DB::select("SHOW COLUMNS FROM " . $table . " LIKE '" . $field . "'");
    $row = formatInAssociativeArray($results);
    return explode("','", preg_replace("/.*\('(.*)'\)/", "\\1", $row["Type"]));
}