<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$_SESSION['temp'] = array(
		'userID' 	=> $_POST['userID'],
		'date' 		=> $_POST['date'],
		'weekday' 	=> $_POST['weekday'],
		'even' 		=> $_POST['even'],
		'comment'	=> $_POST['comment']
	);
		
	if ($_POST['date'] == '' && $_POST['weekday'] == '' && $_POST['even'] == '') {
		$_SESSION['error'] = lang::ERR_SPECIFY_DAY;
		session_write_close();
		header('Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
	
	$sql = "INSERT INTO users_workdays (locationID, userID, date, weekday, even, comment, author) 
			VALUES(:locationID, :userID, :date, :weekday, :even, :comment, :author)";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':locationID', $_POST["loc"], PDO::PARAM_INT);
		$stmt -> bindValue(':userID', $_POST["userID"], PDO::PARAM_INT);
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
		$stmt ->execute();
		$_SESSION['success'] = lang::SUCCESS_GENERAL_ADD;
		unset($_SESSION['temp']);
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
		header('Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
	
	session_write_close();
	header( 'Location: /user/workdays_list.php');
	exit;
}

$title=location_names_only(setLocationID()). '. ' .lang::HDR_NEW_WORKDAY;
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 

echo '<section class="sidebar">';
	echo tabs($tabs);
echo '</section>';
echo '<section class="content">';
	echo '<style>	
		label {grid-row: 1 / 2;}
		input[name="date"] {grid-column: 1/2;}
		select[name="weekday"] {grid-column: 2/3;}
		select[name="even"] {grid-column: 3/4;}
	</style>';

	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'. $title .'</h2>';?>
	<form method="post">
		<fieldset>
			<div class="row col-2">
				<?php user_select(setLocationID(), $_SESSION['temp']['userID']);?>
			</div>
			<div class="row col-3">
				<label for="date"><?=lang::DATE;?>:</label>
				<input name="date" type="date" value="<?php echo $_SESSION['temp']['date']; ?>" />
			
				<?php echo weekday_select($_SESSION['temp']['weekday']); ?>
				<?php echo even_select($_SESSION['temp']['even']); ?>
			</div>	
			<div class="row">
				<textarea name="comment" placeholder="<?=lang::COMMENT_PLACEHOLDER;?>"><?=$_SESSION['temp']['comment']; ?></textarea>
			</div>
			<input name="loc" type="hidden" value="<?=setLocationID();?>" />
		</fieldset>
		<input type="submit" value="<?php echo lang::BTN_ADD; ?>" />
	</form>
</section>

<?php 
unset($_SESSION['temp']);
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	
?> 