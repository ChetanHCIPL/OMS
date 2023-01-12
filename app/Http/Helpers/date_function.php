<?php

function date_getSystemDateTime() {
    return @date("Y-m-d H:i:s"); //2005-04-01 17:16:17
}

function date_getSystemTimeZone() {
    return @date_default_timezone_get();
}

//get yesterday date in Y-m-d format
function getYesterDayDate() { 
    return date('Y-m-d',strtotime("-1 days")); 
}  

function date_convertDatePickerFormat($date) {
    $DATE_PICKER_FORMAT = Config::get('constants.DATE_PICKER_FORMAT');
    $DATE_PICKER_SEP = Config::get('constants.DATE_PICKER_SEP');
    $date_empty = str_replace("-", "", $date);
    if ($date == "" || $date == "0000-00-00")
        return "";
    else {
        $dt_pi_arr = explode($DATE_PICKER_SEP, $DATE_PICKER_FORMAT);
        $final_date = $DATE_PICKER_FORMAT;
        for ($i = 0; $i < count($dt_pi_arr); $i++) {
            $rep = "";
            switch ($dt_pi_arr[$i]) {
                case "d": $rep = "j";
                    break;
                case "dd": $rep = "d";
                    break;
                case "mm": $rep = "m";
                    break;
                case "m": $rep = "n";
                    break;
                case "MM": $rep = "F";
                    break;
                case "M": $rep = "M";
                    break;
                case "yyyy":$rep = "Y";
                    break;
                case "yy": $rep = "y";
                    break;
            }
            if ($rep != "")
                $final_date = str_replace($dt_pi_arr[$i], $rep, $final_date);
        }
        return @date($final_date, @strtotime($date));
    }
}

function date_getFullTimeDifference( $start, $end )
{
    $uts['start']      =    strtotime( $start );
    $uts['end']        =    strtotime( $end );
    if( $uts['start']!==-1 && $uts['end']!==-1 )
    {
        if( $uts['end'] >= $uts['start'] )
        {
            $date1 = new DateTime($start);
            $date2 = $date1->diff(new DateTime($end));
            return( array('years'=>$date2->y,'months'=>$date2->m,'days'=>$date2->d, 'hours'=>$date2->h, 'minutes'=>$date2->i, 'seconds'=>$date2->s) );
        }else{
            trigger_error( "Ending date/time is earlier than the start date/time", E_USER_WARNING );
        }
    }else{
        trigger_error( "Invalid date/time data detected", E_USER_WARNING );
    }
    return( false );
    /*$uts['start']      =    strtotime( $start );
    $uts['end']        =    strtotime( $end );
    if( $uts['start']!==-1 && $uts['end']!==-1 )
    {
        if( $uts['end'] >= $uts['start'] )
        {
            $diff    =    $uts['end'] - $uts['start'];
            if( $years=intval((floor($diff/31104000))) )
                $diff = $diff % 31104000;
            if( $months=intval((floor($diff/2592000))) )
                $diff = $diff % 2592000;
            if( $days=intval((floor($diff/86400))) )
                $diff = $diff % 86400;
            if( $hours=intval((floor($diff/3600))) )
                $diff = $diff % 3600;
            if( $minutes=intval((floor($diff/60))) )
                $diff = $diff % 60;
            $diff    =    intval( $diff );
            return( array('years'=>$years,'months'=>$months,'days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
        }
        else
        {
            trigger_error( "Ending date/time is earlier than the start date/time", E_USER_WARNING );
        }
    }
    else
    {
        trigger_error( "Invalid date/time data detected", E_USER_WARNING );
    }
    return( false );*/


}
function date_getFullTimeDiffString($timeDiffArr)
{
    $displayTime = '';
    if($timeDiffArr['years'] != 0)
        $displayTime .= $timeDiffArr['years']." ".__('mobile app label.mob_lbl_year');
    if($timeDiffArr['months'] != 0)
        $displayTime .= $timeDiffArr['months']." ".__('mobile app label.mob_lbl_month');
    if($timeDiffArr['days'] != 0)
        $displayTime .= $timeDiffArr['days']." ".__('mobile app label.mob_lbl_days');
    if($timeDiffArr['years'] == 0 && $timeDiffArr['months'] == 0 && $timeDiffArr['days'] == 0)
        $displayTime .= " 0"." ".__('mobile app label.mob_lbl_days');
    if($timeDiffArr['hours'] != 0)
        $displayTime .= $timeDiffArr['hours']." ".__('mobile app label.mob_lbl_hours');
    if($timeDiffArr['minutes'] != 0)
        $displayTime .= $timeDiffArr['minutes']." ".__('mobile app label.mob_lbl_minutes');
    if($timeDiffArr['seconds'] != 0)
        $displayTime .= $timeDiffArr['seconds']." ".__('mobile app label.mob_lbl_seconds');
    return $displayTime;
}

