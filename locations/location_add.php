<?php 
$access = 90;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$_SESSION['temp'] = array(
		'city' 		=> $_POST['city'],
		'name' 		=> $_POST['name'],
		'openFrom'	=> $_POST['openFrom'],
		'openTill' => $_POST['openTill']
	);
		
	
	$sql = "INSERT INTO locations (city, name, openFrom, openTill, author) 
			VALUES(:city, :name, :openFrom, :openTill, :author)";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':city', $_POST["city"], PDO::PARAM_STR);
		$stmt -> bindValue(':name', $_POST["name"], PDO::PARAM_STR);
		$stmt -> bindValue(':openFrom', $_POST["openFrom"], PDO::PARAM_INT);
		$stmt -> bindValue(':openTill', $_POST["openTill"], PDO::PARAM_INT);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt ->execute();
		
		if ($_SESSION['role'] !='godmode') {
			$LAST_ID = $pdo->lastInsertId();
			
			
			$_SESSION['locationIDs'] = $_SESSION['locationIDs'] . ',' . $LAST_ID;
			
			$stmt2 = $pdo->prepare("UPDATE users SET locationIDs = :loc WHERE id=:id");
			$stmt2 -> bindValue(':loc', $_SESSION['locationIDs'], PDO::PARAM_STR);
			$stmt2 -> bindValue(':id', $_SESSION['userID'], PDO::PARAM_INT);
			$stmt2 ->execute();
		}
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL_ADD;
		unset($_SESSION['temp']);
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
	}
	session_write_close();
	header( 'Location: /locations/list.php?tab=active');
	exit;
}

$title=lang::HDR_NEW_LOCATION;
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, 'loc_add');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'. lang::HDR_NEW_LOCATION .'</h2>';?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']. '?tab=active'; ?>">
		<fieldset>
			<div class="row">
				<label for="city"><?=lang::HDR_CITY;?>*:</label>
				<input name="city" type="text" value="<?php echo $_SESSION['temp']['city']; ?>" required />
			</div>
			<div class="row">
				<label for="name"><?=lang::HDR_LOCATION;?>*:</label>
				<input name="name" type="text" value="<?php echo $_SESSION['temp']['name']; ?>" required />
			</div>
			<div class="row">
				<label for="openFrom"><?=lang::HDR_OPERATING_HOURS . lang::HDR_OPEN_FROM;?>*:</label>
				<select name="openFrom" required>
					<?=operating_hours($_SESSION['temp']['openFrom']);?>
				</select>
			</div>
			<div class="row">
				<label for="openTill"><?=lang::HDR_OPERATING_HOURS . lang::HDR_OPEN_TILL;?>*:</label>
				<select name="openTill" required>
					<?=operating_hours($_SESSION['temp']['openTill']);?>
				</select>
			</div>
			
		</fieldset>
		<input type="submit" value="<?php echo lang::BTN_ADD; ?>" />
	</form>
</section>

<?php 
unset($_SESSION['temp']);
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	
?> 
