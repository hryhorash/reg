<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	$new_cost = $_POST["volume"] * $_POST["old_cost"] / $_POST["old_volume"];
		
	
	$sql = "UPDATE spent 
		SET volume		= :volume, 
			cost		= :cost, 
			`timestamp`	= :timestamp, 
			author		= :author 
		WHERE id=:id";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':volume', $_POST["volume"], PDO::PARAM_INT);
		$stmt -> bindValue(':cost', $new_cost, PDO::PARAM_STR);
		$stmt -> bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt -> bindValue(':id', $_POST["id"], PDO::PARAM_INT);
		$stmt ->execute();
		
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
		session_write_close();
		header( 'Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
	session_write_close();
	
	header( 'Location: /cosmetics/history.php?cosmID='.$_POST["cosmID"]);
	exit;
}

if($_GET['id'] !=''){
	try {
		$stmt = $pdo->prepare("SELECT spent.id, spent.volume, cost, cosmID
								, cosmetics.name
							FROM `spent` 
							LEFT JOIN cosmetics ON spent.cosmID = cosmetics.id
							WHERE spent.id=:id");
		$stmt -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
		$stmt ->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	
} else {
	$_SESSION['error'] = lang::ERR_NO_ID;
	session_write_close();
	header( 'Location: /cosmetics/cosmetics_list.php?brandID='.$_POST["brandID"]);
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
				<label for="volume"><?=lang::H2_HISTORY_WORK;?>*:</label>
				<input name="volume" type="number" min="0" placeholder="<?php echo $data['volume']; ?>" required autofocus />
			</div>
			<input name="old_cost" type="hidden" value="<?=$data['cost'];?>" />
			<input name="old_volume" type="hidden" value="<?=$data['volume'];?>" />
			<input name="cosmID" type="hidden" value="<?=$data['cosmID'];?>" />
			<input name="id" type="hidden" value="<?=$_GET['id'];?>" />
		</fieldset>
		<input type="submit" value="<?php echo lang::BTN_CHANGE; ?>" />
	</form>
</section>


<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	?> 
