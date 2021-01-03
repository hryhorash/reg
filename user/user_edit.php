<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	try {
		if($_POST['loc'] =='') 
		{
			$_SESSION['error'] = lang::ERR_SELECT_LOCATION;
			session_write_close();
			header( 'Location: ' . $_SERVER['PHP_SELF'] . '?id=' . $_POST['id']);
			exit;
		}	
		
		if($_POST['role'] == 'basic' && $_POST['specialty'] == '') {
			$_SESSION['error'] = lang::ERR_SELECT_SPECIALTY;
			session_write_close();
			header( 'Location: ' . $_SERVER['PHP_SELF'] . '?id=' . $_POST['id']);
			exit;
		}
		
		$phones = phonesSQL($_POST['phones']); //преобразуем массив в строку
		
		$sql = "UPDATE users 
			SET username=:username,
				name=:name,
				surname=:surname,
				email=:email,
				phones=:phones,
				role=:role,
				note=:note,
				`timestamp`	= :timestamp,
				author=:author
			WHERE id= :id";
		
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':username', $_POST["username"], PDO::PARAM_STR);
		$stmt -> bindValue(':name', $_POST["name"], PDO::PARAM_STR);
		$stmt -> bindValue(':surname', $_POST["surname"], PDO::PARAM_STR);
		$stmt -> bindValue(':email', $_POST["email"], PDO::PARAM_STR);
		$stmt -> bindValue(':phones', $phones, PDO::PARAM_STR);
		$stmt -> bindValue(':role', $_POST["role"], PDO::PARAM_STR);
		$stmt -> bindValue(':note', $_POST["note"], PDO::PARAM_STR);
		$stmt -> bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt -> bindValue(':id', $_POST['id'], PDO::PARAM_INT);
		$stmt ->execute();
		$_SESSION['success'] = lang::SUCCESS_GENERAL;
		
		if($_POST['specialty'] != '') {
			$add = $pdo->prepare("INSERT INTO users_specialty (userID, specialtyID, reward_rate, author) VALUES(:userID, :specialtyID, :reward_rate, :author)");
			$add -> bindValue(':userID', $_POST["id"], PDO::PARAM_INT);
			$add -> bindParam(':specialtyID', $specialtyID, PDO::PARAM_INT);
			$add -> bindParam(':reward_rate', $reward_rate, PDO::PARAM_INT);
			$add -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
			
			
			$update = $pdo->prepare("UPDATE users_specialty 
									SET reward_rate = :reward_rate,
										`timestamp` = null,
										author		= :author
									WHERE userID = :userID AND specialtyID = :specialtyID");
			$update -> bindValue(':userID', $_POST["id"], PDO::PARAM_INT);
			$update -> bindParam(':specialtyID', $specialtyID, PDO::PARAM_INT);
			$update -> bindParam(':reward_rate', $reward_rate, PDO::PARAM_INT);
			$update -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
			
			
			
			$delete = $pdo->prepare("DELETE FROM users_specialty WHERE userID=:userID AND specialtyID=:specialtyID");
			$delete -> bindValue(':userID', $_POST["id"], PDO::PARAM_INT);
			$delete -> bindParam(':specialtyID', $specialtyID_old, PDO::PARAM_INT);
			
			if($_POST['specialtyIDs_old'] !='') {
				$specialtyIDs_old=explode(',',$_POST['specialtyIDs_old']);
				sort($specialtyIDs_old);
				$i=0;
				foreach ($_POST['specialty'] as $specialtyID){
						//add
					if(!in_array($specialtyID, $specialtyIDs_old)){
						$reward_rate = $_POST['reward_rate'][$i];
						$add ->execute();
					} else {
						//change
						if ($_POST['reward_rate_old'][$i] != $_POST['reward_rate'][$i]){
							$reward_rate = $_POST['reward_rate'][$i];
							$update ->execute();
						}
					}	
					$i++;
				}
				
						//delete
				foreach($specialtyIDs_old as $specialtyID_old) {
					if(!in_array($specialtyID_old, $_POST["specialty"])) $delete ->execute();
				}
			} else {
				$i=0;
				foreach ($_POST['specialty'] as $specialtyID){
					$reward_rate = $_POST['reward_rate'][$i];
					$add ->execute();
					$i++;
				}	
					
			} 
				
		}
		
		//Обрабатываем список локаций
		$addLoc = $pdo->prepare("INSERT INTO users_locations (userID, locationID, author) VALUES(:userID, :locationID, :author)");
		$addLoc -> bindValue(':userID', $_POST["id"], PDO::PARAM_INT);
		$addLoc -> bindParam(':locationID', $locID, PDO::PARAM_INT);
		$addLoc -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		
		
		$deleteLoc = $pdo->prepare("DELETE FROM users_locations WHERE userID=:userID AND locationID=:locationID");
		$deleteLoc -> bindValue(':userID', $_POST["id"], PDO::PARAM_INT);
		$deleteLoc -> bindParam(':locationID', $locationID_old, PDO::PARAM_INT);
		
		$locationIDs_old = explode(",", $_POST['locationIDs_old']);
		foreach ($_POST['loc'] as $locID){
			if(!in_array($locID, $locationIDs_old))
					$addLoc ->execute();
			else	$addLoc ->execute();
		}
		
		foreach($locationIDs_old as $locationID_old) {
			if(!in_array($locationID_old, $_POST["loc"])) $deleteLoc ->execute();
		}
		
		
		
		
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
	}
	session_write_close();
	header( 'Location: /user/userList.php?tab='.$_GET['tab']);
	exit;
}


