<?php
/***************************************************************************
* forms.php - Modal forms
* -------------------------------------------------------------------------
* Author: Matthew Davidson
* Date: 9/27/2019
* Revision: 0.4.6
***************************************************************************/

include('header.php');
echo '
	 <script type="text/javascript">
	 var dirfromroot = "'.$CFG->directory.'";
	 </script>
     <link type="text/css" rel="stylesheet" href="'.$CFG->wwwroot.'/min/?f='.(empty($CFG->directory) ? '' : $CFG->directory . '/').'styles/styles_main.css" />
';

callfunction();

echo '</body></html>';

function new_user(){
global $MYVARS, $CFG;

	if(!isset($VALIDATELIB)){ include_once($CFG->dirroot . '/lib/validatelib.php'); }
	$content = '
        <div class="formDiv" id="new_user_div">
    		<input id="hiddenusername" type="hidden" /><input id="hiddenpassword" type="hidden" />
    		<form id="signup_form">
    			<fieldset class="formContainer">
    				<div class="rowContainer">
    					<label class="rowTitle" for="email">Email Address</label><input type="text" id="email" name="email" data-rule-required="true" data-rule-email="true" data-rule-ajax1="ajax/site_ajax.php::unique_email::&email=::true" data-msg-required="'.get_error_message('valid_req_email').'" data-msg-email="'.get_error_message('valid_email_invalid').'" data-msg-ajax1="'.get_error_message('valid_email_unique').'" /><div class="tooltipContainer info">'.get_help("input_email").'</div>
    				    <div class="spacer" style="clear: both;"></div>
                    </div>
    				<div class="rowContainer">
    					<label class="rowTitle" for="fname">First Name</label><input type="text" id="fname" name="fname" data-rule-required="true" data-msg-required="'.get_error_message('valid_req_fname').'" /><div class="tooltipContainer info">'.get_help("input_fname").'</div>
    				    <div class="spacer" style="clear: both;"></div>
                    </div>
    				<div class="rowContainer">
    					<label class="rowTitle" for="lname">Last Name</label><input type="text" id="lname" name="lname" data-rule-required="true" data-msg-required="'.get_error_message('valid_req_lname').'" /><div class="tooltipContainer info">'.get_help("input_lname").'</div>
                        <div class="spacer" style="clear: both;"></div>
                    </div>
    			  	<div class="rowContainer">
    			  		<label class="rowTitle" for="mypassword">Password</label><input type="password" id="mypassword" name="mypassword" data-rule-required="true" data-rule-minlength="6" data-msg-required="'.get_error_message('valid_req_password').'" data-msg-minlength="'.get_error_message('valid_password_length').'" /><div class="tooltipContainer info">'.get_help("input_password").'</div>
                        <div class="spacer" style="clear: both;"></div>
                    </div>
    			  	<div class="rowContainer">
    				  	<label class="rowTitle" for="vpassword">Verify Password</label><input type="password" id="vpassword" name="vpassword" data-rule-required="true" data-rule-equalTo="#mypassword" data-msg-required="'.get_error_message('valid_req_vpassword').'" data-msg-equalTo="'.get_error_message('valid_vpassword_match').'" /><div class="tooltipContainer info">'.get_help("input_vpassword").'</div><br/>
                        <div class="spacer" style="clear: both;"></div>
                    </div>
    		  		<input class="submit" name="submit" type="submit" value="Sign Up" style="margin: auto;width: 80px;display: block;" />
    			</fieldset>
    		</form>
    	</div>
    ';

	echo create_validation_script("signup_form" , "ajaxapi('/ajax/site_ajax.php','add_new_user','&email=' + encodeURIComponent($('#email').val()) + '&fname=' + escape($('#fname').val()) + '&lname=' + escape($('#lname').val()) + '&password=' + escape($('#mypassword').val()),function(){ var returned = trim(xmlHttp.responseText).split('**'); if(returned[0] == 'true'){ document.getElementById('new_user_div').innerHTML = returned[1];}else{ document.getElementById('new_user_div').innerHTML = returned[1];}});");
    echo format_popup($content,$CFG->sitename.' Signup',"500px");
}

