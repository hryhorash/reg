<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if($_GET['offset_work'] > 0) 
	 $offset_work = $_GET['offset_work'];
else $offset_work = 0;

if($_GET['offset_sale'] > 0) 
	 $offset_sale = $_GET['offset_sale'];
else $offset_sale = 0;

if($_REQUEST['cosmID'] != '') {
	try {
		$stmt = $pdo->prepare("SELECT cosmetics.id, brands.name as brand, cosmetics.name as cosm_name, volume, purpose, cosmetics.archive
			, MIN( invoices.date ) AS first_shipment 
			, MAX( invoices.date ) AS last_shipment 
			, SUM( received.qtyIn ) AS total_qty 
			, SUM(received.qtyOut) AS qty_sold
			, MIN( received.priceIn / received.qtyIn ) AS min_price 
			, MAX( received.priceIn / received.qtyIn ) AS max_price  
									
				FROM received
				LEFT JOIN cosmetics	ON received.cosmID=cosmetics.id
				LEFT JOIN brands ON cosmetics.brandID = brands.id
				LEFT JOIN invoices ON received.invoiceID = invoices.id
				WHERE cosmetics.id = :cosmID
					AND invoices.locationID = :locationID");
		$stmt -> bindValue(':cosmID', $_REQUEST['cosmID'], PDO::PARAM_INT);
		$stmt -> bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
		$stmt ->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$stmt2 = $pdo->prepare("SELECT SUM(received.qtyIn) as qty, priceIn
								,invoices.date, invoiceID
							FROM `received` 
							LEFT JOIN invoices ON received.invoiceID = invoices.id
							WHERE received.cosmID = :cosmID 
								AND locationID = :locationID
							GROUP BY invoices.date
							ORDER BY date DESC");
		$stmt2 -> bindValue(':cosmID', $_REQUEST['cosmID'], PDO::PARAM_INT);
		$stmt2 -> bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
		$stmt2 ->execute();
		$purchases = $stmt2->fetchAll(PDO::FETCH_ASSOC);
		
		//наличие и стоимость за грамм для косметики в работу
		if($data['purpose'] == 0 || $data['purpose'] == 2) {
			$stmt3 = $pdo->prepare("SELECT cosmetics.id, brandID, brands.name as brand, cosmetics.name, articul, cosmetics.volume
									, SUM(received.qtyIn) * cosmetics.volume as received_V
									, MAX(received.priceIn / received.qtyIn) / cosmetics.volume as max_price_gr
									, tbl_spent.spent_V
									, CASE WHEN tbl_spent.spent_V is not null
										THEN (SUM(received.qtyIn) * cosmetics.volume - tbl_spent.spent_V) 
										ELSE (SUM(received.qtyIn) * cosmetics.volume)
										END as v_available
									, CASE WHEN tbl_spent.spent_V is not null
										THEN (SUM(received.qtyIn) - tbl_spent.spent_V / cosmetics.volume) 
										ELSE SUM(received.qtyIn)
										END as pcs_available
									FROM `cosmetics` 
									LEFT JOIN brands ON cosmetics.brandID = brands.id
									LEFT JOIN received ON cosmetics.id = received.cosmID
									LEFT JOIN invoices ON received.invoiceID = invoices.id
									LEFT JOIN (
										SELECT spent.cosmID, SUM(spent.volume) as spent_V
										FROM spent
										LEFT JOIN visits ON spent.visitID = visits.id
										WHERE visits.locationID = :locationID
										GROUP BY spent.cosmID
									) tbl_spent ON cosmetics.id = tbl_spent.cosmID
									WHERE cosmetics.archive = 0 
										AND received.qtyOut = 0
										AND purpose IN (0,2)
										AND invoices.state >= 4
										AND invoices.locationID = :locationID
										AND cosmetics.id = :cosmID
								GROUP BY cosmetics.id
								ORDER BY brands.name, cosmetics.name");
			$stmt3 -> bindValue(':cosmID', $_REQUEST['cosmID'], PDO::PARAM_INT);
			$stmt3 -> bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
			$stmt3 ->execute();
			$stock4work = $stmt3->fetch(PDO::FETCH_ASSOC);
			
		}
		
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
}

$title=lang::H2_HISTORY . ' ' . $data['brand'] . ' ' . $data['cosm_name'] .', '.$data['volume']; 
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	include($_SERVER['DOCUMENT_ROOT'].'/cosmetics/filters.php'); 
	echo tabs($tabs);
echo '</section>';

echo '<section class="content grid-2x">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	
	
	if ($_REQUEST['cosmID'] != ''){
		echo '<h2 style="grid-column: 1 / -1;">' . $title. '<a href="/cosmetics/cosmetics_edit.php?id='.$data['id'].'&goto=history" title="'.lang::HANDLING_CHANGE.'"><i class="fas fa-edit"></i></a>' .'</h2>';
			
		echo '<div>
			<h2>' . lang::H2_INFO_AND_STATS . '</h2>
			<table class="stripy">
				<tr>
					<td>'.lang::HDR_PURPOSE.':</td>
					<td class="center">' . cosm_purpose($data['purpose'])	. '</td>
				</tr>
				<tr>
					<td>'.lang::HDR_FIRST_SHIPMENT.':</td>
					<td class="center">' . correctDate($data['first_shipment'])	. '</td>
				</tr>
				<tr>
					<td>'.lang::HDR_LAST_SHIPMENT.':</td>
					<td class="center">' . correctDate($data['last_shipment'])	. '</td>
				</tr>
				<tr>
					<td>'.lang::HDR_TOTAL_QTY.':</td>
					<td class="center">' . correctNumber($data['total_qty'])	. '</td>
				</tr>
				<tr>
					<td>'.lang::HDR_WORKTYPE_PRICE_RANGE.':</td>
					<td class="center">' . correctNumber($data['min_price'],2) .' - '. correctNumber($data['max_price'],2)	. curr() . '</td>
				</tr>
				<tr>';
					if($data['purpose'] == 0 || $data['purpose'] == 2) {
						echo '<td>'. lang::HDR_AVAILABILITY. ':<br/>' .lang::HDR_PCS . ' | ' . lang::HDR_GR.' | ' . lang::HDR_COST_PER_GR .'</td>
							<td class="center">' . correctNumber($stock4work['pcs_available'],1) . ' | ' 
												. correctNumber($stock4work['v_available'],0)   . ' | ' 
												. correctNumber($stock4work['max_price_gr'],2) . curr() .'</td>';
					} else {
						echo '<td>'. lang::HDR_AVAILABILITY. ':</td>
							<td class="center">' . correctNumber($data['total_qty'] - $data['qty_sold']) .' '. lang::HDR_PCS . '</td>'; 
					}
				echo '</tr>
				<tr>
					<td>'.lang::HDR_ARCHIVE.':</td>
					<td class="center">' . yes_no_ticks($data['archive']) . '</td>
				</tr>
			</table>
		</div>
		
		<div>
			<h2>' . lang::MENU_PURCHASES . '</h2>
			<table class="stripy">
				<thead>
					<tr>
						<th>'.lang::DATE.'</th>
						<th>'.lang::PLACEHOLDER_QTY.'</th>
						<th>'.lang::HDR_COST.' ('.lang::HDR_PCS.')</th>
					</tr>
				</thead>
				<tbody>';
				foreach($purchases as $purchase) {
					echo '<tr>
						<td class="center">' . correctDate($purchase['date'])	. '</td>
						<td class="center"><a href="/cosmetics/invoice_details.php?id='.$purchase['invoiceID'].'" title="'.lang::HDR_PURCHASE_DETAILS.'">' . $purchase['qty'] . '</a></td>
						<td class="center">' . $purchase['priceIn']	. curr() . '</td>
					</tr>';
				}
				echo '</tbody>	
			</table>
		</div>';
		
		
	
		switch($data['purpose']) {
			case(0):
				cosm_history_work($_REQUEST['cosmID'], $offset_work);
				break;
			case(1):
				cosm_history_sales($_REQUEST['cosmID'], $offset_sale);
				break;
			
			case(2):
				cosm_history_work($_REQUEST['cosmID'], $offset_work);
				cosm_history_sales($_REQUEST['cosmID'], $offset_sale);
				break;
			case(3):
			
				break;
		}
		
		
		
	} else {
		echo '<p>' . lang::ERR_NO_ID . '</p>';
	}
?>
</section>
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>

<script>
	const undo_alert = "<?=lang::HANDLING_UNDO;?>" + '?';

	$('.fa-undo').on('click', undo_sale);
</script>