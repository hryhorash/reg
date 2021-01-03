<?php 
$access = 0;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

if(isset($_POST["restore"])) {
	$query = "SELECT id, username, archive 
			FROM users 
			WHERE email = :email";
	$statement = $pdo->prepare($query);  
		$statement->execute(  
			 array(  
				  'email'			=>	$_POST["email"]
			 )  
		);  
		$count = $statement->rowCount();  
		if($count > 0) {
			$profile = $statement->fetch();
			
			if ($profile['archive'] == 1) {
				$_SESSION['error'] = lang::ERR_BLOCKED;
				//Возврат к форме авторизации
				header('Location: /index.php');
				exit;
			}
			
			function randomPassword() {
				$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
				$pass = array(); //remember to declare $pass as an array
				$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
				for ($i = 0; $i < 8; $i++) {
					$n = rand(0, $alphaLength);
					$pass[] = $alphabet[$n];
				}
				return implode($pass); //turn the array into a string
			}
			
			
			$username = $profile['username'];  
			$tempPass = randomPassword();
			
			// запись нового пароля в БД
			
			$hash = password_hash($tempPass, PASSWORD_DEFAULT);
				
			$sql = "UPDATE users 
				SET pass= :pass
				WHERE username= :username AND id = :id";
			try{
				$stmt = $pdo->prepare($sql);
				$stmt -> bindValue(':pass', $hash, PDO::PARAM_STR);
				$stmt -> bindValue(':username', $username, PDO::PARAM_STR);
				$stmt -> bindValue(':id', $profile['id'], PDO::PARAM_INT);
				if ($stmt ->execute()) {
					$_SESSION['success'] = lang::EMAIL_SENT . $_POST["email"];
			
					//Отправка е-мейла
					$subject = lang::EMAIL_SUBJECT;
					$msg = '
						<html>
							<head>
							<title>' . $subject . '</title>
							</head>
							<body>
							<p>' . lang::EMAIL_YOUR_DATA .'</p>
							<p><a href="http://' . $_SERVER['SERVER_NAME'] . '">' . $_SERVER['SERVER_NAME'] . '</a></p>
							<p>'.lang::USERNAME.': ' . $username . '</p>
							<p>'.lang::PASS.': ' . $tempPass . '<br />&nbsp;</p>
							<p>'.lang::EMAIL_OPTIONS.'<br />&nbsp;</p>
							<p><strong>'.lang::EMAIL_WARNING.'</strong></p>
							</body>
							</html>
					';

					// Always set content-type when sending HTML email
					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
					$headers .= 'From: <noreply@' . $_SERVER['SERVER_NAME'] . '>' . "\r\n";

					// send email
					mail($_POST["email"],$subject,$msg,$headers);
					
					
				} 
			} catch (PDOException $ex){
				$_SESSION['error'] = lang::ERR_GENERAL;
				include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
			}
			
		} else {
			$_SESSION['error'] = lang::ERR_NO_SUCH_EMAIL; 
		}
		
		//Возврат к форме авторизации
		header('Location: /index.php');
		exit;
} 

			
$pdo=NULL;	
	
$title = lang::H2_RESTORE_PASS;		
//-----------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="content flex">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	?>


	<form method="post" name="auth">  
		<fieldset name='auth' class="autoMargin">
		<h2><?=lang::H2_RESTORE_PASS;?></h2>  

		<div class="inline">	
			<input type="email" name="email" required placeholder="<?=lang::HDR_RESTORE_EMAIL;?>" class="marginRight" style="flex:1;"/>
			
			<input type="submit" name="restore" style="flex:0.5;" value="<?=lang::HANDLING_RESTORE;?>" />  
		</div>	
			
		
		</fieldset>
	</form>  
</section>  

	

<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>