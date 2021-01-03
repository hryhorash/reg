<?php$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');if (isset($_GET['supplierID'])) $supplierID = $_GET['supplierID'];
$stmt = $pdo->prepare("SELECT VAT FROM suppliers WHERE id=:id");
$stmt->bindValue(':id', $supplierID, PDO::PARAM_INT);$stmt->execute();$row = $stmt->fetch(PDO::FETCH_ASSOC);echo $row['VAT']; ?>