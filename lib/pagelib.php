<?php
/***************************************************************************
 * pagelib.php - Page function library
 * -------------------------------------------------------------------------
 * Author: Matthew Davidson
 * Date: 6/07/2016
 * Revision: 3.1.5
 ***************************************************************************/

if (!isset($LIBHEADER)) {
    include('header.php');
}
$PAGELIB = true;

function callfunction() {
    global $CFG, $MYVARS;
    if (empty($_POST["aslib"])) {
        //Retrieve from Javascript
        $postorget = isset($_POST["action"]) ? $_POST : false;
        if (empty($MYVARS)) {
            $MYVARS = new stdClass();
        }
        $MYVARS->GET = !$postorget && isset($_GET["action"]) ? $_GET : $postorget;
        if (!empty($MYVARS->GET["i"])) { //universal javascript and css
            echo '
            <script type="text/javascript"> var dirfromroot = "' . $CFG->directory . '"; </script>
            <link type="text/css" rel="stylesheet" href="' . $CFG->wwwroot . '/min/?f=' . (empty($CFG->directory) ? '' : $CFG->directory . '/') . 'styles/styles_main.css" />
            <script type="text/javascript" src="' . $CFG->wwwroot . '/min/?b=' . (empty($CFG->directory) ? '' : $CFG->directory . '/') . 'scripts&amp;f=jquery.min.js,jquery.colorbox.js,jquery.colorbox.extend.js,siteajax.js"></script>';
        }
        if (!empty($MYVARS->GET["v"])) { //validation javascript and css
            echo '
                <script type="text/javascript" src="' . $CFG->wwwroot . '/min/?b=' . (empty($CFG->directory) ? '' : $CFG->directory . '/') . 'scripts&f=jqvalidate.js,jqvalidate_addon.js" ></script>';
            unset($MYVARS->GET["v"]);
        }
        if (function_exists($MYVARS->GET["action"])) {
            $action = $MYVARS->GET["action"];
            $action(); //Go to the function that was called.
        } else {
            echo get_page_error_message("no_function", array(
                $MYVARS->GET["action"]
            ));
        }
    }
}

function postorget($all = false) {
    global $MYVARS;

    //Retrieve from Javascript
    if ($all) {
        $postorget   = $_REQUEST;
    } else {
        $postorget   = isset($_GET["action"]) ? $_GET : $_POST;
        $postorget   = isset($postorget["action"]) ? $postorget : "";
    }

    $MYVARS = new stdClass;
    $MYVARS->GET = $postorget;
    if ($postorget != "") {
        if ($all) {
            return true;
        }
        return $postorget["action"];
    }
    return false;
}

function page_masthead($header_only = false) {
    global $CFG, $USER, $PAGE;

    $returnme = (!$header_only ? (is_logged_in() ? print_logout_button($USER->fname, $USER->lname) : get_login_form()) : '');

    return $returnme;
}

function get_editor_javascript() {
    global $CFG;
    //return '<script type="text/javascript" src="'.$CFG->wwwroot.'/scripts/ckeditor/ckeditor.js"></script>';
    return '<script type="text/javascript" src="' . $CFG->wwwroot . '/scripts/tinymce/jquery.tinymce.min.js"></script>';
}

function get_editor_value_javascript($editorname = "editor1") {
    return '$(\'#' . $editorname . '\').val()';
}

function get_editor_box($initialValue = "", $name = "editor1", $height = "550", $width = "100%", $type = "HTML") {
    global $CFG;
    return '<textarea id="editor1" name="editor1" class="wysiwyg_editor">' . $initialValue . '</textarea>
    <script type="text/javascript">
        $(window).load(function() {
            $(".wysiwyg_editor").tinymce({
                script_url : "' . $CFG->wwwroot . '/scripts/tinymce/tinymce.min.js",
                toolbar: "' . get_editor_toolbar($type) . '",
                height: "' . $height . '",
                width: "' . $width . '",
                removed_menuitems: "newdocument",
                theme : "modern",
                convert_urls: false,
                paste_data_images: true,
                plugins: [
                    ' . get_editor_plugins($type) . '
                ],
                external_filemanager_path: "' . (empty($CFG->directory) ? '' : '/' . $CFG->directory) . '/scripts/tinymce/plugins/filemanager/",
                filemanager_title: "File Manager" ,
                external_plugins: { "filemanager" : "' . (empty($CFG->directory) ? '' : '/' . $CFG->directory) . '/scripts/tinymce/plugins/filemanager/plugin.min.js"}
            });
        });
    </script>';
}

function get_editor_plugins($type) {
    switch ($type) {
        case "Default":
            $set = '"autolink image lists link responsivefilemanager charmap preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen textcolor",
                "insertdatetime media nonbreaking paste table contextmenu directionality"';
            break;
        case "News":
            $set = '"autolink image lists link responsivefilemanager charmap preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen textcolor",
                "insertdatetime media nonbreaking paste table contextmenu directionality"';
            break;
        case "HTML":
            $set = '"autolink image lists link responsivefilemanager charmap preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen textcolor",
                "insertdatetime media nonbreaking paste table contextmenu directionality"';
            break;
        case "Basic":
            $set = '"autolink lists charmap preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "insertdatetime nonbreaking paste table contextmenu directionality"';
            break;
        case "Forum":
            $set = '"autolink image lists link responsivefilemanager charmap preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "insertdatetime media nonbreaking paste table contextmenu directionality"';
            break;
        case "Shoutbox":
            $set = '"autolink"';
            break;
    }
    return $set;
}

