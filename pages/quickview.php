<?php
/***************************************************************************
* quickview.php
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


function viewlesson(){
global $CFG, $MYVARS, $USER;
include('../header.php');
    $content = '';
    $lessonid = empty($MYVARS->GET["lessonid"]) ? false : $MYVARS->GET["lessonid"];
    if ($lessonid) {
        $thisday = DateTime::createFromFormat('Ymd', $lessonid);
        $content .= '<h2>'.$thisday->format("l jS \of F Y").'</h2>';
        if ($lesson = get_db_row("SELECT * FROM lessons WHERE timestamp='$lessonid' AND userid='$USER->userid' ")) {
            $content .= $lesson["content"];
            echo format_popup($content,'Quick View', 'calc(50vh - 10px)', '10px', 'overflow-y: scroll;');
            return;
        }
    }

    $content .= "No lesson found";
    echo format_popup($content,'Quick View', 'calc(50vh - 10px)', '10px', 'overflow-y: scroll;');
}
?>