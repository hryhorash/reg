<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$sql = "UPDATE worktypes 
			SET catID =:catID, 
				name = :name, 
				target =:target, 
				minPrice =:minPrice, 
				maxPrice =:maxPrice, 
				duration =:duration, 
				`timestamp`		= :timestamp, 
				author =:author
			WHERE id=:id";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':catID', $_POST['catID'], PDO::PARAM_INT);
		$stmt -> bindValue(':name', $_POST["name"], PDO::PARAM_STR);
		$stmt -> bindValue(':target', $_POST['target'], PDO::PARAM_INT);
		$stmt -> bindValue(':minPrice', $_POST['minPrice'], PDO::PARAM_STR);
		$stmt -> bindValue(':maxPrice', $_POST['maxPrice'], PDO::PARAM_STR);
		$stmt -> bindValue(':duration', $_POST['duration'], PDO::PARAM_INT);
		$stmt -> bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt -> bindValue(':id', $_POST['id'], PDO::PARAM_INT);
		$stmt ->execute();
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL_ADD;
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
	}
	session_write_close();
	header( 'Location: /worktype/work_list.php?tab=active');
	exit;
}

if($_GET['id'] !=''){
	try {
		$stmt = $pdo->prepare("SELECT worktypes.id,name,catID,category,target,minPrice,maxPrice,duration FROM `worktypes` LEFT JOIN worktype_cat ON worktypes.catID = worktype_cat.id WHERE worktypes.id=:id");
		$stmt -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
		$stmt ->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	
} else {
	$_SESSION['error'] = lang::ERR_NO_ID;
	session_write_close();
	header( 'Location: /worktype/work_list.php?tab='.$_GET['tab']);
	exit;
}

$title=$data['name'];
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'. $data['name'] .'</h2>'; ?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']. '?tab=active'; ?>">
		<fieldset>
			<div class="row">
				<label for="name"><?=lang::HDR_ITEM_NAME;?>*:</label>
				<input name="name" type="text" value="<?php echo $data['name']; ?>" required />
			</div>
			<div class="row">
				<label for="catID"><?=lang::HDR_WORKTYPE_CAT;?>*:</label>
				<select name="catID" required />
					<?=work_cat_select();?>
				</select>
			</div>
			<div class="row">
				<label for="target"><?=lang::HDR_WORKTYPE_TARGET;?>*:</label>
				<select name="target" type="text" required />
					<?=target_select()?>
				</select>
			</div>	
			<div class="row">
				<label for="minPrice"><?=lang::HDR_WORKTYPE_MINPRICE;?>*:</label>
				<input name="minPrice" type="number" min="0" step="0.01" value="<?php echo $data['minPrice']; ?>" required />
			</div>	
			<div class="row">
				<label for="maxPrice"><?=lang::HDR_WORKTYPE_MAXPRICE;?>*:</label>
				<input name="maxPrice" type="number" min="0" step="0.01" value="<?php echo $data['maxPrice']; ?>" required />
				<input name="id" type="hidden" value="<?=$_GET['id']; ?>" />
			</div>	
			<div class="row">
				<label for="duration"><?=lang::HDR_AVG_DURATION;?>*:</label>
				<select name="duration" required />
					<?=event_duration_select($data['duration']);?>
				</select>
			</div>	
		</fieldset>
	<input type="submit" value="<?php echo lang::BTN_CHANGE; ?>" />
	</form>
</section>

<script>
$(document).ready(function(){
	$('select[name="target"]').val('<?=$data['target'];?>');
	$('select[name="catID"]').val('<?=$data['catID'];?>');
});
</script>

<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	
?> 
