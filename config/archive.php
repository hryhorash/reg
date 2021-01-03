<?php 

/* Переменные
 $table (text);
 $state (archive/restore) (INT)
 $goto (URL)
*/
$table = $_GET['table'];
switch($table)
{
	case 'locations':
		$access = 90;
		break;
		
	default:
		$access = 10;
		break;
}

include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

if($_GET['id'] == '')
{
	$_SESSION['error'] = lang::ERR_NO_ID;
	session_write_close();
	header( 'Location: '. $_GET['URL'] . '?tab='.$_GET['tab']);
	exit;
}

if ($access <= $_SESSION['pwr'])
{
	$sql = "UPDATE $table
			SET archive = :archive,
				`timestamp` = :timestamp,
				author = :author
			WHERE id=:id";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt ->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
		$stmt ->bindParam(':archive', $_GET['archive'], PDO::PARAM_INT);
		$stmt ->bindParam(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
		$stmt ->bindParam(':author', $_SESSION['userID'], PDO::PARAM_INT);
		if ($stmt->execute() === TRUE) $_SESSION['success'] = lang::SUCCESS_GENERAL;
		else $_SESSION['error'] = lang::ERR_GENERAL;
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	$pdo=NULL;
	
	if ($table == 'cosmetics' && $_GET['brandID'] !='') {
		header( 'Location: '. $_GET['URL'] . '?tab='.$_GET['tab'] . '&brandID='.$_GET['brandID']);
		exit;
	}
	
	header( 'Location: '. $_GET['URL'] . '?tab='.$_GET['tab']);
	exit;
} else 
{
	$_SESSION['error'] = lang::ERR_NO_RIGHTS;
	session_write_close();
	header( 'Location: '. $_GET['URL'] . '?tab='.$_GET['tab']);
	exit;
}
 ?>