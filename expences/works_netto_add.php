<?php
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	$_SESSION['temp'] = array(
		'name' 		=> $_POST['name'],
		'cost' 		=> $_POST['cost']
	);
	
	
	$sql = "INSERT INTO service_netto (name, cost, author)
	VALUES (:name, :cost, :author)";
	
	
	try {
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':name', $_POST["name"], PDO::PARAM_STR); 
		$stmt->bindValue(':cost', $_POST['cost'], PDO::PARAM_STR); 
		$stmt->bindValue(':author', $_SESSION["userID"], PDO::PARAM_INT); 
		$stmt->execute();
		$_SESSION['success'] = lang::SUCCESS_GENERAL_ADD;
		unset($_SESSION['temp']);
		session_write_close();
		header( 'Location: /expences/works_netto_list.php');
		exit;
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = lang::ERR_GENERAL;
	}
}

$pdo = NULL;
$title = lang::HDR_ADD_SERVICE_NETTO;
//----------------------------VIEW-------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, $pageID);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>' . lang::HDR_ADD_SERVICE_NETTO . '</h2>';?>

	<form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
		<fieldset>
			<div class="row">
				<label for="name"><?=lang::HDR_ITEM_NAME;?>*:</label>
				<input name="name" type="text" value="<?=$_SESSION['temp']['name'];?>" required />
			</div>
			<div class="row">
				<label for="cost"><?=lang::HDR_COST;?>*:</label>
				<input name="cost" type="number" step="any" min="0" value="<?=$_SESSION['temp']['cost'];?>" required />
			</div>
		</fieldset>

		<input id="button" type="submit" value="<?=lang::BTN_ADD;?>" />
		
	</form>
</section>


<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');
	unset($_SESSION['temp']);
 ?>

