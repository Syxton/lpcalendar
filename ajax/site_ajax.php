<?php
/***************************************************************************
* site_ajax.php - Main backend ajax script.  Usually sends off to feature libraries.
* -------------------------------------------------------------------------
* Author: Matthew Davidson
* Date: 6/07/2016
* Revision: 2.9.7
***************************************************************************/

include('header.php');

callfunction();

function login(){
global $CFG, $USER, $MYVARS;
	$reroute = '';
	$username = dbescape($MYVARS->GET["username"]);
	$password = md5($MYVARS->GET["password"]);
	if($row = authenticate($username, $password)) {
		if($row["alternate"] == $password){ $reroute = '<input type="hidden" id="reroute" value="/pages/forms.php?action=reset_password&amp;userid=' . $row["userid"] . '&amp;alternate=' . $password . '" />';}
        echo 'true**' . $reroute;
	}else{ echo "false**" . get_error_message("no_login"); }
}

function unique_email(){
global $CFG, $MYVARS;
	$email = dbescape($MYVARS->GET["email"]);
	if(get_db_count("SELECT * FROM users WHERE email='$email'")){ echo "false";
	}else{  echo "true";}
}

function reset_password(){
global $CFG, $MYVARS;
	$userid = dbescape($MYVARS->GET["userid"]);
	$password = md5($MYVARS->GET["password"]);
	if(execute_db_sql("UPDATE users SET alternate='',password='$password' WHERE userid='$userid'")){
		echo '<br /><br /><span class="centered_span">Password changed successfully.</span>';
	}else{
		echo '<br /><span class="centered_span">Password change failed.</span>';
	}
}

function change_profile(){
global $CFG, $MYVARS;
    $userid = dbescape($MYVARS->GET["userid"]);
	$email = dbescape($MYVARS->GET["email"]);
	$fname = dbescape(nameize($MYVARS->GET["fname"]));
	$lname = dbescape(nameize($MYVARS->GET["lname"]));
    $passchanged = empty($MYVARS->GET["password"]) ? false : true;
    $password = md5($MYVARS->GET["password"]);
    $passwordsql = $passchanged ? ",alternate='',password='$password'" : "";

    if(!get_db_row("SELECT * FROM users WHERE email='$email' AND userid !='$userid'")){
        if(execute_db_sql("UPDATE users SET fname='$fname',lname='$lname',email='$email'$passwordsql WHERE userid='$userid'")){
            echo '<br /><br /><span class="centered_span">Profile changed successfully.</span>';
        }else{
            echo '<br /><br /><span class="centered_span">Profile change failed.</span>';
        }
    }else{ echo '<br /><br /><span class="centered_span">This email address is already associated with another account.</span>'; }
}

