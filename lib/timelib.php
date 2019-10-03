<?php
/***************************************************************************
* timelib.php - Time Library
* -------------------------------------------------------------------------
* Author: Matthew Davidson
* Date: 4/24/2012
* Revision: 0.3.2
***************************************************************************/

if(!isset($LIBHEADER)) include('header.php');
$TIMELIB = true;

function get_timestamp($timezone = "UTC"){
global $CFG;
	date_default_timezone_set($timezone);
	$time = time();
	date_default_timezone_set($CFG->timezone);
	return $time;
}

function get_offset(){
global $CFG;
	// Create two timezone objects, one for UTC and one for local timezone
	$LOCAL = new DateTimeZone($CFG->timezone);
	$timeLOCAL = new DateTime("now", $LOCAL);
	$timeOffset = timezone_offset_get($LOCAL,$timeLOCAL);
	return $timeOffset;
}

function ago($timestamp){
global $CFG;
    if(!$timestamp){ return "Never"; };
	$minutes = ""; $seconds = "";
	$difference = (get_timestamp()) - $timestamp;
	if($difference == 0){ return "now"; }
	$ago = $difference >= 0 ? "ago" : "";
	$difference = abs($difference);

	if($difference > 31449600){
        $years = floor($difference / 31449600) > 1 ? floor($difference/31449600) . " years" : floor($difference/31449600) . " year";
        $weeks = "";
        $difference = $difference - (floor($difference / 31449600) * 31449600);
	}
	if($difference == 31449600){
		$years = "1 year";
		$difference = 0;
	}
	if($difference > 604800){
        $weeks = floor($difference / 604800) > 1 ? floor($difference/604800) . " weeks" : floor($difference/604800) . " week";
        $days = "";
        $difference = $difference - (floor($difference / 604800) * 604800);
	}
	if($difference == 604800){
		$weeks = "1 week";
		$difference = 0;
	}
	if($difference > 86400){
        $days = floor($difference / 86400) > 1 ? floor($difference/86400) . " days" : floor($difference/86400) . " day";
        $hours = "";
        $difference = $difference - (floor($difference / 86400) * 86400);
	}
	if($difference == 86400){
		$days = "1 day";
		$difference = 0;
	}
	if($difference > 3600){
        $hours = floor($difference / 3600) > 1 ? floor($difference/3600) . " hrs" : floor($difference/3600) . " hr";
        $minutes = "";
        $difference = $difference - (floor($difference / 3600) * 3600);
	}
	if($difference == 3600){
		$hours = "1 hour";
		$difference = 0;
	}
	if($difference > 60){
        $minutes = floor($difference / 60) > 1 ? floor($difference/60) . " mins" : floor($difference/60) . " min";
        $seconds = "";
        $difference = $difference - (floor($difference / 60) * 60);
	}
	if ($difference == 60){
		$minutes = "1 min";
	}else{ $seconds = floor($difference) > 1 ? $difference . " secs" : $difference . " sec"; }

	if($difference == 0){ $seconds = ""; }

	if(isset($years)){ return "$years $weeks $ago";
	}elseif(isset($weeks)){ return "$weeks $days $ago";
	}elseif(isset($days)){ return "$days $hours $ago";
	}elseif(isset($hours)){ return "$hours $minutes $ago";
	}else{ return "$minutes $seconds $ago"; }
}

function convert_time($time){
	date_default_timezone_set(date_default_timezone_get());
	$time = explode(":",$time);
    $time[1] = empty($time[1]) ? "00" : $time[1];
	if($time[0] > 12){
		return ($time[0]-12) . ":" . $time[1] . "pm";
	}else{
		if($time[0] == "00"){ return "12:" . $time[1] . "am"; }
		return $time[0] . ":" . $time[1] . "am";
	}
}

function cleandates($cleanme) {
    if (strstr($cleanme, "-")) { //date formate is presumed Y-m-d
        return str_replace("-", "", $cleanme);
    } elseif (strstr($cleanme, "/")) { //date formate is presumed m/d/Y
        return substr($cleanme, 6, 4) . substr($cleanme, 0, 2) . substr($cleanme, 3, 2);
    } else {
        return $cleanme;
    }
}

function make_timestamp_from_date($date, $timezone = false) {
    if (strpos($date, "/")) {
        return DateTime::createFromFormat('!m/d/Y', $date)->getTimestamp() + 43200; // Noon
    } elseif (stropos($date, "-")) {
        return DateTime::createFromFormat('!m-d-Y', $date)->getTimestamp() + 43200; // Noon
    } else {
        return strtotime($date) + 43200; // Noon
    }
}
?>