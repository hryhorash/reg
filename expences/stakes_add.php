<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	$_SESSION['temp'] = array(
		'catID' 		=> $_POST['catID'],
		'subcatID' 		=> $_POST['subcatID'],
		'date' 			=> $_POST['date'],
		'unitPrice'		=> $_POST['unitPrice'],
		'monthlyPrice' 	=> $_POST['monthlyPrice'],
		'loc' 			=> $_POST['loc']
	);
	
	if ($_POST["unitPrice"] =='' && $_POST["monthlyPrice"]=='') {
		$_SESSION['error'] = lang::ERR_NO_PRICE;
		header('Location: /expences/stakes_add.php?tab=active');
		exit();
	}
	
	if ($_POST["unitPrice"] != '')	$unitPrice = $_POST["unitPrice"];
	else $unitPrice = 0;
	
	if ($_POST["monthlyPrice"] != '') $monthlyPrice = $_POST["monthlyPrice"];
	else $monthlyPrice = 0;
	
	if($_POST['loc'] !='') {
	
		$sql = "INSERT INTO stakes (subcatID, date, unitPrice, monthlyPrice, locationID, author)
				VALUES(:subcatID, :date, :unitPrice, :monthlyPrice, :locationID, :author)";
				
		$sql_archive = "UPDATE stakes
				SET archive = 1,
					`timestamp` = :timestamp,
					author = :author
				WHERE subcatID=:subcatID and archive=0 and locationID=:locationID  and stakes.date = (SELECT MIN(stakes.date) from stakes where subcatID=:subcatID and locationID=:locationID and archive=0)";
		try {
			$insert = $pdo->prepare($sql);
			$insert->bindValue(':subcatID', $_POST["subcatID"], PDO::PARAM_INT); 
			$insert->bindValue(':date', $_POST["date"], PDO::PARAM_STR); 
			$insert->bindValue(':unitPrice', $unitPrice, PDO::PARAM_STR); 
			$insert->bindValue(':monthlyPrice', $monthlyPrice, PDO::PARAM_STR); 
			$insert->bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT); 
			$insert->bindParam(':locationID', $locID, PDO::PARAM_INT); 
			
			$update = $pdo->prepare($sql_archive);
			$update->bindValue(':subcatID', $_POST["subcatID"], PDO::PARAM_INT); 
			$update->bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR); 
			$update->bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT); 
			$update->bindParam(':locationID', $locID, PDO::PARAM_INT); 
			
			foreach ($_POST["loc"] as $locID => $v) 
			{
				$insert->execute();
				$update->execute();	//переносим старую ставку в архив
			
			}
			
			$_SESSION['success'] = lang::SUCCESS_GENERAL_ADD;
			unset($_SESSION['temp']);
		} catch (PDOException $ex){
			include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
			$_SESSION['error'] = lang::ERR_GENERAL;
		}
	} else 
	{
			$_SESSION['error'] = lang::ERR_SELECT_LOCATION;
			session_write_close();
			header( 'Location: /expences/stakes_add.php?tab=active');
			exit;
	}
	header('Location: /expences/stakes_add.php?tab=active');
	exit;
}

$pdo = NULL;
$title = lang::STAKES_ADD;
//----------------------------VIEW-------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, 'rate_add');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>' . lang::STAKES_ADD . '</h2>';?>


	<form action="<?php $_SERVER['PHP_SELF'].'?tab='.$_GET['tab'];?>" method="post">
		<fieldset>
			<div class="row">
				<label for="catID"><?=lang::HDR_CATEGORY;?>*:</label>
				<select name="catID" id="category">
					<?=cat_list(0); ?>
				</select>
			</div>
			<div class="row">
				<label for="subcatID"><?=lang::HDR_SUBCATEGORY;?>*:</label>
				<select name="subcatID" id="subcategory" required>
					
				</select>
			</div>
			
			<?=location_options('','',$_SESSION['temp']['loc']); ?>
			<div class="row">
				<label for="date"><?=lang::HDR_ACTIVE_FROM;?>*:</label>
				<input name="date" type="date" value="<?=defaultDate();?>" required />
			</div>
			<div class="row">
				<label for="price"><?=lang::HDR_COST;?>*:</label>
				<input name="unitPrice" type="number" step="0.01" placeholder="<?=lang::PER_PIECE_PLACEHOLDER;?>" class="half-flex" style="margin-right: 10px;" value="<?=$_SESSION['temp']['unitPrice'];?>" />
				<input name="monthlyPrice" type="number" step="0.01" placeholder="<?=lang::PER_MONTH_PLACEHOLDER;?>" class="half-flex" value="<?=$_SESSION['temp']['monthlyPrice'];?>" />
			</div>
		</fieldset>
		<input id="button" type="submit" value="<?=lang::BTN_ADD;?>" />
		
	</form>
</section>


<?php 
	include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');
	include($_SERVER['DOCUMENT_ROOT'].'/expences/ajax_function.php'); 
	unset($_SESSION['temp']);?>