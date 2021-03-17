<?php
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	$_SESSION['temp'] = array(
		'category' 		=> $_POST['category'],
		'subcatID' 		=> $_POST['subcatID'],
		'loc' 			=> $_POST['loc'],
		'date' 			=> $_POST['date'],
		'item'			=> $_POST['item'],
		'price' 		=> $_POST['price'],
		'comment' 		=> $_POST['comment']
	);
	
	
	$sql = "INSERT INTO expences (date, locationID, subcatID, item, price, comment, author)
	VALUES (:date, :locationID, :subcatID, :item, :price, :comment, :author)";
	
	
	try {
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':date', $_POST["date"], PDO::PARAM_STR); 
		$stmt->bindValue(':locationID', $_POST['loc'], PDO::PARAM_INT); 
		$stmt->bindValue(':subcatID', $_POST["subcatID"], PDO::PARAM_INT); 
		$stmt->bindValue(':item', $_POST["item"], PDO::PARAM_STR); 
		$stmt->bindValue(':price', $_POST["price"], PDO::PARAM_STR); 
		$stmt->bindValue(':comment', $_POST["comment"], PDO::PARAM_STR); 
		$stmt->bindValue(':author', $_SESSION["userID"], PDO::PARAM_INT); 
		$stmt->execute();
		$_SESSION['success'] = lang::SUCCESS_GENERAL_ADD;
		unset($_SESSION['temp']);
		session_write_close();
		header( 'Location: /expences/expencesList.php');
		exit;
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = lang::ERR_GENERAL;
	}
}

$pdo = NULL;
$title = lang::MENU_EXPENCES_ADD;
//----------------------------VIEW-------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, $pageID);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>' . lang::H2_EXPENCES_WORKS_MATCH . '</h2>';?>

	<form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
		<fieldset>
			<div class="row col-2e">
				<div class="row">
					<label for="category"><?=lang::MENU_EXPENCES_CAT;?>*:</label>
					<select name="category" id="category">
						<?=cat_list(1,$_SESSION['temp']['category']); ?>
					</select>
				</div>
				<div class="row">
				
					<label for="catID"><?=lang::HDR_WORKTYPE_CAT;?>*:</label>
					<?=work_cat_select(1, $_SESSION['temp']['catID']);?>
					
				</div>
			</div>
		</fieldset>

		<input id="button" type="submit" value="<?=lang::BTN_ADD;?>" />
		
	</form>
</section>


<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');
	include($_SERVER['DOCUMENT_ROOT'].'/expences/ajax_function.php');
	unset($_SESSION['temp']);
 ?>

