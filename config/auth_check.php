<?php 
session_start();
include($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
include($_SERVER['DOCUMENT_ROOT'].'/config/lang_select.php');


try {
	$statement = $pdo->prepare("
		SELECT users.id, username, pass, users.name, users.surname, users.email, users.role,  locations.name as locationName, lang, users.archive,GROUP_CONCAT(DISTINCT locations.id) as locationIDs
		FROM users 
		LEFT JOIN users_locations ON users.id=users_locations.userID
		LEFT JOIN locations ON users_locations.locationID = locations.id
		WHERE username = :username
	");  
	$statement->execute(  
		 array(  
			  'username'     =>     $_POST["username"]
		 )  
	);  
	$count = $statement->rowCount();  
	if($count > 0) {
		$auth = $statement->fetch();
		
		if ($auth['archive'] == 1) {
				$_SESSION['alert'] = lang::ERR_BLOCKED;
				//Возврат к форме авторизации
				header('Location: /index.php');
				exit;
		}
		
		if(password_verify($_POST['pass'], $auth['pass'])) {
			session_regenerate_id();
			$_SESSION['loggedin'] = TRUE;
			$_SESSION['userID'] = $auth['id'];  
			$_SESSION['username'] = $_POST['username'];  
			$_SESSION['name'] = $auth['name']; 
			$_SESSION['surname'] = $auth['surname'];   			
			$_SESSION['role'] = $auth['role'];
			
			if ($auth['role'] == 'godmode') {
				$_SESSION['pwr'] = 99;
				$_SESSION['roleName'] = lang::LEVEL_GODMODE;
			} elseif ($auth['role'] == 'general') {
				$_SESSION['pwr'] = 90;
				$_SESSION['roleName'] = lang::LEVEL_GENERAL;
			} elseif ($auth['role'] == 'basic') {
				$_SESSION['pwr'] = 2;
				$_SESSION['roleName'] = lang::LEVEL_BASIC;
			} else {
				$_SESSION['pwr'] = 10;
				$_SESSION['roleName'] = lang::LEVEL_ADMIN;
			}
			$_SESSION['locationIDs'] = $auth['locationIDs'];
			if(!preg_match('/,/',$auth['locationIDs'])) { //проверка на наличие запятых
				$_SESSION['locationName'] = $auth['locationName'];
				$_SESSION['locationSelected'] = $auth['locationIDs'];
			}
			$_SESSION['lang'] = $auth['lang'];
			$_SESSION['attempt'] = null;
			
			// Подгружаем базовые настройки
			$q = $pdo->prepare("SELECT 
					name, country, currency, VAT, logoURL, favicon_URL 
				FROM settings 
				LIMIT 1");
			$q ->execute();
			$_SESSION['settings'] = $q->fetch(PDO::FETCH_ASSOC);
			
			if(isset($_POST['goto'])) {
				header("location: " . $_POST['goto']);  
			} else {
				header("location: /user/dashboard.php");  
			}
		} else {
			$_SESSION['alert'] = lang::ERR_PASS; 
			if(!isset($_SESSION['attempt'])) $_SESSION['attempt'] = 1;
		}
	} else {
		$_SESSION['alert'] = lang::ERR_NO_SUCH_USER;  
		if(!isset($_SESSION['attempt'])) $_SESSION['attempt'] = 1;
	}  
} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
$pdo=NULL;
?>