<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$_SESSION['temp'] = array(
		'name' 			=> $_POST['name'],
		'surname' 		=> $_POST['surname'],
		'prompt'		=> $_POST['prompt'],
		'refClient'		=> $_POST['refClient'],
		'refClientID'	=> $_POST['clientID'],
		'phones' 		=> $_POST['phones'],
		'email' 		=> $_POST['email'],
		'DOB' 			=> $_POST['DOB'],
		'gender' 		=> $_POST['gender'],
		'note' 			=> $_POST['note'],
		'sourceID' 		=> $_POST['sourceID'],
		'locationID' 	=> $_POST['loc']
	);
		
		
	if ($_POST['clientID'] == '' && $_POST['refClient'] !='') {
		$_SESSION['error'] = lang::ERR_NO_RECOMMENDATION;
		session_write_close();
		if ($_POST['backTo'] !='') {
			header( 'Location: ' . $_POST['backTo']);
			exit;
		} else {
			header( 'Location: /clients/client_add.php');
			exit;

		}
	}
	
	if ($_POST['gender'] == '') {
		$_SESSION['error'] = lang::ERR_NO_GENDER;
		session_write_close();
		if ($_POST['backTo'] !='') {
			header( 'Location: ' . $_POST['backTo']);
			exit;
		} else {
			header( 'Location: /clients/client_add.php');
			exit;

		}
	}
	
	
	$phones = phonesSQL($_POST['phones']); //преобразуем массив в строку
		
	
	
	$sql = "INSERT INTO clients (photo, name, surname, prompt, refClientID, phones, email, DOB, gender, note, sourceID, locationID, author) VALUES(:photo, :name, :surname, :prompt, :refClientID, :phones,  :email, :DOB, :gender, :note, :sourceID, :locationID, :author)";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':photo', $target_file, PDO::PARAM_STR);
		$stmt -> bindValue(':name', $_POST["name"], PDO::PARAM_STR);
		if($_POST["surname"] !='')
			 $stmt -> bindValue(':surname', $_POST["surname"], PDO::PARAM_STR);
		else $stmt -> bindValue(':surname', null, PDO::PARAM_STR);
		if($_POST["prompt"] !='')	
			$stmt -> bindValue(':prompt', $_POST["prompt"], PDO::PARAM_STR);
		else $stmt -> bindValue(':prompt', null, PDO::PARAM_STR);
		if($_POST["clientID"] !='')
			$stmt -> bindValue(':refClientID', $_POST["clientID"], PDO::PARAM_INT);
		else $stmt -> bindValue(':refClientID', 0, PDO::PARAM_STR);
		$stmt -> bindValue(':phones', $phones, PDO::PARAM_STR);
		if($_POST["email"] !='')
			$stmt -> bindValue(':email', $_POST["email"], PDO::PARAM_STR);
		else $stmt -> bindValue(':email', null, PDO::PARAM_STR);
		if($_POST["DOB"] !='')
			 $stmt -> bindValue(':DOB', $_POST['DOB'], PDO::PARAM_STR);
		else $stmt -> bindValue(':DOB', null, PDO::PARAM_STR);
		$stmt -> bindValue(':gender', $_POST['gender'],  PDO::PARAM_INT);
		if($_POST["note"] !='')
			$stmt -> bindValue(':note', $_POST["note"], PDO::PARAM_STR);
		else $stmt -> bindValue(':note', null, PDO::PARAM_STR);
		$stmt -> bindValue(':sourceID', $_POST["sourceID"], PDO::PARAM_INT);
		$stmt -> bindValue(':locationID', $_POST["loc"], PDO::PARAM_INT);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt ->execute();
		$clientID = $pdo->lastInsertId();
		
		if (isset($_FILES["photo"]) && $_FILES["photo"]["name"] != '') {
		
			$target_dir = $_SERVER['DOCUMENT_ROOT'] . "/clients/photo/";
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo(basename($_FILES["photo"]["name"]),PATHINFO_EXTENSION));
			
			$target_file = $target_dir . $clientID . '.' . $imageFileType;
			$relative_file_path = "/clients/photo/" . $clientID . '.' . $imageFileType;
			// Check if image file is a actual image or fake image
			  $check = getimagesize($_FILES["photo"]["tmp_name"]);
			  if($check !== false) {
				$uploadOk = 1;
			  } else {
				$_SESSION['error'] = lang::ERR_NOT_AN_IMG;
				$uploadOk = 0;
			  }
			
			// Check if file already exists
			if (file_exists($target_file)) {
			 unlink ($target_file);
			}

			// Check file size
			if ($_FILES["photo"]["size"] > 500000) {
			  $_SESSION['error'] = lang::ERR_FILESIZE;
			  $uploadOk = 0;
			}

			// Allow certain file formats
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
			&& $imageFileType != "gif" ) {
			  $_SESSION['error'] = lang::ERR_FILETYPE;
			  $uploadOk = 0;
			}

			if ($uploadOk == 0) {  // Check if $uploadOk is set to 0 by an error
			
			} else {	// if everything is ok, try to upload file
				if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
					try {
						$sql = "UPDATE clients 
							SET
								photo		= :photo, 
								`timestamp`	= :timestamp, 
								author		= :author
							WHERE id = :id";
						
						$stmt = $pdo->prepare($sql);
						$stmt -> bindValue(':photo', $relative_file_path, PDO::PARAM_STR);
						$stmt -> bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
						$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
						$stmt -> bindValue(':id', $clientID, PDO::PARAM_INT);
						$stmt ->execute();
					} catch (PDOException $ex){
						include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
						$_SESSION['error'] = $ex;
					}
				}
			}
		}
		//Конец загрузки файла
		
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL_ADD;
		unset($_SESSION['temp']);

		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
	}
	session_write_close();
	
	if ($_POST['backTo'] !='') {
		header( 'Location: ' . $_POST['backTo']);
		exit;
	} else {
		header( 'Location: /clients/client_list.php');
		exit;

	}
}