function forgot_password(){
global $CFG, $MYVARS;
	if (!isset($COMLIB)) { include_once ($CFG->dirroot . '/lib/comlib.php'); }

    if (isset($MYVARS->GET["userid"])) {
        $MYVARS->GET["email"] = get_db_field("email", "users", "userid='" . $MYVARS->GET["userid"] . "'");
    }

    if (isset($MYVARS->GET["email"])) {
        $email = dbescape($MYVARS->GET["email"]);

    	// Check to see if email matches an existing user.
    	if ($user = get_db_row("SELECT * FROM users WHERE email='$email'")) {
    		$alternate = create_random_password();

            // Check to see if account is activated
    		if (strlen($user["temp"]) > 0) {
                $userid = execute_db_sql("UPDATE users SET password='" . md5($alternate) . "' WHERE email='$email'");
    		} else {
    			$userid = execute_db_sql("UPDATE users SET alternate='" . md5($alternate) . "' WHERE email='$email'");
            }

    		// Email new password to the email address.
            $TOUSER = new stdClass();
    		$TOUSER->userid = $user['userid'];
    		$TOUSER->fname = $user['fname'];
    		$TOUSER->lname = $user['lname'];
    		$TOUSER->email = $email;
            $FROMUSER = new stdClass();
    		$FROMUSER->fname = $CFG->sitename;
    		$FROMUSER->lname = '';
    		$FROMUSER->email = $CFG->siteemail;
    		$message = '
    			<p><font face="Tahoma"><font size="3" color="#993366">Dear <strong>' . $user['fname'] . ' ' . $user['lname'] . '</strong>,</font><br />
    			</font></p>
    			<blockquote>
    			<p><font size="3" face="Tahoma"><strong>' . $CFG->sitename . '</strong> has recieved notification that you have forgotten your password.&nbsp; A new temporary password is being sent to you in this email.</font></p>
    			</blockquote>
    			<p>&nbsp;</p>
    			<hr width="100%" size="2" />
    			<p>&nbsp;</p>
    			<blockquote>
    			<p align="left"><font face="Tahoma"><strong>Username:</strong> <font color="#3366ff">' . $email . '</font></font></p>
    			<p align="left"><font face="Tahoma"><strong>Password:</strong> <font color="#3366ff">' . $alternate . '</font></font></p>
    			</blockquote>
    			<p>&nbsp;</p>
    			<hr width="100%" size="2" />
    			<blockquote>
    			<p><font size="3" face="Tahoma">After you have successfully logged into the site using the password provided a password reset form will open up.  Please create a new password at that time.  If you somehow exit this form without entering a new password, your forgotten password will still be valid and the password in this email will still be valid.  If you have any questions during your use of the site, feel free to contact us at <font color="#ff0000">' . $CFG->siteemail . '</font>.<br />
    			</font></p>
    			</blockquote>
    			<p>&nbsp;</p>
    			<p><font face="Tahoma"><strong><font size="3" color="#666699">Enjoy the site,</font></strong></font></p>
    			<p><font size="3" face="Tahoma"><em>' . $CFG->siteowner . ' </em></font><font size="3" face="Tahoma" color="#ff0000">&lt;' . $CFG->siteemail . '</font><font face="Tahoma"><font size="3" color="#ff0000">&gt;</font></font></p>
    			<p>&nbsp;</p>';
    		$subject = $CFG->sitename . ' Password Reset';
    		if (!$userid || send_email($TOUSER, $FROMUSER, null, $subject, $message)) {
    			send_email($FROMUSER, $FROMUSER, null, $subject, $message); // Send a copy to the site admin

    			// Log
    			log_entry("user", $TOUSER->email, "Password Reset");
    			if (!$admin) { echo '<div class="centered_div">An email has been sent to your address that contains a new temporary password. <br />Your forgotten password will still work until you log into the site with the new password.<br />If you remember your password and log into the site, the password contained in the email will no longer work.</div>';
                } else { echo '<img src="'.$CFG->wwwroot.'/images/reset_disabled.png" />'; }
    		} else {
                echo '<br /><br /><span class="centered_span">A password reset could not be done at this time.  Please try again later.</span>';
    		}
    	} else {
    	   echo '<br /><br /><span class="centered_span">There is no user with this email address.</span>';
        }
    }
}

function add_new_user(){
global $CFG, $MYVARS;
    $newuser = new stdClass();
	$newuser->email = trim($MYVARS->GET["email"]);
	$newuser->fname = nameize($MYVARS->GET["fname"]);
	$newuser->lname = nameize($MYVARS->GET["lname"]);
	$newuser->password = md5(trim($MYVARS->GET["password"]));
	echo create_new_user($newuser);
}

function delete_user(){
global $CFG, $MYVARS, $USER;
	$userid = $MYVARS->GET["userid"];

	if($USER->userid == $userid){
        echo "You can't delete yourself!";
    }else{
		if($user = get_db_row("SELECT * FROM users WHERE userid = '$userid'")){
            $SQL = "DELETE FROM users WHERE userid='$userid'";
            if(execute_db_sql($SQL)){
                echo "User deleted.";
            }
        }
	}
}

function refresh_user_alerts(){
global $CFG, $MYVARS;
    $userid = empty($MYVARS->GET["userid"]) ? false : $MYVARS->GET["userid"];

    get_user_alerts($userid,false,false);
}

function get_login_box(){
global $CFG, $USER, $MYVARS;
	if(isset($MYVARS->GET["logout"])){
        $_SESSION['userid'] = "0";
        session_destroy();
        session_write_close();
        unset($USER);
	}
	echo get_login_form();
}

function update_login_contents(){
global $CFG, $PAGE, $USER, $MYVARS;
	if(is_logged_in()) {
		if(isset($MYVARS->GET['check'])) {
            if(isset($_SESSION['userid'])) {
                $USER->userid = $_SESSION['userid'];
                echo "true**check";
            } else {
                load_user_cookie();
                echo "false";
            }
		} else {
			update_user_cookie();
			echo "true**" . print_logout_button($USER->fname, $USER->lname, $pageid);
		}
	} else { //Cookie has timed out or they haven't logged in yet.
        load_user_cookie();
		echo "false";
	}
}

