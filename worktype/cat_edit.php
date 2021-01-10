<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
		
	$sql = "UPDATE worktype_cat
			SET category = :category,
				`timestamp`		= :timestamp, 
				author = :author
			WHERE id = :id";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':category', $_POST["category"], PDO::PARAM_STR);
		$stmt -> bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt -> bindValue(':id', $_POST["id"], PDO::PARAM_INT);
		$stmt ->execute();
		
		
		
		
		if($_POST['serv_work'] != '') {
			$add = $pdo->prepare("INSERT INTO worktype_netto (catID, nettoID, author)
									VALUES(:catID, :nettoID, :author)");
			$add -> bindValue(':catID', $_POST["id"], PDO::PARAM_INT);
			$add -> bindParam(':nettoID', $nettoID, PDO::PARAM_INT);
			$add -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
			
			
			$delete = $pdo->prepare("DELETE FROM worktype_netto WHERE catID=:catID AND nettoID=:nettoID");
			$delete -> bindValue(':catID', $_POST["id"], PDO::PARAM_INT);
			$delete -> bindParam(':nettoID', $nettoID, PDO::PARAM_INT);
			
			if($_POST['serv_work_old'] !='') {
				$serv_work_old=explode(',',$_POST['serv_work_old']);
				sort($serv_work_old);
				foreach ($_POST['serv_work'] as $nettoID){
						//add
					if(!in_array($nettoID, $serv_work_old)){
						$add ->execute();
					} 	
				}
				
						//delete
				foreach($serv_work_old as $nettoID) {
					if(!in_array($nettoID, $_POST["serv_work"])) $delete ->execute();
				}
			} else {
				foreach ($_POST['serv_work'] as $nettoID){
					$add ->execute();	
				}
					
			} 
		}
		
		
		
		
		
		
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL_ADD;
		unset($_SESSION['temp']);
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
	}
	session_write_close();
	header( 'Location: /worktype/cat_list.php');
	exit;
}

if($_GET['id'] !=''){
	try {
		$stmt = $pdo->prepare("SELECT worktype_cat.id, category, GROUP_CONCAT(DISTINCT service_netto.id) as serv_nettoIDs
			FROM `worktype_cat` 
			LEFT JOIN worktype_netto ON worktype_cat.id = worktype_netto.catID
			LEFT JOIN service_netto ON  worktype_netto.nettoID = service_netto.id
			WHERE worktype_cat.id=:id");
		$stmt -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
		$stmt ->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}

	
} else {
	$_SESSION['error'] = lang::ERR_NO_ID;
	session_write_close();
	header( 'Location: /worktype/cal_ist.php?tab='.$_GET['tab']);
	exit;
}

$title=$data['category'];
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php');
echo '<section class="sidebar">';
	echo tabs($tabs);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php'); 
	echo '<h2>'. $data['category'] .'</h2>'; ?>
	<form method="post">
		<fieldset>
			<div class="row col-2">
				<label for="category"><?=lang::HDR_ITEM_NAME;?>*:</label>
				<input name="category" class="worktype_cat" type="text" value="<?=$data['category']; ?>" required />
				
				<?=work_netto_services_options($data['serv_nettoIDs']);?>
			</div>	
			
			<input name="id" type="hidden" value="<?=$_GET['id']; ?>" />
			<input name="serv_work_old" type="hidden" value="<?=$data['serv_nettoIDs']; ?>" />
		</fieldset>
		<input type="submit" value="<?php echo lang::BTN_CHANGE; ?>" />
	</form>
</section>

<?php 
unset($_SESSION['temp']);
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	
?> 
