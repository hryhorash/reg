<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$_SESSION['temp'] = array(
		'name' 			=> $_POST['name']	
	);
	
	$sql = "INSERT INTO brands (name, author) 
			VALUES(:name, :author)";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':name', $_POST["name"], PDO::PARAM_STR);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt ->execute();
		$brandID = $pdo->lastInsertId();
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL_ADD;
		unset($_SESSION['temp']);
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
		session_write_close();
		
		if ($_POST['backTo'] == 'cosmetics_add') {
			header( 'Location: /cosmetics/cosmetics_add.php?brandID=' . $brandID);
			exit;
		}
		
		header( 'Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
	
	session_write_close();
	if ($_POST['backTo'] == 'cosmetics_add') {
		header( 'Location: /cosmetics/cosmetics_add.php?brandID=' . $brandID);
		exit;
	}
	header( 'Location: /cosmetics/brand_list.php');
	exit;
}

$title=lang::H2_NEW_BRAND;
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, 'brd_add');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'. lang::H2_NEW_BRAND .'</h2>';?>
	<form method="post">
		<fieldset class="noBorders noPadding">
			<input name="name" type="text" placeholder="<?=lang::HDR_ITEM_NAME;?>" value="<?php echo $_SESSION['temp']['name']; ?>" required autofocus />
		</fieldset>
		<input name="backTo" type="hidden" value="<?=$_GET['backTo'];?>">
		<input type="submit" value="<?php echo lang::BTN_ADD; ?>" />
	</form>
</section>

<?php 
unset($_SESSION['temp']);
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	
?> 