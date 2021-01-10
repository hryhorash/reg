<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	//print_r($_POST);
	//exit;
	
	$_SESSION['temp'] = array(
		'category' 		=> $_POST['category'],
		'serv_nettoIDs'	=> $_POST['serv_nettoIDs']
	);
		
	
	$sql = "INSERT INTO worktype_cat (category, author) 
			VALUES(:category, :author)";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':category', $_POST["category"], PDO::PARAM_STR);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt ->execute();
		$catID = $pdo->lastInsertId();
		
		if(isset($_POST['serv_work']) && $_POST['serv_work'] != '') {
			$netto = $pdo->prepare("INSERT INTO worktype_netto (catID, nettoID, author)
									VALUES(:catID, :nettoID, :author)");
			$netto -> bindValue(':catID', $catID, PDO::PARAM_INT);
			$netto -> bindParam(':nettoID', $nettoID, PDO::PARAM_INT);
			$netto -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		
			foreach ($_POST['serv_work'] as $nettoID) {
				if($_POST['serv_work'] > 0) {
					$netto -> bindValue(':nettoID', $nettoID, PDO::PARAM_INT);
					$netto ->execute();
				}
				$i++;
			}
		}
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL_ADD;
		unset($_SESSION['temp']);
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
	}
	session_write_close();
	header( 'Location: /worktype/cat_list.php?tab=active');
	exit;
}

$title=lang::H2_WORKTYPE_CAT;
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, 'cat_add');
echo '</section>';
echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'. lang::H2_WORKTYPE_CAT .'</h2>';?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']. '?tab=active'; ?>">
		<fieldset>
			<div class="row col-2">
				<label for="category"><?=lang::HDR_ITEM_NAME;?>*:</label>
				<input name="category" class="worktype_cat" type="text" value="<?php echo $_SESSION['temp']['category']; ?>" required />
				
				<?=work_netto_services_options($_SESSION['serv_nettoIDs']);?>
			</div>	
		</fieldset>
	<input type="submit" value="<?php echo lang::BTN_ADD; ?>" />
	</form>
</section>

<?php 
unset($_SESSION['temp']);
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	
?> 
