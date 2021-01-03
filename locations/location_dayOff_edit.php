<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if ($_POST['date'] == '' && $_POST['weekday'] == '') {
		$_SESSION['error'] = lang::ERR_SPECIFY_DAY;
		session_write_close();
		header( 'Location: /locations/location_dayOff_add.php');
		exit;
	}
	
	$sql = "UPDATE locations_vacations 
			SET	locationID	= :locationID, 
				date		= :date, 
				weekday		= :weekday, 
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
		else $stmt -> bindValue(':weekday', null, PDO::PARAM_STR);
		$stmt -> bindValue(':comment', $_POST["comment"], PDO::PARAM_STR);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt -> bindValue(':id', $_POST["id"], PDO::PARAM_INT);	
		$stmt -> bindParam(':locationID', $_POST["loc"], PDO::PARAM_INT);
		$stmt ->execute();
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL;
	} catch (PDOException $ex){
				include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
				$_SESSION['error'] = $ex;
			}
	
	session_write_close();
	header( 'Location: /locations/location_daysOff.php');
	exit;
}

if($_GET['id'] !=''){
	try {
		$stmt = $pdo->prepare("SELECT locations_vacations.date, locations_vacations.weekday, locations_vacations.comment, locations.name, locationID 
			FROM `locations_vacations` 
			LEFT JOIN locations ON locations_vacations.locationID = locations.id
			WHERE locations_vacations.id = :id");
		$stmt -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
		$stmt ->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}

	
	//проверить права на редактирование воизбежание простого перебора id-шками
	if (handle_rights('user', $data['locationID']) != 1) 
	{		
		$_SESSION['error'] = lang::ERR_NO_RIGHTS;
		session_write_close();
		header( 'Location: /locations/location_daysOff.php');
		exit;
	}	
} else {
	$_SESSION['error'] = lang::ERR_NO_ID;
	session_write_close();
	header( 'Location: /locations/location_daysOff.php');
	exit;
}

$title=$data['name'] . '. ' . lang::HDR_DAY_OFF;
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
				<label for="date"><?=lang::DATE;?>*:</label>
				<input name="date" type="date" value="<?php echo $data['date']; ?>" />
			</div>
			<?php echo weekday_select($data['weekday']); ?>
			<div class="row">
				<textarea name="comment" placeholder="<?=lang::COMMENT_PLACEHOLDER;?>"><?=$data['comment']; ?></textarea>
			</div>
			<input name="id" type="hidden" value="<?=$_GET['id'];?>" />
			<input name="loc" type="hidden" value="<?=$data['locationID'];?>" />
		</fieldset>
		<input type="submit" value="<?php echo lang::BTN_CHANGE; ?>" />
	</form>
</section>

<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	
?> 
<script src="dayOff_select_controller.js"></script>