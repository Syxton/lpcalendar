<?php
// SyxtonCMS Configuration File
unset($CFG);
unset($USER);

$CFG = new stdClass();

//Website info
$CFG->sitename 	= 'Lessonplan Calendar';
$CFG->siteowner	= ''; // Required for signup emails to be sent
$CFG->siteemail	= ''; // Required for signup emails to be sent
$CFG->sitefooter = '';

//Database connection variables
$CFG->dbtype    = 'mysqli'; // Required: mysql or mysqli.
$CFG->dbhost    = 'localhost';  // Required: normally localhost.
$CFG->dbname    = 'lpcalendar'; // Required: name of db.
$CFG->dbuser    = 'root';  // Required: db username w/ permissions on above table.
$CFG->dbpass    = '';  // Required: db password for above user.

//SMTP server
$CFG->smtppath	= '';
$CFG->smtp	= false;
$CFG->smtpauth = true;
$CFG->smtpuser	= '';
$CFG->smtppass	= '';

//Directory variables
$CFG->directory = ''; // If root directory, leave empty.  if in folder make 'foldername'
$CFG->wwwroot   = '//'.$_SERVER['SERVER_NAME'];
$CFG->wwwroot   = $CFG->directory ? $CFG->wwwroot.'/'.$CFG->directory : $CFG->wwwroot;
$CFG->docroot   = dirname(__FILE__);
$CFG->dirroot   = $CFG->docroot;

//Userfile path
$CFG->userfilespath = $CFG->docroot  . '\userfiles';

//Cookie variables in seconds
$CFG->cookietimeout = 600;
$CFG->timezone = "America/Indianapolis"; // Required, look at: https://www.php.net/manual/en/timezones.php

?>