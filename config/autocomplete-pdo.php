<?php
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':key', $key, PDO::PARAM_STR);
$stmt->execute();
$reply = array();
$reply['query'] = $type_in;
$reply['suggestions'] = array();
?>