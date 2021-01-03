<?php
$tabs = array();
$tabs[] =  array('id'=>'inv_all',			'power'=>10, 'title'=>lang::MENU_PURCHASES,		'tab'=>'all',			'link'=>'/cosmetics/invoice_list.php?tab=all',	'name'=>lang::TAB_COSMETICS);
$tabs[] =  array('id'=>'exp_report',		'power'=>10, 'title'=>lang::MENU_PURCHASES,		'tab'=>'exp_report',	'link'=>'/expences/expencesList.php',			'name'=>lang::TAB_EXTRA);
$tabs[] =  array('id'=>'cat_active',		'power'=>10, 'title'=>lang::MENU_EXPENCES_CAT,  'tab'=>'cat_list',		'link'=>'/expences/catList.php',				'name'=>lang::TAB_LIST);
$tabs[] =  array('id'=>'cat_add',			'power'=>10, 'title'=>lang::MENU_EXPENCES_CAT,	'tab'=>'add',			'link'=>'/expences/cat_add.php',				'name'=>lang::TAB_ADD);	
$tabs[] =  array('id'=>'cat_archive',		'power'=>10, 'title'=>lang::MENU_EXPENCES_CAT,	'tab'=>'archive',		'link'=>'/expences/catList.php?tab=archive',	'name'=>lang::TAB_ARCHIVE);
$tabs[] =  array('id'=>'rate_active',		'power'=>10, 'title'=>lang::MENU_STAKES,		'tab'=>'active', 		'link'=>'/expences/stakesList.php',			 	'name'=>lang::TAB_LIST);
$tabs[] =  array('id'=>'rate_add',			'power'=>10, 'title'=>lang::MENU_STAKES,		'tab'=>'add',	 		'link'=>'/expences/stakes_add.php',  			'name'=>lang::TAB_ADD);	
$tabs[] =  array('id'=>'rate_archive',		'power'=>10, 'title'=>lang::MENU_STAKES,		'tab'=>'archive',		'link'=>'/expences/stakesList.php?tab=archive', 'name'=>lang::TAB_ARCHIVE);

$tabs[] =  array('id'=>'net_active',		'power'=>10, 'title'=>lang::HDR_SERVICE_NETTO,	'tab'=>'active',		'link'=>'/expences/works_netto_list.php', 	'name'=>lang::TAB_LIST);
$tabs[] =  array('id'=>'net_add',			'power'=>10, 'title'=>lang::HDR_SERVICE_NETTO,	'tab'=>'add',	 		'link'=>'/expences/works_netto_add.php',  			'name'=>lang::TAB_ADD);	

?>