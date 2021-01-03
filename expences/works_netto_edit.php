<?php
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	try {
		$stmt = $pdo->prepare("UPDATE service_netto 
			SET name		= :name, 
				cost		= :cost, 
				`timestamp`	= null, 
				author		= :author
			WHERE id = :id");
		$stmt->bindValue(':name', $_POST["name"], PDO::PARAM_STR); 
		$stmt->bindValue(':cost', $_POST["cost"], PDO::PARAM_STR); 
		$stmt->bindValue(':author', $_SESSION["userID"], PDO::PARAM_INT); 
		$stmt->bindValue(':id', $_POST["id"], PDO::PARAM_INT); 
		$stmt ->execute();
		$_SESSION['success'] = lang::SUCCESS_GENERAL;
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = lang::ERR_GENERAL;
	}
	session_write_close();
	header( 'Location: /expences/works_netto_list.php');
	exit;
}

if($_GET['id'] !=''){
	try {
		$stmt = $pdo->prepare("SELECT service_netto.id, service_netto.name, service_netto.cost
		FROM `service_netto` 
			WHERE id=:id");
		$stmt -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
		$stmt ->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}

	
	//проверить права на редактирование воизбежание простого перебора id-шками
	if (handle_rights('basic', $data['locationID']) != 1) 
	{		
		$_SESSION['error'] = lang::ERR_NO_RIGHTS;
		session_write_close();
		header( 'Location: /expences/expencesList.php');
		exit;
	}
	
} else {
	$_SESSION['error'] = lang::ERR_NO_ID;
	session_write_close();
	header( 'Location: /expences/works_netto_list.php');
	exit;
}

$pdo = NULL;
$title = $data['name'];
//----------------------------VIEW-------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>' . $data['name'] . '</h2>';?>


	<form method="post">
		<fieldset>
			<div class="row">
				<label for="name"><?=lang::HDR_ITEM_NAME;?>*:</label>
				<input name="name" type="text" value="<?=$data['name'];?>" required />
			</div>
			<div class="row">
				<label for="cost"><?=lang::HDR_COST;?>*:</label>
				<input name="cost" type="number" min="0" step="any" value="<?=$data['cost'];?>" required />
			</div>
		</fieldset>
		<input name="id" type="hidden" value="<?=$data['id'];?>" />

		<input id="button" type="submit" value="<?=lang::BTN_CHANGE;?>" />
		
	</form>
</section>


<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');
?>

