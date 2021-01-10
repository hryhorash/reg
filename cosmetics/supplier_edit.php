<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	$brandIDs = implode(',',$_POST["brandIDs"]);
	$phones = phonesSQL($_POST['phones']); //преобразуем массив в строку
	
	$sql = "UPDATE suppliers 
		SET name		= :name, 
			VAT			= :VAT, 
			contact		= :contact, 
			position	= :position, 
			phones		= :phones, 
			email		= :email, 
			site		= :site, 
			address		= :address, 
			comment		= :comment, 
			`timestamp`	= :timestamp, 
			author		= :author
		WHERE id=:id";
	
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
		$stmt -> bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt -> bindValue(':id', $_POST["id"], PDO::PARAM_INT);
		$stmt ->execute();
		
		$add = $pdo->prepare("INSERT INTO supplier_brands (supplierID, brandID, author) 
				VALUES(:supplierID, :brandID, :author)");
		$add -> bindValue(':supplierID', $_POST["id"], PDO::PARAM_INT);
		$add -> bindParam(':brandID', $brandID, PDO::PARAM_INT);
		$add -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		
		$delete = $pdo->prepare("DELETE FROM supplier_brands WHERE supplierID=:supplierID AND brandID=:brandID");
		$delete -> bindValue(':supplierID', $_POST["id"], PDO::PARAM_INT);
		$delete -> bindParam(':brandID', $brandID_old, PDO::PARAM_INT);
		
		
		$brandIDs_old=explode(',',$_POST['brandIDs_old']);
		foreach($_POST["brandIDs"] as $brandID) {
			if(!in_array($brandID, $brandIDs_old))	$add ->execute();
		}
		
		foreach($brandIDs_old as $brandID_old) {
			if(!in_array($brandID_old, $_POST["brandIDs"])) $delete ->execute();
		}
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL_ADD;
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
		session_write_close();
		header( 'Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
	
	session_write_close();
	header( 'Location: /cosmetics/suppliers_list.php');
	exit;
}

if($_GET['id'] !=''){
	try {
		$stmt = $pdo->prepare("SELECT suppliers.name, VAT, contact, position, phones, email, site, address, comment, GROUP_CONCAT(brands.id) as brandIDs
		FROM `suppliers`  
        LEFT JOIN supplier_brands ON suppliers.id=supplier_brands.supplierID
        LEFT JOIN brands ON supplier_brands.brandID=brands.id  
		WHERE suppliers.id=:id");
		$stmt -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
		$stmt ->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}

	$phones = explode(',',$data['phones']);
	
} else {
	$_SESSION['error'] = lang::ERR_NO_ID;
	session_write_close();
	header( 'Location: /cosmetics/suppliers_list.php');
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
			<div class="row col-2">
				<label for="name"><?=lang::HDR_ITEM_NAME;?>*:</label>
				<input name="name" type="text" value="<?php echo $data['name']; ?>" required />
			
				<?=brand_multiselect($data['brandIDs']);?>
			
				<?=VAT_select($data['VAT']);?>
			
				<label for="contact"><?=lang::HDR_CONTACT_NAME;?>:</label>
				<input name="contact" type="text" value="<?php echo $data['contact']; ?>" />
				<label for="position"><?=lang::HDR_CONTACT_POSITION;?>:</label>
				<input name="position" type="text" value="<?php echo $data['position']; ?>" />
			</div>
			
			<div id="morePhones col-2">
				<?=phones_add($phones);?>
			</div>
			
			<div class="row col-2">
				<label for="email"><?php echo lang::HDR_EMAIL; ?>:</label>
				<input name="email" type="email" value="<?=$data['email']; ?>" /> 
			
				<label for="site"><?=lang::HDR_SITE;?>:</label>
				<input name="site" type="text" value="<?php echo $data['site']; ?>" />
			
				<label for="address"><?=lang::HDR_ADDRESS;?>:</label>
				<input name="address" type="text" value="<?php echo $data['address']; ?>" />
			</div>
			<div class="row">
				<textarea name="comment" placeholder="<?=lang::COMMENT_PLACEHOLDER;?>"><?=$data['comment']; ?></textarea>
			</div>
			
		</fieldset>
		<input name="id" type="hidden" value="<?=$_GET['id'];?>">
		<input name="brandIDs_old" type="hidden" value="<?=$data['brandIDs'];?>">
		<input type="submit" value="<?php echo lang::BTN_CHANGE; ?>" />
	</form>
</section>


<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	
?> 
