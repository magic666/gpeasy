<?php


define('gp_start_time',microtime(true));

defined('is_running') or define('is_running',true);
require_once('common.php');
common::EntryPoint(0);

/*
 *	Flow Control
 */

if( !empty($GLOBALS['config']['updating_message']) ){
	die($GLOBALS['config']['updating_message']);
}


$title = common::WhichPage();
$type = common::SpecialOrAdmin($title);
switch($type){

	case 'special':
		includeFile('special.php');
		$page = new special_display($title,$type);
	break;

	case 'admin':
		if( common::LoggedIn() ){
			includeFile('admin/admin_display.php');
			$page = new admin_display($title,$type);
		}else{
			includeFile('admin/admin_login.php');
			$page = new admin_login($title,$type);
		}
	break;

	default:
		if( common::LoggedIn() ){
			includeFile('tool/editing_page.php');
			$page = new editing_page($title,$type);
		}else{
			$page = new display($title,$type);
		}
	break;
}

gpOutput::RunOut();





