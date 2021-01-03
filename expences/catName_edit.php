<?php $access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	try {
		$q = "UPDATE expences_cat 
			SET category	= :category, 
				`timestamp`		= :timestamp, 
				author		= :author
			WHERE id = :id";
		$stmt = $pdo->prepare($q);
		$stmt->bindValue(':category', $_POST['category'], PDO::PARAM_STR); 
		$stmt->bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
		$stmt->bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT); 
		$stmt->bindValue(':id', $_POST['id'], PDO::PARAM_INT); 
		$stmt->execute();
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL;
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = lang::ERR_GENERAL;
	}

	header('Location: /expences/catList.php?tab='.$_GET['tab']);
	exit;
}

if($_GET['name'] !=''){
	try {
		$stmt = $pdo->prepare("SELECT id, category FROM `expences_cat` WHERE category =:name");
		$stmt -> bindValue(':name', $_GET['name'], PDO::PARAM_STR);
		$stmt ->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}	
} else {
	$_SESSION['error'] = lang::ERR_NO_ID;
	session_write_close();
	header( 'Location: /expences/catList.php?tab='.$_GET['tab']);
	exit;
}

$pdo = NULL;
$title = $data['category'];
//----------------------------VIEW-------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>' . $data['category'] . '</h2>';?>

	<form action="<?php $_SERVER['PHP_SELF'].'tab=active'?>" method="post">
		<fieldset>
			<div class="row">
				<label for="category"><?=lang::HDR_CATEGORY;?>*:</label>
				<input name="category" class="expences_cat" type="text" value="<?=$data['category']?>" required autofocus />
				
			</div>
			<input name="id" type="hidden" value="<?=$data['id'];?>">
		</fieldset>

		<input type="submit" value="<?=lang::BTN_CHANGE;?>" />
		
	</form>
</section>

<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>