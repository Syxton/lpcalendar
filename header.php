<?php
/***************************************************************************
 * index.php
 * -------------------------------------------------------------------------
 * Author: Matthew Davidson
 * Date: 6/07/2016
 * Revision: 1.0.2
 ***************************************************************************/
error_reporting(E_ALL);
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.

if (!isset($CFG)) {
    include(__DIR__ . '/config.php');
}

include_once($CFG->dirroot . '/lib/header.php');

//Get User info
load_user_cookie();
update_user_cookie();

$directory = $CFG->directory == '' ? 'root' : $CFG->directory;
$_SESSION['directory'] = $directory;
setcookie('directory', $directory, get_timestamp() + $CFG->cookietimeout, '/');

//Use this page only to keep session and cookies refreshed (during forms)
if (!empty($_GET['keepalive'])) {
    header("Refresh:30");
    echo rand();
    die();
}

include("header.html");

//Check for upgrades or uninstalled components
upgrade_check();

if (!is_logged_in()) {
    echo page_masthead();
    include('footer.html');
    die();
}

postorget(true);
?>