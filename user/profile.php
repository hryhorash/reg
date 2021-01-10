<?php 
$access = 1;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');


if (isset($_GET['lang'])) {
	try {
		$stmt = $pdo->prepare("UPDATE users SET lang=:lang	WHERE id = :id");
		$stmt -> bindValue(':lang', $_GET['lang'], PDO::PARAM_STR);
		$stmt -> bindValue(':id', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt ->execute();
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
	}
	session_write_close();
	header( 'Location: /user/profile.php/');
	exit;
	
}

if ($_SESSION["role"] === 'godmode') {
	$locationName = lang::GODMODE_LOCATIONS;
} elseif (filter_var($_SESSION["locationIDs"], FILTER_VALIDATE_INT) == TRUE) {
	$locationName = $_SESSION['locationName'];		
} 
			
$pdo=NULL;		

$title = $_SESSION['name'] . ' ' . $_SESSION['surname'];		
//-----------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	
echo '</section>';
echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php'); ?>
	<h2><? echo $_SESSION['name'] . ' ' . $_SESSION['surname'];?></h2>
	<form>
		<fieldset>

			<div class="row col-2">
				<label><?php echo lang::USERNAME;?>:</label>
				<input value="<?=$_SESSION['username']?>" disabled />
		
		
		
				<?php if (isset($locationName)) {
					echo '<label>'.lang::HDR_LOCATION.':</label>
					<input value="' . $locationName .'" disabled />';
				} else {
					echo '<label>'.lang::HDR_LOCATION_PLURAL.':</label>
					<input value="'; echo location_names_only($_SESSION["locationIDs"]); echo '" disabled />';
				}?>
		
				<label><?=lang::HDR_ROLE;?>:</label>
				<input value="<?=$_SESSION["roleName"]?>" disabled />
			
				<?php echo select_lang();?>
			</div>
		</fieldset>
	</form>
	<button id="ch_pass"><?php echo lang::HDR_CHANGE_PASS;?></button>

</section>			
<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>
<script>
 	$('select[name="lang"]').change(function(){
		window.location.href = "profile.php?lang="+ $('select[name="lang"]').val();
	})

	$('#ch_pass').click(function(){
		$.ajax({
				type: "GET",
				url:
					"/user/newpass.php",
				success: function (data) {
					$('body').append(data);
				},
			});
	});
</script>