<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');



if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$_SESSION['temp'] = array(
		'name' 		=> $_POST['name'],
		'surname' 	=> $_POST['surname'],
		'username'	=> $_POST['username'],
		'email' 	=> $_POST['email'],
		'phones' 	=> $_POST['phones'],
		'note' 		=> $_POST['note'],
		'specialty' => $_POST['specialty'],
		'role' 		=> $_POST['role']
	);
	if($_POST['loc'] == '') {
		$_SESSION['error'] = lang::ERR_SELECT_LOCATION;
		session_write_close();
		header( 'Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
	
	if($_POST['role'] == 'basic' && $_POST['specialty'] == '') {
		$_SESSION['error'] = lang::ERR_SELECT_SPECIALTY;
		session_write_close();
		header( 'Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
	
	$pass = password_hash($_POST["pass"], PASSWORD_DEFAULT);
	$phones = phonesSQL($_POST['phones']); //преобразуем массив в строку
	
	
	$sql = "INSERT INTO users (username, pass, name, surname, email, phones, role, lang, note, author) VALUES(:username, :pass, :name, :surname, :email,  :phones, :role, :lang, :note, :author)";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':username', $_POST["username"], PDO::PARAM_STR);
		$stmt -> bindValue(':pass', $pass, PDO::PARAM_STR);
		$stmt -> bindValue(':name', $_POST["name"], PDO::PARAM_STR);
		$stmt -> bindValue(':surname', $_POST["surname"], PDO::PARAM_STR);
		$stmt -> bindValue(':email', $_POST["email"], PDO::PARAM_STR);
		$stmt -> bindValue(':phones', $phones, PDO::PARAM_STR);
		$stmt -> bindValue(':role', $_POST['role'], PDO::PARAM_STR);
		$stmt -> bindValue(':lang', $_POST["lang"], PDO::PARAM_STR);
		$stmt -> bindValue(':note', $_POST["note"], PDO::PARAM_STR);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt ->execute();
		$userID = $pdo->lastInsertId();
		
		if($_POST['specialty'] != '') {
			$worktype = $pdo->prepare("INSERT INTO users_specialty (userID, specialtyID, reward_rate, author) VALUES(:userID, :specialtyID, :reward_rate, :author)");
			$worktype -> bindValue(':userID', $userID, PDO::PARAM_INT);
			$worktype -> bindParam(':specialtyID', $specialtyID, PDO::PARAM_INT);
			$worktype -> bindParam(':reward_rate', $reward_rate, PDO::PARAM_INT);
			$worktype -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
			
			$i=0;
			while ($_POST['specialty'][$i] != null) {
				$specialtyID = $_POST['specialty'][$i];
				$reward_rate = $_POST['reward_rate'][$i];
				$worktype -> execute();
				$i++;
			}
		}
		
		$loc = $pdo->prepare("INSERT INTO users_locations (userID, locationID, author) VALUES(:userID, :locationID, :author)");
		$loc -> bindValue(':userID', $userID, PDO::PARAM_INT);
		$loc -> bindParam(':locationID', $locationID, PDO::PARAM_INT);
		$loc -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		
		foreach($_POST['loc'] as $locationID) {
			$loc -> execute();
		}
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL_ADD;
		unset($_SESSION['temp']);

		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
	}
	session_write_close();
	header( 'Location: /user/userList.php');
	exit;
}

$title = lang::HDR_NEW_USER;
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 

echo '<section class="sidebar">';
	echo tabs($tabs,'usr_add');
echo '</section>';
echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'. lang::HDR_NEW_USER .'</h2>';?>
	<form method="post">
		<fieldset>
			<div class="row col-2">
				<label for="name"><?php echo lang::NAME; ?>*:</label>
				<input name="name" type="text" value="<?php echo $_SESSION['temp']['name']; ?>" autofocus required />
			
				<label for="surname"><?php echo lang::SURNAME; ?>*:</label>
				<input name="surname" type="text" value="<?php echo $_SESSION['temp']['surname']; ?>" required />
			
				<label for="username"><?php echo lang::USERNAME; ?>*:</label>
				<input name="username" type="text" value="<?php echo $_SESSION['temp']['username']; ?>" required />
			
				<label for="pass"><?php echo lang::PASS; ?>*:</label>
				<input name="pass" type="password" required /> 
			
				<label for="email"><?php echo lang::HDR_EMAIL; ?>*:</label>
				<input name="email" type="email" value="<?=$_SESSION['temp']['email']; ?>" required /> 
			</div>
			<div id="morePhones">
				<?=phones_add();?>
			</div>

			<div class="row col-2">
				<?php echo select_lang(1); ?>
			
				<label for="role"><?php echo lang::HDR_ROLE; ?>*:</label>
				<select name="role" id="role" required>
					<?php echo role_options($_SESSION['pwr']); ?>
				</select>
			</div>
			
			<div id="locList" class="row col-2">
			<?php echo location_options(); ?>
			</div>
			
			<div class="row col-2" id="basicOnly" <?php// if($_SESSION['temp']['role'] != 'basic') echo 'style="display:none;"';?>>
				<label for="specialty[]"><?=lang::HDR_WORKTYPE_CATS;?>:</label>
				<?=work_cat_select('check');?>
			</div>
			
			<div class="row">
				<textarea name="note" placeholder="<?=lang::COMMENT_PLACEHOLDER;?>"><?=$_SESSION['temp']['note']; ?></textarea>
			</div>
			
		</fieldset>
		<input type="submit" value="<?php echo lang::BTN_ADD; ?>" />
	</form>
</section>

<template id="rate">
<input name="reward_rate[]" class="short" type="number" min="0" max="100" step="1" placeholder="<?=lang::HDR_RATE_PLACEHOLDER;?>" style="margin-right:10px;" required />
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
