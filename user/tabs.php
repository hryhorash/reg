<?php 
$tabs = array();
$tabs[] = array('id'=>'usr_active',		'power'=>10, 'title'=>lang::TAB_USERS,    'link'=>'userList.php',											'name'=>lang::TAB_LIST);
$tabs[] = array('id'=>'usr_add',		'power'=>10, 'title'=>lang::TAB_USERS,	  'link'=>'user_add.php',											'name'=>lang::TAB_ADD);	
$tabs[] = array('id'=>'usr_archive',	'power'=>10, 'title'=>lang::TAB_USERS,    'link'=>'userList.php?tab=archive',								'name'=>lang::TAB_ARCHIVE);
$tabs[] = array('id'=>'wrk_active',		'power'=>10, 'title'=>lang::TAB_WORKDAYS, 'link'=>'workdays_list.php?userID=' .$_GET['userID'],				'name'=>lang::TAB_LIST);
$tabs[] = array('id'=>'wrk_archive',	'power'=>10, 'title'=>lang::TAB_WORKDAYS, 'link'=>'workdays_list.php?tab=archive&userID='.$_GET['userID'],  'name'=>lang::TAB_ARCHIVE);


?>