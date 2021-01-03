<?php $access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$_SESSION['temp'] = array(
		'subcatID' 		=> $_POST['subcatID'],
		'inmenu' 		=> $_POST['inmenu']
	);
	
	if (!isset($_POST["inmenu"])) $inmenu = 1;
	else $inmenu = $_POST["inmenu"];
	
	
	if ($_POST['catID_autocomplete'] > 0) $catID = $_POST['catID_autocomplete'];
	else { // Если категории не существует, нужно сначала ее создать и получить catID
		$q = "INSERT INTO expences_cat (category, author)
			VALUES(:category, :author)";
		$stmt = $pdo->prepare($q);
		$stmt->bindValue(':category', $_POST['category'], PDO::PARAM_STR); 
		$stmt->bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT); 
		$stmt->execute();
		$catID = $pdo->lastInsertId();
	}
		
	$sql = "INSERT INTO expences_subcat (catID, subcategory, inmenu, author)
			VALUES(:catID, :subcategory, :inmenu, :author)";
	try {
		$insert = $pdo->prepare($sql);
		$insert->bindValue(':catID', $catID, PDO::PARAM_INT); 
		$insert->bindValue(':subcategory', $_POST["subcategory"], PDO::PARAM_STR); 
		$insert->bindValue(':inmenu', $inmenu, PDO::PARAM_INT); 
		$insert->bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT); 
		$insert->execute();
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL;
		unset($_SESSION['temp']);
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = lang::ERR_GENERAL;
	}
	header('Location: /expences/catList.php?tab='.$_GET['tab']);
	exit;
}

$pdo = NULL;
$title = lang::H2_NEW_CAT;
//----------------------------VIEW-------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, 'cat_add');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>' . lang::H2_NEW_CAT . '</h2>';?>

	<form action="<?php $_SERVER['PHP_SELF'].'tab=active'?>" method="post">
		<fieldset>
			<div class="row">
				<label for="category"><?=lang::HDR_CATEGORY;?>*:</label>
				<input name="category" class="expences_cat" id="catID" type="text" value="" required autofocus />
				<input type="hidden" name="catID_autocomplete" >
			</div>
			<div class="row">
				<label for="subcategory"><?=lang::HDR_SUBCATEGORY;?>*:</label>
				<input name="subcategory" type="text" required />
			</div>
			<div class="row">
				<label for="inmenu"><?=lang::HDR_SHOW_IN_MENU;?></label>
				<div style="flex:13.7;line-height:2.9em;">
					<input name="inmenu" value="1" type="radio" /><i class="fas fa-check marginLeft" style="color:green;"></i>
					<input name="inmenu" value="0" type="radio" class="marginLeft" /><i class="fas fa-times marginLeft" style="color:red;"></i>
				</div>
			</div>
		</fieldset>
		<input type="submit" value="<?=lang::BTN_ADD;?>" />
	</form>
</section>


<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');
	unset($_SESSION['temp']); ?>