function get_editor_toolbar($type) {
    switch ($type) {
        case "Default":
            $set = "insertfile undo redo | formatselect | bold italic strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image";
            break;
        case "News":
            $set = "insertfile undo redo | formatselect | bold italic strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image";
            break;
        case "HTML":
            $set = "insertfile undo redo | formatselect | bold italic strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image";
            break;
        case "Basic":
            $set = "undo redo bold italic | alignleft aligncenter alignright alignjustify link image";
            break;
        case "Forum":
            $set = "insertfile undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image";
            break;
        case "Shoutbox":
            $set = "undo redo bold italic";
            break;
    }
    return $set;
}

function upgrade_check() {
    global $CFG;

}

function print_logout_button($fname, $lname, $pageid = false) {
    global $CFG, $USER;

    $param   = array(
        "title" => "Edit Profile",
        "text" => "$fname $lname",
        "path" => $CFG->wwwroot . "/pages/forms.php?action=change_profile_form",
        "validate" => "true",
        "width" => "500",
        "image" => $CFG->wwwroot . "/images/16x16/profile_16x16.png",
        "styles" => ""
    );

    $profile = make_modal_links($param);

    // Logged in as someone else.
    $logoutas = "";
    if (!empty($_SESSION["lia_original"])) {
        $lia_name = get_user_name($_SESSION["lia_original"]);
        $logoutas = '<a title="Switch back to: ' . $lia_name . '" href="javascript: void(0)" onclick="ajaxapi(\'/features/adminpanel/adminpanel_ajax.php\',\'logoutas\',\'\',function() { go_to_page(' . $CFG->SITEID . ');});">Switch back to: ' . $lia_name . '</a><br />';
    }

    $logout = '<a title="Log Out" href="javascript: void(0)" onclick="ajaxapi(\'/ajax/site_ajax.php\',\'get_login_box\',\'&amp;logout=1\',function() { clearInterval(myInterval); location.reload(); });">(Log Out)</a>';
    return '<div id="login_box" class="login_box logout" style="text-align:right">
                ' . print_view_selector() . '
                ' . print_actions_selector() . '
                ' . $logoutas . '
                <span style="display:inline-block;line-height: 18px;">
                    ' . $profile . ' ' . $logout . '
                </span>' . '</div>';
}

function make_select($name, $values, $valuename, $displayname, $selected = false, $onchange = "", $leadingblank = false, $size = 1, $style = "", $leadingblanktitle = "", $excludevalue = false) {
    $returnme = '<select size="' . $size . '" id="' . $name . '" name="' . $name . '" ' . $onchange . ' style="' . $style . '" >';
    if ($leadingblank) {
        $returnme .= '<option value="">' . $leadingblanktitle . '</option>';
    }
    if ($values) {
        while ($row = fetch_row($values)) {
            $exclude = false;
            if ($excludevalue) { //exclude value
                switch (gettype($excludevalue)) {
                    case "string":
                        $exclude = $excludevalue == $row[$valuename] ? true : false;
                        break;
                    case "array":
                        foreach ($excludevalue as $e) {
                            if ($e == $row[$valuename]) {
                                $exclude = true;
                            }
                        }
                        break;
                    case "object":
                        while ($e = fetch_row($excludevalue)) {
                            if ($e[$valuename] == $row[$valuename]) {

                                $exclude = true;
                            }
                        }

                        db_goto_row($excludevalue);
                        break;
                }
            }

            if (!$excludevalue || !$exclude) {
                $returnme .= $row[$valuename] == $selected ? '<option value="' . $row[$valuename] . '" selected="selected">' . $row[$displayname] . '</option>' : '<option value="' . $row[$valuename] . '">' . $row[$displayname] . '</option>';
            }
        }
    }
    $returnme .= '</select>';
    return $returnme;
}

function make_select_from_array($name, $values, $valuename, $displayname, $selected = false, $onchange = "", $leadingblank = false, $size = 1, $style = "", $leadingblanktitle = "", $excludevalue = false) {
    $returnme = '<select size="' . $size . '" id="' . $name . '" name="' . $name . '" ' . 'onchange="' . $onchange . '" ' . ' style="' . $style . '">';
    if ($leadingblank) {
        $returnme .= '<option value="">' . $leadingblanktitle . '</option>';
    }
    foreach ($values as $value) {
        $exclude = false;
        if ($excludevalue) { //exclude value
            switch (gettype($excludevalue)) {
                case "string":
                    $exclude = $excludevalue == $value[$valuename] ? true : false;
                    break;
                case "array":
                    foreach ($excludevalue as $e) {
                        if ($e == $value[$valuename]) {
                            $exclude = true;
                        }
                    }
                    break;
                case "object":
                    while ($e = fetch_row($excludevalue)) {
                        if ($e[$valuename] == $value[$valuename]) {
                            $exclude = true;
                        }
                    }

                    db_goto_row($excludevalue);
                    break;
            }
        }
        if (!$excludevalue || !$exclude) {
            $returnme .= $value[$valuename] == $selected ? '<option value="' . $value[$valuename] . '" selected="selected">' . $value[$displayname] . '</option>' : '<option value="' . $value[$valuename] . '">' . $value[$displayname] . '</option>';
        }
    }

    $returnme .= '</select>';
    return $returnme;
}