$title = lang::H2_NEW_CLIENT;
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	include('filters.php');
	echo tabs($tabs, 'clt_add');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'. lang::H2_NEW_CLIENT .'</h2>'; ?>
	<form method="post" enctype="multipart/form-data">
		<fieldset>
			<div class="row col-2">
				<label for="name"><?php echo lang::NAME; ?>*:</label>
				<input name="name" type="text" value="<?php echo $_SESSION['temp']['name']; ?>" autofocus required />
				<label for="surname"><?php echo lang::SURNAME; ?>:</label>
				<input name="surname" type="text" value="<?php echo $_SESSION['temp']['surname']; ?>" />
				<label for="prompt"><?php echo lang::HDR_PROMPT; ?>:</label>
				<input name="prompt" type="text" value="<?php echo $_SESSION['temp']['prompt']; ?>" />
			</div>
			<div id="morePhones" class="one-col">
				<?=phones_add();?>
			</div>
			<div class="row col-2">
				<label for="email"><?php echo lang::HDR_EMAIL; ?>:</label>
				<input name="email" type="email" value="<?=$_SESSION['temp']['email']; ?>" /> 
				<label for="DOB"><?php echo lang::HDR_DOB; ?>:</label>
				<input name="DOB" type="date" value="<?=$_SESSION['temp']['DOB']; ?>" /> 
				<label><?php echo lang::HDR_GENDER; ?>*:</label>
				<div class="flex">
					<input name="gender" type="checkbox" value="1" <?php if($_SESSION['temp']['gender']==1) echo 'checked'; ?>/> 
					<label><?=lang::HDR_MALE;?></label>
					<input name="gender" type="checkbox" value="0"  <?php if(isset($_SESSION['temp']['gender']) && $_SESSION['temp']['gender']==0) echo 'checked'; ?>/> 
					<label><?=lang::HDR_FEMALE;?></label>
				</div>
				<label for="sourceID"><?php echo lang::HDR_CLIENT_SOURCE; ?>*:</label>
				<?=client_source_select($_SESSION['temp']['sourceID']);?>
			</div>
			
			<div class="row" id="refClient" 
			<?php if($_SESSION['temp']['refClient'] =='') echo 'style="display:none;"';?> >
				<label for="refClient"><?php echo lang::HDR_RECOMMENDATION; ?>:</label>
				<input name="refClient" class="FIO" type="text" value="<?php echo $_SESSION['temp']['refClient']; ?>" />
				<input name="clientID" type="hidden" value="<?php echo $_SESSION['temp']['refClientID']; ?>" />
			</div>
			
			
			<div id="locList" class="row col-2">
			<?php echo location_options(1,"","",1); ?>
			</div>
			
			<div class="row col-2">
				<label for="photo"><?php echo lang::HDR_PHOTO; ?>:</label>
				<input name="photo" type="file" style="line-height:1em; border:none;margin-left: -5px;">
			</div>
			
			<div class="row">
				<textarea name="note" placeholder="<?=lang::COMMENT_PLACEHOLDER;?>"><?=$_SESSION['temp']['note']; ?></textarea>
			</div>
			
			<input name="backTo" type="hidden" value="<?=$_GET['backTo']?>"/>
		</fieldset>
		<input type="submit" value="<?php echo lang::BTN_ADD; ?>" />
	</form>
</section>

<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');
unset($_SESSION['temp']);	
?> 
<script>
	$('select[name="sourceID"]').change(function() {
	
	var refID = $('select[name="sourceID"]').val();
	
	if ( refID == 2) 
	{
		$('#refClient').show();
	} else {
		$('#refClient').hide();
		$('input[name="refClient"]').val('');
		$('input[name="сlientID"]').val('');
	}
		
	});
</script>