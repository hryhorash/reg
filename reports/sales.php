<?php 
$access = 90;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_GET['cosmID'] > 0) $cosmID_cond = 'cosmID = ' . $_GET['cosmID'];
else $cosmID_cond = 1;

if(isset($_SESSION['monthSelected'])) $month_cond = 'DATE_FORMAT(dateOut, "%Y-%m") = "' . $_SESSION['monthSelected'] . '"';
else $month_cond = 1;
	
	$sql = "SELECT received.id, cosmID, qtyOut, priceIn, dateOut, priceOut, (priceOut - priceIn) as profit
					, invoices.date as dateIn
					, clients.id, clients.name, clients.surname, clients.prompt
					, cosmetics.id, CONCAT(brands.name, ' ', cosmetics.name, ', ', cosmetics.volume) as cosm_name
			FROM `received` 
			LEFT JOIN clients ON received.soldToID = clients.id
			LEFT JOIN invoices ON invoiceID = invoices.id
			LEFT JOIN cosmetics ON received.cosmID = cosmetics.id
			LEFT JOIN brands ON cosmetics.brandID = brands.id
			WHERE qtyOut > 0
				AND invoices.locationID = :locationID
				AND $cosmID_cond
				AND $month_cond
			ORDER BY dateOut DESC";
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
	$stmt->execute();
	
	$total_qty = $total_income = $total_expences = $total_profit = 0;
	$count=1;
	while ($sales[$count] = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$total_qty = $total_qty + $sales[$count]['qtyOut'];
		$total_income = $total_income + $sales[$count]['priceOut'];
		$total_expences = $total_expences + $sales[$count]['priceIn'];
		$total_profit = $total_profit + $sales[$count]['profit'];
		
		
		$dateIn = date_create($sales[$count]['dateIn']);
		$dateOut = date_create($sales[$count]['dateOut']);
		$interval[$count] = date_diff($dateIn, $dateOut);
		
		$count++;
	}
	
	
	
if(isset($_SESSION['monthSelected'])) $title = $_SESSION['monthSelected'] . ': ' . lang::H2_SALES_REPORT . lang::TXT_AT;
else $title = lang::H2_SALES_REPORT . lang::TXT_AT;

//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	include('filters.php');
	echo tabs($tabs, 'sales');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc($title);
	
	if ($count > 1) {?>
		<table class='stripy table-autofilter table-autosort'>
			<thead>
				<tr>
					<th>№			</th>
					<th class="table-sortable:*"><?=lang::DATE;?></th>
					<?php if (!isset($_GET['cosmID'])) echo '<th>'. lang::HDR_ENTITY_NAME .'</th>';?>
					<th class="table-sortable:numeric"><?=lang::HDR_NETTO;?></th>
					<th class="table-sortable:numeric"><?=lang::HDR_PRICE;?></th>
					<th class="table-sortable:numeric"><?=lang::HDR_PROFIT;?></th>
					<th class="table-sortable:numeric"><?=lang::HDR_SALE_PERIOD;?></th>
				</tr>
			</thead>
			<tbody>	
				<?php $count = 1;
				while($sales[$count] != NULL) {
					echo '<tr>
						<td class="small center">'. $count .'</td>
						<td>' . correctDate($sales[$count]['dateOut']) . '</td>';
						if (!isset($_GET['cosmID'])) echo '<td>' . $sales[$count]['cosm_name'] . '</td>';
						echo '<td class="center">' . correctNumber($sales[$count]['priceIn']) . '</td>
						<td class="center">' . correctNumber($sales[$count]['priceOut']) . '</td>
						<td class="center">' . correctNumber($sales[$count]['profit']) . '</td>
						<td class="center">' . $interval[$count]->format('%a') . '</td>
					</tr>';
					$days[] =  $interval[$count]->format('%a');
					$count++;
				} ?>
				<tfoot>
				<tr>
					<th colspan="2"><?=lang::HDR_TOTAL;?>:</th>
					<?php if (!isset($_GET['cosmID'])) echo '<th></th>';?>
					<th><?=correctNumber($total_expences);?></th>
					<th><?=correctNumber($total_income);?></th>
					<th><?=correctNumber($total_profit);?></th>
					<th><?=correctNumber(array_sum($days) / count($days));?></th>
				</tr>
				</tfoot>
			</tbody>	
		</table>
		
			
	<?php } else {
		echo lang::EXP_USE_FILTER;
	}
	
	?>
</section>	
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>