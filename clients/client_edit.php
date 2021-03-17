<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if ($_POST['clientID'] == '' && $_POST['refClient'] !='') {
		$_SESSION['error'] = lang::ERR_NO_RECOMMENDATION;
		session_write_close();
	}
	
	if ($_POST['gender'] == '') {
		$_SESSION['error'] = lang::ERR_NO_GENDER;
		session_write_close();
		if ($_POST['backTo'] !='') {
			header( 'Location: ' . $_POST['backTo']);
			exit;
		} else {
			header( 'Location: /clients/client_edit.php');
			exit;

		}
	}
	$phones = phonesSQL($_POST['phones']); //преобразуем массив в строку
		
	if ($_POST["clientID"] == '') $refClientID = 0;
	else $refClientID = $_POST["clientID"];
	
	$sql = "UPDATE clients 
		SET
			name		= :name, 
			surname		= :surname, 
			prompt		= :prompt, 
			refClientID	= :refClientID, 
			phones		= :phones, 
			email		= :email, 
			DOB			= :DOB, 
			gender		= :gender, 
			note		= :note, 
			sourceID	= :sourceID, 
			locationID	= :locationID, 
			`timestamp`	= :timestamp, 
			author		= :author
		WHERE id = :id";
	try {
		$stmt = $pdo->prepare($sql);
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
		$stmt -> bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt -> bindValue(':id', $_POST["id"], PDO::PARAM_INT);
		$stmt ->execute();
		$_SESSION['success'] = lang::SUCCESS_GENERAL;
		
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
	}
	session_write_close();
	
	switch($_POST['goto']) {
		case 'profile':
			header( 'Location: /clients/client_profile.php?id='.$_POST["id"]);
			break;
		case 'clientTree':
			header( 'Location: /reports/clientTree.php');
			break;
		case 'visitDetails':
			header( 'Location: /visits/visit_details.php?id='.$_GET['visitID'].'&goto=dashboard');
			break;
		default:
			header( 'Location: /clients/client_list.php');
			break;
	}
	exit;

	/* if ($_POST['goto'] =='profile') {
		
		exit;
	} else {
		header( 'Location: /clients/client_list.php');
		exit;

	} */
}


if($_GET['id'] !=''){
	try {
		$stmt = $pdo->prepare("SELECT c.name, c.surname, c.prompt, c.refClientID, c.phones, c.email, c.DOB, c.gender, c.note, c.sourceID, c.locationID, c1.name as refName, c1.surname as refSurame, c1.prompt as refPrompt
		FROM `clients` c 
		LEFT JOIN clients c1 ON c.refClientID = c1.id
		WHERE c.id=:id");
		$stmt -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
		$stmt ->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}

	
	//проверить права на редактирование воизбежание простого перебора id-шками
	if (handle_rights('user', $data['locationID']) != 1) 
	{		
		$_SESSION['error'] = lang::ERR_NO_RIGHTS;
		session_write_close();
		header( 'Location: /clients/client_list.php');
		exit;
	}
	
	$phones = explode(',',$data['phones']);
	
} else {
	$_SESSION['error'] = lang::ERR_NO_ID;
	session_write_close();
	header( 'Location: /clients/client_list.php');
	exit;
}


$title = $data['name'] .' ' . $data['surname'];
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	include('filters.php');
	echo tabs($tabs);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'; FIO($data['name'], $data['surname'], $data['prompt']); echo '</h2>';?>
	<form method="post">
		<fieldset>
			<div class="row col-2">
				<label for="name"><?php echo lang::NAME; ?>*:</label>
				<input name="name" type="text" value="<?php echo $data['name']; ?>" autofocus required />
				<label for="surname"><?php echo lang::SURNAME; ?>:</label>
				<input name="surname" type="text" value="<?php echo $data['surname']; ?>" />
				<label for="prompt"><?php echo lang::HDR_PROMPT; ?>:</label>
				<input name="prompt" type="text" value="<?php echo $data['prompt']; ?>" />
			</div>
			<div id="morePhones" class="one-col">
				<?=phones_add($phones);?>
			</div>
			<div class="row col-2">
				<label for="email"><?php echo lang::HDR_EMAIL; ?>:</label>
				<input name="email" type="email" value="<?=$data['email']; ?>" /> 
				
				<label for="DOB"><?php echo lang::HDR_DOB; ?>:</label>
				<input name="DOB" type="date" value="<?=$data['DOB']; ?>" /> 
				
				<label><?php echo lang::HDR_GENDER; ?>*:</label>
				<div class="row col-2" style="height:2em;">
					<div>
						<input name="gender" type="checkbox" style="margin:0;" value="1" <?php if($data['gender']==1) echo 'checked'; ?>/> 
						<label><?=lang::HDR_MALE;?></label>
					</div>
					<div>
						<input name="gender" type="checkbox" style="margin:0;" value="0"  <?php if($data['gender']==0) echo 'checked'; ?>/> 
						<label><?=lang::HDR_FEMALE;?></label>
					</div>
				</div>
			
				<label for="sourceID"><?php echo lang::HDR_CLIENT_SOURCE; ?>*:</label>
				<?=client_source_select($data['sourceID']);?>
			</div>
			
			<div class="row col-2" id="refClient" 
			<?php if($data['refClientID'] ==0) echo 'style="display:none;"';?> >
				<label for="refClient"><?php echo lang::HDR_RECOMMENDATION; ?>:</label>
				<input name="refClient" class="FIO" type="text" value="<?php FIO($data['refName'],$data['refSurame'],$data['refPrompt']); ?>" />
				<input name="clientID" type="hidden" value="<?php echo $data['refClientID']; ?>" />
			</div>
			
			
			<div id="locList" class="row col-2">
				<?php echo location_options(1,"","",1); ?>
			</div>
			
			<div class="row">
				<textarea name="note" placeholder="<?=lang::COMMENT_PLACEHOLDER;?>"><?=$data['note']; ?></textarea>
			</div>
			
			<input name="goto" type="hidden" value="<?=$_GET['goto'];?>"/>
			<input name="id" type="hidden" value="<?=$_GET['id'];?>"/>
		</fieldset>
		<input type="submit" value="<?php echo lang::BTN_CHANGE; ?>" />
	</form>
</section>

<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	?> 
<script>
$(document).ready(function(){
	$('select[name="sourceID"]').change(function() {
	
		var refID = $('select[name="sourceID"]').val();
		
		if ( refID == 2) 
		{
			$('#refClient').show();
		} else {
			$('#refClient').hide();
			$('input[name="refClient"]').val('');
			$('input[name="clientID"]').val('');
		}
		
	});
});
</script>