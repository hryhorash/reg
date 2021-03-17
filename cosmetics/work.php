<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if($_GET['brandID'] != 'all' && $_GET['brandID'] != '') $brandID='brandID = ' . $_GET['brandID'];
else $brandID = 1;

// ?запрос с учетом расхода "универсальной" косметики (с округлением в большую сторону)
$sql = "SELECT cosmetics.id, brandID, brands.name as brand, cosmetics.name, articul, cosmetics.volume
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
		AND $brandID
   GROUP BY cosmetics.id
   ORDER BY brands.name, cosmetics.name";
$stmt = $pdo->prepare($sql);
try 
{
	$stmt = $pdo->prepare($sql);
	$stmt ->bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
	$stmt ->execute();
	$total_pcs = $total_v = $total_price = 0;
	$count=1;
	while ($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC)) {
		if($brandID = 1) {
			$brands[$data[$count]['brandID']]=$data[$count]['brand'];
		}
		$total_pcs = $total_pcs + $data[$count]['pcs_available'];
		$total_v = $total_v + $data[$count]['v_available'];
		$total_price = $total_price + $data[$count]['v_available'] * $data[$count]['max_price_gr'];
		$count++;
	}
} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}$stmt = $pdo->prepare($sql);
$pdo=NULL;


if ($brandID != 1)	$title=$data[1]['brand'] . '. ' .lang::H2_WORK_COSM_AVAILABLE;
else 				$title=lang::H2_WORK_COSM_AVAILABLE;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo '<p class="title">' . lang::SIDEBAR_FILTERS . '</p>';
	echo '<form method="get" class="filter">
		<fieldset class="noBorders">
			<select name="brandID">';
				brand_select_filter($brands);
			echo '</select>';
			
			cosm_purpose_select($_GET['purpose'], 1);
			
			echo '<input name="tab" type="hidden" value="'.$_GET['tab'].'">
			<input type="submit" value="'.lang::BTN_SHOW.'"">';
			
			
		echo '</fieldset>
	</form>';
	
	
	echo tabs($tabs, 'work');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc($title);

	if ($count > 1) {?>
		<table class='stripy table-autosort table-autofilter'>
			<thead>
				<tr>
					<th style='max-width: 10%;'>№			</th>
					<th class='table-sortable:*'><?=lang::HDR_ITEM_NAME;?></th>
					<th class='table-sortable:*'><?=lang::HDR_VOLUME;?></th>
					<th class='table-sortable:*'><?=lang::HDR_AVAILABILITY;?><br />
												 <?=lang::HDR_PCS . ' | ' . lang::HDR_GR;?></th>
					<th class='table-sortable:*'><?=lang::HDR_COST_PER_GR;?>*</th>
				</tr>
			</thead>
			<tbody>	
			<?php $count=1;
				while($data[$count] !=NULL) {
					if($data[$count]['pcs_available'] > 0) {
						echo '<tr>
							<td class="small center">' . $count	. '</td>
							<td>' . $data[$count]['brand'] . ' ' . $data[$count]['name']	. '</td>
							<td class="center">' . $data[$count]['volume']	. '</td>
							<td class="center">' . correctNumber($data[$count]['pcs_available'],1) .' | ' 
												 . correctNumber($data[$count]['v_available'],0) . '</td>
							<td class="center">' . correctNumber($data[$count]['max_price_gr'],2) . '</tr>';
					}
					$count++;
				} ?>
				<tfoot>
				<tr>
					<th>			</th>
					<th class='alignRight' colspan="2"><?=lang::HDR_TOTAL;?>:</th>
					<th><?=correctNumber($total_pcs,1) . ' | ' . correctNumber($total_v,0);?></th>
					<th><?=correctNumber($total_price,2) . curr();?></th>
				</tr>
			</tfoot>
			</tbody>	
		</table>
		<p class="small italic">* <?=lang::EXPL_PRICE_PER_GR;?></p>
		<?php 
		
	} else {
		echo '<p>' . lang::ERR_NO_INFO . '</p>';
	}
	?>
</section>	
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>