<?php
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	try {
		$stmt = $pdo->prepare("UPDATE expences 
			SET date		= :date, 
				locationID	= :locationID, 
				subcatID	= :subcatID, 
				item		= :item, 
				price		= :price, 
				comment		= :comment, 
				`timestamp`	= :timestamp, 
				author		= :author
			WHERE id = :id");
		$stmt->bindValue(':date', $_POST["date"], PDO::PARAM_STR); 
		$stmt->bindValue(':locationID', $_POST["loc"], PDO::PARAM_INT); 
		$stmt->bindValue(':subcatID', $_POST["subcatID"], PDO::PARAM_INT); 
		$stmt->bindValue(':item', $_POST["item"], PDO::PARAM_STR); 
		$stmt->bindValue(':price', $_POST["price"], PDO::PARAM_STR); 
		$stmt->bindValue(':comment', $_POST["comment"], PDO::PARAM_STR); 
		$stmt->bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
		$stmt->bindValue(':author', $_SESSION["userID"], PDO::PARAM_INT); 
		$stmt->bindValue(':id', $_POST["id"], PDO::PARAM_INT); 
		$stmt ->execute();
		$_SESSION['success'] = lang::SUCCESS_GENERAL;
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = lang::ERR_GENERAL;
	}
	session_write_close();
	header( 'Location: /expences/expencesList.php');
	exit;
}

if($_GET['id'] !=''){
	try {
		$stmt = $pdo->prepare("SELECT expences.id, date, locationID, subcatID,item,price,comment, catID
			FROM `expences` 
			LEFT JOIN expences_subcat ON expences.subcatID = expences_subcat.id
			LEFT JOIN expences_cat ON expences_subcat.catID = expences_cat.id
			WHERE expences.id=:id");
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
	header( 'Location: /expences/expencesList.php');
	exit;
}

$pdo = NULL;
$title = $data['item'];
//----------------------------VIEW-------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>' . $data['item'] . '</h2>';?>


	<form method="post">
		<fieldset>
			<div class="row">
				<label for="date"><?=lang::DATE;?>*:</label>
				<input name="date" type="date" value="<?=$data['date'];?>" required />
			</div>
			<?php echo location_options(1, null, $data['locationID'], 1); ?>
			
			<div class="row">
				<label for="category"><?=lang::HDR_CATEGORY;?>*:</label>
				<select name="category" id="category">
					<?=cat_list(1,$data['catID']); ?>
				</select>
			</div>
			<div class="row">
				<label for="subcatID"><?=lang::HDR_SUBCATEGORY;?>*:</label>
				<select name="subcatID" id="subcategory" required>
					
				</select>
			</div>
			<div class="row">
				<label for="item"><?=lang::HDR_ITEM_NAME;?>*:</label>
				<input name="item" type="text" value="<?=$data['item'];?>" required />
			</div>
			<div class="row">
				<label for="price"><?=lang::HDR_COST;?>*:</label>
				<input name="price" type="number" step="any" value="<?=$data['price'];?>" required />
			</div>
			<div class="row">
				<textarea name="comment" placeholder="<?=lang::COMMENT_PLACEHOLDER;?>"><?=$data['comment'];?></textarea>
			</div>
		</fieldset>
		<input name="id" type="hidden" value="<?=$data['id'];?>" />

		<input id="button" type="submit" value="<?=lang::BTN_CHANGE;?>" />
		
	</form>
</section>


<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');
	include($_SERVER['DOCUMENT_ROOT'].'/expences/ajax_function.php');
 ?>

