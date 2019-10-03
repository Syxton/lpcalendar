<?php

//Start Page
include('header.php');

$offset = empty($MYVARS->GET['offset']) ? 0 : $MYVARS->GET['offset'];
$view = empty($MYVARS->GET['view']) ? "cal" : $MYVARS->GET['view'];

// Approximate every 15 seconds
echo '<script type="text/javascript">if(typeof(window.myInterval) == "undefined"){ var myInterval = setInterval(function(){update_login_contents(false,"check");}, 14599);}</script>';
echo page_masthead();
if ($view == "printrange") {
    echo printable_range_form();
} elseif ($view == "month") {
    echo printable_month($offset);
} elseif ($view == "printcal") {
    echo printable_calendar($offset);
} else {
    echo get_calendar_list($offset);
}

//End Page
include('footer.html');
?>