function date_getFullTimeDiffString2($timeDiffArr)
{
    $displayTime = '';
    if($timeDiffArr['years'] != 0)
        $displayTime .= $timeDiffArr['years']." ".__('mobile app label.mob_lbl_year');
    if($timeDiffArr['months'] != 0)
        $displayTime .= $timeDiffArr['months']." ".__('mobile app label.mob_lbl_month');
    if($timeDiffArr['days'] != 0)
        $displayTime .= $timeDiffArr['days']." ".__('mobile app label.mob_lbl_days');
    if($timeDiffArr['hours'] != 0)
        $displayTime .= $timeDiffArr['hours']." ".__('mobile app label.mob_lbl_hours');
    if($timeDiffArr['minutes'] != 0)
        $displayTime .= $timeDiffArr['minutes']." ".__('mobile app label.mob_lbl_minutes');
    if($timeDiffArr['seconds'] != 0)
        $displayTime .= $timeDiffArr['seconds']." ".__('mobile app label.mob_lbl_seconds');
    return $displayTime;
}

function date_total_days($start,$end){
    $uts['start']      =    strtotime( $start );
    $uts['end']        =    strtotime( $end );
    if( $uts['start']!==-1 && $uts['end']!==-1 )
    {
        if( $uts['end'] >= $uts['start'] )
        {
            $date1 = new DateTime($start);
            $date2 = $date1->diff(new DateTime($end));
            return( array('total_days'=>$date2->days));
        }else{
            trigger_error( "Ending date/time is earlier than the start date/time", E_USER_WARNING );
        }
    }else{
        trigger_error( "Invalid date/time data detected", E_USER_WARNING );
    }
    return( false );
}
function date_convertDBDateFormat($date) {
    $DATE_PICKER_FORMAT = Config::get('constants.DATE_PICKER_FORMAT');
    $DATE_PICKER_SEP = Config::get('constants.DATE_PICKER_SEP');
    $date_empty = str_replace($DATE_PICKER_SEP, "", $date);
    if ($date_empty == "" || $date_empty == "00000000")
        return "";
    else {
        $dt_pi_arr = explode($DATE_PICKER_SEP, $DATE_PICKER_FORMAT);
        $dt_arr = explode($DATE_PICKER_SEP, $date);
        $dd = '';
        $mm = '';
        $yyyy = '';
        for ($i = 0; $i < count($dt_pi_arr); $i++) {
            switch ($dt_pi_arr[$i]) {
                case "d": $dd = $dt_arr[$i];
                    break;
                case "dd": $dd = $dt_arr[$i];
                    break;
                case "mm": $mm = $dt_arr[$i];
                    break;
                case "m": $mm = $dt_arr[$i];
                    break;
                case "M":
                    switch ($dt_arr[$i]) {
                        case "Jan":$mm = "01";
                            break;
                        case "Feb":$mm = "02";
                            break;
                        case "Mar":$mm = "03";
                            break;
                        case "Apr":$mm = "04";
                            break;
                        case "May":$mm = "05";
                            break;
                        case "Jun":$mm = "06";
                            break;
                        case "Jul":$mm = "07";
                            break;
                        case "Aug":$mm = "08";
                            break;
                        case "Sep":$mm = "09";
                            break;
                        case "Oct":$mm = "10";
                            break;
                        case "Nov":$mm = "11";
                            break;
                        case "Dec":$mm = "12";
                            break;
                    }
                    break;
                case "MM":
                    switch ($dt_arr[$i]) {
                        case "January":$mm = "01";
                            break;
                        case "February":$mm = "02";
                            break;
                        case "March":$mm = "03";
                            break;
                        case "April":$mm = "04";
                            break;
                        case "May":$mm = "05";
                            break;
                        case "June":$mm = "06";
                            break;
                        case "July":$mm = "07";
                            break;
                        case "August":$mm = "08";
                            break;
                        case "Septembr":$mm = "09";
                            break;
                        case "October":$mm = "10";
                            break;
                        case "November":$mm = "11";
                            break;
                        case "December":$mm = "12";
                            break;
                    }
                    break;
                case "yyyy": $yyyy = $dt_arr[$i];
                    break;
                case "yy": $yyyy = $dt_arr[$i];
                    break;
            }
        }
        $final_date = $yyyy . "-" . $mm . "-" . $dd;
        return $final_date;
    }
}

