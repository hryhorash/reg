<?php $access = 90;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

$soldID = $_GET["id"];

$sql = "UPDATE received 
    SET dateOut		= NULL, 
        priceOut	= NULL, 
        qtyOut		= 0, 
        soldToID	= NULL, 
        `timestamp`	= :timestamp, 
        author		= :author
    WHERE id=:id";

try {
    $stmt = $pdo->prepare($sql);
    $stmt -> bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
    $stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
    $stmt -> bindValue(':id', $_GET["id"], PDO::PARAM_INT);
    $stmt -> execute();
    
} catch (PDOException $ex) {
    include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
    $_SESSION['error'] = $ex;
    session_write_close();
    
}
	
