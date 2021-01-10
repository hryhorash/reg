<?php
$tabs = array();

$tabs[] =  array('id'=>'fin',		'power'=>90, 'title'=>lang::MENU_REPORTS,	'tab'=>'fin',			'link'=>'/reports/finance_main.php',		'name'=>lang::H2_FINANCE_REPORT);
$tabs[] =  array('id'=>'year',		'power'=>90, 'title'=>lang::MENU_REPORTS,	'tab'=>'year',			'link'=>'/reports/finance_yearly.php',		'name'=>lang::H2_FINANCE_YEARLY);
$tabs[] =  array('id'=>'cl_val',	'power'=>90, 'title'=>lang::MENU_REPORTS,	'tab'=>'cl_val',		'link'=>'/reports/client_value.php',		'name'=>lang::MENU_CLIENT_VALUE);
$tabs[] =  array('id'=>'work',		'power'=>90, 'title'=>lang::MENU_REPORTS,	'tab'=>'work',			'link'=>'/reports/visits_per_day.php',		'name'=>lang::H2_VISITS_REVENUE_PER_DAY);
$tabs[] =  array('id'=>'sales',		'power'=>90, 'title'=>lang::MENU_REPORTS,	'tab'=>'sales',			'link'=>'/reports/sales.php',				'name'=>lang::H2_SALES_REPORT);


?>