function date_getTimeDifference($start, $end) {
    $uts['start'] = strtotime($start);
    $uts['end'] = strtotime($end);
    if ($uts['start'] !== -1 && $uts['end'] !== -1) {
        if ($uts['end'] >= $uts['start']) {
            $diff = $uts['end'] - $uts['start'];
            if ($days = intval((floor($diff / 86400))))
                $diff = $diff % 86400;
            if ($hours = intval((floor($diff / 3600))))
                $diff = $diff % 3600;
            if ($minutes = intval((floor($diff / 60))))
                $diff = $diff % 60;
            $diff = intval($diff);
            return( array('days' => $days, 'hours' => $hours, 'minutes' => $minutes, 'seconds' => $diff) );
        }
        else {
            trigger_error("Ending date/time is earlier than the start date/time", E_USER_WARNING);
        }
    } else {
        trigger_error("Invalid date/time data detected", E_USER_WARNING);
    }
    return ( false );
}

function date_getUnixDate($text) {
    if ($text == "" || $text == "0000-00-00")
        return "---";
    else
        return @date('Y-m-d', $text);
}

function date_getUnixFormateDate($text) {
    if ($text == "" || $text == "0000-00-00")
        return "---";
    else
        return @date('j M, y H:i', $text);
}

function date_getUnixDateTime($text) {
    if ($text == "" || $text == "0000-00-00 00:00:00" || $text == "0000-00-00")
        return "---";
    else
        return @date('Y-m-d H:i:s', $text);
}

function date_getSystemDate() {
    return @date("Y-m-d"); //2005-04-01
}
function date_getSystemTime() {
    return @date("H:i:s"); //12:10:15
}

function date_getTimeFormate($text) {
    if ($text == "" || $text == "0000-00-00 00:00:00" || $text == "0000-00-00")
        return "---";
    else
        return @date('H:i:s', strtotime($text));
}

function date_getTimeAmPmFormate($text) {
    if ($text == "" || $text == "0000-00-00 00:00:00" || $text == "0000-00-00")
        return "---";
    else
        return @date('h:i a', strtotime($text));
}
function date_getUnixSystemTime() {
    return time(); //12:10:15
}

#---------- Function Used For Showing Date in a Short Form ---------------#

function date_getDateUSFormatShort($text) {
    if ($text == "" || $text == "0000-00-00" || $text == "0000-00-00 00:00:00")
        return "---";
    else
        return date('d-M-Y', strtotime($text));
}

