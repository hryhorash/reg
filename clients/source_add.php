<?php 
$access = 90;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$_SESSION['temp'] = array(
		'name' 		=> $_POST['name']
	);
		
	
	$sql = "INSERT INTO sources (name, author) 
			VALUES(:name, :author)";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':name', $_POST["name"], PDO::PARAM_STR);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt ->execute();
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL_ADD;
		unset($_SESSION['temp']);
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
		session_write_close();
		header( 'Location: /clients/source_add.php');
		exit;
	}
	session_write_close();
	header( 'Location: /clients/source_list.php?tab=active');
	exit;
}

$title=lang::H2_NEW_SOURCE;
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	include('filters.php');
	echo tabs($tabs, 'src_add');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'. lang::H2_NEW_SOURCE .'</h2>';?>
	
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']. '?tab=active'; ?>">
		<fieldset>
			<div class="row">
				<label for="name"><?=lang::HDR_ITEM_NAME;?>*:</label>
				<input name="name" type="text" value="<?php echo $_SESSION['temp']['name']; ?>" required />
			</div>
			
		</fieldset>
		<input type="submit" value="<?php echo lang::BTN_ADD; ?>" />
	</form>
</section>

<?php 
unset($_SESSION['temp']);
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	
?> 
