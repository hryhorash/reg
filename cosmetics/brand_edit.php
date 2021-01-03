<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	$sql = "UPDATE brands 
		SET name		= :name, 
			`timestamp`	= :timestamp,
			author		= :author 
		WHERE id=:id";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':name', $_POST["name"], PDO::PARAM_STR);
		$stmt -> bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt -> bindValue(':id', $_POST["id"], PDO::PARAM_INT);
		$stmt ->execute();
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL;
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
		session_write_close();
		header( 'Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
	session_write_close();
	header( 'Location: /cosmetics/brand_list.php');
	exit;
}

if($_GET['id'] !=''){
	try {
		$stmt = $pdo->prepare("SELECT name FROM `brands` WHERE id=:id");
		$stmt -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
		$stmt ->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	
} else {
	$_SESSION['error'] = lang::ERR_NO_ID;
	session_write_close();
	header( 'Location: /cosmetics/brand_list.php');
	exit;
}

$title=$data['name'];
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, $pageID);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'. $data['name'] .'</h2>';?>
	
	<form method="post">
		<fieldset>
			<div class="row">
				<label for="name"><?=lang::HDR_ITEM_NAME;?>*:</label>
				<input name="name" type="text" value="<?php echo $data['name']; ?>" required />
			</div>
			<input name="id" type="hidden" value="<?=$_GET['id'];?>" />
		</fieldset>
		<input type="submit" value="<?php echo lang::BTN_CHANGE; ?>" />
	</form>
</section>

<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	
?> 
