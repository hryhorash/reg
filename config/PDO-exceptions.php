<?php
//date.timezone = Europe/Kiev;
file_put_contents($_SERVER['DOCUMENT_ROOT'].'/logs/pdoErrors.txt', date('d.m.Y h:i:s') . ' ' . $ex->getMessage() . PHP_EOL, FILE_APPEND);
$_SESSION['error'] = $ex;
?>