function get_cookie() {
global $MYVARS;
    $cname = $MYVARS->GET['cname'];
    if (isset($_SESSION["$cname"])) {
        echo $_SESSION["$cname"];
    }
    echo "";
}

function donothing(){
	echo "";
}

function save_lesson_move($from = false, $to = false, $duplicate = false) {
    global $CFG, $MYVARS, $USER;
        $from = empty($from) ? dbescape($MYVARS->GET["from"]) : $from;
    	$to = empty($to) ? dbescape($MYVARS->GET["to"]) : $to;
        $duplicate = empty($duplicate) ? ($MYVARS->GET["duplicate"] == "false" ? false : true) : $duplicate;

        if (get_db_row("SELECT * FROM lessons WHERE userid='$USER->userid' AND timestamp='$to'")) {
            $to = shift_lessons($from, $to, $duplicate);
        } else {  //empty, so just update from -> to
            if (!empty($duplicate)) {
                $copythis = get_db_row("SELECT * FROM lessons WHERE userid='$USER->userid' AND timestamp='$from'");
                copy_db_row($copythis, "lessons", "id=null,timestamp=$to");
            } else {
                execute_db_sql("UPDATE lessons SET timestamp='$to' WHERE userid='$USER->userid' AND timestamp='$from'");
            }
        }
        return $to;
}

function shift_lessons($from, $to, $duplicate) { // Push lessons until empty, non weekend day is found.
    global $USER;
    $emptyfound = false; $temp = false;

    while(!$emptyfound) {
        if ($temp) { // temp save from previous run.
            $move = $temp;
        } else {
            $move = get_db_row("SELECT * FROM lessons WHERE userid='$USER->userid' AND timestamp='$from'"); // this is about to be replaced.
        }

        // Save current date to be overwritten
        $temp = get_db_row("SELECT * FROM lessons WHERE userid='$USER->userid' AND timestamp='$to'");

        // overwrite content on target.
        execute_db_sql("UPDATE lessons SET content='".$move["content"]."', locked='".$move["locked"]."' WHERE userid='$USER->userid' AND timestamp='$to'");

        // delete original.
        if (!$duplicate) {
            execute_db_sql("DELETE FROM lessons WHERE userid='$USER->userid' AND timestamp='$from'");
        }

        $date = DateTime::createFromFormat('Ymd', $to);
        $date->modify('+1 day'); //skip a day

        // while weekend or locked -> keep loocking
        $to = $date->format("Ymd");
        while(($date->format("w") == 0 || $date->format("w") == 6) || get_db_row("SELECT * FROM lessons WHERE locked='1' AND userid='$USER->userid' AND timestamp='$to'")) {
            $date->modify('+1 day'); //skip a day
            $to = $date->format("Ymd");
        }

        if (!get_db_row("SELECT * FROM lessons WHERE userid='$USER->userid' AND timestamp='$to'")) {
            $emptyfound = true;
        }
    }

    execute_db_sql("INSERT INTO lessons (userid,timestamp,content) VALUES(".$temp['userid'].",$to,'".$temp['content']."')");

    return $to;
}

function save_add_edit_lesson() {
    global $USER, $CFG, $MYVARS;

    if (!empty($MYVARS->GET["cancel"]) && !empty($MYVARS->GET["lessonid"])) {
        echo back_to_calendar($MYVARS->GET["lessonid"]);
        exit();
    } elseif (!empty($MYVARS->GET["cancel"])) {
        echo back_to_calendar(0);
        exit();
    }

    if (!empty($USER->userid)) {
        if (!empty($MYVARS->GET["lessonid"])) {
            $lessonid = dbescape($MYVARS->GET["lessonid"]);
            $locked = empty($MYVARS->GET["locked"]) ? 0 : 1;
            $content = dbescape($MYVARS->GET["editor1"]);

            if (get_db_row("SELECT * FROM lessons WHERE userid='$USER->userid' AND timestamp='$lessonid'")) {
                $SQL = "UPDATE lessons SET content='$content', locked='$locked' WHERE userid='$USER->userid' AND timestamp='$lessonid'";
            } else {
                $SQL = "INSERT INTO lessons (userid, timestamp, content, locked) VALUES('$USER->userid', '$lessonid', '$content', '$locked')";
            }

            if (execute_db_sql($SQL)) {
                echo back_to_calendar($MYVARS->GET["lessonid"]);
            } else {
                echo get_page_error_message("generic_db_error");
                echo back_to_calendar($MYVARS->GET["lessonid"], '5');
            }
        } else { // required form data not found.
            echo get_page_error_message("field_req");
            echo back_to_calendar(0, '5');
        }
    } else { // Not logged in.
        echo get_page_error_message("generic_permissions");
        echo back_to_calendar(0, '5');
    }
}