function get_login_form($loginonly = false, $newuser = true) {
    global $CFG;
    if (!isset($VALIDATELIB)) {
        include_once($CFG->dirroot . '/lib/validatelib.php');
    }
    $title   = "Login";
    $content = '
     <script type="text/javascript" src="' . $CFG->wwwroot . '/min/?b=' . (empty($CFG->directory) ? '' : $CFG->directory . '/') . 'scripts&amp;f=jqvalidate.js,jqvalidate_addon.js"></script>
     ' . create_validation_script("login_form", "login(document.getElementById('username').value,document.getElementById('password').value);") . '
     <form id="login_form">
         <h1>'.$CFG->sitename.' Login</h1>
         <fieldset>
                <div class="rowContainer">
                    <label class="rowTitle" for="username">Username</label>
                    <input tabindex=1 style="margin-right:0px;width:80%" type="email" id="username" name="username" data-rule-required="true" data-msg-required="' . get_error_message('valid_req_username') . '" /><div class="tooltipContainer info">' . get_help("input_username") . '</div>
                    <div class="spacer" style="clear: both;"></div>
                </div>
                <div class="rowContainer">
                      <label class="rowTitle" for="password">Password</label>
                    <input tabindex=2 style="margin-right:0px;width:80%" type="password" id="password" name="password" data-rule-required="true" data-msg-required="' . get_error_message('valid_req_password') . '" /><div class="tooltipContainer info">' . get_help("input_password2") . '</div>
                    <div class="spacer" style="clear: both;"></div>
                </div>
        </fieldset>
        <input name="submit" type="submit" value="Sign In" style="margin-left:5px;" />
    <span style="float:right;font-size:.9em">';
    $content .= $newuser ? make_modal_links(array(
        "title" => "New User",
        "path" => $CFG->wwwroot . "/pages/forms.php?action=new_user",
        "width" => "500"
    )) . '<br />' : '';
    $content .= make_modal_links(array(
        "title" => "Forgot password?",
        "path" => $CFG->wwwroot . "/pages/forms.php?action=forgot_password_form",
        "width" => "500"
    )) . '
    </span>
    </form>
    <div id="login_box_error" class="error_text"></div>';

    $returnme = $content;
    return $returnme;
}

function format_popup($content = "", $title = "", $height = "calc(100vh - 102px)", $padding = "25px", $extrastyles = "") {
    return '<div style="margin: 25px;padding:' . $padding . ';border:1px solid silver;border-radius: 5px;height:' . $height . ';'.$extrastyles.'">
                <h3>' . $title . '</h3>' . $content . '
            </div>';
}

function keepalive() {
    global $CFG;
    return '<iframe style="display:none;" src="' . $CFG->wwwroot . '/index.php?keepalive=true"></iframe>';
}

