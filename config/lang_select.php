<?php
switch ($_SESSION['lang']) {
	case 'ru':
		include_once($_SERVER['DOCUMENT_ROOT'].'/lang/ru.php');
		break;
	case 'ua':
		include_once($_SERVER['DOCUMENT_ROOT'].'/lang/ua.php');
		break;
	case 'en':
		include_once($_SERVER['DOCUMENT_ROOT'].'/lang/en.php');
		break;
	default:
		include_once($_SERVER['DOCUMENT_ROOT'].'/lang/ru.php');
		break;
}

 ?>