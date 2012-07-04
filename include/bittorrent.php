<?php
define('IN_TRACKER', true);
define("PROJECTNAME","清影PT");
define("NEXUSPHPURL","http://www.nexusphp.com");
define("NEXUSWIKIURL","http://www.nexusphp.com/wiki");
define("VERSION","Powered by <a href=\"aboutnexus.php\">NexusPHP</a>");
define("THISTRACKER","General");
$showversion = " - Powered by NexusPHP";
$rootpath=realpath(dirname(__FILE__) . '/..');
set_include_path(get_include_path() . PATH_SEPARATOR . $rootpath);
$rootpath .= "/";
include($rootpath . 'include/core.php');
include_once($rootpath . 'include/functions.php');
