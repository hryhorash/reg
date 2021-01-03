<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if ($_POST['date'] == '' && $_POST['weekday'] == '' && $_POST['even'] == '') {
		$_SESSION['error'] = lang::ERR_SPECIFY_DAY;
		session_write_close();
		header('Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
	
	$sql = "UPDATE users_workdays 
			SET	date		= :date, 
				weekday		= :weekday, 
				even		= :even, 
				comment		= :comment, 
				author		= :author 
			WHERE id = :id";
	try {
		$stmt = $pdo->prepare($sql);
		if($_POST["date"] != '')
			 $stmt -> bindValue(':date', $_POST["date"], PDO::PARAM_STR);
		else $stmt -> bindValue(':date', null, PDO::PARAM_STR);
		if($_POST["weekday"] != '')
			 $stmt -> bindValue(':weekday', $_POST["weekday"], PDO::PARAM_INT);
		else $stmt -> bindValue(':weekday', null, PDO::PARAM_INT);
		if($_POST["even"] != '')
			 $stmt -> bindValue(':even', $_POST["even"], PDO::PARAM_INT);
		else $stmt -> bindValue(':even', null, PDO::PARAM_INT);
		$stmt -> bindValue(':comment', $_POST["comment"], PDO::PARAM_STR);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt -> bindValue(':id', $_POST["id"], PDO::PARAM_INT);
		$stmt ->execute();
		$_SESSION['success'] = lang::SUCCESS_GENERAL;
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
		header('Location: ' . $_SERVER['PHP_SELF'] . '?id='.$_POST['id']);
		exit;
	}
	
	session_write_close();
	header( 'Location: /user/workdays_list.php');
	exit;
}

if($_GET['id'] !=''){
	try {
		$stmt = $pdo->prepare("SELECT DISTINCT users_workdays.id, date, weekday, even,
			  CONCAT (users.name, ' ', users.surname) AS user, locations.name as location, locations.id as locationID
			  FROM `users_workdays`
			  LEFT JOIN users ON users_workdays.userID = users.id
			  LEFT JOIN users_locations ON users.id = users_locations.userID
			  LEFT JOIN locations ON users_locations.locationID = locations.id
			  WHERE users_workdays.id = :id
		 	  LIMIT 1");
		$stmt -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
		$stmt ->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}

	
	//проверить права на редактирование воизбежание простого перебора id-шками
	if (handle_rights('user', $data['locationID']) != 1) 
	{		
		$_SESSION['error'] = lang::ERR_NO_RIGHTS;
		session_write_close();
		header( 'Location: /user/workdays_list.php');
		exit;
	}	
} else {
	$_SESSION['error'] = lang::ERR_NO_ID;
	session_write_close();
	header( 'Location: /user/workdays_list.php');
	exit;
}

$title=$data['location']. '. ' . $data['user'];
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs);
echo '</section>';
echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'. $title .'</h2>';?>
	<form method="post">
		<fieldset>
			<div class="row">
				<label for="date"><?=lang::DATE;?>:</label>
				<input name="date" type="date" value="<?php echo $data['date']; ?>" />
			</div>
			<?php echo weekday_select($data['weekday']); ?>
			<?php echo even_select($data['even']); ?>
			<div class="row">
				<textarea name="comment" placeholder="<?=lang::COMMENT_PLACEHOLDER;?>"><?=$data['comment']; ?></textarea>
			</div>
			<input name="loc" type="hidden" value="<?=$data['locationID'];?>" />
			<input name="id" type="hidden" value="<?=$_GET['id'];?>" />
		</fieldset>
		<input type="submit" value="<?php echo lang::BTN_CHANGE; ?>" />
	</form>
</section>
<script src="/user/workday_select_controller.js"></script>
<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	
?> 
