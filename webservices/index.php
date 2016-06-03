<?php

require_once('class/config.php');
require_once('class/mysql.php');

if (isset($_REQUEST['version']) && $_REQUEST['version'] == 'v2')
    require_once('class/web-service-v2.php');
else
	require_once('class/web-service.php');

$DB = new DBmysql($CFG['dbhost'], $CFG['dbuser'], $CFG['dbpass'], $CFG['dbname']);
$Main = new webservice();

if (isset($_REQUEST['action']))
    $action = $_REQUEST['action'];
else
    $action = '';

if ($action != '' && !preg_match('|^[a-z0-9_]+$|i', $action))
    $action = 'wrongurl';

if (method_exists($Main, $action))
    $Main->$action();
else
    $Main->wrongurl();
?>