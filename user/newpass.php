<?php 
$access = 1;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

if(isset($_POST["changePass"])) {
	if($_POST["pass"] === $_POST["pass2"]) {
	
		$statement = $pdo->prepare("SELECT pass FROM users WHERE username = :username");  
		$statement->execute(  
			 array(  
				  'username'     =>     $_SESSION["username"]
			 )  
		);  
		$count = $statement->rowCount();  
		if($count > 0) {
				$auth = $statement->fetch();
			if(password_verify($_POST['oldPass'], $auth['pass'])) {
				$newPass = password_hash($_POST["pass"], PASSWORD_DEFAULT);
				
				$sql = "UPDATE users 
					SET pass= :pass
					WHERE username= :username AND id = :id";
				try{
					$stmt = $pdo->prepare($sql);
					$stmt -> bindValue(':pass', $newPass, PDO::PARAM_STR);
					$stmt -> bindValue(':username', $_SESSION["username"], PDO::PARAM_STR);
					$stmt -> bindValue(':id', $_SESSION["userID"], PDO::PARAM_INT);
					if ($stmt ->execute()) $_SESSION['success'] = lang::SUCCESS_PASS;
					else $_SESSION['error'] = lang::ERR_GENERAL;
				} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
				
			} else $_SESSION['error'] = lang::ERR_PASS;
			
			
			$pdo=NULL;
		}
	} else $_SESSION['error'] = lang::ERR_PASS_DONT_MATCH;			
	header( 'location: profile.php');
	exit();
}		?>

<form id="ch_pass_form"	name="ch_pass_form"	method="post" action="/user/newpass.php"
	style="
		position: absolute;
		top: 0;
		width: 100%;
		height: 100%;
		left: 0;
		background-color: rgba(0, 0, 0, 0.5);
		max-width: unset;">
	<fieldset style="
			position: absolute;
			top: 150px;
			left: 50%;
			transform: translateX(-50%);
			background: var(--clr-bg);
			padding: var(--padding-std);">
		<div class="row">
			<input type="password" name="oldPass" required placeholder="<?php echo lang::OLD_PASS; ?>" autofocus />
		</div>
		<div class="row col-2e">
			<input type="password" name="pass"  required	placeholder="<?php echo lang::NEW_PASS; ?>" />
			<input type="password" name="pass2" required	placeholder="<?php echo lang::CONFIRM_PASS; ?>"	/>

			<button type="submit" form="ch_pass_form" name="changePass" value="Submit">
				<?=lang::BTN_CHANGE;?>
			</button>
			<button type="button" id="cancel">
				<?=lang::BTN_CANCEL;?>
			</button>
		</div>
	</fieldset>
</form>

<script>
	$("#cancel").click(function () {
		$("#ch_pass_form").remove();
	});
</script>
