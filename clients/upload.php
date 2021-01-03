<?php $access = 10;
	include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
	$target_dir = $_SERVER['DOCUMENT_ROOT'] . "/clients/photo/";
	$uploadOk = 1;
	$imageFileType = strtolower(pathinfo(basename($_FILES["photo"]["name"]),PATHINFO_EXTENSION));
	
	$target_file = $target_dir . $_POST['id'] . '.' . $imageFileType;
	$relative_file_path = "/clients/photo/" . $_POST['id'] . '.' . $imageFileType;
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
	 //$_SESSION['error'] = lang::ERR_FILE_ALREADY_EXISTS;
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
			
			$sql = "UPDATE clients 
				SET
					photo		= :photo, 
					`timestamp`	= :timestamp, 
					author		= :author
				WHERE id = :id";
			try {
				$stmt = $pdo->prepare($sql);
				$stmt -> bindValue(':photo', $relative_file_path, PDO::PARAM_STR);
				$stmt -> bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
				$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
				$stmt -> bindValue(':id', $_POST["id"], PDO::PARAM_INT);
				$stmt ->execute();
			} catch (PDOException $ex){
				include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
				$_SESSION['error'] = $ex;
			}

			  
		} else {
			//действия при ошибке загрузки
		}
	}
	header( 'Location: /clients/client_profile.php?id='.$_POST['id']);
	exit;
?> 