<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$_SESSION['temp'] = array(
		'name' 			=> $_POST['name'],
		'brandIDs' 		=> $_POST['brandIDs'],
		'VAT' 			=> $_POST['VAT'],
		'contact' 		=> $_POST['contact'],
		'position' 		=> $_POST['position'],
		'phones' 		=> $_POST['phones'],
		'email' 		=> $_POST['email'],
		'site' 			=> $_POST['site'],
		'address' 		=> $_POST['address'],
		'comment' 		=> $_POST['comment']
	);
	
	$phones = phonesSQL($_POST['phones']); //преобразуем массив в строку
	
	$sql = "INSERT INTO suppliers (name, VAT, contact, position, phones, email, site, address, comment, author) 
			VALUES(:name, :VAT, :contact, :position, :phones, :email, :site, :address, :comment, :author)";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':name', $_POST["name"], PDO::PARAM_STR);
		$stmt -> bindValue(':VAT', $_POST["VAT"], PDO::PARAM_INT);
		$stmt -> bindValue(':contact', $_POST["contact"], PDO::PARAM_STR);
		$stmt -> bindValue(':position', $_POST["position"], PDO::PARAM_STR);
		$stmt -> bindValue(':phones', $phones, PDO::PARAM_STR);
		$stmt -> bindValue(':email', $_POST["email"], PDO::PARAM_STR);
		$stmt -> bindValue(':site', $_POST["site"], PDO::PARAM_STR);
		$stmt -> bindValue(':address', $_POST["address"], PDO::PARAM_STR);
		$stmt -> bindValue(':comment', $_POST["comment"], PDO::PARAM_STR);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt ->execute();
		$supplierID = $pdo->lastInsertId();
		
		$stmt2 = $pdo->prepare("INSERT INTO supplier_brands (supplierID, brandID, author) 
				VALUES(:supplierID, :brandID, :author)");
		$stmt2 -> bindValue(':supplierID', $supplierID, PDO::PARAM_INT);
		$stmt2 -> bindParam(':brandID', $brandID, PDO::PARAM_INT);
		$stmt2 -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
			
		foreach($_POST["brandIDs"] as $brandID) {
			$stmt2 ->execute();
		}
		
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL_ADD;
		unset($_SESSION['temp']);
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
		session_write_close();
		
		/*if ($_POST['backTo'] == 'cosmetics_add') {
			header( 'Location: /cosmetics/cosmetics_add.php?brandID=' . $brandID);
			exit;
		}*/
		
		header( 'Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
	
	session_write_close();
	/*if ($_POST['backTo'] == 'cosmetics_add') {
		header( 'Location: /cosmetics/cosmetics_add.php?brandID=' . $brandID);
		exit;
	}*/
	header( 'Location: /cosmetics/suppliers_list.php');
	exit;
}

$title=lang::H2_NEW_SUPPLIER;
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, 'spl_add');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'. lang::H2_NEW_SUPPLIER .'</h2>';?>
	<form method="post">
		<fieldset>
			<div class="row">
				<label for="name"><?=lang::HDR_ITEM_NAME;?>*:</label>
				<input name="name" type="text" value="<?php echo $_SESSION['temp']['name']; ?>" required />
			</div>
			
			<?=brand_multiselect();?>
			
			<?=VAT_select();?>
			
			<div class="row">
				<label for="contact"><?=lang::HDR_CONTACT_NAME;?>:</label>
				<input name="contact" type="text" value="<?php echo $_SESSION['temp']['contact']; ?>" />
			</div>
			<div class="row">
				<label for="position"><?=lang::HDR_CONTACT_POSITION;?>:</label>
				<input name="position" type="text" value="<?php echo $_SESSION['temp']['position']; ?>" />
			</div>
			
			<div id="morePhones">
				<?=phones_add();?>
			</div>
			
			<div class="row">
				<label for="email"><?php echo lang::HDR_EMAIL; ?>:</label>
				<input name="email" type="email" value="<?=$_SESSION['temp']['email']; ?>" /> 
			</div>
			
			<div class="row">
				<label for="site"><?=lang::HDR_SITE;?>:</label>
				<input name="site" type="text" value="<?php echo $_SESSION['temp']['site']; ?>" />
			</div>
			<div class="row">
				<label for="address"><?=lang::HDR_ADDRESS;?>:</label>
				<input name="address" type="text" value="<?php echo $_SESSION['temp']['address']; ?>" />
			</div>
			<div class="row">
				<textarea name="comment" placeholder="<?=lang::COMMENT_PLACEHOLDER;?>"><?=$_SESSION['temp']['comment']; ?></textarea>
			</div>
			
		</fieldset>
		<input name="backTo" type="hidden" value="<?=$_GET['backTo'];?>">
		<input type="submit" value="<?php echo lang::BTN_ADD; ?>" />
	</form>
</section>


<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	
unset($_SESSION['temp']);
?> 
