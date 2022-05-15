<?php $access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
if (isset($_GET['category'])) $catID = $_GET['category'];
$stmt = $pdo->prepare("SELECT expences_works.worktypeCatID as selected
						FROM `worktype_cat` 
						LEFT JOIN expences_works ON expences_works.worktypeCatID = worktype_cat.id
						WHERE expences_works.expencesCatID=:catID
						ORDER BY category");
$stmt->bindValue('catID', $catID, PDO::PARAM_INT);
$stmt->execute();

while($row = $stmt->fetch()) {
	$reply[] = $row['selected'];
	
} 
echo json_encode($reply);
/*  $rows = $stmt->fetchAll();
echo json_encode($rows);  */
?>