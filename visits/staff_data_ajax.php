<?php
if (isset($_GET['catID']) && isset($_GET['locationID'])) {
	require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
	require($_SERVER['DOCUMENT_ROOT'].'/config/functions.php');
	
	
	try {
		$stmt = $pdo->prepare("SELECT DISTINCT users.id,
			  CONCAT (users.name, ' ', users.surname) AS user
			  FROM `users`
			  LEFT JOIN users_specialty ON users.id = users_specialty.userID
              LEFT JOIN users_locations ON users.id = users_locations.userID
			  WHERE locationID = :locationID
              	AND specialtyID = :catID
				AND users.archive = 0
			  ORDER BY user");
		$stmt -> bindParam(':locationID', $_GET['locationID'], PDO::PARAM_INT);
		$stmt -> bindParam(':catID', $_GET['catID'], PDO::PARAM_INT);
		$stmt->execute();
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	
	
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			echo '<option value="'.$row['id'].'"';
				if($_GET['lastSelected'] == $row['id']) echo ' selected';
			echo ' >' . $row['user'] . '</option>';
		}
			
}

?>