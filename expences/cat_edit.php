<?php $access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	if (!isset($_POST["inmenu"])) $inmenu = 1;
	else $inmenu = $_POST["inmenu"];
	
	if ($_POST['catID_autocomplete'] > 0) $catID = $_POST['catID_autocomplete'];
	else { // Если категории не существует, нужно сначала ее создать и получить catID
		$q = "INSERT INTO expences_cat (category, author)
			VALUES(:category, :author)";
		$stmt = $pdo->prepare($q);
		$stmt->bindValue(':category', $_POST['category'], PDO::PARAM_STR); 
		$stmt->bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT); 
		$stmt->execute();
		$catID = $pdo->lastInsertId();
	}
		
	$sql = "UPDATE expences_subcat 
				SET catID		= :catID, 
					subcategory	= :subcategory, 
					inmenu		= :inmenu,
					`timestamp`	= :timestamp, 
					author		= :author
				WHERE id = :id";
	try {
		$insert = $pdo->prepare($sql);
		$insert->bindValue(':catID', $catID, PDO::PARAM_INT); 
		$insert->bindValue(':subcategory', $_POST["subcategory"], PDO::PARAM_STR); 
		$insert->bindValue(':inmenu', $inmenu, PDO::PARAM_INT); 
		$insert->bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
		$insert->bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT); 
		$insert->bindValue(':id', $_POST['id'], PDO::PARAM_INT); 
		$insert->execute();
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL;
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = lang::ERR_GENERAL;
	}

	header('Location: /expences/catList.php?tab='.$_GET['tab']);
	exit;
}

if($_GET['id'] !=''){
	try {
		$stmt = $pdo->prepare("SELECT expences_subcat.id , category, subcategory, catID, inmenu
			FROM expences_subcat
			LEFT JOIN expences_cat ON expences_subcat.catID = expences_cat.id
            WHERE expences_subcat.id=:id");
		$stmt -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
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
$title = $data['subcategory'];
//----------------------------VIEW-------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>' . $data['subcategory'] . '</h2>';?>

	<form method="post">
		<fieldset>
			<div class="row">
				<label for="category"><?=lang::HDR_CATEGORY;?>*:</label>
				<input name="category" id="catID" class="expences_cat" type="text" value="<?=$data['category']?>" required autofocus />
				<input type="hidden" name="catID_autocomplete" value="<?=$data['catID']?>"> <!--см. js ниже -->
			</div>
			<div class="row">
				<label for="subcategory"><?=lang::HDR_SUBCATEGORY;?>*:</label>
				<input name="subcategory" type="text" value="<?=$data['subcategory'];?>" required />
			</div>
			<div class="row">
				<label for="inmenu"><?=lang::HDR_SHOW_IN_MENU;?></label>
				<div style="flex:13.7;line-height:2.9em;">
					<input name="inmenu" value="1" type="radio" <?php if($data['inmenu']==1) echo 'checked';?>/><i class="fas fa-check marginLeft" style="color:green;"></i>
					<input name="inmenu" value="0" type="radio" class="marginLeft" <?php if($data['inmenu']==0) echo 'checked';?> /><i class="fas fa-times marginLeft" style="color:red;"></i>
				</div>
			</div>
			<input name="id" type="hidden" value="<?=$data['id'];?>">
			
		</fieldset>
		<input type="submit" value="<?=lang::BTN_CHANGE;?>" />
	</form>
</section>
<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>
<script>
$(document).ready(function(){
	$('.expences_cat').keypress(function(){
		$("input[name='catID_autocomplete']").val('');
	}); 
	
  /*var xhttp;    
  xhttp = new XMLHttpRequest();
	$.ajax({
		type: "GET",
		url: "/config/autocomplete.php?catID&q="+$("input[name='catID']").val(),
		data:	{  },  
		success: function(data){
			var obj = JSON.parse(data);
			$("input[name='catID_autocomplete']").val(obj['suggestions'][0]['data']);
		  }
	});*/
});
</script>