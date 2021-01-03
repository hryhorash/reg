<?php
if (isset($_GET['id'])) {
	require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
	if(!isset($_GET['offset'])) 
			$offset = floor($_GET['pcsOut']);   //минус 0,0001 не нужен???? Вроде нет...
	else	$offset = ceil($_GET['pcsOut']+0.000000001);
	
	$sql = "SELECT (priceIn / qtyIn) as priceIn
		FROM received
		LEFT JOIN invoices ON received.invoiceID = invoices.id
		WHERE received.cosmID = :id 
			AND locationID = :locationID
			AND invoices.state >= 4
		LIMIT 1 OFFSET :offset";  
	
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
	$stmt->bindValue(':locationID', $_GET['locationID'], PDO::PARAM_INT);
	$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
	$stmt->execute();
	$row = $stmt->fetch();
	if ($row['priceIn'] != NULL) echo $row['priceIn'];
	else  echo 0;			
}

?>