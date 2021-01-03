<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$_SESSION['temp'] = array(
		'name' 		=> $_POST['name'],
		'catID' 		=> $_POST['catID'],
		'target' 		=> $_POST['target'],
		'minPrice' 		=> $_POST['minPrice'],
		'maxPrice' 		=> $_POST['maxPrice'],
		'duration' 		=> $_POST['duration']
	);
		
	
	$sql = "INSERT INTO worktypes (name, catID, target, minPrice, maxPrice, duration, author) 
			VALUES(:name, :catID, :target,  :minPrice, :maxPrice, :duration, :author)";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':name', $_POST["name"], PDO::PARAM_STR);
		$stmt -> bindValue(':catID', $_POST['catID'], PDO::PARAM_STR);
		$stmt -> bindValue(':target', $_POST['target'], PDO::PARAM_INT);
		$stmt -> bindValue(':minPrice', $_POST['minPrice'], PDO::PARAM_STR);
		$stmt -> bindValue(':maxPrice', $_POST['maxPrice'], PDO::PARAM_STR);
		$stmt -> bindValue(':duration', $_POST['duration'], PDO::PARAM_INT);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt ->execute();
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL_ADD;
		unset($_SESSION['temp']);
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
	}
	session_write_close();
	header( 'Location: /worktype/work_list.php');
	exit;
}

$title=lang::H2_WORKTYPE_NEW;
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 

echo '<section class="sidebar">';
	echo tabs($tabs, 'wrk_add');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'. lang::H2_WORKTYPE_NEW .'</h2>';?>
	<form method="post">
		<fieldset>
			<div class="row">
				<label for="name"><?=lang::HDR_ITEM_NAME;?>*:</label>
				<input name="name" type="text" value="<?=$_SESSION['temp']['name']; ?>" required />
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
					<?=target_select();?>
				</select>
			</div>	
			<div class="row">
				<label for="minPrice"><?=lang::HDR_WORKTYPE_MINPRICE;?>*:</label>
				<input name="minPrice" type="number" min="0" step="0.01" value="<?=$_SESSION['temp']['minPrice']; ?>" required />
			</div>	
			<div class="row">
				<label for="maxPrice"><?=lang::HDR_WORKTYPE_MAXPRICE;?>*:</label>
				<input name="maxPrice" type="number" min="0" step="0.01" value="<?=$_SESSION['temp']['maxPrice']; ?>" required />
			</div>	
			<div class="row">
				<label for="duration"><?=lang::HDR_AVG_DURATION;?>*:</label>
				<select name="duration" required />
					<?=event_duration_select($_SESSION['temp']['duration']);?>
				</select>
			</div>	
			
		</fieldset>
	<input type="submit" value="<?=lang::BTN_ADD; ?>" />
	</form>
</section>

<script>
$(document).ready(function(){
	$('select[name="target"]').val('<?=$_SESSION['temp']['target'];?>');
	$('select[name="catID"]').val('<?=$_SESSION['temp']['catID'];?>');
});
</script>

<?php 
unset($_SESSION['temp']);
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	
?> 
