<?php 
$access = 90;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$sql = "UPDATE locations 
			SET city=:city,
				name=:name,
				openFrom=:openFrom,
				openTill=:openTill,
				author=:author
			WHERE id=:id";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':city', $_POST["city"], PDO::PARAM_STR);
		$stmt -> bindValue(':name', $_POST["name"], PDO::PARAM_STR);
		$stmt -> bindValue(':openFrom', $_POST["openFrom"], PDO::PARAM_INT);
		$stmt -> bindValue(':openTill', $_POST["openTill"], PDO::PARAM_INT);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt -> bindValue(':id', $_POST['id'], PDO::PARAM_INT);
		$stmt ->execute();
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL;
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
	}
	session_write_close();
	header( 'Location: /locations/list.php?tab='.$_GET['tab']);
	exit;
}

if($_GET['id'] !=''){
	try {
		$stmt = $pdo->prepare("SELECT city,name,openFrom,openTill FROM `locations` WHERE id=:id");
		$stmt -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
		$stmt ->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}

	
	//проверить права на редактирование воизбежание простого перебора id-шками
	if (handle_rights('user', $_GET['id']) != 1) 
	{		
		$_SESSION['error'] = lang::ERR_NO_RIGHTS;
		session_write_close();
		header( 'Location: /locations/list.php?tab='.$_GET['tab']);
		exit;
	}
	
} else {
	$_SESSION['error'] = lang::ERR_NO_ID;
	session_write_close();
	header( 'Location: /locations/list.php?tab='.$_GET['tab']);
	exit;
}

$title=$data['name'];
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'. $data['name'] .'</h2>';?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']. '?tab=active'; ?>">
		<fieldset>
			<div class="row">
				<label for="city"><?=lang::HDR_CITY;?>*:</label>
				<input name="city" type="text" value="<?=$data['city']; ?>" required />
			</div>
			<div class="row">
				<label for="name"><?=lang::HDR_LOCATION;?>*:</label>
				<input name="name" type="text" value="<?=$data['name']; ?>" required />
			</div>
			<div class="row">
				<label for="openFrom"><?=lang::HDR_OPERATING_HOURS . lang::HDR_OPEN_FROM;?>*:</label>
				<select name="openFrom" required>
					<?=operating_hours($data['openFrom']);?>
				</select>
			</div>
			<div class="row">
				<label for="openTill"><?=lang::HDR_OPERATING_HOURS . lang::HDR_OPEN_TILL;?>*:</label>
				<select name="openTill" required>
					<?=operating_hours($data['openTill']);?>
				</select>
			</div>
			<input name="id" type="hidden" value="<?=$_GET['id'];?>" />
		</fieldset>
		<input type="submit" value="<?php echo lang::BTN_CHANGE; ?>" />
	</form>
</section>

<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	
?> 