function make_modal_links($v) {
    global $CFG;
    $v["button"]      = empty($v["button"]) ? "link" : "button";
    $v["title"]       = empty($v["title"]) ? "" : $v["title"];
    $v["confirmexit"] = empty($v["confirmexit"]) ? "" : $v["confirmexit"];
    $v["id"]          = empty($v["id"]) ? "" : 'id="' . $v["id"] . '"';
    $v["type"]        = empty($v["type"]) ? "" : 'type="' . $v["type"] . '"';
    $gallery_name     = empty($v["gallery"]) ? "" : $v["gallery"];
    $gallery          = empty($v["gallery"]) ? "" : "('*[data-rel=\'$gallery_name\']')";
    $v["gallery"]     = empty($v["gallery"]) ? "" : ",rel:'$gallery_name',photo:'true',preloading:'true'";
    $v["imagestyles"] = empty($v["imagestyles"]) ? "" : $v["imagestyles"];
    $v["image"]       = empty($v["image"]) ? "" : '<img alt="' . $v["title"] . '" title="' . $v["title"] . '" src="' . $v["image"] . '" style="' . $v["imagestyles"] . '" />';
    $v["width"]       = empty($v["width"]) ? (empty($v["gallery"]) ? "" : "") : ",width:'" . $v["width"] . "'";
    $v["height"]      = empty($v["height"]) ? (empty($v["gallery"]) ? "" : "") : ",height:'" . $v["height"] . "'";
    $v["path"]        = empty($v["path"]) ? "" : $v["path"];
    $v["class"]       = empty($v["class"]) ? "" : $v["class"];
    $path             = $v["path"] && $v["gallery"] ? $v["path"] : "javascript: void(0);";
    $v["text"]        = empty($v["text"]) ? (empty($v["image"]) ? (empty($v["title"]) ? "" : $v["title"]) : $v["image"]) : (empty($v["image"]) ? $v["text"] : $v["image"] . " " . $v["text"]);

    $iframe      = empty($v["iframe"]) ? "" : ",fastIframe:true,iframe:true";
    $i           = empty($v["iframe"]) ? "" : "&amp;i=!";
    $rand        = '&amp;t=' . get_timestamp();
    $v["styles"] = empty($v["styles"]) ? "" : $v["styles"];

    $v["refresh"]  = empty($v["refresh"]) ? "" : $v["refresh"];
    $v["runafter"] = empty($v["runafter"]) ? "" : $v["runafter"];

    $modal = $onOpen = $onComplete = $valid = '';

    if (!empty($v["validate"]) && empty($v["iframe"])) { //load validation javascript
        $onOpen .= 'loadjs(\'' . $CFG->wwwroot . '/min/?b=\' + (dirfromroot == \'\' ? \'\' : dirfromroot + \'/\') + \'scripts&f=jqvalidate.js,jqvalidate_addon.js\');';
    } elseif (!empty($v["validate"]) && !empty($v["iframe"])) {
        $valid = "&amp;v=!";
    }

    if (!empty($v["refresh"])) {
        $modal .= '
        $.colorbox.close = function(){
            window.location.reload( true );
        };';
    }

    if (!empty($v["runafter"])) {
        $modal .= '
        var originalClose = $.colorbox.close;
        $.colorbox.close = function(){';

        $modal .= empty($v["confirmexit"]) ? "" : 'if(confirm(\'Are you sure you wish to close this window?\')){';
        $modal .= 'eval(stripslashes(unescape(self.parent.$(\'#' . $v["runafter"] . '\').val())));
                   setTimeout(function(){ originalClose(); $.colorbox.close = originalClose; },100);';
        $modal .= empty($v["confirmexit"]) ? "" : '}';
        $modal .= '};';
    } elseif (!empty($v["confirmexit"])) {
        $modal .= '
        var originalClose = $.colorbox.close;
        $.colorbox.close = function(){
            if(confirm(\'Are you sure you wish to close this window?\')){
                originalClose(); $.colorbox.close = originalClose;
            }
        };';
    }

    if ((empty($v["height"]) || empty($v["width"]))) {
        if (empty($v["iframe"])) {
            $onComplete = 'setTimeout(function(){ $.colorbox.resize(); },1500);';
        } else {
            $onComplete .= '
            setTimeout(function(){
                parent.$.colorbox.resize({
                    width: Math.max(document.documentElement.clientWidth, window.innerWidth || 0) * .90,
                    height: Math.max(document.documentElement.clientHeight, window.innerHeight || 0) * .90,
                });
            },1500);';
        }
    }

    if ($v["gallery"]) {
        $modal .= '$' . $gallery . '.colorbox({maxWidth: \'95%\',maxHeight: \'95%\',fixed: true' . $v["width"] . $v["height"] . $v["gallery"] . ',speed:0});';
    } else {
        $modal .= '$.colorbox({maxWidth: \'98%\',maxHeight: \'98%\',fixed: true,onComplete:function(){ ' . $onComplete . ' $(\'#cboxTitle\').attr({\'style\': \'display: none\'}); },href:\'' . $v["path"] . $i . $valid . $rand . '\'' . $v["width"] . $v["height"] . $v["gallery"] . ',speed:0' . $iframe . '});';
    }

    if (!empty($onOpen)) {
        $modal = "setTimeout(function(){ $modal },500);";
    }

    if ($v["button"] == "button") {
        return '<button ' . trim($v["id"]) . ' class="smallbutton ' . $v["class"] . '" ' . trim($v["type"]) . ' title="' . trim(strip_tags($v["title"])) . '" style="' . $v["styles"] . '" onclick="' . $onOpen . ' ' . $modal . '" />' . $v["text"] . '</button>';
    } else {
        return '<a ' . trim($v["id"]) . ' class="' . $v["class"] . '" ' . trim($v["type"]) . ' data-rel="' . $gallery_name . '" title="' . trim(strip_tags($v["title"])) . '" style="' . $v["styles"] . '" onclick="' . $onOpen . ' ' . $modal . '" href="' . $path . '">' . $v["text"] . '</a>';
    }
}

// offset is current month +/-
function get_calendar_list($offset = 0) {
    $offset = $offset >= 0 ? "+$offset" : "$offset";
    $today = strtotime("$offset month");
    $month = date("m", $today);
    $year = date("Y", $today);

    echo '<div>';
    echo '<a class="cal_arrows" href="index.php?offset='.($offset-1).'"><<</a>';

    $prev = $month == 1 ? 12 : $month - 1;
    $prevyear = $prev == 12 ? $year - 1 : $year;
    echo get_mini_calendar($prev, $prevyear);

    echo get_mini_calendar($month, $year, true);

    $next = $month == 12 ? 1 : $month + 1;
    $nextyear = $next == 1 ? $year + 1 : $year;
    echo get_mini_calendar($next, $nextyear);

    echo '<a class="cal_arrows" href="index.php?offset='.($offset+1).'">>></a>';
    echo '</div>';

    echo get_big_calendar($month, $year);

    // Initialize calendar
    echo '<script>
    $( function() {
        $(".lessoncontent img").css({"width":"100%","height":"auto"});
        $(".bigcal > span.dayofweek.contentexists").not("span.betweenday").not("span.weekheader").mousedown(function(event) {
          $(this).draggable("option", { helper : event.ctrlKey ? "clone" : "original"});
        }).draggable({ revert: "invalid" });

        $(".bigcal > span.dayofweek").not("span.betweenday").not("span.weekheader").not(".day6").not(".day7").droppable({
            hoverClass: "ui-droppable-hover",
            classes: {
                "ui-droppable-active": "ui-state-active",
                "ui-droppable-hover": "ui-state-hover"
            },
            drop: function(event, ui) {
                var duplicate = $(ui.draggable).draggable("option","helper") == "clone" ? true : false;
                var from = $(ui.draggable).find("#timestamp").val();
                var to = $(this).find("#timestamp").val();
                save(from, to, duplicate);
            }
        });
      } );
      </script>';
}

function get_mini_calendar($month, $year, $highlight = false){
    global $CFG;
    $daysinmonth = cal_days_in_month(CAL_GREGORIAN,$month,$year);

    $date = new DateTime("$year-$month-01 00:00:00", new DateTimeZone($CFG->timezone));
    $timestamp = $date->getTimestamp();

    $dayofweek = date("N", $timestamp);
    $highlight = $highlight ? "highlight" : "";
    $printout = '<span class="minical '.$highlight.'">';
    $printout .= '<div class="minical_month">' . date("F Y", $timestamp) . '</div>';

    $lastsunday = strtotime('last sunday', $timestamp);
    $printout .= "<span class='dayofweek day7'>".date("D", $lastsunday)."</span>";
    for($i = 1; $i < 7; $i++) {
        $printout .= "<span class='dayofweek day$i'>".date("D", strtotime("+ $i days", $lastsunday))."</span>";
    }

    if ($dayofweek < 7) { // Fill out days from previous month.
        $day = date("j", strtotime("-$dayofweek days", $timestamp));
        $lastmonth = $month == 1 ? 12 : $month - 1;
        $lastyear = $month == 1 ? $year - 1 : $year;
        $lessonid = $lastyear . sprintf("%02d", $lastmonth) . sprintf("%02d", $day);
        $content = get_content($lessonid);
        $contentclass = empty($content) ? '' : "contentexists";
        $lockedclass = !empty($content) && !empty($content["locked"]) ? "contentlocked" : "";
        $param   = array(
            "title" => "View Lesson",
            "text" => $day,
            "path" => $CFG->wwwroot . "/pages/quickview.php?action=viewlesson&lessonid=$lessonid",
            "width" => "500"
        );
        $onclick = empty($content) ? $day : make_modal_links($param);
        $printout .= "<span class='dayofweek lastmonth day7 $contentclass $lockedclass'>$onclick</span>";
        for($i = 1; $i < $dayofweek; $i++) {
            $day = date("j", strtotime("-".($dayofweek - $i)." days", $timestamp));
            $lessonid = $lastyear . sprintf("%02d", $lastmonth) . sprintf("%02d", $day);
            $content = get_content($lessonid);
            $contentclass = empty($content) ? '' : "contentexists";
            $lockedclass = !empty($content) && !empty($content["locked"]) ? "contentlocked" : "";
            $param   = array(
                "title" => "View Lesson",
                "text" => $day,
                "path" => $CFG->wwwroot . "/pages/quickview.php?action=viewlesson&lessonid=$lessonid",
                "width" => "500"
            );
            $onclick = empty($content) ? $day : make_modal_links($param);
            $printout .= "<span class='dayofweek lastmonth day$i $contentclass $lockedclass'>$onclick</span>";
        }
    }

    // Fill out month.
    for($i = 1; $i <= $daysinmonth; $i++) {
        $date = new DateTime("$year-$month-$i 00:00:00", new DateTimeZone($CFG->timezone));
        $timestamp = $date->getTimestamp();
        $dayofweek = date("N", $timestamp);
        $lessonid = $year . sprintf("%02d", $month) . sprintf("%02d", $i);
        $content = get_content($lessonid);
        $contentclass = empty($content) ? '' : "contentexists";
        $lockedclass = !empty($content) && !empty($content["locked"]) ? "contentlocked" : "";
        $param   = array(
            "title" => "View Lesson",
            "text" => $i,
            "path" => $CFG->wwwroot . "/pages/quickview.php?action=viewlesson&lessonid=$lessonid",
            "width" => "500"
        );
        $onclick = empty($content) ? $i : make_modal_links($param);
        $printout .= "<span class='dayofweek day$dayofweek $contentclass $lockedclass'>$onclick</span>";
    }

    if ($dayofweek !== 6) { // Fill out days for next month.
        $dayofweek = $dayofweek == 7 ? 7 : 7 - $dayofweek;
        for($i = 1; $i < $dayofweek; $i++) {
            $nextmonth = $month == 12 ? 1 : $month + 1;
            $nextyear = $month == 12 ? $year + 1 : $year;
            $lessonid = $nextyear . sprintf("%02d", $nextmonth) . sprintf("%02d", $i);
            $content = get_content($lessonid);
            $contentclass = empty($content) ? '' : "contentexists";
            $lockedclass = !empty($content) && !empty($content["locked"]) ? "contentlocked" : "";
            $param   = array(
                "title" => "View Lesson",
                "text" => $i,
                "path" => $CFG->wwwroot . "/pages/quickview.php?action=viewlesson&lessonid=$lessonid",
                "width" => "500"
            );
            $onclick = empty($content) ? $i : make_modal_links($param);
            $printout .= "<span class='dayofweek lastmonth day$i $contentclass $lockedclass'>$onclick</span>";
        }
    }

    $printout .= '</span>';
    return $printout;
}

function get_big_calendar($month, $year){
    global $CFG;
    $daysinmonth = cal_days_in_month(CAL_GREGORIAN,$month,$year);
    $weeksinmonth = "weeks" . weeks($month, $year);
    $date = new DateTime("$year-$month-01 00:00:00", new DateTimeZone($CFG->timezone));
    $timestamp = $date->getTimestamp();

    $dayofweek = date("N", $timestamp);
    $printout = '<span class="bigcal">';

    $lastsunday = strtotime('last sunday', $timestamp);
    $day = date("D", $lastsunday);
    $printout .= "<span class='dayofweek day7 weekheader $weeksinmonth'>" .
                    $day .
                 "</span>";
    for($i = 1; $i < 7; $i++) {
        $day = date("D", strtotime("+ $i days", $lastsunday));
        $printout .= "<span class='dayofweek day$i weekheader $weeksinmonth'>" .
                        $day .
                     "</span>";
    }

    if ($dayofweek < 7) { // Fill out days from previous month.
        $day = date("j", strtotime("-$dayofweek days", $timestamp));
        $lastmonth = $month == 1 ? 12 : $month - 1;
        $lastyear = $month == 1 ? $year - 1 : $year;
        $content = get_content($lastyear . sprintf("%02d", $lastmonth) . sprintf("%02d", $day));
        $text = empty($content) ? "" : $content["content"];
        $lockedclass = !empty($content) && !empty($content["locked"]) ? "contentlocked" : "";
        $contentclass = empty($text) ? '' : "contentexists";
        $controls = get_controls($lastyear . sprintf("%02d", $lastmonth) . sprintf("%02d", $day));
        $printout .= "<span class='dayofweek lastmonth day7 $weeksinmonth $contentclass $lockedclass'>" .
                        '<span class="numbers">' . $day . '</span>' .
                        '<div class="lessoncontent">' .
                            $text .
                        '</div>'
                        . $controls .
                     "</span>";
        for($i = 1; $i < $dayofweek; $i++) {
            $day = date("j", strtotime("-".($dayofweek - $i)." days", $timestamp));
            $content = get_content($lastyear . sprintf("%02d", $lastmonth) . sprintf("%02d", $day));
            $text = empty($content) ? "" : $content["content"];
            $lockedclass = !empty($content) && !empty($content["locked"]) ? "contentlocked" : "";
            $contentclass = empty($text) ? '' : "contentexists";
            $controls = get_controls($lastyear . sprintf("%02d", $lastmonth) . sprintf("%02d", $day));
            $printout .= "<span class='dayofweek lastmonth day$i $weeksinmonth $contentclass $lockedclass'>" .
                            '<span class="numbers">' . $day . '</span>' .
                            '<div class="lessoncontent">' .
                                $text .
                            '</div>'
                            . $controls .
                         "</span>";
        }
    }

    // Fill out month.
    for($i = 1; $i <= $daysinmonth; $i++) {
        $date = new DateTime("$year-$month-$i 00:00:00", new DateTimeZone($CFG->timezone));
        $timestamp = $date->getTimestamp();
        $dayofweek = date("N", $timestamp);
        $content = get_content($year . sprintf("%02d", $month) . sprintf("%02d", $i));
        $text = empty($content) ? "" : $content["content"];
        $lockedclass = !empty($content) && !empty($content["locked"]) ? "contentlocked" : "";
        $contentclass = empty($text) ? '' : "contentexists";
        $controls = get_controls($year . sprintf("%02d", $month) . sprintf("%02d", $i));
        $printout .= "<span class='dayofweek day$dayofweek $weeksinmonth $contentclass $lockedclass'>" .
                        '<span class="numbers">' . $i . '</span>' .
                        '<div class="lessoncontent">' .
                            $text .
                        '</div>'
                        . $controls .
                     "</span>";
    }

    if ($dayofweek !== 6) { // Fill out days for next month.
        $dayofweek = $dayofweek == 7 ? 7 : 7 - $dayofweek;
        for($i = 1; $i < $dayofweek; $i++) {
            $nextmonth = $month == 12 ? 1 : $month + 1;
            $nextyear = $month == 12 ? $year + 1 : $year;
            $content = get_content($nextyear . sprintf("%02d", $nextmonth) . sprintf("%02d", $i));
            $text = empty($content) ? "" : $content["content"];
            $lockedclass = !empty($content) && !empty($content["locked"]) ? "contentlocked" : "";
            $contentclass = empty($text) ? '' : "contentexists";
            $controls = get_controls($nextyear . sprintf("%02d", $nextmonth) . sprintf("%02d", $i));
            $printout .= "<span class='dayofweek lastmonth day$i $weeksinmonth $contentclass $lockedclass'>" .
                            '<span class="numbers">' . $i . '</span>' .
                            '<div class="lessoncontent">' .
                                $text .
                            '</div>'
                            . $controls .
                         "</span>";
        }
    }

    $printout .= '</span>';
    return $printout;
}

function get_controls($id) {
    global $CFG;
    $editlink = $deletelink = "";

    $edit   = array(
        "title" => "Edit",
        "text" => "Edit",
        "path" => $CFG->wwwroot . "/pages/forms.php?action=add_edit_lesson&lessonid=$id",
        "image" => $CFG->wwwroot . "/images/16x16/edit_16x16.png"
    );
    $editlink = '<a title="'.$edit["title"].'" href="'.$edit["path"].'"><img src="'.$edit["image"].'" /> '.$edit["text"].'</a>';

    if (get_content($id)) {
        $delete   = array(
            "title" => "Delete",
            "text" => "Delete",
            "path" => $CFG->wwwroot . "/ajax/site_ajax.php?action=delete_lesson&lessonid=$id",
            "image" => $CFG->wwwroot . "/images/16x16/delete_16x16.png"
        );
        $deletelink = '<a title="'.$delete["title"].'" href="'.$delete["path"].'" onclick="return confirm(\'Are you sure?\')"><img src="'.$delete["image"].'" /> '.$delete["text"].'</a>';
    }

    return '<form class="tools">
                <div class="dropdown">
                  <div class="dropbtn">
                    <img src="'.$CFG->wwwroot.'/images/16x16/gear-4_16x16.png" style="width: 10px;" />
                  </div>
                  <div class="dropdown-content">
                    '.$editlink.'
                    '.$deletelink.'
                  </div>
                </div>
                <input type="hidden" id="timestamp" value="' . $id . '"/>
            </form>';
}

function get_content($id) {
    global $USER;
    if ($row = get_db_row("SELECT * FROM lessons WHERE userid='" . $USER->userid . "' AND timestamp='$id'")) {
        return $row;
    } else {
        return false;
    }
}

function weeks($month, $year) {
    global $CFG;

    // Count Sundays in a month.
    $sundays=0;
    $total_days=cal_days_in_month(CAL_GREGORIAN, $month, $year);
    for($i=1;$i<=$total_days;$i++)
    if(date('N',strtotime($year.'-'.$month.'-'.$i))==7)
    $sundays++;

    $date = new DateTime("$year-$month-01 00:00:00", new DateTimeZone($CFG->timezone));
    $timestamp = $date->getTimestamp();
    $dayofweek = date("N", $timestamp);

    if ($dayofweek < 7) { $sundays++; } // Add one for the month if first day wasn't a sunday.

    return $sundays;
}

function get_months_offset($lessonid) {
    if(empty($lessonid)) {
        return 0;
    }

    $today = get_timestamp();
    $yeara = date("Y", $today);
    $montha = date("m", $today);

    $date = new DateTime(substr($lessonid,0,4) . "-" . substr($lessonid,4,2) . "-01 00:00:00");
    $yearb = date_format($date, "Y");
    $monthb = date_format($date, "m");

    $offset = (($yearb - $yeara) * 12) + ($monthb - $montha);
    return $offset;
}

function back_to_calendar($lessonid, $timer='0') {
    global $CFG;
    $offset = get_months_offset($lessonid);
    if ($offset !== "0") {
        return '<meta http-equiv="Refresh" content="'.$timer.'; url='.$CFG->wwwroot.'/index.php?offset='.$offset.'" />';
    } else {
        return '<meta http-equiv="Refresh" content="'.$timer.'; url='.$CFG->wwwroot.' />';
    }
}

function print_view_selector() {
    global $MYVARS, $CFG;
    $offset = empty($MYVARS->GET["offset"]) ? 0 : $MYVARS->GET["offset"];
    $selected = empty($MYVARS->GET["view"]) ? "" : $MYVARS->GET["view"];
    $actions = array();
    $actions[] = array("val" => "cal", "txt" => "Manage Lessons");
    $actions[] = array("val" => "printcal", "txt" => "Printable Month");
    $actions[] = array("val" => "month", "txt" => "Agenda Month");
    $actions[] = array("val" => "printrange", "txt" => "Agenda Range");

    $onchange = "window.location.href = '".$CFG->wwwroot."/index.php?offset=".$offset."&view=' + this.value;";
    return '<div style="display: inline-block;">View:&nbsp;' .
                make_select_from_array("view", $actions, "val", "txt", $selected, $onchange, true, 1, 'width:calc(100vw / 3);') .
            '</div>';
}

function print_actions_selector() {
    global $MYVARS, $CFG;
    $offset = empty($MYVARS->GET["offset"]) ? 0 : $MYVARS->GET["offset"];
    $selected = empty($MYVARS->GET["actions"]) ? "" : $MYVARS->GET["actions"];
    $actions = array();
    $actions[] = array("val" => "copy_range_form", "txt" => "Copy Range");
    $actions[] = array("val" => "delete_range_form", "txt" => "Delete Range");
    $actions[] = array("val" => "delete_all_form", "txt" => "Delete All");

    $onchange = "window.location.href = '".$CFG->wwwroot."/pages/forms.php?offset=".$offset."&action=' + this.value;";
    return '<div style="display: inline-block;">Actions:&nbsp;' .
                make_select_from_array("actions", $actions, "val", "txt", $selected, $onchange, true, 1, 'width:calc(100vw / 3);') .
            '</div>';
}

function printable_month($offset) {
    global $USER, $CFG;

    $offset = $offset >= 0 ? "+$offset" : "$offset";
    $today = strtotime("$offset month");
    $month = date("m", $today);
    $year = date("Y", $today);

    $returnme = '<a class="monthly_scroll" href="'.$CFG->wwwroot.'/index.php?offset='.($offset-1).'&view=month">'. date("F Y", strtotime(($offset - 1) . " month")).'</a>';
    $returnme .= "<div><h1>" . date("F Y", $today) . "</h1></div>";
    if ($results = get_db_result("SELECT * FROM lessons WHERE userid='$USER->userid' AND timestamp LIKE '$year$month%' ORDER BY timestamp")) {
        while ($lesson = fetch_row($results)) {
            $lessonid = $lesson["timestamp"];
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
    } else {
        $returnme .= '<div class="weeklyview">
                        <div class="weeklycontent">No Lessons Found</div>
                      </div>';
    }

    $returnme .= '<a class="monthly_scroll" href="'.$CFG->wwwroot.'/index.php?offset='.($offset+1).'&view=month">'. date("F Y", strtotime(($offset + 1) . " month")).'</a>';

    return $returnme;
}

function printable_range_form() {
    global $USER;

    $returnme = '
        <form class="centerform" action="./pages/forms.php" method="post">
            <div><h1>Select Date Range</h1></div>
            <input type="hidden" id="action" name="action" value="print_range" />
            From: <input type="date" name="from" required pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}">
            To: <input type="date" name="to" required pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}">
            <input type="submit" value="View">
        </form>
    ';
    return $returnme;
}

function printable_calendar($offset) {
    global $USER, $CFG;

    $offset = $offset >= 0 ? "+$offset" : "$offset";
    $today = strtotime("$offset month");
    $month = date("m", $today);
    $year = date("Y", $today);

    $returnme = '<a class="monthly_scroll" href="'.$CFG->wwwroot.'/index.php?offset='.($offset-1).'&view=printcal">'. date("F Y", strtotime(($offset - 1) . " month")).'</a>';

    $daysinmonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $weeksinmonth = "weeks" . weeks($month, $year);
    $date = new DateTime("$year-$month-01 00:00:00", new DateTimeZone($CFG->timezone));
    $timestamp = $date->getTimestamp();

    $dayofweek = date("N", $timestamp);
    $returnme .= '<h1 style="text-align: center">' . date("F Y", strtotime(($offset) . " month")) . "</h1>";
    $returnme .= '<span class="bigcal printable_calendar">';
    $lastsunday = strtotime('last sunday', $timestamp);
    $day = date("D", $lastsunday);
    $returnme .= "<span class='dayofweek day7 weekheader $weeksinmonth'>" .
                    $day .
                 "</span>";
    for($i = 1; $i < 7; $i++) {
        $day = date("D", strtotime("+ $i days", $lastsunday));
        $returnme .= "<span class='dayofweek day$i weekheader $weeksinmonth'>" .
                        $day .
                     "</span>";
    }

    if ($dayofweek < 7) { // Fill out days from previous month.
        $day = date("j", strtotime("-$dayofweek days", $timestamp));
        $lastmonth = $month == 1 ? 12 : $month - 1;
        $lastyear = $month == 1 ? $year - 1 : $year;
        $content = get_content($lastyear . sprintf("%02d", $lastmonth) . sprintf("%02d", $day));
        $text = empty($content) ? "" : $content["content"];
        $lockedclass = !empty($content) && !empty($content["locked"]) ? "contentlocked" : "";
        $contentclass = empty($text) ? '' : "contentexists";
        $returnme .= '<span class="week">';
        $returnme .= "<span class='dayofweek lastmonth day7 $weeksinmonth $contentclass $lockedclass'>" .
                        '<span class="numbers">' . $day . '</span>' .
                        '<div class="lessoncontent">' .
                            $text .
                        '</div>
                     </span>';
        for($i = 1; $i < $dayofweek; $i++) {
            $day = date("j", strtotime("-".($dayofweek - $i)." days", $timestamp));
            $content = get_content($lastyear . sprintf("%02d", $lastmonth) . sprintf("%02d", $day));
            $text = empty($content) ? "" : $content["content"];
            $lockedclass = !empty($content) && !empty($content["locked"]) ? "contentlocked" : "";
            $contentclass = empty($text) ? '' : "contentexists";
            $returnme .= "<span class='dayofweek lastmonth day$i $weeksinmonth $contentclass $lockedclass'>" .
                            '<span class="numbers">' . $day . '</span>' .
                            '<div class="lessoncontent">' .
                                $text .
                            '</div>
                         </span>';
        }
    }

    // Fill out month.
    for($i = 1; $i <= $daysinmonth; $i++) {
        $date = new DateTime("$year-$month-$i 00:00:00", new DateTimeZone($CFG->timezone));
        $timestamp = $date->getTimestamp();
        $dayofweek = date("N", $timestamp);
        $content = get_content($year . sprintf("%02d", $month) . sprintf("%02d", $i));
        $text = empty($content) ? "" : $content["content"];

        if ($dayofweek == 7) { $returnme .= '<span class="week">'; }
        $returnme .= "<span class='dayofweek day$dayofweek $weeksinmonth'>" .
                        '<span class="numbers">' . $i . '</span>' .
                        '<div class="lessoncontent">' .
                            $text .
                        '</div>
                    </span>';
        if ($dayofweek == 6) { $returnme .= '</span>'; }
    }

    if ($dayofweek !== 6) { // Fill out days for next month.
        $dayofweek = $dayofweek == 7 ? 7 : 7 - $dayofweek;
        for($i = 1; $i < $dayofweek; $i++) {
            $nextmonth = $month == 12 ? 1 : $month + 1;
            $nextyear = $month == 12 ? $year + 1 : $year;
            $content = get_content($nextyear . sprintf("%02d", $nextmonth) . sprintf("%02d", $i));
            $text = empty($content) ? "" : $content["content"];
            $lockedclass = !empty($content) && !empty($content["locked"]) ? "contentlocked" : "";
            $contentclass = empty($text) ? '' : "contentexists";
            $returnme .= "<span class='dayofweek lastmonth day$i $weeksinmonth $contentclass $lockedclass'>" .
                            '<span class="numbers">' . $i . '</span>' .
                            '<div class="lessoncontent">' .
                                $text .
                            '</div>
                        </span>';
        }
    }
    $returnme .= '</span></span>';

    $returnme .= '<a class="monthly_scroll" href="'.$CFG->wwwroot.'/index.php?offset='.($offset+1).'&view=printcal">'. date("F Y", strtotime(($offset + 1) . " month")).'</a>';

    // Initialize calendar
    $returnme .=  '<script>
                    $( function() {
                        $(".lessoncontent img").css({"width":"100%","height":"auto"});
                    } );
                   </script>';

    return $returnme;
}

?>