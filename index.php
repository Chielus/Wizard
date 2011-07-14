<?php

include_once("model/Settings.php");
include_once("model/Customer.php");
include_once("model/Db.php");
include_once("model/Infoscreen.php");
include_once("model/Stations.php");

$frameworkPath='/home/ruben/Aptana Studio 3 Workspace/prado/framework/prado.php';
// The following directory checks may be removed if performance is required
$basePath=dirname(__FILE__);
$assetsPath=$basePath.'/assets';
$runtimePath=$basePath.'/protected/runtime';

if(!is_file($frameworkPath))
	die("Unable to find prado framework path $frameworkPath.");
if(!is_writable($assetsPath))
	die("Please make sure that the directory $assetsPath is writable by Web server process.");
if(!is_writable($runtimePath))
	die("Please make sure that the directory $runtimePath is writable by Web server process.");


require_once($frameworkPath);

$application=new TApplication;
$application->run();

?>