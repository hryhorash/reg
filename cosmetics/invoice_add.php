<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$_SESSION['temp'] = array(
		'supplier' 			=> $_POST['supplier'],
		'invoice' 			=> $_POST['invoice'],
		'state' 			=> $_POST['state'],
		'date' 				=> $_POST['date'],
		'datePaid' 			=> $_POST['datePaid'],
		'dateReceived' 		=> $_POST['dateReceived']	
	);
	if($_POST["invoice"] !='') $name = $_POST["invoice"];
	else $name = lang::NO_NAME;
		
	
	$sql = "INSERT INTO invoices (name, date, supplierID, locationID, datePaid, dateReceived, state, author) 
			VALUES(:name, :date, :supplierID, :locationID, :datePaid, :dateReceived, :state, :author)";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':name', $name, PDO::PARAM_STR);
		if ($_POST["date"]!='') $stmt -> bindValue(':date', $_POST["date"], PDO::PARAM_STR);
		else $stmt -> bindValue(':date', date('Y-m-d'), PDO::PARAM_STR);
		$stmt -> bindValue(':supplierID', $_POST["supplier"], PDO::PARAM_INT);
		if ($_POST["datePaid"]!='') $stmt -> bindValue(':datePaid', $_POST["datePaid"], PDO::PARAM_STR);
		else $stmt -> bindValue(':datePaid', null, PDO::PARAM_STR);
		$stmt -> bindValue(':locationID', $_POST["loc"], PDO::PARAM_INT);
		if ($_POST["dateReceived"]!='') $stmt -> bindValue(':dateReceived', $_POST["dateReceived"], PDO::PARAM_STR);
		else $stmt -> bindValue(':dateReceived', null, PDO::PARAM_STR);
		$stmt -> bindValue(':state',  $_POST["state"], PDO::PARAM_INT);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$stmt ->execute();
		$invoiceID = $pdo->lastInsertId();
		
		unset($_SESSION['temp']);
		session_write_close();
		header( 'Location: /cosmetics/invoice_details.php?id='.$invoiceID . '&new=1');
		exit;
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
		session_write_close();
		header( 'Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
	
	
}

$title=lang::H2_NEW_INVOICE;

//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs,'inv_add');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'; echo $title;  echo '</h2>';?>

	<form method="post" style="max-width: 810px;">
		<fieldset >
			<div class="row col-2">
				<?php 
				location_options($select = 1, null, null, 1);
				supplier_select();
				invoice_state_select($supplierData['state']);?>
			</div>
			<div id="invoiceDetails" style="display:none;">
				<div class="row col-2">
					<label for="invoice"><?=lang::HDR_INVOICE;?>*:</label>
					<input name="invoice" type="text" value="<?=$_SESSION['temp']['invoice'];?>" />
				
					<label for="date"><?=lang::HDR_INVOICE_DATE;?>*:</label>
					<input name="date" type="date" value="<?=$_SESSION['temp']['date'];?>" />
				
					<label for="datePaid"><?=lang::HDR_DATE_PAID;?>:</label>
					<input name="datePaid" type="date" value="<?=$_SESSION['temp']['datePaid'];?>" />
				
					<label for="dateReceived"><?=lang::HDR_DATE_RECEIVED;?>:</label>
					<input name="dateReceived" type="date" value="<?=$_SESSION['temp']['dateReceived'];?>" />
				</div>
			</div>
				
		</fieldset>
		<input type="submit" value="<?=lang::BTN_NEXT;?>" style="float: right;"/>
	</form>
</section>


<?php 
unset($_SESSION['temp']);
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	
?> 
<script>

$(document).ready(function(){
		
	$("select[name='state']").change(function() {
		var state = $("select[name='state']").val();
	
		if (state == 0 || state == 1) {
			$("#invoiceDetails").hide();
			$("[name='invoice']").prop("required", false);
			$("[name='date']").prop("required", false);
			
			
		} else {
			$("#invoiceDetails").show();
			$("[name='invoice']").prop("required", true);
			$("[name='date']").prop("required", true);
		}
		 
	});
});
</script>