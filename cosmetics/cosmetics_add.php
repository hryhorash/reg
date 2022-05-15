<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$_SESSION['temp'] = array(
		'brandID' 		=> $_POST['brandID'],
		'name' 			=> $_POST['name'],
		'description'	=> $_POST['description'],
		'articul' 		=> $_POST['articul'],
		'volume' 		=> $_POST['volume'],
		'RRP' 			=> $_POST['RRP'],
		'purpose' 		=> $_POST['purpose'],
		'backTo' 		=> $_POST['backTo'],
		'invoiceID' 	=> $_POST['invoiceID']		
	);
	
	if($_POST['RRP'] == '') $RRP=null;
	else $RRP=$_POST['RRP'];
		
	
	$sql = "INSERT INTO cosmetics (brandID, name, description, articul, volume, RRP, purpose, author) 
			VALUES(:brandID, :name, :description, :articul, :volume, :RRP, :purpose, :author)";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':brandID', $_POST["brandID"], PDO::PARAM_INT);
		$stmt -> bindValue(':name', $_POST["name"], PDO::PARAM_STR);
		$stmt -> bindValue(':description', $_POST["description"], PDO::PARAM_STR);
		$stmt -> bindValue(':articul', $_POST["articul"], PDO::PARAM_STR);
		$stmt -> bindValue(':volume', $_POST["volume"], PDO::PARAM_INT);
		$stmt -> bindValue(':RRP', $RRP, PDO::PARAM_STR);
		$stmt -> bindValue(':purpose', $_POST["purpose"], PDO::PARAM_STR);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt ->execute();
		
		unset($_SESSION['temp']);
		
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL_ADD;
		
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
		session_write_close();
		header( 'Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
	session_write_close();
	
	if($_POST['backTo'] == 'close') {
		echo "<script>window.close();</script>";
		exit;
	}
	
	header( 'Location: /cosmetics/cosmetics_list.php?brandID='.$_POST["brandID"]);
	exit;
}

$title=lang::H2_COSMETICS;
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, 'csm_add');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'. lang::H2_COSMETICS .'</h2>';?>
	
	<form method="post">
		<fieldset class="wSign">
			<div class="row col-2">
				<?=brand_select($_SESSION['brandID']);?>
			
				<label for="name"><?=lang::HDR_ITEM_NAME;?>*:</label>
				<input name="name" type="text" value="<?php echo $_SESSION['temp']['name']; ?>" required autofocus />
			
				<label for="volume"><?=lang::HDR_VOLUME;?>*:</label>
				<input name="volume" type="number" value="<?php echo $_SESSION['temp']['volume']; ?>" required />
			
				<label for="articul"><?=lang::HDR_ARTICUL;?>:</label>
				<input name="articul" type="text" value="<?php echo $_SESSION['temp']['articul']; ?>" />
			
				
				<?=cosm_purpose_select($_SESSION['purpose']);?>
			</div>
			<div class="row col-2" id="RRP" <?php if($_SESSION['purpose'] == 0 || $_SESSION['purpose'] == 3) echo 'style="display:none;"';?>>
				<label for="RRP"><?=lang::HDR_RRP;?>:</label>
				<input name="RRP" type="number" step="0.01" value="<?php echo $_SESSION['temp']['RRP']; ?>" />
			</div>
			
			<div class="row">
				<textarea name="description" placeholder="<?=lang::HDR_DESCRIPTION;?>"><?php echo $_SESSION['temp']['description']; ?></textarea>
			</div>
			<input name="backTo" type="hidden" value="<?=$_GET['backTo'];?>" />
			<input name="invoiceID" type="hidden" value="<?=$_GET['invoiceID'];?>" />
			
		</fieldset>
		<input type="submit" value="<?php echo lang::BTN_ADD; ?>" />
	</form>
</section>

<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');
unset($_SESSION['temp']);	?> 

<script>
	$("select[name='purpose']").change(function() {
		if ($("select[name='purpose']").val() == 1 || $("select[name='purpose']").val() == 2) {
			$("#RRP").show();			
		} else {
			$("#RRP").hide();
			$("input[name='RRP']").val('');
		}		 
	});
</script>