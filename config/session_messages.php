<?php if (isset($_SESSION['alert'])){
	$errMessage = $_SESSION['alert'];
	echo "<script>alert('$errMessage');</script>";
	unset($_SESSION['alert']);
	$errMessage = null;
}
if (isset($_SESSION['success'])){ ?>
	<div class="alert green">
		<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
		<?php echo $_SESSION['success'];
	echo '</div>';
	unset($_SESSION['success']);
}

if (isset($_SESSION['error'])){?>
	<div class="alert red">
		<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
		<?php echo $_SESSION['error'];
	echo '</div>';
	unset($_SESSION['error']);
}

if (isset($_SESSION['details'])){
	echo '<div class="details">' . $_SESSION['details'] . '</div>';
	unset($_SESSION['details']);
}
?>