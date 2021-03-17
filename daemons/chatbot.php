<?php
	$url = 'https://api.pipe.bot/push';

	
	$raw = '{
		"apikey" : "d39f911447cfdbd4787579750ee4f910",
		"Content-Type" : "application/json",
		"segment" : "?sys.uid=51576",
		"text" : "Привет из программки! \nЗдорово, да?"
	}';



// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'method'  => 'POST',
        'content' => $raw
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) { /* Handle error */ }

var_dump($result);
?>