function date_getDateUSFormat($text) {
    if ($text == "" || $text == "0000-00-00" || $text == "0000-00-00 00:00:00")
        return "---";
    else
        return date('d-M-y', strtotime($text));
}

function date_getDateTime12HourFormat($text) {
    if ($text == "" || $text == "0000-00-00 00:00:00" || $text == "0000-00-00")
        return "---";
    else
        return @date('j-M-Y H:i:s A', strtotime($text));
}

function date_getMyDateFormat($text) {
    if ($text == "" || $text == "0000-00-00" || $text == "0000-00-00 00:00:00")
        return "---";
    else
        return date('d M Y', strtotime($text));
}
function date_getMyDateFormat1($text) {
    if ($text == "" || $text == "0000-00-00" || $text == "0000-00-00 00:00:00")
        return "";
    else
        return date('d-m-Y', strtotime($text));
}
function date_getMyDateFormat2($text) {
    if ($text == "" || $text == "0000-00-00" || $text == "0000-00-00 00:00:00")
        return "---";
    else
        return date('Y/m/d H:i:s',strtotime($text));
}
function date_getMyDateFormat3($text) {
    if ($text == "" || $text == "0000-00-00" || $text == "0000-00-00 00:00:00")
        return "---";
    else
        return date('d-m-y H:i:s',strtotime($text));
}
function date_getFullDate($text) {//will display date in format -> Sat, 24 Jan 2009
    if ($text == "" || $text == "0000-00-00 00:00:00")
        return "---";
    else
        return date('D, j M Y', strtotime($text));
}

function date_getFullDate2($text) {//will display date in format -> Sat, 24 Jan 2009
    if ($text == "" || $text == "0000-00-00 00:00:00")
        return "---";
    else
        return date('l, j F Y', strtotime($text));
}

function date_getTimeinHoursMinute($text) {//will display date in format -> Sat, 24 Jan 2009
    if ($text == "" || $text == "0000-00-00 00:00:00")
        return "---";
    else
        return date('H:i', strtotime($text));
}

function date_getDateTimeAll($text) {
    if ($text == "" || $text == "0000-00-00 00:00:00" || $text == "0000-00-00")
        return "---";
    else
        return @date('j M, y H:i', strtotime($text));
}
function date_getDateTimeAllAmPm($text) {
    if ($text == "" || $text == "0000-00-00 00:00:00" || $text == "0000-00-00")
        return "---";
    else
        return @date('j M Y, H:i A', strtotime($text));
}

function date_getDateTimeAllFromStr($text) {
    if ($text == "" || $text == "0000-00-00 00:00:00" || $text == "0000-00-00")
        return "---";
    else
        return @date('j M, y H:i', $text);
}

function date_getDateFull($text) {
    if ($text == "" || $text == "0000-00-00 00:00:00" || $text == "0000-00-00")
        return "---";
    else
        return @date('jS M Y', strtotime($text));
}

function date_getDateFullWithSeparate($text) {
    if ($text == "" || $text == "0000-00-00 00:00:00" || $text == "0000-00-00")
        return "---";
    else
        return @date('j F,Y', strtotime($text));
}

function date_getDateFullFromTime($text) {
    if ($text == "" || $text == "0000-00-00 00:00:00" || $text == "0000-00-00")
        return "---";
    else
        return @date('j F Y', $text);
}

function date_getDateDBFormat($text) {
    if ($text == "" || $text == "0000-00-00 00:00:00" || $text == "0000-00-00")
        return "---";
    else
        return @date('Y-m-d', strtotime($text));
}

function date_convertDateInDBFormat($text) {
    if ($text == "")
        return "";
    else
        return @date('Y-m-d', strtotime($text));
}

