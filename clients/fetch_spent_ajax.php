<?php $access = 2;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
if($_POST['id'] > 0) {
$sql="SELECT spent.id as spentID, spent.volume as spentV, spent.cost as spentC, cosmID, CONCAT(brands.name, ' ', cosmetics.name) as cosmNames
		FROM `spent` 
		LEFT JOIN cosmetics ON spent.cosmID=cosmetics.id
		LEFT JOIN brands ON cosmetics.brandID=brands.id
		WHERE visitID=:id
		ORDER BY spent.id
		";
	$stmt = $pdo->prepare($sql);
	$stmt -> bindValue(':id', $_POST['id'], PDO::PARAM_INT);
	$stmt ->execute();
	echo '<p class="title">' . lang::HDR_FORMULA . '</p>';
	$count = 0;
	while($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC)) {
		echo '<p>' . $data[$count]['cosmNames'] . ' - ' . $data[$count]['spentV'] . ' ('.$data[$count]['spentC'] . curr() .')</p>';
		$count++;
	}
	
	
	
	
}
?>