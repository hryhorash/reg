<?php
$tabs = array();
$tabs[] =  array('id'=>'loc_active',		'power'=>90, 'title'=>lang::TAB_LOCATIONS,'tab'=>'active','link'=>'list.php','name'=>lang::TAB_LIST);
$tabs[] =  array('id'=>'loc_add',			'power'=>90, 'title'=>lang::TAB_LOCATIONS,'tab'=>'add','link'=>'location_add.php','name'=>lang::TAB_ADD);	
$tabs[] =  array('id'=>'loc_archive',		'power'=>90, 'title'=>lang::TAB_LOCATIONS,'tab'=>'archive','link'=>'list.php?tab=archive','name'=>lang::TAB_ARCHIVE);
$tabs[] =  array('id'=>'day_active',		'power'=>10, 'title'=>lang::TAB_DAYS_OFF, 'tab'=>'off','link'=>'location_daysOff.php','name'=>lang::TAB_LIST);
$tabs[] =  array('id'=>'day_add',			'power'=>10, 'title'=>lang::TAB_DAYS_OFF, 'tab'=>'add','link'=>'location_dayOff_add.php','name'=>lang::TAB_ADD);	
$tabs[] =  array('id'=>'day_archive',		'power'=>10, 'title'=>lang::TAB_DAYS_OFF, 'tab'=>'off','link'=>'location_daysOff.php?tab=archive','name'=>lang::TAB_ARCHIVE);

?>