function timeBetween($time1, $time2, $precision = 6) {
    // If not numeric then convert texts to unix timestamps
    if (!is_int($time1)) {
        $time1 = strtotime($time1);
    }
    if (!is_int($time2)) {
        $time2 = strtotime($time2);
    }

    // If time1 is bigger than time2
    // Then swap time1 and time2
    if ($time1 > $time2) {
        $ttime = $time1;
        $time1 = $time2;
        $time2 = $ttime;
    }
    // Set up intervals and diffs arrays
    $intervals = array('year', 'month', 'day', 'hour', 'minute', 'second');
    $diffs = array();

    // Loop thru all intervals
    foreach ($intervals as $interval) {
        // Create temp time from time1 and interval
        $ttime = strtotime('+1 ' . $interval, $time1);
        // Set initial values
        $add = 1;
        $looped = 0;
        // Loop until temp time is smaller than time2
        while ($time2 >= $ttime) {
            // Create new temp time from time1 and interval
            $add++;
            $ttime = strtotime("+" . $add . " " . $interval, $time1);
            $looped++;
        }

        $time1 = strtotime("+" . $looped . " " . $interval, $time1);
        $diffs[$interval] = $looped;
    }

    $count = 0;
    $times = array();
    // Loop thru all diffs
    foreach ($diffs as $interval => $value) {
        // Break if we have needed precission
        if ($count >= $precision) {
            break;
        }
        // Add value and interval 
        // if value is bigger than 0
        if ($value > 0) {
            // Add s if value is not 1
            if ($value != 1) {
                $interval .= "s";
            }
            // Add value and interval to times array
            $times[] = $value . " " . $interval;
            $count++;
        }
    }

    // Return string with times
    return implode(", ", $times);
}
#-----------------------------------------------------------------
/*function date_addDate($text, $da=0, $ma=0, $ya=0, $ha=0)
{
    $h=date('H',strtotime($text));
    $d=date('d',strtotime($text));
    $m=date('m',strtotime($text));
    $y=date('Y',strtotime($text));
    $s=date('s',strtotime($text));
    $mi=date('i',strtotime($text));
    $fromTime =date("Y-m-d H:i:s", mktime($h+$ha, $mi, $s, $m+$ma, $d+$da, $y+$ya));
    return $fromTime;
}*/

function date_addDate($text, $da=0, $ma=0, $ya=0, $ha=0)
{
    //echo $text."<hr>";
    $h=date('H',strtotime($text));
    $d=date('d',strtotime($text));
    $m=date('m',strtotime($text));
    $y=date('Y',strtotime($text));
    $s=date('s',strtotime($text));
    $mi=date('i',strtotime($text));
    //echo "$h-$ha<hr>$d-$da<hr>$m-$ma<hr>$y-$ya";exit;
    $fromTime =date("Y-m-d", mktime($h+$ha, $mi, $s, $m+$ma, $d+$da, $y+$ya));
    //echo "<br>".$fromTime;
    return $fromTime;
}

