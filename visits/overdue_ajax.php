<?php $access = 10;
include($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

if ($_POST['action'] == 'approve') $state = 10;
else $state = 9;
	
$sql = 'UPDATE `visits`
		SET state = :state,
			author = :author,
			`timestamp` = :timestamp
		WHERE id = :id';
		
try {
	$stmt = $pdo->prepare($sql);
	$stmt -> bindValue(':state', $state, PDO::PARAM_INT);
	$stmt -> bindValue(':id', $_POST['id'], PDO::PARAM_INT);
	$stmt -> bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
	$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
	$stmt -> execute();
} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}


//include($_SERVER['DOCUMENT_ROOT'].'/visits/overdue.php');

?>