<?php $access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
if (isset($_GET['category'])) $catID = $_GET['category'];
$stmt = $pdo->prepare("SELECT id, subcategory FROM `expences_subcat` WHERE catID=:catID AND archive=0 ORDER BY subcategory");
$stmt->bindValue('catID', $catID, PDO::PARAM_INT);
$stmt->execute();

echo '<option value="">'.lang::SELECT_DEFAULT.'</option>';
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	echo '<option value="' . $row['id'] . '" ';
	if(isset($_GET['subcategory']) && $_GET['subcategory'] == $row["id"]) echo 'selected';
	echo '>' . $row['subcategory'] . '</option>';
}?>