function reset_password(){
global $MYVARS, $CFG;
	$userid = $MYVARS->GET["userid"];
	$alternate = $MYVARS->GET["alternate"];
	echo '<script src="'.$CFG->wwwroot.'/min/?b='.(empty($CFG->directory) ? '' : $CFG->directory . '/').'scripts&f=jquery.min.js,jqvalidate.js,jqvalidate_addon.js,jqmetadata.js" type="text/javascript"></script>';

	if(get_db_row("SELECT * FROM users WHERE userid='$userid' AND alternate='$alternate'")){
		if(!isset($VALIDATELIB)){ include_once($CFG->dirroot . '/lib/validatelib.php'); }
		$content = '<div id="forgot_password">
            			Please type a new password then verify it.  After submitting your new password, you will be logged into the site and your new password will be set.
            			<br /><br />
                        <form id="password_request_form">
            				<fieldset class="formContainer">
            				  	<div class="rowContainer">
            			  			<label class="rowTitle" for="mypassword">Password</label><input value="" type="password" id="mypassword" name="mypassword" data-rule-required="true" data-rule-minlength="6" data-msg-required="'.get_error_message('valid_req_password').'" data-msg-minlength="'.get_error_message('valid_password_length').'" /><div class="tooltipContainer info">'.get_help("input_password").'</div>
                                    <div class="spacer" style="clear: both;"></div>
                                </div>
            				  	<div class="rowContainer">
            					  	<label class="rowTitle" for="vpassword">Verify Password</label><input value="" type="password" id="vpassword" name="vpassword" data-rule-required="true" data-rule-equalTo="#mypassword" data-msg-required="'.get_error_message('valid_req_vpassword').'" data-msg-equalTo="'.get_error_message('valid_vpassword_match').'" /><div class="tooltipContainer info">'.get_help("input_vpassword").'</div><br/>
                                    <div class="spacer" style="clear: both;"></div>
                                </div>
            			  		<input class="submit" name="submit" type="submit" value="Save" style="margin: auto;width: 80px;display: block;" />
            				</fieldset>
            			</form>
            			<script type="text/javascript">
            			setTimeout(function(){
            				document.getElementById("mypassword").value = "";
            				document.getElementById("vpassword").value = "";
            				document.getElementById("mypassword").focus();
            			},500
            			);
            			</script>
            		</div>';

        echo create_validation_script("password_request_form" , "ajaxapi('/ajax/site_ajax.php','reset_password','&userid=$userid&password='+escape($('#mypassword').val()),function() { go_to_page(1); });");
        echo format_popup($content,'Change Password',"500px");
    }else{
		echo '<script type="text/javascript">go_to_page(1);</script>';
	}
}

function change_profile(){
global $MYVARS, $CFG, $USER, $PAGE;
	if(!empty($USER->userid)){
	   $userid = $USER->userid;

		if(!isset($VALIDATELIB)){ include_once($CFG->dirroot . '/lib/validatelib.php'); }
        $content = '
		<div id="change_profile">
			You can change you profile details here.
			<br /><br />
            <form id="profile_change_form">
				<fieldset class="formContainer">
				  	<div class="rowContainer">
			  			<label class="rowTitle" for="myfname">First Name</label><input value="'.$USER->fname.'" type="text" id="myfname" name="myfname" data-rule-required="true" /><div class="tooltipContainer info">'.get_help("input_fname").'</div>
                        <div class="spacer" style="clear: both;"></div>
                    </div>
                    <div class="rowContainer">
			  			<label class="rowTitle" for="mylname">Last Name</label><input value="'.$USER->lname.'" type="text" id="mylname" name="mylname" data-rule-required="true" /><div class="tooltipContainer info">'.get_help("input_fname").'</div>
                        <div class="spacer" style="clear: both;"></div>
                    </div>
                    <div class="rowContainer">
				        <label class="rowTitle" for="email">Email Address</label><input type="text" value="'.$USER->email.'" id="email" name="email" data-rule-required="true" data-rule-email="true" data-rule-ajax1="ajax/site_ajax.php::unique_email::&email=::true::'.$USER->email.'" data-msg-required="'.get_error_message('valid_req_email').'" data-msg-email="'.get_error_message('valid_email_invalid').'" data-msg-ajax1="'.get_error_message('valid_email_unique').'" /><div class="tooltipContainer info">'.get_help("input_email").'</div>
				        <div class="spacer" style="clear: both;"></div>
                    </div>
 				  	<div class="rowContainer">
			  			<label class="rowTitle" for="mypassword">Password</label><input type="password" id="mypassword" name="mypassword" data-rule-minlength="6" data-msg-minlength="'.get_error_message('valid_password_length').'" /><div class="tooltipContainer info">'.get_help("input_password").'</div>
                        <div class="spacer" style="clear: both;"></div>
                    </div>
    			  	<div class="rowContainer">
					  	<label class="rowTitle" for="vpassword">Verify Password</label><input type="password" id="vpassword" name="vpassword" data-rule-equalTo="#mypassword" data-msg-equalTo="'.get_error_message('valid_vpassword_match').'" /><div class="tooltipContainer info">'.get_help("input_vpassword").'</div><br/>
                        <div class="spacer" style="clear: both;"></div>
                    </div>
			  		<input class="submit" name="submit" type="submit" value="Save" style="margin: auto;width: 80px;display: block;" />
				</fieldset>
			</form>
		</div>';
		echo create_validation_script("profile_change_form" , "ajaxapi('/ajax/site_ajax.php','change_profile','&userid=$userid&password='+escape($('#mypassword').val())+'&email='+encodeURIComponent($('#email').val())+'&fname='+escape($('#myfname').val())+'&lname='+escape($('#mylname').val()),function() { simple_display('change_profile'); });");
        echo format_popup($content,'Edit Profile',"500px");
	}else{
		echo '<script type="text/javascript">go_to_page(1);</script>';
	}
}

