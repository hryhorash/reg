<?php 
$access = 1;
include($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	$_SESSION['temp'] = array(
		'date' 				=> $_POST['date'],
		'startTime' 		=> $_POST['startTime'],
		'endTime' 			=> $_POST['endTime'],
		'comment' 			=> $_POST['comment'],
		'name' 				=> $_POST['name'],
		'surname' 			=> $_POST['surname'],
		'phone' 			=> $_POST['phone']
	);
	
	function findUser($phone) {
		require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
		
		$stmt = $pdo->prepare("SELECT id FROM `clients` WHERE phones LIKE '%$phone%'");
		$stmt->execute();
		$row = $stmt->fetch();
		if ($row['id'] != NULL) return $row['id'];
		else  return 0;			
	}
	
	
	if(strlen($_POST['phone']) == 12) {
		$clientID = findUser($_POST['phone']);
	} else if (strlen($_POST['phone']) == 10 && substr($_POST['phone'], 0, 1) == 0) {
		$clientID = findUser('38'.$_POST['phone']);
	} else {
		$_SESSION['error'] = lang::ERR_PHONE;
		header( 'Location: /site/visit.php?date='.$_POST['date'].'&timeFrom='.$_POST['startTime']);
		exit;
	}
	
	/*$cat_name = array();
	if($_POST['specialty'] !='') {
		foreach($_POST['specialty'] as $id) {
			$cat_name[] = $_SESSION['categories'][$id];
		}
	}
	$works = implode(', ', $cat_name);*/
		
	
	$comment = '';
	if($clientID > 0) {
		if ($_POST['surname'] !='') $comment = "Фамилия: " . $_POST['surname'] . "\n";
	} else {
		$comment = "Имя: " . $_POST['name'];
		if ($_POST['surname'] !='') $comment = $comment . "\n" . "Фамилия: " . $_POST['surname'];
		$comment = $comment . "\n" . $_POST['phone'];	
		
	}
	//if ($works !='') $comment = $comment . "\n" . "Работы: " . $works;
	if ($_POST['comment'] !='') $comment = $comment . "\n" . $_POST['comment'];
	
	
	try {
		$visitNew = $pdo->prepare("
				INSERT INTO visits (locationID, date, startTime, endTime, clientID, state, price_total, comment,  author) 
				VALUES(1, :date, :startTime, :endTime, :clientID, 2, 0, :comment, 0)");
		$visitNew -> bindValue(':date', 		$_POST["date"], PDO::PARAM_STR);
		$visitNew -> bindValue(':startTime', 	$_POST["startTime"], PDO::PARAM_STR);
		$visitNew -> bindValue(':endTime', 		$_POST["endTime"], PDO::PARAM_STR);
		$visitNew -> bindValue(':clientID', 	$clientID, PDO::PARAM_INT);
		$visitNew -> bindValue(':comment', 		$comment, PDO::PARAM_STR);
		$visitNew -> execute();
		$visitID = $pdo->lastInsertId();
			
		
		
		
		
		$title = 'Новая заявка на ' . correctDate($_POST['date']) . '.';
		$link = 'http://registry.style.kiev.ua/visits/visit_details.php?id='.$visitID . '&goto=dashboard';
	
		/*$msg = "Имя: " .$_POST['name'] . " \n " . 
				$_POST['phone']  ." \n ". 
				$works  ." \n ". 
				$_POST['comment']." \n ".
				$link;*/
		
		$msg = $comment ." \n ". $link;
		
		
		//сообщение в бота BotNote
		
		$url = 'https://api.pipe.bot/push';
		$data = array(
					"apikey" 		=> "apikey", 
					"Content-type"  => "application/json",
					"segment"		=> "?isAdmin=1",
					"text"			=> $title . "\n". $msg
					);

		// use key 'http' even if you send the request to https://...
		$options = array(
			'http' => array(
				'method'  => 'POST',
				'content' => http_build_query($data)
			)
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		if ($result === FALSE) { /* Handle error */ }

		//var_dump($result);
		$_SESSION['success'] = lang::SUCCESS_NEW_VISIT;
		
		header( 'Location: /site/calendar_site.php?date='.$_POST['date'].'&lang='.$_SESSION['lang']);
		exit;
		
		
		
		
		
		
		
		
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	
	
	
	
	

}



if($_GET['date'] !='')	$_SESSION['temp']['date'] = $_GET['date'];
if($_GET['timeFrom'] !='') {
	$_SESSION['temp']['startTime'] = $_GET['timeFrom'];	
	$start = explode(":", $_GET['timeFrom']);
	$_SESSION['temp']['endTime'] = get_std_end_time($_GET['timeFrom']);
}

	
	
$title=lang::TITLE_CLIENT_VISIT; 
//---------------------VIEW--------------------------------------?>
<!DOCTYPE html><html lang="ru-UA">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/layout/css/main.css" type="text/css" media="screen" />
</head>
<body style="background-color:white;">
	<?php include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');?>
	<h2><?=$title;?></h2>
	
	<form method="post" id="form" name="client_visit" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
		<fieldset>
			<div class="row nested">
				<label for="name" style="flex:7.8;"><?=lang::HDR_YOUR_NAME;?>*:</label>
				<input name="name" type="text" placeholder="<?=lang::NAME;?>" value="<?=$_SESSION['temp']['name'];?>" required />
				<input name="surname" type="text" placeholder="<?=lang::SURNAME;?>" value="<?=$_SESSION['temp']['surname'];?>" />
			</div>
			<div class="row">
				<label for="phone"><?=lang::HDR_PHONE;?>*:</label>
				<input name="phone" type="number" placeholder="<?=lang::PHONE_PLACEHOLDER_PATTERN;?>" pattern="[0-9]{12}" value="<?=$_SESSION['temp']['phone'];?>" required />
			</div>
			
			<div class="row">
				<label for="date"><?=lang::DATE;?>*:</label>
				<input name="date" type="date" value="<?=$_SESSION['temp']['date'];?>" required />
			</div>
			<div class="row nested">
				<label for="startTime" style="flex: 8.3;"><?=lang::HDR_TIME;?>*:</label>
				<select name="startTime" required />
					<?=time_options($_SESSION['temp']['startTime'], 1);?>
				</select>
				<select name="endTime" style="margin-left: 10px;" required />
					<?=time_options($_SESSION['temp']['endTime'], 1, $start[0]);?>
				</select>
			</div>
			
			
			<div class="row">
				<textarea name="comment" placeholder="<?=lang::HDR_COMMENT;?>"><?=$_SESSION['temp']['comment'];?></textarea>
			</div>
			<!--input type="hidden" name="loc" value="1">
			<input type="hidden" name="state" value="2">
			<input type="hidden" name="price_total" value="1">
			<input type="hidden" name="author" value="0"-->
				
	
		</fieldset>	
	</form>
	<button type="submit" form="form" value="Submit" style="margin-top: 10px;"><?=lang::BTN_CLIENT_VISIT;?></button>
</body>
</html>