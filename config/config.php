<?php 
	/*if (PHP_VERSION_ID >= 70300) { 
		session_set_cookie_params([
			'samesite' => 'Strict'
		]);
	} else { 
		session_set_cookie_params(
			'/; samesite=Strict'
		);
	}*/
	session_start();
	include (dirname(__FILE__).'/connect.php');
	include (dirname(__FILE__).'/functions.php');
	
	if ($_REQUEST['lang'] != '') $_SESSION['lang'] = $_REQUEST['lang'];
	include_once($_SERVER['DOCUMENT_ROOT'].'/config/lang_select.php');
	
	
	// перенаправляем на страницу логина, если пользователь еще не залогинился
	if (strpos($_SERVER['REQUEST_URI'], '/site/') === 0) {
		$_SESSION['locationSelected'] = 1;  // КОСТЫЛЬ, ИЗМЕНИТЬ ДЛЯ ПРОДАКШН
	} else {
	
		if(!isset($_SESSION['loggedin'])) {
			if ($_SERVER['REQUEST_URI'] == '/index.php' 
				OR $_SERVER['REQUEST_URI'] == '/' 
				OR $_SERVER['REQUEST_URI'] == '/user/restore_pass.php'
				OR strpos($_SERVER['REQUEST_URI'], '/site/') === 0	) {
					//не перенаправляем
			} else {
				$_SESSION['locationSelected'] = 1;  // КОСТЫЛЬ, УДАЛИТЬ ИЛИ ИЗМЕНИТЬ ДЛЯ ПРОДАКШН
				header('Location: /index.php?goto=' . $_SERVER['REQUEST_URI']);
				exit;
			}
			// проверяем уровень доступа. ВАЖНО, чтобы уровень страницы был указан ДО подключения конфига
			if (isset($access)) {
				if($_SESSION['pwr'] < $access) {
					$_SESSION['error'] = lang::ERR_NO_RIGHTS;
					header('Location: /user/dashboard.php');
					//include ($_SERVER['DOCUMENT_ROOT'].'/user/access_denied.php');
					exit; // нужно, чтобы не отображалась страница
				}
			} else {
				echo "<script>alert('Уровень доступа к странице не указан');</script>";
			}
		} 
	}
	
	// Устанавливаем значения фильтров
	if($_REQUEST['loc'] != '') 	$_SESSION['locationSelected'] 	= $_REQUEST['loc'];
	if($_REQUEST['month'] != '')	$_SESSION['monthSelected'] 	= $_REQUEST['month'];
	if($_REQUEST['brandID'] != '')	$_SESSION['brandID'] 		= $_REQUEST['brandID'];
	if($_REQUEST['purpose'] != '')	$_SESSION['purpose'] 		= $_REQUEST['purpose'];
			
?>