function forgot_password(){
global $MYVARS, $CFG;

	if(!isset($VALIDATELIB)){ include_once($CFG->dirroot . '/lib/validatelib.php'); }
    $content = '
	<div id="forgot_password">
	Please type the email address that is associated with your user account.  A new temporary password will be sent to this address.  You will then be able to log into the website and change your password.<br /><br />
		<form id="password_request_form">
			<fieldset class="formContainer">
				<div class="rowContainer">
					<label class="rowTitle" for="email">Email Address</label><input type="text" id="email" name="email" data-rule-required="true" data-rule-email="true" data-rule-ajax1="ajax/site_ajax.php::unique_email::&email=::false" data-msg-required="'.get_error_message('valid_req_email').'" data-msg-email="'.get_error_message('valid_email_invalid').'" data-msg-ajax1="'.get_error_message('valid_email_used').'" /><div class="tooltipContainer info">'.get_help("input_email").'</div>
				    <div class="spacer" style="clear: both;"></div>
                </div>
		  		<input class="submit" name="submit" type="submit" value="Check" style="margin: auto;width: 80px;display: block;" />
			</fieldset>
		</form>
	</div>';
	echo create_validation_script("password_request_form" , "ajaxapi('/ajax/site_ajax.php','forgot_password','&email='+encodeURIComponent($('#email').val()),function() { simple_display('forgot_password'); });");
    echo format_popup($content,'Forgot Password',"500px");
}

function user_alerts(){
global $MYVARS, $CFG, $USER;
	echo '<div id="user_alerts_div">';
	   get_user_alerts($MYVARS->GET["userid"],false, false);
	echo '</div>';
}

function add_edit_lesson(){
global $CFG, $MYVARS, $USER;
include('../header.php');
    $content = '';
    $lessonid = empty($MYVARS->GET["lessonid"]) ? false : $MYVARS->GET["lessonid"];
    if ($lessonid) {
        $lesson = get_db_row("SELECT * FROM lessons WHERE timestamp='$lessonid' AND userid='$USER->userid' ");
        $lesson_text = $lesson["content"];
        $locked_yes = !empty($lesson["locked"]) ? "selected" : "";
        $locked_no = $locked_yes == "" ? "selected" : "";
    } else {
        $locked_no = $locked_yes = $lesson_text = "";
    }

    $content .= '
    <div class="formDiv" id="add_edit_lesson_div">
    	<form id="add_edit_lesson_form" action="../ajax/site_ajax.php" method="post">
            <input type="hidden" id="action" name="action" value="save_add_edit_lesson" />
            <input type="hidden" id="lessonid" name="lessonid" value="'.$lessonid.'" />
    		<fieldset class="formContainer">
                <div class="rowContainer">
                    <label class="rowTitle" for="siteviewable">Locked Date</label>
                    <select name="locked" id="locked">
                        <option value="0" ' . $locked_no . '>No</option>
                        <option value="1" ' . $locked_yes . '>Yes</option>
                    </select>
                </div><br /><br />
    			<div class="rowContainer">
                    '.get_editor_box($lesson_text, "lessoneditor", "calc(100vh - 400px)", "100%").'
                </div>
            </fieldset>
            <input class="submit" name="submit" type="submit" value="Save Lesson" />
            <input class="cancel" name="cancel" type="submit" value="Cancel" />
    	</form>
    </div>';

    echo format_popup($content,'Edit Lesson') . get_editor_javascript() . '<iframe style="display: none;" src="../index.php?keepalive=true" />';
}

