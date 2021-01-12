<?php
switch ($_SESSION['lang']) {
	case 'ru':
		include_once($_SERVER['DOCUMENT_ROOT'].'/lang/ru.php');
		$_SESSION['locale'] = 'ru';
		break;
	case 'ua':
		include_once($_SERVER['DOCUMENT_ROOT'].'/lang/ua.php');
		$_SESSION['locale'] = 'uk';
		break;
	case 'en':
		include_once($_SERVER['DOCUMENT_ROOT'].'/lang/en.php');
		$_SESSION['locale'] = 'en';
		break;
	default:
		include_once($_SERVER['DOCUMENT_ROOT'].'/lang/ru.php');
		break;
}

 ?>