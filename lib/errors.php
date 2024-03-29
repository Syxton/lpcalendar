<?php
/***************************************************************************
* errors.php - Error library
* -------------------------------------------------------------------------
* Author: Matthew Davidson
* Date: 1/29/2016
* Revision: 0.1.4
***************************************************************************/

unset($ERRORS);
if(!isset($LIBHEADER)){ include('header.php'); }

$ERRORS = new stdClass();

//Login Errors *********************************************************
	$ERRORS->no_login = "Username or password was incorrect.<br />Please try again.";
    //username
	$ERRORS->valid_req_username = "Please enter your username.";

	//first name
	$ERRORS->valid_req_fname = "Please enter your first name.";

	//last name
	$ERRORS->valid_req_lname = "Please enter your last name.";

	//email
	$ERRORS->valid_req_email = "Please enter your email address.";
	$ERRORS->valid_email_invalid = "Please enter a valid email address.";
	$ERRORS->valid_email_unique = "This email address is already in use.";
	$ERRORS->valid_email_used = "Could not find a user with that email.";

	//password
	$ERRORS->valid_req_password = "Please enter a password.";
	$ERRORS->valid_password_length = "Must be at least 6 characters long.";

	//verify password
	$ERRORS->valid_req_vpassword = "Please verify your password.";
	$ERRORS->valid_vpassword_match = "Must match the password field.";

//Permission Errors *********************************************************
    $ERRORS->generic_permissions = "You do not have the correct permissions to do this.";
    $ERRORS->generic_error = "Congratulations, you found a bug.  Please inform " .$CFG->siteemail;
    $ERRORS->generic_db_error = "Congratulations, you found a database bug.  Please inform " .$CFG->siteemail;

// Validation Errors *********************************************************
	//generic
	$ERRORS->valid_req = "This field is required.";
    $ERRORS->field_req = "This form is missing required information.";
    $ERRORS->no_function  = "Requested function not found.";
    $ERRORS->copyto_invalid = "Date to copy to cannot be inside selected range";

function get_error_message($error,$vars=false){
global $CFG, $ERRORS;
    $lang = explode(":",$error);
    $string = $lang[0];
    if(isset($lang[2])){
        include($CFG->dirroot . '/features/'.$lang[1]."/".$lang[2]."/lang.php");
        return $ERRORS->$string;
    }elseif(isset($lang[1])){
        include($CFG->dirroot . '/features/' . $lang[1] . "/lang.php");
        if($vars){ return fill_template($ERRORS->$string,$vars); }
        return $ERRORS->$string;
    }else{ if($vars){ return fill_template($ERRORS->$error,$vars); } return $ERRORS->$error; }
}

function get_page_error_message($error,$vars=false){
    return '<div style="background:red;padding:20px;text-align:center;">' . get_error_message($error,$vars) . '</div>';
}

function fill_template($string,$vars){
    $i=0;
    foreach($vars as $var){
        $string = str_replace("[$i]",$var,$string);
        $i++;
    }
    return $string;
}
?>