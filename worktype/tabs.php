<?php
$tabs = array();
$tabs[] =  array('id'=>'wrk_active',	'power'=>1,  'title'=>lang::MENU_WORKS,			'tab'=>'work_list','link'=>'work_list.php',			 'name'=>lang::TAB_LIST);	
$tabs[] =  array('id'=>'wrk_add', 		'power'=>10, 'title'=>lang::MENU_WORKS,			'tab'=>'add','link'=>'work_add.php',				 'name'=>lang::TAB_ADD);	
$tabs[] =  array('id'=>'wrk_archive', 	'power'=>10, 'title'=>lang::MENU_WORKS,			'tab'=>'archive','link'=>'work_list.php?tab=archive','name'=>lang::TAB_ARCHIVE);
$tabs[] =  array('id'=>'cat_active', 	'power'=>10, 'title'=>lang::MENU_WORKTYPE_CAT,	'tab'=>'active','link'=>'cat_list.php',				 'name'=>lang::TAB_LIST);
$tabs[] =  array('id'=>'cat_add', 		'power'=>10, 'title'=>lang::MENU_WORKTYPE_CAT,	'tab'=>'add','link'=>'cat_add.php',					 'name'=>lang::TAB_ADD);
$tabs[] =  array('id'=>'cat_archive', 	'power'=>10, 'title'=>lang::MENU_WORKTYPE_CAT,	'tab'=>'archive','link'=>'cat_list.php?tab=archive', 'name'=>lang::TAB_ARCHIVE);

?>