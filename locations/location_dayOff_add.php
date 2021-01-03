<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$_SESSION['temp'] = array(
		'date' 		=> $_POST['date'],
		'weekday' 	=> $_POST['weekday'],
		'comment'	=> $_POST['comment']
	);
		
	if ($_POST['date'] == '' && $_POST['weekday'] == '') {
		$_SESSION['error'] = lang::ERR_SPECIFY_DAY;
		session_write_close();
		header( 'Location: /locations/location_dayOff_add.php');
		exit;
	}
	
	$sql = "INSERT INTO locations_vacations (locationID, date, weekday, comment, author) 
			VALUES(:locationID, :date, :weekday, :comment, :author)";
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
		$stmt -> bindParam(':locationID', $locationID, PDO::PARAM_INT);
						
		
		foreach ($_POST["loc"] as $locationID) {
			$stmt ->execute();
		}
		$_SESSION['success'] = lang::SUCCESS_GENERAL_ADD;
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
	}
	
	if (isset($_SESSION['success'])) unset($_SESSION['temp']);
	
	session_write_close();
	header( 'Location: /locations/location_daysOff.php');
	exit;
}

$title=lang::HDR_NEW_DAY_OFF;
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, 'day_add');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'. lang::HDR_NEW_DAY_OFF .'</h2>';?>
	<form method="post">
		<fieldset>
			<?php echo location_options(); ?>
			<div class="row">
				<label for="date"><?=lang::DATE;?>*:</label>
				<input name="date" type="date" value="<?php echo $_SESSION['temp']['date']; ?>" />
			</div>
			<?php echo weekday_select(); ?>
			<div class="row">
				<textarea name="comment" placeholder="<?=lang::COMMENT_PLACEHOLDER;?>"><?=$_SESSION['temp']['comment']; ?></textarea>
			</div>
			
		</fieldset>
		<input type="submit" value="<?php echo lang::BTN_ADD; ?>" />
	</form>
</section>
<?php 
unset($_SESSION['temp']);
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	
?> 
<script src="dayOff_select_controller.js"></script>