<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	if ($_POST["unitPrice"] =='' && $_POST["monthlyPrice"]=='') {
		$_SESSION['error'] = lang::ERR_NO_PRICE;
		header('Location: /expences/stakes_edit.php?tab=active');
		exit();
	}
	
	if ($_POST["unitPrice"] != '')	$unitPrice = $_POST["unitPrice"];
	else $unitPrice = 0;
	
	if ($_POST["monthlyPrice"] != '') $monthlyPrice = $_POST["monthlyPrice"];
	else $monthlyPrice = 0;
	
	if($_POST['loc'] !='') {
	
		$sql = "UPDATE stakes 
				SET subcatID	= :subcatID,
					date		= :date, 
					unitPrice	= :unitPrice, 
					monthlyPrice= :monthlyPrice, 
					locationID	= :locationID, 
					`timestamp`	= :timestamp, 
					author		= :author
				WHERE id=:id";
		try {
			$insert = $pdo->prepare($sql);
			$insert->bindValue(':subcatID', $_POST["subcatID"], PDO::PARAM_INT); 
			$insert->bindValue(':date', $_POST["date"], PDO::PARAM_STR); 
			$insert->bindValue(':unitPrice', $unitPrice, PDO::PARAM_STR); 
			$insert->bindValue(':monthlyPrice', $monthlyPrice, PDO::PARAM_STR); 
			$insert->bindParam(':locationID', $locID, PDO::PARAM_INT); 
			$insert->bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
			$insert->bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT); 
			$insert->bindValue(':id', $_POST["id"], PDO::PARAM_INT); 
			
			foreach ($_POST["loc"] as $locID => $v) 
			{
				$insert->execute();
			}
			
			$_SESSION['success'] = lang::SUCCESS_GENERAL;
			session_write_close();
			header( 'Location: /expences/stakesList.php?tab='.$_GET['tab']);
			exit;
		} catch (PDOException $ex){
			include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
			$_SESSION['error'] = lang::ERR_GENERAL;
		}
	} else 
	{
			$_SESSION['error'] = lang::ERR_SELECT_LOCATION;
			session_write_close();
			header( 'Location: /expences/stakes_edit.php?tab=active');
			exit;
	}
	header('Location: /expences/stakes_edit.php?tab='.$_GET['tab']);
	exit;
}

if($_GET['id'] !=''){
	try {
		$stmt = $pdo->prepare("SELECT stakes.id , category, subcategory, date, unitPrice, monthlyPrice, stakes.locationID, catID, subcatID, inmenu
			FROM stakes
			LEFT JOIN expences_subcat ON stakes.subcatID = expences_subcat.id
			LEFT JOIN expences_cat ON expences_subcat.catID = expences_cat.id
			WHERE stakes.id=:id");
		$stmt -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
		$stmt ->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}

	
	//проверить права на редактирование воизбежание простого перебора id-шками
	if (handle_rights('basic', $data['locationID']) != 1) 
	{		
		$_SESSION['error'] = lang::ERR_NO_RIGHTS;
		session_write_close();
		header( 'Location: /expences/stakesList.php?tab='.$_GET['tab']);
		exit;
	}
	
} else {
	$_SESSION['error'] = lang::ERR_NO_ID;
	session_write_close();
	header( 'Location: /expences/stakesList.php?tab='.$_GET['tab']);
	exit;
}


$pdo = NULL;
$title = $data['subcategory'];
//----------------------------VIEW-------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>' . $data['subcategory'] . '</h2>';?>

	<form action="<?php $_SERVER['PHP_SELF'].'?tab='.$_GET['tab'];?>" method="post">
		<fieldset>
			<div class="row">
				<label for="catID"><?=lang::HDR_CATEGORY;?>*:</label>
				<select name="catID" id="category">
					<?=cat_list($data['inmenu'],$data['catID']); ?>
				</select>
			</div>
			<div class="row">
				<label for="subcatID"><?=lang::HDR_SUBCATEGORY;?>*:</label>
				<select name="subcatID" id="subcategory" required>
					
				</select>
			</div>
			
				<?=location_options('','',$data['locationID']); ?>
			<div class="row">
				<label for="date"><?=lang::HDR_ACTIVE_FROM;?>*:</label>
				<input name="date" type="date" value="<?=$data['date'];?>" required />
			</div>
			<div class="row">
				<label for="price"><?=lang::HDR_COST;?>*:</label>
				<input name="unitPrice" type="number" step="0.01" placeholder="<?=lang::PER_PIECE_PLACEHOLDER;?>" class="half-flex" style="margin-right: 10px;" value="<?=$data['unitPrice'];?>" />
				<input name="monthlyPrice" type="number" step="0.01" placeholder="<?=lang::PER_MONTH_PLACEHOLDER;?>" class="half-flex" value="<?=$data['monthlyPrice'];?>" />
				<input name="id" type="hidden" value="<?=$_GET['id'];?>" />
			</div>
		</fieldset>

		<input id="button" type="submit" value="<?=lang::BTN_CHANGE;?>" />
		
	</form>
</section>


<?php 
	include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');
	include($_SERVER['DOCUMENT_ROOT'].'/expences/ajax_function.php'); 
?>