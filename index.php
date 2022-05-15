<?php 
session_start();
include($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');

// Подгружаем базовые настройки
$q = $pdo->prepare("SELECT `name`, `country`, `currency`, `logoURL`, `defaultLang` FROM `settings` LIMIT 1");
$q ->execute();
$_SESSION['settings'] = $q->fetch(PDO::FETCH_ASSOC);

if(isset($_SESSION['settings']['defaultLang'])) $_SESSION['lang'] = $_SESSION['settings']['defaultLang'];
if(isset($_GET['lang'])) $_SESSION['lang'] = $_GET['lang'];


include_once($_SERVER['DOCUMENT_ROOT'].'/config/lang_select.php');

$res = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
			
	if(!isset($_POST['g-recaptcha-response']) && isset($_POST["login"])) {
		include($_SERVER['DOCUMENT_ROOT'].'/config/auth_check.php');
	} else {
		function post_captcha($user_response) {
			$fields_string = '';
			$fields = array(
				'secret' => '6LfoV8cZAAAAACaAkOcUzoNJ_5wuOPMUgGP79CwT',  // РАБОЧИЙ
				//'secret' => '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe', // Тестовый
				'response' => $user_response
			);
			foreach($fields as $key=>$value)
			$fields_string .= $key . '=' . $value . '&';
			$fields_string = rtrim($fields_string, '&');

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
			curl_setopt($ch, CURLOPT_POST, count($fields));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);

			$result = curl_exec($ch);
			curl_close($ch);

			return json_decode($result, true);
		}
		
		// Call the function post_captcha
		$res = post_captcha($_POST['g-recaptcha-response']);
		if ($res['success'] == true) {
			// If CAPTCHA is successfully completed...
			include($_SERVER['DOCUMENT_ROOT'].'/config/auth_check.php');
		} else {
			// What happens when the CAPTCHA wasn't checked
			$_SESSION['error'] = lang::ERR_CAPTCHA;
		}	
	}
}


// ИТЕРАЦИЯ ПОПЫТОК ВВОДА ПАРОЛЯ
if(isset($_SESSION['attempt'])) $_SESSION['attempt'] = $_SESSION['attempt'] + 1;

$title = lang::TITLE_LOGIN;		
//---------------------view------------------------
require_once($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="content flex">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	?>  
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>

	<form name="auth" method="post">  
		<fieldset name="auth" class="autoMargin padding">
		<h2><?php echo lang::HDR_LOGIN;?></h2>  

			<div class="row">
				<input type="text" name="username" required placeholder="<?php echo lang::USERNAME; ?>" autofocus />
				<input type="password" name="pass" required placeholder="<?php echo lang::PASS; ?>" />
			</div>

			<?php 
			switch (true) {
				case (isset($_GET["fwd"]) && isset($_GET["goto"])):	
					echo '<input name="fwd" type="hidden" value="'. $_GET["fwd"] .'&goto='.$_GET["goto"] .'">';
					break;
				case (isset($_GET["fwd"])):
					echo '<input name="fwd" type="hidden" value="'. $_GET["fwd"] .'">';
					break;
			}
			if($_SESSION['attempt'] > 3 ) {
			echo '<div class="g-recaptcha" data-sitekey="6LfoV8cZAAAAAOnACO5FLFkHbRJ6rilvG3uJYxZE"></div>'; // РАБОЧИЙ
			//echo '<div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div>'; // ТЕСТОВЫЙ
			} ?>
		<div class="inline" style="line-height: 1.5em;">
			<input type="submit" name="login" style="flex:0.5;" value="<?php echo lang::BTN_ENTER;?>" />  
			
		<a href="/user/restore_pass.php" class="alignRight grey"><?php echo lang::RESTORE_PASS; ?></a>
		</div>	
		
		</fieldset>
	</form> 
</section>
<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>