function date_addDateTime($text, $da=0, $ma=0, $ya=0, $ha=0,$ia=0,$sa=0)
{       
        $h=date('H',strtotime($text));
        $d=date('d',strtotime($text));
        $m=date('m',strtotime($text));
        $y=date('Y',strtotime($text));
        $s=date('s',strtotime($text));
        $i=date('i',strtotime($text));  
    
        $fromTime =date("Y-m-d H:i:s", mktime($h+$ha, $i+$ia, $s+$sa, $m+$ma, $d+$da, $y+$ya));
        return $fromTime;
}
function date_getFullTimeDifferenceFromInt( $start, $end )
{
    $uts['start']      =   $start;
    $uts['end']        =   $end;
    if( $uts['start']!==-1 && $uts['end']!==-1 )
    {
        if( $uts['end'] >= $uts['start'] )
        {
            $diff    =    $uts['end'] - $uts['start'];
            if( $years=intval((floor($diff/31104000))) )
                $diff = $diff % 31104000;
            if( $months=intval((floor($diff/2592000))) )
                $diff = $diff % 2592000;
            if( $days=intval((floor($diff/86400))) )
                $diff = $diff % 86400;
            if( $hours=intval((floor($diff/3600))) )
                $diff = $diff % 3600;
            if( $minutes=intval((floor($diff/60))) )
                $diff = $diff % 60;
            $diff    =    intval( $diff );
            return( array('years'=>$years,'months'=>$months,'days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
        }
        else
        {
            trigger_error( "Ending date/time is earlier than the start date/time", E_USER_WARNING );
        }
    }
    else
    {
        trigger_error( "Invalid date/time data detected", E_USER_WARNING );
    }
    return( false );
}

Function date_getWeekDays(){
    return  array('1'=>'Sunday', '2'=>'Monday', '3'=>'Tuesday', '4'=>'Wednesday', '5'=>'Thursday', '6'=>'Friday', '7'=>'Saturday');
}
function date_getOneHourDiffArray() {
    $hourtime_arr = array(
        array("from"=>"00:00","to"=>" 01:00", "display"=>"12:00 AM to 01:00 AM"),
        array("from"=>"01:01","to"=>" 02:00", "display"=>"01:01 AM to 02:00 AM"),
        array("from"=>"02:01","to"=>" 03:00", "display"=>"02:01 AM to 03:00 AM"),
        array("from"=>"03:01","to"=>" 04:00", "display"=>"03:01 AM to 04:00 AM"),
        array("from"=>"04:01","to"=>" 05:00", "display"=>"04:01 AM to 05:00 AM"),
        array("from"=>"05:01","to"=>" 06:00", "display"=>"05:01 AM to 06:00 AM"),
        array("from"=>"06:01","to"=>" 07:00", "display"=>"06:01 AM to 07:00 AM"),
        array("from"=>"07:01","to"=>" 08:00", "display"=>"07:01 AM to 08:00 AM"),
        array("from"=>"08:01","to"=>" 09:00", "display"=>"08:01 AM to 09:00 AM"),
        array("from"=>"09:01","to"=>" 10:00", "display"=>"09:01 AM to 10:00 AM"),
        array("from"=>"10:01","to"=>" 11:00", "display"=>"10:01 AM to 11:00 AM"),
        array("from"=>"11:01","to"=>" 12:00", "display"=>"11:01 AM to 12:00 PM"),
        array("from"=>"12:01","to"=>" 13:00", "display"=>"12:01 PM to 01:00 PM"),
        array("from"=>"13:01","to"=>" 14:00", "display"=>"01:01 PM to 02:00 PM"),
        array("from"=>"14:01","to"=>" 15:00", "display"=>"02:01 PM to 03:00 PM"),
        array("from"=>"15:01","to"=>" 16:00", "display"=>"03:01 PM to 04:00 PM"),
        array("from"=>"16:01","to"=>" 17:00", "display"=>"04:01 PM to 05:00 PM"),
        array("from"=>"17:01","to"=>" 18:00", "display"=>"05:01 PM to 06:00 PM"),
        array("from"=>"18:01","to"=>" 19:00", "display"=>"06:01 PM to 07:00 PM"),
        array("from"=>"19:01","to"=>" 20:00", "display"=>"07:01 PM to 08:00 PM"),
        array("from"=>"20:01","to"=>" 21:00", "display"=>"08:01 PM to 09:00 PM"),
        array("from"=>"21:01","to"=>" 22:00", "display"=>"09:01 PM to 10:00 PM"),
        array("from"=>"22:01","to"=>" 23:00", "display"=>"10:01 PM to 11:00 PM"),
        array("from"=>"23:01","to"=>" 24:00", "display"=>"11:01 PM to 12:00 AM")
    );
    return $hourtime_arr;
}

## Set Date in the format set at Site settings at TMWPanel
function date_getFormattedDate($date) {
    $format = config('settings.DATE_FORMAT');
    if ($date == "" || $date == "0000-00-00 00:00:00" || $date == "0000-00-00")
        return "---";
    else
        return @date($format, strtotime($date));
}

## Set DateTime in the format set at Site settings at TMWPanel
function date_getFormattedDateTime($date) {
    $format = config('settings.DATETIME_FORMAT');
    if ($date == "" || $date == "0000-00-00 00:00:00" || $date == "0000-00-00")
        return "---";
    else
        if($format == "Duration"){
            $cur_date = date("Y-m-d H:i:s");
            $timeCalc = strtotime($cur_date)-strtotime($date);
            $timeDur = '';
            if ($timeCalc > (12 * 30 * 24 * 60 * 60)) 
            {
                 $timeDur .= round($timeCalc/(12 * 30 * 24 * 60 * 60))." year ";
            }
            else if ($timeCalc > (30 * 24 * 60 * 60)) 
            {
                $timeDur .= round($timeCalc/(30 * 24 * 60 * 60))." month ";
            }
            else if ($timeCalc >  (24 * 60 * 60 )) 
            {
                $timeDur .= round($timeCalc/(24 * 60 * 60 ))." day ";
            }
            else if ($timeCalc >  ( 60 * 60 )) 
            {
                $timeDur .= round($timeCalc/( 60 * 60 ))." hour ";
            }
            else if ($timeCalc >  ( 60 )) 
            {
                $timeDur = round($timeCalc/( 60 ))." min ";
            }
            else if ($timeCalc > 0) 
            {
                $timeDur .= $timeCalc." sec ";
            }            
            return $timeDur . " ago";
        }else{
            return @date($format, strtotime($date));
        }
    
}

 // Comparision function 
function date_compare($element1, $element2) { 
    $datetime1 = strtotime($element1['date']); 
    $datetime2 = strtotime($element2['date']); 
    return $datetime1 - $datetime2; 
}  

 // Comparision function 
function getMonth() { 
    return  date('m'); 
}  

function getTodayDate() { 
    return  date('d'); 
}  

function getTodayDay($text) { 
    return  date('l', strtotime($text));
}  

// function getDaysFromGivenDateAndTodaysDate($dt){
//     $start_date = date("Y-m-d", strtotime($dt))." 00:00:00";
//     $today      = date_getSystemDateTime();
//     $datetime1  = new DateTime($start_date);
//     $datetime2  = new DateTime($today);
//     $difference = $datetime1->diff($datetime2);
//     return ($difference->days+1);
// }
function getDaysFromGivenDateAndTodaysDate($dt){
    $visible_days = 0;

    $start_date = date("Y-m-d", strtotime($dt))." 00:00:00";
    $today      = date_getSystemDateTime();
    if(strtotime($start_date) < strtotime($today)){

        $datetime1  = new DateTime($start_date);
        $datetime2  = new DateTime($today);
        $difference = $datetime1->diff($datetime2);
        $visible_days = ($difference->days+1);;
    }
    return $visible_days;
}

//get 
function getMediaTimeDuration($time) { 
    if($time !='' || !empty($time)){
        return $time.' '.__('mobile app label.mob_lbl_mins'); 
    }else{
        return '';
    }
} 

function getSecondsToMin($time) { 
    if($time !='' || !empty($time)){
       return floor($time/60).' '.__('mobile app label.mob_lbl_mins'); 
    }else{
        return '';
    }
} 

function time_elapsed_string($time, $is_timestemp=true) {
    // Calculate difference between current
    // time and given timestamp in seconds
    if($is_timestemp == false){
        $time = date("Y-m-d h:i:s",$time);
    }
    $diff = time() - $time;
    
    if( $diff < 1 ) {
        return 'less than 1 second ago';
    }
    
    $time_rules = array (
                12 * 30 * 24 * 60 * 60 => __('mobile app label.mob_lbl_year'),
                30 * 24 * 60 * 60    => __('mobile app label.mob_lbl_month'),
                24 * 60 * 60         => __('mobile app label.mob_lbl_days'),
                60 * 60              => __('mobile app label.mob_lbl_hours'),
                60                   => __('mobile app label.mob_lbl_minutes'),
                1                    => __('mobile app label.mob_lbl_seconds')
    );

    foreach( $time_rules as $secs => $str ) {
        
        $div = $diff / $secs;

        if( $div >= 1 ) {
            
            $t = round( $div );
            
            return $t . ' ' . $str .
                ( $t > 1 ? '' : '' ) .' '.__('mobile app label.mob_lbl_ago'); ;
        }
    }
}

function date_getDateDBFormat_strtotime($text) {
    if ($text == "" || $text == "0000-00-00 00:00:00" || $text == "0000-00-00")
        return "---";
    else
        return @date('Y-m-d h:i:s', strtotime($text));
}

/* S - The English ordinal suffix for the day of the month (2 characters st, nd, rd or th.)*/
function date_getDateFormatOrdinalSuffix($text) {
    if ($text == "" || $text == "0000-00-00 00:00:00" || $text == "0000-00-00")
        return "---";
    else
        return @date('dS M Y',strtotime($text));
}
function date_getDayAgoDateFormat($text) {
    $return = "";
    $text = $text." 00:00:00";
    $seconds_ago = (time() - strtotime($text));

    if ($seconds_ago >= 31536000) {
        $return =  intval($seconds_ago / 31536000) . " years ago";
    } elseif ($seconds_ago >= 2419200) {
        $return =  intval($seconds_ago / 2419200) . " months ago";
    } elseif ($seconds_ago >= 86400) {
        $return =  intval($seconds_ago / 86400) . " days ago";
    } elseif ($seconds_ago >= 3600) {
        $return =  "Today";
    } else {
        $return = date_getDateFormatOrdinalSuffix($text);
        //$return =  "Seen less than min ago";
    }
    return $return;
}

/* S - The English ordinal suffix for the day of the month (2 characters st, nd, rd or th.)*/
function date_getDateDisplayFormatOrdinalSuffix($startdate, $enddate) {

    if (($startdate == "" || $startdate == "0000-00-00 00:00:00" || $startdate == "0000-00-00") && ($enddate == "" || $enddate == "0000-00-00 00:00:00" || $enddate == "0000-00-00"))
        return "---";
    else{
            $display_date = '';
            $startdate =  strtotime($startdate);
            $enddate    = strtotime($enddate);

            if($startdate == $enddate){
                $display_date = @date('dS M Y',$startdate);
            }

            else if(date("Y",$startdate)==date("Y",$enddate) && date("M",$startdate)==date("M",$enddate)){
                  $display_date = date("dS",$startdate)." - ".date("dS",$enddate)." ".date("M",$enddate)." ".date("Y",$enddate);
            }

            else if(date("Y",$startdate)==date("Y",$enddate) && date("M",$startdate) != date("M",$enddate)){
                  $display_date = date("dS",$startdate)." ".date("M",$startdate)." - ".date("dS",$enddate)." ".date("M",$enddate)." ".date("Y",$enddate);
            }

            else if(date("Y",$startdate)!=date("Y",$enddate)){
                   $display_date = date("dS",$startdate)." ".date("M",$startdate)." ".date("Y",$startdate)." to ".date("dS",$enddate)." ".date("M",$enddate)." ".date("Y",$enddate);
            }
            return $display_date;
      }

}

function getDateFromGivenAdmissionDateAndVisibleDay($admission_date,$visible_on_day){
    // $visible_on_day = $visible_on_day - 1;    
    $convert_date = date_addDate($admission_date, $da=$visible_on_day, $ma=0, $ya=0, $ha=0);  
    return date_getDateFull($convert_date);
}