<?php 
$access = 1;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	try {
		$stmt = $pdo->prepare("UPDATE users SET lang=:lang	WHERE id= :id");
		$stmt -> bindValue(':lang', $_POST['lang'], PDO::PARAM_STR);
		$stmt -> bindValue(':id', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt ->execute();
		$_SESSION['success'] = lang::SUCCESS_GENERAL;
		$_SESSION['lang'] = $_POST['lang'];
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
	}
	session_write_close();
	header( 'Location: /user/profile.php');
	exit;
	
}

if ($_SESSION["role"] === 'godmode') {
	$locationName = lang::GODMODE_LOCATIONS;
} elseif (filter_var($_SESSION["locationIDs"], FILTER_VALIDATE_INT) == TRUE) {

	/*$query = "SELECT users.location as locationID, locations.name as locationName
			FROM users 
			LEFT JOIN locations ON users.location = locations.id
			WHERE username = :username AND users.id = :id";
	$statement = $pdo->prepare($query);  
		$statement->execute(  
			 array(  
				  'username'    =>  $_SESSION["username"],
				  'id'			=>	$_SESSION["userID"]
			 )  
		);  
		$profile = $statement->fetch();
		$location = $profile['locationName'];  */
		$locationName = $_SESSION['locationName'];
		
} else {}

			
$pdo=NULL;		

$title = $_SESSION['name'] . ' ' . $_SESSION['surname'];		
//-----------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	
echo '</section>';
echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php'); ?>
	<h2><? echo $_SESSION['name'] . ' ' . $_SESSION['surname'];?></h2>
	<table>
		
		<tr>
			<td><?php echo lang::USERNAME;?>:</td>
			<td><?=$_SESSION['username']?></td>
		</tr>
		
		<tr>
			<?php if (isset($locationName)) {
				echo '<td>'.lang::HDR_LOCATION.':</td>
				<td>' . $locationName .'</td>';
			} else {
				echo '<td>'.lang::HDR_LOCATION_PLURAL.':</td>
				<td>'; echo location_names_only($_SESSION["locationIDs"]); echo '</td>';
			}?>
		</tr>
		<tr>
			<td><?php echo lang::HDR_ROLE;?>:</td>
			<td><?=$_SESSION["roleName"]?></td>
		</tr>
		<tr>
			<td><?php echo lang::LANGUAGE;?>:</td>
			<td><?php echo select_lang();?></td>
		</tr>
	</table>
	<a href="newpass.php" id="button" class="marginLeft"><?php echo lang::HDR_CHANGE_PASS;?></a>

</section>			
<script>
$(document).ready(function(){
	$('#lang').val('<?php echo $_SESSION['lang']; ?>');
});
</script>			

<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>