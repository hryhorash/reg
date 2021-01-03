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
				
				header( 'location: profile.php');
				exit();
			} else $_SESSION['error'] = lang::ERR_PASS;
			
			
			$pdo=NULL;
		}
	} else $_SESSION['error'] = lang::ERR_PASS_DONT_MATCH;			
}		

$title = $_SESSION['name'];
//-----------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="content flex">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');	?>
	<h2><?=$_SESSION['name'];?></h2>
	<form method="post" style="max-width: 510px;">  
		<fieldset class="autoMargin">
		<div class='row' style="width:100%;">
			<input type="password" name="oldPass" required placeholder="<?php echo lang::OLD_PASS; ?>" />
		</div>
		<div class='row nested' style="width:100%;">	
			<input type="password" name="pass" required placeholder="<?php echo lang::NEW_PASS; ?>"  />
			<input type="password" name="pass2" required placeholder="<?php echo lang::CONFIRM_PASS; ?>" />
			
		</div>	
		<input type="submit" name="changePass" id="button" value="<?php echo lang::BTN_CHANGE; ?>" />  
		</fieldset>
	</form>  
</section>  


<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>