function delete_lesson() {
    global $USER, $CFG, $MYVARS;

    if (!empty($USER->userid)) {
        if (!empty($MYVARS->GET["lessonid"])) {
            $lessonid = dbescape($MYVARS->GET["lessonid"]);
            $SQL = "DELETE FROM lessons WHERE userid='$USER->userid' AND timestamp='$lessonid'";

            if (execute_db_sql($SQL)) {
                echo back_to_calendar($MYVARS->GET["lessonid"]);
            } else {
                echo get_page_error_message("generic_db_error");
                echo back_to_calendar($MYVARS->GET["lessonid"], '5');
            }
        } else { // required form data not found.
            echo get_page_error_message("field_req");
            echo back_to_calendar(0, '5');
        }
    } else { // Not logged in.
        echo get_page_error_message("generic_permissions");
        echo back_to_calendar(0, '5');
    }
}

function copy_range() {
    global $USER, $CFG, $MYVARS;

    echo page_masthead();
    $from = empty($MYVARS->GET["from"]) ? false : $MYVARS->GET["from"];
    $to = empty($MYVARS->GET["to"]) ? false : $MYVARS->GET["to"];
    $copyto = empty($MYVARS->GET["copyto"]) ? false : $MYVARS->GET["copyto"];

    if (empty($from) || empty($to) || empty($copyto)) {
        echo get_page_error_message("field_req");
        echo back_to_calendar(0, '5');
        exit();
    }

    $from = cleandates($from);
    $to = cleandates($to);
    $copyto = cleandates($copyto);

    // Swap to and from if from and to dates are not in order
    if ($from > $to) {
        $tmp=$from; $from=$to; $to=$tmp;
    }

    // check if copy to is in the middle of from and to.
    if ($copyto >= $from && $copyto <= $to) {
        echo get_page_error_message("copyto_invalid");
        echo back_to_calendar(0, '5');
        exit();
    }

    $process = $from;
    while ($process <= $to) {
        $jumpahead = false;
        $processday = DateTime::createFromFormat('Ymd', $process);
        $copytoday = DateTime::createFromFormat('Ymd', $copyto);

        // get lesson if it file_exists
        $lesson = get_db_row("SELECT * FROM lessons WHERE userid='$USER->userid' AND timestamp ='$process'");

        // Get target to copy to.
        // if weekend
        if (($processday->format("w") == 0 || $processday->format("w") == 6)) {
            if ($lesson) { //lesson exists on this weekend
                if ($processday->format("w") == 6) { // saturday
                    if ($copytoday->format("w") != 6) { // target is not a saturday
                        $jumpahead = date("Ymd", strtotime('next Saturday', $copytoday->getTimestamp()));
                    }
                } else { // sunday
                    if ($copytoday->format("w") != 0) { // target is not a sunday
                        $jumpahead = date("Ymd", strtotime('next Sunday', $copytoday->getTimestamp()));
                    }
                }

                $target = empty($jumpahead) ? $copyto : $jumpahead;
            }
        } else { // if copying a weekday.
                while(($copytoday->format("w") == 0 || $copytoday->format("w") == 6)) {
                    $copytoday->modify('+1 day'); //skip a day
                }
                $copyto = $copytoday->format("Ymd");
                $target = $copyto;
        }

        if ($lesson) {
            save_lesson_move($process, $target, true);
        }

        if (empty($jumpahead)) { // only move the copyto target if we didn't jump ahead
            $copytoday->modify('+1 day'); //skip a day
            $copyto = $copytoday->format("Ymd");
        }

        $processday->modify('+1 day'); //skip a day
        $process = $processday->format("Ymd");
    }

    echo back_to_calendar(0);
}

function delete_range() {
    global $USER, $CFG, $MYVARS;

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

    $SQL = "DELETE FROM lessons WHERE userid='$USER->userid' AND timestamp >= $from AND timestamp <= $to";
    execute_db_sql($SQL);

    echo back_to_calendar(0);
}

function delete_all() {
    global $USER, $CFG, $MYVARS;

    $SQL = "DELETE FROM lessons WHERE userid='$USER->userid'";
    execute_db_sql($SQL);

    echo back_to_calendar(0);
}
?>