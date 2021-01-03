<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	if($_POST['RRP'] == '') $RRP=null;
	else $RRP=$_POST['RRP'];
		
	
	$sql = "UPDATE cosmetics 
		SET brandID		= :brandID, 
			name		= :name, 
			description	= :description, 
			articul		= :articul, 
			volume		= :volume, 
			RRP			= :RRP, 
			purpose		= :purpose, 
			`timestamp`	= :timestamp, 
			author		= :author 
		WHERE id=:id";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':brandID', $_POST["brandID"], PDO::PARAM_INT);
		$stmt -> bindValue(':name', $_POST["name"], PDO::PARAM_STR);
		$stmt -> bindValue(':description', $_POST["description"], PDO::PARAM_STR);
		$stmt -> bindValue(':articul', $_POST["articul"], PDO::PARAM_STR);
		$stmt -> bindValue(':volume', $_POST["volume"], PDO::PARAM_INT);
		$stmt -> bindValue(':RRP', $RRP, PDO::PARAM_STR);
		$stmt -> bindValue(':purpose', $_POST["purpose"], PDO::PARAM_STR);
		$stmt -> bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt -> bindValue(':id', $_POST["id"], PDO::PARAM_INT);
		$stmt ->execute();
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL;
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
		session_write_close();
		header( 'Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
	session_write_close();
	if($_GET['goto'] == 'history') {
		header( 'Location: /cosmetics/history.php?cosmID='.$_POST["id"]);
		exit;
	}
	
	header( 'Location: /cosmetics/cosmetics_list.php?brandID='.$_POST["brandID"]);
	exit;
}

if($_GET['id'] !=''){
	try {
		$stmt = $pdo->prepare("SELECT brandID, name, description, articul, volume,RRP,purpose FROM `cosmetics` WHERE id=:id");
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
			<?=brand_select($data['brandID']);?>
			
			<div class="row">
				<label for="name"><?=lang::HDR_ITEM_NAME;?>*:</label>
				<input name="name" type="text" value="<?php echo $data['name']; ?>" required />
			</div>
			<div class="row">
				<label for="volume"><?=lang::HDR_VOLUME;?>*:</label>
				<input name="volume" type="number" value="<?php echo $data['volume']; ?>" required />
			</div>
			<div class="row">
				<label for="articul"><?=lang::HDR_ARTICUL;?>:</label>
				<input name="articul" type="text" value="<?php echo $data['articul']; ?>" />
			</div>
			
			<?=cosm_purpose_select($data['purpose']);?>
			
			<div class="row" id="RRP"<?php if($data['purpose'] == 0 || $data['purpose'] == 3) echo 'style="display:none"';?>>
				<label for="RRP"><?=lang::HDR_RRP;?>:</label>
				<input name="RRP" type="number" step="0.01" value="<?php echo $data['RRP']; ?>" />
			</div>
			
			
			<div class="row">
				<textarea name="description" placeholder="<?=lang::HDR_DESCRIPTION;?>"><?php echo $data['description']; ?></textarea>
			</div>
			<input name="id" type="hidden" value="<?=$_GET['id'];?>" />
		</fieldset>
		<input type="submit" value="<?php echo lang::BTN_CHANGE; ?>" />
	</form>
</section>


<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	?> 
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
