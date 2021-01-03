<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$i=0;
	if (isset($_POST['receivedID'])) {
		foreach($_POST['receivedID'] as $item) {
			$receivedIDs_string[$i] = $item;
			$i++;
		}
	}
	$i=0;
	foreach($_POST['discount'] as $item) {
		if($_POST['qtyIn'][$i] !=0) {
			if ($item == '') 
				 $discount[$i] = 0;
			else $discount[$i] = $item / $_POST['qtyIn'][$i];
		} else   $discount[$i] = $item;
		$i++;
	}
	$i=0;
	foreach($_POST['expire'] as $item) {
		$expire[$i] = $item;
		$i++;
	}
	
	function add_cosm_qty($cosm_id, $priceIn, $qtyIn, $discount, $expire) {
		if ($qtyIn != 0 && $qtyIn != '') {
			require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
			$add = $pdo->prepare("
						INSERT INTO received (invoiceID, cosmID, qtyIn, expire, discount, priceIn, author) 
						VALUES(:invoiceID, :cosmID, :qtyIn, :expire, :discount, :priceIn, :author)
					");
			$add -> bindValue(':invoiceID', $_POST["id"], PDO::PARAM_INT);
			$add -> bindValue(':cosmID', $cosm_id, PDO::PARAM_INT);
			if($expire !='') 
				 $add -> bindValue(':expire', $expire, PDO::PARAM_STR);
			else $add -> bindValue(':expire', null, PDO::PARAM_STR);
			if($discount !='') 
				$add -> bindValue(':discount', $discount, PDO::PARAM_STR);
			else $add -> bindValue(':discount', 0, PDO::PARAM_INT);
			$add -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
				
			if ($qtyIn !=1) {
				$qty = $qtyIn;
				$inserted = 0;
				
				while ($qty >= 1 ) {
					$add -> bindValue(':priceIn', $priceIn, PDO::PARAM_STR);
					$add -> bindValue(':qtyIn', 1, PDO::PARAM_STR);
					$add -> execute();
					$qty = $qty - 1;
					$inserted++;
				}
				if ($qty > 0 & $qty < 1) {
					$priceIn = $priceIn * $qty;
					$add -> bindValue(':priceIn', $priceIn, PDO::PARAM_STR);
					$add -> bindParam(':qtyIn', $qty, PDO::PARAM_STR);
					$add ->execute();
					$inserted++;
				}
			} else {
				$add -> bindValue(':priceIn', $priceIn, PDO::PARAM_STR);
				$add -> bindValue(':qtyIn', $qtyIn, PDO::PARAM_STR);
				$add -> execute();
				$inserted = 1;
			}
		}
	}
	
	function update_cosm_received($receivedID, $priceIn, $discount, $expire) {
		require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
		$update = $pdo->prepare("
			UPDATE received 
			SET expire			= :expire, 
				discount		= :discount, 
				priceIn			= :priceIn, 
				`timestamp`		= :timestamp, 
				author			= :author
			WHERE id = :receivedID
			");
		if($expire !='') 
			 $update -> bindValue(':expire', $expire, PDO::PARAM_STR);
		else $update -> bindValue(':expire', null, PDO::PARAM_STR);
		if($discount !='') 
			 $update -> bindValue(':discount', $discount, PDO::PARAM_STR);
		else $update -> bindValue(':discount', 0, PDO::PARAM_INT);
		$update -> bindValue(':priceIn', $priceIn, PDO::PARAM_STR);
		$update -> bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
		$update -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$update -> bindValue(':receivedID', $receivedID, PDO::PARAM_INT);
		$update -> execute();
	}
	
	function delete_cosm_received($receivedID) {
		require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
		$delete = $pdo->prepare("DELETE FROM received WHERE id = :receivedID");
		$delete -> bindValue(':receivedID', $receivedID, PDO::PARAM_INT);
				
		if ($_POST['state'] > 3) { //ДО статуса заказ получен не в счет
			$check = $pdo->prepare("SELECT sold.clientID, cosmetics.name, CONCAT(clients.name, ' ', clients.surname) as client, sold.date FROM `sold` 
				LEFT JOIN received ON sold.receivedID=received.id
				LEFT JOIN cosmetics ON received.cosmID=cosmetics.id
				LEFT JOIN clients ON sold.clientID=clients.id
				
				WHERE received.id=:receivedID");
			$check -> bindValue(':receivedID', $receivedID, PDO::PARAM_INT);
			$check -> execute();
			$data = $check->fetch(PDO::FETCH_ASSOC);
			
			if(!empty($data)) {
				$text = $data['name'] . lang::MSG_SOLD . $data['client'] . ' ' . $data['date'];
				if(isset($_SESSION['error'])) {
					$_SESSION['error'] = $_SESSION['error'] . '<br />' . $text;
				} else {
					$_SESSION['error'] = lang::ERR_CONSTRAINT . '<br />' . $text;
				}
				
			} else	$delete -> execute();
		} else		$delete -> execute();
	}

	//Проверяем, новый ли инвойс. Если да, не обновляем данные по инвойсу
	if($_POST['new'] != 1 ) {
		try {
		$invoice = $pdo->prepare("
			UPDATE invoices
			SET name 		 = :name, 
				date		 = :date, 
				supplierID	 = :supplierID, 
				locationID	 = :locationID, 
				datePaid	 = :datePaid, 
				dateReceived = :dateReceived, 
				state		 = :state, 
				author		 = :author
			WHERE id = :id
		");
		$invoice -> bindValue(':name', $_POST["invoice"], PDO::PARAM_STR);
		if ($_POST["date"]!='') $invoice -> bindValue(':date', $_POST["date"], PDO::PARAM_STR);
		else $invoice -> bindValue(':date', null, PDO::PARAM_INT);
		$invoice -> bindValue(':supplierID', $_POST["supplier"], PDO::PARAM_INT);
		if ($_POST["datePaid"]!='') $invoice -> bindValue(':datePaid', $_POST["datePaid"], PDO::PARAM_STR);
		else $invoice -> bindValue(':datePaid', null, PDO::PARAM_STR);
		$invoice -> bindValue(':locationID', $_POST["loc"], PDO::PARAM_INT);
		if ($_POST["dateReceived"]!='') $invoice -> bindValue(':dateReceived', $_POST["dateReceived"], PDO::PARAM_STR);
		else $invoice -> bindValue(':dateReceived', null, PDO::PARAM_STR);
		$invoice -> bindValue(':state',  $_POST["state"], PDO::PARAM_INT);
		$invoice -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		$invoice -> bindValue(':id', $_POST["id"], PDO::PARAM_INT);
		$invoice ->execute();
		
		
		} catch (PDOException $ex){
			include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
			$_SESSION['error'] = $ex;
			session_write_close();
			header( 'Location: ' . $_SERVER['PHP_SELF']);
			exit;
		}
	}
	
	$i=0;
	foreach ($_POST['cosmID'] as $cosmID) {
		// Добавляем НДС при необходимости
		if ($_POST['qtyIn'][$i] != 0) {
			if($_POST['VAT'] == 0)	$priceIn = $_POST['priceIn'][$i] * (100+$_SESSION['settings']['VAT'])/100 / $_POST['qtyIn'][$i];
			else					$priceIn = $_POST['priceIn'][$i] / $_POST['qtyIn'][$i];
		} else $priceIn = 0;
		
		if ($_POST['cosmID_old'][$i] != '') {
			$receivedIDs[$i] = explode(',',$receivedIDs_string[$i]);
			
			switch (true)
			{
				case($_POST['cosmID_old'][$i] == $cosmID):
					switch(true)
					{
						case ($_POST['qtyIn'][$i] == 0):
							foreach($receivedIDs[$i] as $receivedID)
							{
								delete_cosm_received($receivedID);
							}
							break;
						case ($_POST['qtyIn'][$i] == $_POST['qtyIn_old'][$i]):
							foreach($receivedIDs[$i] as $receivedID)
							{
								if($_POST['isChanged'][$i] == 1)
									update_cosm_received($receivedID, $priceIn, $discount[$i], $expire[$i]);
							}
							break;
						
						case ($_POST['qtyIn'][$i] > $_POST['qtyIn_old'][$i]):
							if ($_POST['qtyIn_old'][$i] < 1) {
								delete_cosm_received($receivedIDs[$i][0]);
								if(!isset($_SESSION['error'])) 
									add_cosm_qty($cosmID, $priceIn, $_POST['qtyIn'][$i], $discount[$i], $expire[$i]);
								break;
							} else {
								foreach($receivedIDs[$i] as $receivedID)
								{
									if($_POST['isChanged'][$i] == 1)
										update_cosm_received($receivedID, $priceIn, $discount[$i], $expire[$i]);
								}
								$qty_new = $_POST['qtyIn'][$i] - $_POST['qtyIn_old'][$i];
								add_cosm_qty($cosmID, $priceIn, $qty_new, $discount[$i], $expire[$i]);
							break;
							}
							
						case ($_POST['qtyIn'][$i] < $_POST['qtyIn_old'][$i]):
								$n = 0;
								foreach($receivedIDs[$i] as $receivedID)
								{
									$qty_left = $_POST['qtyIn'][$i] - $n;
									
									switch(true){
										case($qty_left >= 1):
											if($_POST['isChanged'][$i] == 1)
											update_cosm_received($receivedID, $priceIn, $discount[$i], $expire[$i]);
											break;
										case($qty_left > 0 && $qty_left < 1):
											delete_cosm_received($receivedID);
											if(!isset($_SESSION['error'])) 
												add_cosm_qty($cosmID, $priceIn, $qty_left, $discount[$i], $expire[$i]);
											break;
										case($qty_left <= 0):
											delete_cosm_received($receivedID);
											break;
									}
									$n++;
								}
							break;
					}
					break;
					
					
					
				
				case($_POST['cosmID_old'][$i] != $cosmID):
					//if()
					break;
			}
		} else {
			if ($cosmID !='' && $_POST['qtyIn'][$i] > 0)
				add_cosm_qty($cosmID, $priceIn, $_POST['qtyIn'][$i], $discount[$i], $expire[$i]);
				
				//обновляем RRP при необходимости
				if($_POST['RRP'][$i] > 0) {
					$RRP_update = $pdo->prepare("
						UPDATE cosmetics 
						SET RRP			= :RRP, 
							`timestamp`	= :timestamp, 
							author		= :author
						WHERE id 		= :cosmID
						");
					$RRP_update -> bindValue(':RRP', $_POST['RRP'][$i], PDO::PARAM_STR);
					$RRP_update -> bindValue(':timestamp', date('Y-m-d h:i:s'), PDO::PARAM_STR);
					$RRP_update -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
					$RRP_update -> bindValue(':cosmID', $cosmID, PDO::PARAM_INT);
					$RRP_update -> execute();
				}
			
		}
		$i++;
	}
	
	
	
	//END POST
	session_write_close();
	header( 'Location: /cosmetics/invoice_list.php');
	exit;
}

if ($_GET['id'] != '') {
	
	$supplier = $pdo->prepare("SELECT suppliers.id, suppliers.name as supplier, VAT, GROUP_CONCAT(DISTINCT brands.name SEPARATOR ', ') as brandNames 
		FROM `suppliers` 
		LEFT JOIN invoices ON suppliers.id=invoices.supplierID
		LEFT JOIN supplier_brands ON suppliers.id=supplier_brands.supplierID
		LEFT JOIN brands ON supplier_brands.brandID=brands.id
		WHERE invoices.id = :id");
	$supplier -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
	$supplier ->execute();
	$supplierData=$supplier->fetch(PDO::FETCH_ASSOC);
	
	
	$sql="SELECT GROUP_CONCAT(DISTINCT received.id) as receivedID, invoices.id, invoices.name as invoice, invoices.date, invoices.datePaid, invoices.dateReceived, cosmetics.volume, sum(received.qtyIn) as qty, received.priceIn, cosmetics.articul, invoices.locationID, invoices.supplierID, discount, state, expire, cosmID, cosmetics.RRP,
			CASE 
				WHEN LENGTH(articul) THEN CONCAT(articul,', ', brands.name, ' ', cosmetics.name,', ',volume)
				ELSE CONCAT(brands.name, ' ', cosmetics.name,', ',volume)
			   END AS cosm_name
		FROM `invoices` 
		LEFT JOIN received ON invoices.id=received.invoiceID
		LEFT JOIN cosmetics ON received.cosmID=cosmetics.id
		LEFT JOIN brands ON cosmetics.brandID=brands.id
        LEFT JOIN suppliers ON invoices.supplierID=suppliers.id
        WHERE invoices.id=:id
		GROUP BY cosmID, priceIn, expire
		ORDER BY received.id";
	$stmt = $pdo->prepare($sql);
	$stmt -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
	$stmt ->execute();
	$total=0;
	$count = 1;
	while($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$_SESSION['supplierVAT'] = $supplierData['VAT'];
		$discount[$count] = $data[$count]['discount'] * $data[$count]['qty'];
		
		if ($data[$count]['qty'] < 1 && $data[$count]['qty'] !=0)
			 $price[$count]	= VAT_subtract($data[$count]['priceIn'] / $data[$count]['qty']) + $data[$count]['discount'];
		else $price[$count]	= VAT_subtract($data[$count]['priceIn']) + $data[$count]['discount'];
		$priceIn[$count]= $price[$count] * $data[$count]['qty']  - $discount[$count];
		$total			= $total+$data[$count]['priceIn'] * $data[$count]['qty'];
		 
		if($data[$count]['qty'] < 1 )
			 $tooltip = round(($data[$count]['priceIn'] * $data[$count]['qty']),2);
		else $tooltip = $data[$count]['priceIn'];
		
		$count++;
	}
	
	
	
	//проверить права на редактирование воизбежание простого перебора id-шками
	if (handle_rights('user', $data[1]['locationID']) != 1) 
	{		
		$_SESSION['error'] = lang::ERR_NO_RIGHTS;
		session_write_close();
		header( 'Location: /cosmetics/invoice_list.php');
		exit;
	}
	$title=$data[1]['invoice'] . lang::HDR_INVOICE_FROM . correctDate($data[1]['date']);
	$subtitle=$supplierData['supplier'] .'<span class="small"> (' . $supplierData['brandNames'] .')</span>';
} else {
	$title=lang::H2_NEW_INVOICE;
}

//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, $pageID);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>' . $title . '</h2>';
	if(isset($subtitle)) echo '<p class="subtitle">'.$subtitle.'</p>';?>

	<template id="purchase">
		<div class="row nested">
			<input name="cosm[]" type="text" placeholder="<?=lang::HDR_ITEM_NAME;?>" />
			<a class="inside-input" href="/cosmetics/cosmetics_add.php?backTo=close" target="_blank" title="<?=lang::H2_COSMETICS;?>" tabindex="-1"><i class="fas fa-plus inline-fa"></i></a>
			<input name="qtyIn[]" class="short" type="number" step="0.01" min="0" placeholder="<?=lang::PLACEHOLDER_QTY;?>" value="1" />
			<input name="price[]" class="short" type="number" step="0.01" min="0" placeholder="<?=lang::HDR_PRICE;?>" />
			<input name="discount[]" class="short" type="number" step="0.01" min="0" placeholder="<?=lang::PLACEHOLDER_DISCOUNT;?>" />
			<input name="priceIn[]" class="short bold" type="number" step="0.01" min="0" placeholder="<?=lang::HDR_TOTAL;?>"  value="0" />
			<input name="cosmID[]" type="hidden" value="" />
			
		</div>
		<div class="row nested">
			<label name="expire[]" style="color: grey;"><?=lang::HDR_EXPIRE;?>:</label>
			<input name="expire[]" type="date" style="color: grey;"/>
			<input name="RRP[]" class="short" type="number" step="0.01" min="0" placeholder="<?=lang::PLACEHOLDER_RRP;?>" />
		</div>
	</template>

	<form method="post" style="max-width: 810px;">
		<fieldset >
			<?php 
			if (!isset($_GET['new'])) {
				location_options($select = 1, $data[1]['locationID'], null, 1);
				supplier_select($supplierData['id']);
				invoice_state_select($data[1]['state']);?>
			<div id="invoiceDetails">
				<div class="row">
					<label for="invoice"><?=lang::HDR_INVOICE;?>*:</label>
					<input name="invoice" type="text" value="<?=$data[1]['invoice'];?>" required />
				</div>
				<div class="row">
					<label for="date"><?=lang::HDR_INVOICE_DATE;?>:</label>
					<input name="date" type="date" value="<?=$data[1]['date'];?>" />
				</div>
				<div class="row">
					<label for="datePaid"><?=lang::HDR_DATE_PAID;?>:</label>
					<input name="datePaid" type="date" value="<?=$data[1]['datePaid'];?>" />
				</div>
				<div class="row">
					<label for="dateReceived"><?=lang::HDR_DATE_RECEIVED;?>:</label>
					<input name="dateReceived" type="date" value="<?=$data[1]['dateReceived'];?>" />
				</div>
			</div>
				
			<?php } ?>
			<div id="cosmLines">
			<div class="row nested">
				<input class="cosmSupplier00 input-hdr bold" value="<?=lang::HDR_ITEM_NAME;?>" disabled />
				<input class="input-hdr bold short" value="<?=lang::PLACEHOLDER_QTY;?>" disabled />
				<input class="input-hdr bold short" value="<?=lang::HDR_PRICE;?>" disabled />
				<input class="input-hdr bold short" value="<?=lang::HDR_DISCOUNT;?>" disabled />
				<input class="input-hdr bold short" value="<?=lang::HDR_TOTAL;?>" disabled />
				
			</div>		
			<?php 
			$count=1;
			if(!isset($_GET['new'])){
				while($data[$count] != null){
					echo '<div class="row nested">
						<input name="cosm[]" class="cosmSupplier'.$count.'" style="margin: 5px 0;" type="text" placeholder="'.lang::HDR_ITEM_NAME.'" value="'.$data[$count]['cosm_name'].'" disabled />
						<input name="qtyIn[]" class="short" type="number" step="0.01" placeholder="'.lang::PLACEHOLDER_QTY.'" value="'.$data[$count]['qty'].'" />
						<input name="price[]" class="short" type="number" step="0.01" placeholder="'.lang::HDR_PRICE.'" value="'.round($price[$count],2).'"  title="'.$tooltip; echo curr(); echo'" />
						<input name="discount[]" class="short" type="number" step="0.01" placeholder="'.lang::PLACEHOLDER_DISCOUNT.'"  value="'.round($discount[$count],2).'"/>
						<input name="priceIn[]" class="short bold" type="number" step="0.01" placeholder="'.lang::HDR_TOTAL.'" value="'.round($priceIn[$count],2).'" />
						<input name="cosmID[]" type="hidden" value="'.$data[$count]['cosmID'].'" required />
						<input name="isChanged[]" type="hidden" value="0">';
						if ($data[$count]['soldTo'] =='') echo '<i class="fas fa-times inline-fa" id="'.$count.'" onclick="deleteRow('.$count.');" title="'.lang::HANDLING_DELETE.'"></i>';
					echo '</div>
					<div class="row nested">
						<label name="expire[]" style="color: grey;">'.lang::HDR_EXPIRE.':</label>
						<input name="expire[]" type="date"  style="color: grey;" value="'.$data[$count]['expire'].'" />
						<input name="RRP[]" type="number" step="0.01" min="0" class="short" value="'.$data[$count]['RRP'].'"/>
						<input name="expire_old[]" type="hidden" value="'.$data[$count]['expire'].'" />
						<input name="cosmID_old[]" type="hidden" value="'.$data[$count]['cosmID'].'" />
						<input name="qtyIn_old[]" type="hidden" value="'.$data[$count]['qty'].'" />
						<input name="priceIn_old[]" type="hidden" value="'.$priceIn[$count].'" />
						<input name="discount_old[]" type="hidden" value="'.$discount[$count].'" />
						<input name="receivedID[]" type="hidden" value="'.$data[$count]['receivedID'].'" />
					</div>';
					
					$count++;
				}
			} else {
			
			?>
			
			
				<div class="row nested">
					<input name="cosm[]" class="cosmSupplier" style="margin: 5px 10px 5px 0;" type="text" placeholder="<?=lang::HDR_ITEM_NAME;?>" autofocus />
					<a class="inside-input" href="/cosmetics/cosmetics_add.php?backTo=close" target="_blank" title="<?=lang::H2_COSMETICS;?>" tabindex="-1"><i class="fas fa-plus inline-fa"></i></a>
					<input name="qtyIn[]" class="short" type="number" step="0.01" placeholder="<?=lang::PLACEHOLDER_QTY;?>" value="1" />
					<input name="price[]" class="short" type="number" step="0.01" placeholder="<?=lang::HDR_PRICE;?>" title="" />
					<input name="discount[]" class="short" type="number" step="0.01" placeholder="<?=lang::PLACEHOLDER_DISCOUNT;?>" />
					<input name="priceIn[]" class="short bold" type="number" step="0.01" placeholder="<?=lang::HDR_TOTAL;?>" />
					<input name="cosmID[]" type="hidden" value="" required />
				</div>
				<div class="row nested">
					<label name="expire[]" style="color: grey;"><?=lang::HDR_EXPIRE;?>:</label>
					<input name="expire[]" type="date" style="color: grey;" />
					<input name="RRP[]" class="short" type="number" step="0.01" min="0" placeholder="<?=lang::PLACEHOLDER_RRP;?>" />
				</div>
			<input name="new" type="hidden" value="<?=$_GET['new'];?>" />
			<?php }?>
			<input name="id" type="hidden" value="<?=$_GET['id'];?>" />
			<input name="VAT" type="hidden" value="<?=$supplierData['VAT'];?>" />
			</div>
			<input type="button" value="<?=lang::BTN_ADD;?>" onclick="lineAdd();" />
		</fieldset>
		<div class="results">
		<?php switch(true)
		{
			case ($_SESSION['supplierVAT'] == 0):
				echo '<div class="VAT">НДС: <span id="VATTotal">' . correctNumber(VAT_only($total),2) . '</span>' . curr() . '</div>';
				echo '<div class="invoiceTotal">Итого: <span id="invoiceTotal">' . correctNumber($total,2) . '</span>' . curr() . '</div>';
				break;
				
			case ($_SESSION['supplierVAT'] == 1):
				echo '<div class="invoiceTotal">Итого: <span id="invoiceTotal">' . correctNumber($total,2) . '</span>' . curr() . '</div>';
				break;
				
			default:
				echo '<div class="VAT" style="display:none;">НДС: <span id="VATTotal"></span>' . curr() . '</div>';
				
				echo '<div class="invoiceTotal">Итого: <span id="invoiceTotal">0</span>' . curr() .'</div>';
				break;
		}
		
		?>
		</div>
		
		<input type="submit" value="<?=lang::BTN_SAVE;?>" style="float: right;"/>
	</form>
</section>

<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>
<?php
if ($_GET['new'] !=1) { ?>
	<script>
		//Статус заказа
		$(document).ready(function(){
			
			$("select[name='state']").change(function(){
				var invoice_state = $("select[name='state']").val();
				if (invoice_state < 2 ) {
					$("[name='date']").prop("required", false);
					$("[name='datePaid']").prop("required", false);
					$("[name='dateReceived']").prop("required", false);
				} else if (invoice_state == 2) {
					$("[name='date']").prop("required", true);
					$("[name='datePaid']").prop("required", false);
					$("[name='dateReceived']").prop("required", false);
				} else if (invoice_state == 3) {
					$("[name='date']").prop("required", true);
					$("[name='datePaid']").prop("required", true);
					$("[name='dateReceived']").prop("required", false);
				} else if (invoice_state == 4) {
					$("[name='date']").prop("required", true);
					$("[name='datePaid']").prop("required", false);
					$("[name='dateReceived']").prop("required", true);
				} else {
					$("[name='date']").prop("required", true);
					$("[name='datePaid']").prop("required", true);
					$("[name='dateReceived']").prop("required", true);
				}
			});
			
			$("input[name='date'], input[name='datePaid'], input[name='dateReceived']").change(function(){
				var invoice_date = $("input[name='date']").val();
				var invoice_datePaid = $("input[name='datePaid']").val();
				var invoice_dateReceived = $("input[name='dateReceived']").val();
				if (invoice_dateReceived != '' && invoice_datePaid !='') {
					$("select[name='state']").val(5);
				} else if (invoice_dateReceived != '' && invoice_date != '') {
					$("select[name='state']").val(4);
				} else if (invoice_datePaid != '' && invoice_date != '') {
					$("select[name='state']").val(3);
				} else if (invoice_date != '') {
					$("select[name='state']").val(2);
				} else {
					$("select[name='state']").val(<?=$data[1]['state'];?>);
				}
			});
			
		});

	</script>
<?php }?>

<script>
//Изменение внесенной косметики
$(document).ready(function(){
	$("input[name='price[]'],input[name='discount[]'],input[name='priceIn[]']").change(function(){
		$(this).siblings("input[name='isChanged[]']").val(1);
	});
	$("input[name='expire[]']").change(function(){
		$(this).parent().prev().children("input[name='isChanged[]']").val(1);
	});
});

var supplierID = <?=$data[1]['supplierID'];?>;
$('.cosmSupplier').autocomplete({
	serviceUrl: '/config/autocomplete.php?supplierID='+supplierID,
	minChars:2,
	autoSelectFirst: true,
	preventBadQueries: false,
	onSelect: function (suggestion) {
		let cosmID = suggestion.data.split('--')[0];
		let RRP = suggestion.data.split('--')[1];
		$(this).siblings("input[name='cosmID[]']").val(cosmID);
		$(this).parent().next().children("input[name='RRP[]']").prop('placeholder', RRP);
	}
});



//Добавление строки
var _counter = <?=$count;?>;
var template = document.querySelector("#purchase");
var documentFragment = template.content;
function lineAdd() {
	//значения из предыдущей строки
	var prev_cost = $('input[name="price[]"]').last().val();
	var prev_discount = $('input[name="discount[]"]').last().val() / $('input[name="qtyIn[]"]').last().val();
	
	
	_counter++;
	var oClone = template.content.cloneNode(true);
	oClone.id += (_counter + "");
	document.getElementById("cosmLines").appendChild(oClone);
	
	//Уникальный класс для добавленного поля
	$('input[name="cosm[]"]').last().addClass('cosmSupplier'+_counter);
	
	$('input[name="price[]"]').last().val(prev_cost);
	$('input[name="discount[]"]').last().val(prev_discount);
	
	$('input[name="cosm[]"]').last().focus();
	
	
	$('.cosmSupplier'+_counter).autocomplete({
		serviceUrl: '/config/autocomplete.php?supplierID='+supplierID,
		minChars:2,
		autoSelectFirst: true,
		preventBadQueries: false,
		onSelect: function (suggestion) {
			let cosmID = suggestion.data.split('--')[0];
			let RRP = suggestion.data.split('--')[1];
			$(this).siblings("input[name='cosmID[]']").val(cosmID);
			$(this).parent().next().children("input[name='RRP[]']").prop('placeholder', RRP);
		}
	});
}

//Удаление строки
function deleteRow(rowID) {
	//alert('text' + $(this).siblings("input[name='qtyIn[]']").val())
	
	$(this).parent().siblings("input[name='qtyIn[]']").val(0);
	$("#"+rowID).siblings("input[name='qtyIn[]']").val(0);
	$("#"+rowID).parent().hide();
	$("#"+rowID).parent().next().hide();
}



/*$(document).ready(function(){
		
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
	// Невозможность выбрать "чужую" для поставщика косметику
	$("select[name='supplier']").change(function() {
		var supplierID = $("select[name='supplier']").val();
	
		if (supplierID == '') {
			$("[name='cosm[]']").prop("disabled", true);
		} else {
			$("[name='cosm[]']").prop("disabled", false);
		}
		 
	});
});
*/

	
var VAT = <?=$_SESSION['supplierVAT'];?>;
var VAT_rate = <?php echo $_SESSION['settings']['VAT'];?>

function str2number(str) {
	var res = parseFloat(str);
	if (isNaN(res)) res = '';
	return res;
}

function itemTotal(price, discount, qty) {
	var total = 0;
	if (price !='' && qty != '') {
		if (discount == null) 
			total = price * qty;
		else 
			total = price * qty - discount;
	}
	return total.toFixed(2);
}

function realPricePerItem (price, discount, qty) {
	var finalPrice = 0
	if (VAT == 0) {
		finalPrice = price * (100 + VAT_rate) / 100 - discount / qty;
		tooltip = finalPrice.toFixed(2) + $.trim('<?=curr();?>') + ' <?=lang::VAT_YES;?>';
	} else {
		finalPrice = price - discount / qty;
		tooltip = finalPrice.toFixed(2);
		
	}
	return tooltip;
}

function invoiceTotals() {
	var sum = 0;
	 $("input[name='priceIn[]']").each(function(){
        sum += +$(this).val();
    });
	
	if (VAT == 0 && VAT != NaN)
	{
		VAT_sum = sum * VAT_rate / 100;
		sum = sum * (100 + VAT_rate) /100;
		document.getElementById("VATTotal").innerHTML = (VAT_sum).toFixed(2);
	
	}
	document.getElementById("invoiceTotal").innerHTML = (sum).toFixed(2);
}

$(document).on("change", function() {
	invoiceTotals();
	
	$("input[name='price[]']").on('keyup blur', function(){ 
		var price	 = str2number($(this).val());
		var qty 	 = str2number($(this).siblings("input[name='qtyIn[]']").val());
		var discount = str2number($(this).siblings("input[name='discount[]']").val());
		
		$(this).prop('title', realPricePerItem (price, discount, qty));
		$(this).siblings("input[name='priceIn[]']").val(itemTotal(price, discount, qty));
		invoiceTotals();
		
	});
	$("input[name='discount[]']").on('keyup blur', function() {
		var price	 = str2number($(this).siblings("input[name='price[]']").val());
		var qty 	 = str2number($(this).siblings("input[name='qtyIn[]']").val());
		var discount = str2number($(this).val());
		
		$(this).siblings("input[name='price[]']").prop('title', realPricePerItem (price, discount, qty));
		$(this).siblings("input[name='priceIn[]']").val(itemTotal(price, discount, qty));
		invoiceTotals();
	});
	$("input[name='qtyIn[]']").on('keyup blur', function(){ 
		var price	 = str2number($(this).siblings("input[name='price[]']").val());
		var qty 	 = str2number($(this).val());
		var discount = str2number($(this).siblings("input[name='discount[]']").val());
		
		$(this).siblings("input[name='price[]']").prop('title', realPricePerItem (price, discount, qty));
		$(this).siblings("input[name='priceIn[]']").val(itemTotal(price, discount, qty));
		invoiceTotals();
	});
	$("input[name='priceIn[]']").focus(function(){ 
		var price	 = str2number($(this).siblings("input[name='price[]']").val());
		var qty 	 = str2number($(this).siblings("input[name='qtyIn[]']").val());
		var discount = str2number($(this).siblings("input[name='discount[]']").val());
		
		$(this).siblings("input[name='price[]']").prop('title', realPricePerItem (price, discount, qty));
		$(this).val(itemTotal(price, discount, qty));
		invoiceTotals();
		
	});
	
	$("input[name='priceIn[]']").on('keyup blur', function(){ 
		var price	 = 0;
		var qty 	 = parseFloat($(this).siblings("input[name='qtyIn[]']").val());
		var discount = parseFloat($(this).siblings("input[name='discount[]']").val());
		var total	 = parseFloat($(this).val());
		
		if (qty != '' && qty != 0) {
			if (discount == null || discount == '') 
				price = total / qty;
			else 
				price = (total + discount) / qty;
			
			$(this).siblings("input[name='price[]']").val(price.toFixed(2));
			$(this).siblings("input[name='price[]']").prop('title', realPricePerItem (price, discount, qty));
			invoiceTotals();
		}
		
	});
	
	//Убрать значек плюса
	$("input[name='cosm[]']").on('keyup blur focus', function(){ 
		var itemID = $(this).siblings("input[name='cosmID[]']").val();
		var itemName = $(this).val();
		if (itemID != '' && itemName !='')
			 $(this).siblings("a").hide();
		else $(this).siblings("a").show();
	});
   
});



//AJAX
$(document).ready(function () {
	
	// При изменении значения
	$("select[name='supplier']").change(function() {
	  var xhttp;    
	  xhttp = new XMLHttpRequest();
		$.ajax({
				type: "GET",
				url: "/cosmetics/AJAX_VAT.php",
				data:	{ 'supplierID': $("select[name='supplier'] option:selected").val() },  
				success: function(data){
					VAT = data;
					if (VAT == 0) $(".VAT").show();
					else $(".VAT").hide();
				  }
			});
	});

});
</script>
<?php unset($_SESSION['supplierVAT']); // Нужна была для корректной работы функции VAT_subtract() ?> 