function print_range() {
    global $USER, $CFG, $MYVARS;

    include('../header.php');
    echo page_masthead();
    $from = empty($MYVARS->GET["from"]) ? false : $MYVARS->GET["from"];
    $to = empty($MYVARS->GET["to"]) ? false : $MYVARS->GET["to"];

    if (empty($from) || empty($to)) {
        echo get_page_error_message("field_req");
        echo back_to_calendar(0, '5');
        exit();
    }

    $from = cleandates($from);
    $to = cleandates($to);

    // Swap to and from if from and to dates are not in order
    if ($from > $to) {
        $tmp=$from; $from=$to; $to=$tmp;
    }

    $day = substr($from,6,2);
    $month = substr($from,4,2);
    $year = substr($from,0,4);

    $pastmonth = 0;
    $returnme = "";
    if ($results = get_db_result("SELECT * FROM lessons WHERE userid='$USER->userid' AND timestamp >= $from AND timestamp <= $to ORDER BY timestamp")) {
        while ($lesson = fetch_row($results)) {
            $lessonid = $lesson["timestamp"];

            $day = substr($lessonid,6,2);
            $month = substr($lessonid,4,2);
            $year = substr($lessonid,0,4);

            if ($month !== $pastmonth) {
                $returnme .= "<div><h1>" . date("F Y", make_timestamp_from_date("$month/$day/$year")) . "</h1></div>";
                $pastmonth = $month;
            }
            $date = new DateTime(substr($lessonid,0,4) . "-" . substr($lessonid,4,2) . "-" . substr($lessonid,6,2) . " 00:00:00");
            $day = date_format($date, "d");

            $returnme .= '<div class="weeklyview">
                            <div class="weeklyday">
                                <div class="textual">'.date_format($date, "D").'</div>
                                <div class="number">'.$day.'</div>
                            </div>
                            <div class="weeklycontent">'.$lesson["content"].'</div>
                          </div>';
        }
    }

    echo $returnme;
}


function copy_range_form() {
    global $USER, $MYVARS, $CFG;
    include('../header.php');

    $offset = empty($MYVARS->GET["offset"]) ? 0 : $MYVARS->GET["offset"];

    echo page_masthead();
    $returnme = '
        <form class="centerform" action="../ajax/site_ajax.php" method="post">
            <div><h1>Copy Date Range</h1></div>
            <input type="hidden" id="action" name="action" value="copy_range" />
            From: <input type="date" name="from" required pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}">
            To: <input type="date" name="to" required pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}">
            <br /><br /><br /><br />
            Copy To: <input type="date" name="copyto" required pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}">
            <br /><br />
            <input type="submit" value="Copy"> <input type="button" value="Cancel" onclick="window.location.href = \''.$CFG->wwwroot.'/index.php?offset='.$offset.'\'">
        </form>
    ';
    echo $returnme;
}

function delete_range_form() {
    global $USER, $MYVARS, $CFG;
    include('../header.php');

    $offset = empty($MYVARS->GET["offset"]) ? 0 : $MYVARS->GET["offset"];

    echo page_masthead();
    $returnme = '
        <form class="centerform" action="../ajax/site_ajax.php" method="post">
            <div><h1>Delete Date Range</h1></div>
            <input type="hidden" id="action" name="action" value="delete_range" />
            From: <input type="date" name="from" required pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}">
            To: <input type="date" name="to" required pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}">
            <br /><br />
            <input type="submit" value="Delete"> <input type="button" value="Cancel" onclick="window.location.href = \''.$CFG->wwwroot.'/index.php?offset='.$offset.'\'">
        </form>
    ';
    echo $returnme;
}

function delete_all_form() {
    global $USER, $MYVARS, $CFG;
    include('../header.php');

    $offset = empty($MYVARS->GET["offset"]) ? 0 : $MYVARS->GET["offset"];

    echo page_masthead();
    $returnme = '
        <form class="centerform" action="../ajax/site_ajax.php" method="post">
            <div><h1>Delete All</h1></div>
            <input type="hidden" id="action" name="action" value="delete_all" />
            Are you sure you want to delete all of your lessons?
            <br /><br />
            <input type="submit" value="Yes"> <input type="button" value="No" onclick="window.location.href = \''.$CFG->wwwroot.'/index.php?offset='.$offset.'\'">
        </form>
    ';
    echo $returnme;
}

//Start Page
include('../footer.html');
?>

<script type="text/javascript">
window.onload = function() {
    if ('parentIFrame' in window) {
	       parentIFrame.autoResize(true);
    }
}
</script>