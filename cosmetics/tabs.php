<?php
$tabs = array();

$tabs[] =  array('id'=>'sale',		'power'=>10, 'title'=>lang::MENU_WAREHOUSE,	'tab'=>'sale',			'link'=>'/cosmetics/sale.php',						'name'=>lang::HDR_PURPOSE_SALE);
$tabs[] =  array('id'=>'work',		'power'=>10, 'title'=>lang::MENU_WAREHOUSE,	'tab'=>'work',			'link'=>'/cosmetics/work.php',						'name'=>lang::HDR_PURPOSE_WORK);
$tabs[] =  array('id'=>'sell',		'power'=>10, 'title'=>lang::MENU_WAREHOUSE,	'tab'=>'sell',			'link'=>'/cosmetics/sell.php',						'name'=>lang::TAB_SELL);


$tabs[] =  array('id'=>'inv_all',		'power'=>10, 'title'=>lang::MENU_PURCHASES,	'tab'=>'all',		'link'=>'/cosmetics/invoice_list.php?tab=all',		'name'=>lang::TAB_ALL);
$tabs[] =  array('id'=>'inv_active',	'power'=>10, 'title'=>lang::MENU_PURCHASES,	'tab'=>'active',	'link'=>'/cosmetics/invoice_list.php',				'name'=>lang::TAB_IN_PROGRESS);
$tabs[] =  array('id'=>'inv_add',		'power'=>10, 'title'=>lang::MENU_PURCHASES,	'tab'=>'add',		'link'=>'/cosmetics/invoice_add.php',				'name'=>lang::TAB_ADD);	
$tabs[] =  array('id'=>'inv_archive',	'power'=>10, 'title'=>lang::MENU_PURCHASES, 'tab'=>'archive',	'link'=>'/cosmetics/invoice_list.php?tab=archive',	'name'=>lang::TAB_ARCHIVE);
$tabs[] =  array('id'=>'exp_report',	'power'=>10, 'title'=>lang::MENU_PURCHASES,	'tab'=>'exp_report','link'=>'/expences/expencesList.php',				'name'=>lang::TAB_EXTRA);

$tabs[] =  array('id'=>'brd_active',	'power'=>10, 'title'=>lang::H2_BRANDS,		'tab'=>'active',	'link'=>'/cosmetics/brand_list.php',				'name'=>lang::TAB_LIST);
$tabs[] =  array('id'=>'brd_add',		'power'=>10, 'title'=>lang::H2_BRANDS,		'tab'=>'active',	'link'=>'/cosmetics/brand_add.php',					'name'=>lang::TAB_ADD);
$tabs[] =  array('id'=>'brd_archive',	'power'=>10, 'title'=>lang::H2_BRANDS,		'tab'=>'active',	'link'=>'/cosmetics/brand_list.php?tab=archive',	'name'=>lang::TAB_ARCHIVE);

$tabs[] =  array('id'=>'csm_active',	'power'=>10, 'title'=>lang::MENU_COSMETICS,	'tab'=>'active',	'link'=>'/cosmetics/cosmetics_list.php?brandID='.$_GET['brandID'],				'name'=>lang::TAB_LIST);
$tabs[] =  array('id'=>'csm_add',		'power'=>10, 'title'=>lang::MENU_COSMETICS,	'tab'=>'add',		'link'=>'/cosmetics/cosmetics_add.php',											'name'=>lang::TAB_ADD);	
$tabs[] =  array('id'=>'csm_archive',	'power'=>10, 'title'=>lang::MENU_COSMETICS,	'tab'=>'archive',	'link'=>'/cosmetics/cosmetics_list.php?tab=archive&brandID='.$_GET['brandID'],	'name'=>lang::TAB_ARCHIVE);

$tabs[] =  array('id'=>'spl_active',	'power'=>10, 'title'=>lang::H2_SUPPLIERS,	'tab'=>'active',	'link'=>'/cosmetics/suppliers_list.php?brandID='.$_GET['brandID'],				'name'=>lang::TAB_LIST);
$tabs[] =  array('id'=>'spl_add',		'power'=>10, 'title'=>lang::H2_SUPPLIERS,	'tab'=>'add',		'link'=>'/cosmetics/supplier_add.php',											'name'=>lang::TAB_ADD);	
$tabs[] =  array('id'=>'spl_archive',	'power'=>10, 'title'=>lang::H2_SUPPLIERS,	'tab'=>'archive',	'link'=>'/cosmetics/suppliers_list.php?tab=archive?brandID='.$_GET['brandID'],	'name'=>lang::TAB_ARCHIVE);



?>