if($_GET['id'] !=''){
	try {
		$stmt = $pdo->prepare("SELECT users.id,username,users.name,surname,email,role,phones,note,users.archive,GROUP_CONCAT(DISTINCT specialtyID) as specialtyIDs,GROUP_CONCAT(DISTINCT locationID) as locationIDs
		FROM `users` 
		LEFT JOIN users_locations ON users.id = users_locations.userID
		LEFT JOIN users_specialty ON users.id = users_specialty.userID
		WHERE users.id=:id");
		$stmt -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
		$stmt ->execute();
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}

	
	//проверить права на редактирование воизбежание простого перебора id-шками
	if (handle_rights($user['role'], $user['locationIDs']) != 1) 
	{		
		$_SESSION['error'] = lang::ERR_NO_RIGHTS;
		session_write_close();
		header( 'Location: /user/userList.php?tab='.$_GET['tab']);
		exit;
	}
	
	$phones = explode(',',$user['phones']);
	
} else {
	$_SESSION['error'] = lang::ERR_NO_ID;
	session_write_close();
	header( 'Location: /user/userList.php?tab='.$_GET['tab']);
	exit;
}

$title=lang::HDR_ACCESS_EDIT .$user['name'].' '.$user['surname'];
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs);
echo '</section>';
echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'. lang::HDR_ACCESS_EDIT .$user['name'].' '.$user['surname'].'</h2>';?>
	<form method="post">
		<fieldset>
			<div class="row">
				<label for="name"><?=lang::NAME;?>*:</label>
				<input name="name" type="text" value="<?php echo $user['name']; ?>" required />
			</div>
			<div class="row">
				<label for="surname"><?=lang::SURNAME;?>*:</label>
				<input name="surname" type="text" value="<?php echo $user['surname']; ?>" required />
			</div>
			<div class="row">
				<label for="username"><?=lang::USERNAME;?>*:</label>
				<input name="username" type="text" value="<?php echo $user['username']; ?>" required />
			</div>
			<div class="row">
				<label for="email"><?=lang::HDR_EMAIL;?>*:</label>
				<input name="email" type="email" value="<?php echo $user['email']; ?>" required /> 
			</div>
			<div id="morePhones">
				<?=phones_add($phones);?>
			</div>
			<div class="row">
				<label for="role"><?=lang::HDR_ROLE;?>*:</label>
				<select name="role" id="role" required>
					<?php echo role_options($user['role']); ?>
				</select>
			</div>
			
			<div id="locList">
				<?=location_options('','',$user['locationIDs']);?>
			</div>
			
			
			<div class="row" id="basicOnly" <?php //if($user['role'] != 'basic') echo 'style="display:none;"';?>>
				<label for="specialty[]"><?=lang::HDR_WORKTYPE_CATS;?>:</label>
				<?=work_cat_select('check', $user['specialtyIDs'], null, $_GET['id']);?>
			</div>
			
			<div class="row">
				<textarea name="note" placeholder="<?=lang::COMMENT_PLACEHOLDER;?>"><?=$user['note']; ?></textarea>
			</div>
			
			<input name="id" type="hidden" value="<?=$_GET['id']?>" />
			<input name="locationIDs_old" type="hidden" value="<?=$user['locationIDs'];?>" />
			<input name="specialtyIDs_old" type="hidden" value="<?=$user['specialtyIDs'];?>" />
			
				
		</fieldset>
		<input type="submit" value="<?=lang::BTN_CHANGE;?>" />
	</form>
</section>

<template id="rate">
<input name="reward_rate[]" class="short" type="number" min="0" max="100" step="1" placeholder="<?=lang::HDR_RATE_PLACEHOLDER;?>" style="margin-right:10px;" required />
<input name="reward_rate_old[]" type="hidden" value="0" />
</template>


<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	
unset($_SESSION['temp']);
?> 
<script>
	
$("input[name='specialty[]']").change( function() {
	var oClone = document.querySelector("#rate").content.cloneNode(true);

	if( $(this).is(':checked') ) {
		$(this).parent().append(oClone);
	} else {
		$(this).siblings("input[name='reward_rate[]']").remove();
		$(this).siblings("input[name='reward_rate_old[]']").remove();
	}
	 
});

 /*
$(document).ready(function(){
	$('#lang').val('<?php echo $_SESSION['lang']; ?>');
	$('#role').val('<?php echo $_SESSION['temp']['role']; ?>');
	$("#role").change(function() {
		var loc = '<?=$loc[0]; ?>';
		var role = $("#role").val();
	
		if (role == 'godmode') {
			$("#locList").hide();
			$("#basicOnly").hide();
			
		} else {
			$("#locList").show();
			if ((role == 'basic')) {
				$("#basicOnly").show();
			} else {
				$("#basicOnly").hide();
			}
			
		}
		 
	});
});
*/


</script>