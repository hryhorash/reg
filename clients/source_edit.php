<?php 
$access = 90;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$sql = "UPDATE sources 
			SET name		= :name,
				`timestamp`	= :timestamp, 
				author		= :author
			WHERE id=:id";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':name', $_POST["name"], PDO::PARAM_STR);
		$stmt -> bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt -> bindValue(':id', $_POST['id'], PDO::PARAM_INT);
		$stmt ->execute();
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL;
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
	}
	session_write_close();
	header( 'Location: /clients/source_list.php?tab='.$_GET['tab']);
	exit;
}

if($_GET['id'] !=''){
	try {
		$stmt = $pdo->prepare("SELECT name FROM `sources` WHERE id=:id");
		$stmt -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
		$stmt ->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}

	
	//проверить права на редактирование воизбежание простого перебора id-шками
	if (handle_rights('user', $_GET['id']) != 1) 
	{		
		$_SESSION['error'] = lang::ERR_NO_RIGHTS;
		session_write_close();
		header( 'Location: /clients/source_list.php?tab='.$_GET['tab']);
		exit;
	}
	
} else {
	$_SESSION['error'] = lang::ERR_NO_ID;
	session_write_close();
	header( 'Location: /clients/source_list.php?tab='.$_GET['tab']);
	exit;
}

$title=$data['name'];
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	include('filters.php');
	echo tabs($tabs);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'. $data['name'] .'</h2>';?>
	
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']. '?tab=active'; ?>">
		<fieldset>
			<div class="row">
				<label for="name"><?=lang::HDR_ITEM_NAME;?>*:</label>
				<input name="name" type="text" value="<?=$data['name']; ?>" required />
			</div>
			<input name="id" type="hidden" value="<?=$_GET['id'];?>" />
		</fieldset>
		<input type="submit" value="<?php echo lang::BTN_CHANGE; ?>" />
	</form>
</section>
<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	?> 