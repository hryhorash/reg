<?php
$tabs = array();
$tabs[] =  array('id'=>'cal',		'power'=>2, 'title'=>lang::MENU_VISITS,		'tab'=>'all',		'link'=>'/user/dashboard.php?date='.$_GET['date'] . '&staffID='.$_GET['staffID'],	'name'=>lang::SIDE_CALENDAR);
$tabs[] =  array('id'=>'vst_all',		'power'=>10, 'title'=>lang::MENU_VISITS,		'tab'=>'all',		'link'=>'/visits/visits_list.php?state=all&date='.$_GET['date'],	'name'=>lang::TAB_ALL);
$tabs[] =  array('id'=>'vst_active',	'power'=>10, 'title'=>lang::MENU_VISITS,		'tab'=>'active',	'link'=>'/visits/visits_list.php?date='.$_GET['date'],				'name'=>lang::TAB_IN_PROGRESS);
$tabs[] =  array('id'=>'vst_add',		'power'=>10, 'title'=>lang::MENU_VISITS,		'tab'=>'add',		'link'=>'/visits/visit_details.php?new&date='.$_GET['date'],		'name'=>lang::TAB_ADD);	
$tabs[] =  array('id'=>'vst_archive',	'power'=>10, 'title'=>lang::MENU_VISITS,		'tab'=>'archive',	'link'=>'/visits/visits_list.php?tab=archive&date='.$_GET['date'],	'name'=>lang::TAB_ARCHIVE);


$tabs[] =  array('id'=>'clt_active',	'power'=>10, 'title'=>lang::MENU_CLIENTS,		'tab'=>'active',	'link'=>'/clients/client_list.php',				'name'=>lang::TAB_LIST);
$tabs[] =  array('id'=>'clt_add',		'power'=>10, 'title'=>lang::MENU_CLIENTS,		'tab'=>'add',		'link'=>'/clients/client_add.php',				'name'=>lang::TAB_ADD);	
$tabs[] =  array('id'=>'clt_archive',	'power'=>10, 'title'=>lang::MENU_CLIENTS,		'tab'=>'archive',	'link'=>'/clients/client_list.php?tab=archive',	'name'=>lang::TAB_ARCHIVE);

$tabs[] =  array('id'=>'src_active',	'power'=>10, 'title'=>lang::MENU_CLIENT_SOURCES,'tab'=>'active',	'link'=>'/clients/source_list.php',				'name'=>lang::TAB_LIST);
$tabs[] =  array('id'=>'src_add',		'power'=>10, 'title'=>lang::MENU_CLIENT_SOURCES,'tab'=>'add',		'link'=>'/clients/source_add.php?tab=active',	'name'=>lang::TAB_ADD);	
$tabs[] =  array('id'=>'src_archive',	'power'=>10, 'title'=>lang::MENU_CLIENT_SOURCES,'tab'=>'archive',	'link'=>'/clients/source_list.php?tab=archive',	'name'=>lang::TAB_ARCHIVE);

?>