<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if($_GET['brandID'] != 'all' && $_GET['brandID'] != '') $brandID='brandID = ' . $_GET['brandID'];
else $brandID = 1;

if($_GET['purpose'] !='') 
	 $purpose = $_GET['purpose'];
else $purpose = 1;

// запрос с учетом расхода "универсальной" косметики (с округлением в большую сторону)
$sql = "SELECT cosmetics.id, brandID, brands.name as brand, cosmetics.name, articul, cosmetics.volume, RRP, description
	, COUNT(case when received.qtyOut = 0 then 1 else 0 end) as qty
    , GROUP_CONCAT(received.id) as to_sell_all_IDs
	, MIN(invoices.dateReceived) as dateIn
	, MIN(received.expire) as expire
	, CEIL((SUM(spent.volume) / cosmetics.volume)) as spent_pcs
    , CASE WHEN
            SUM(spent.volume) is not null 
        THEN 
            COUNT(case when received.qtyOut = 0 then 1 else 0 end) - CEIL(SUM(spent.volume) / cosmetics.volume)
        ELSE 
            COUNT(case when received.qtyOut = 0 then 1 else 0 end)
        END
	AS pcs_available
    FROM `cosmetics` 
	LEFT JOIN brands ON cosmetics.brandID = brands.id
	LEFT JOIN received ON cosmetics.id = received.cosmID
    LEFT JOIN invoices ON received.invoiceID = invoices.id
    LEFT JOIN spent ON cosmetics.id = spent.cosmID
    WHERE cosmetics.archive = 0 
    	AND received.qtyOut = 0
		AND purpose IN (1,2)
        AND invoices.state >= 4
		AND $brandID
        AND invoices.locationID = :locationID
   GROUP BY cosmetics.id
   ORDER BY brands.name, cosmetics.name";
$stmt = $pdo->prepare($sql);
try 
{
	$stmt = $pdo->prepare($sql);
	$stmt ->bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
	$stmt ->execute();
	$count=1;
	while ($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC)) {
		if($brandID = 1) {
			$brands[$data[$count]['brandID']]=$data[$count]['brand'];
		}$count++;
	}
} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
$pdo=NULL;

// Кнопки управления доступом
$handle = array();
$handle['change'] = array(
	'title'=>lang::HANDLING_CHANGE, 
	'link_start'=>'/cosmetics/cosmetics_edit.php?id=',
	'button'=>'<i class="fas fa-edit"></i>'
);
$handle['block'] = array(
	'title'=>lang::HANDLING_ARCHIVE, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=cosmetics&archive=1&URL='.$_SERVER['PHP_SELF'].'&brandID='.$_GET['brandID'],
	'button'=>'<i class="fas fa-trash"></i>',
	'alertMSG'=>lang::ALERT_BLOCK_DEFAULT
);
$handle['restore'] = array(
	'title'=>lang::HANDLING_RESTORE, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=cosmetics&archive=0&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'].'&brandID='.$_GET['brandID'],'button'=>'<i class="fas fa-trash-restore"></i>',
	'alertMSG'=>lang::HANDLING_RESTORE
);

if ($brandID != 1)	$title=lang::H2_GOODS_TO_SELL . ' ' . $data[1]['brand'];
else 				$title=lang::H2_GOODS_TO_SELL;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo '<p class="title">' . lang::SIDEBAR_FILTERS . '</p>';
	echo '<form method="get" class="filter">
		<fieldset class="noBorders">
			<select name="brandID">';
				brand_select_filter($brands);
			echo '</select>';
			
			cosm_purpose_select($purpose, 1);
			
			echo '<input name="tab" type="hidden" value="'.$_GET['tab'].'">
			<input type="submit" value="'.lang::BTN_SHOW.'"">';
			
			
		echo '</fieldset>
	</form>';
	
	
	echo tabs($tabs, 'sale');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc($title);

	if ($count > 1) {?>
		<table class='stripy table-autosort table-autofilter'>
			<thead>
				<tr>
					<th style='max-width: 10%;'>№			</th>
					<?php if ($brandID == 1) echo '<th class="table-sortable:*">'. lang::HDR_BRAND .'</th>'; ?>
					<th class="mobile-hide table-sortable:*"><?=lang::HDR_ARTICUL;?></th>
					<th class='table-sortable:*' style="width:30%;"><?=lang::HDR_ITEM_NAME;?><br />
												 <?=lang::HDR_DESCRIPTION;?></th>
					<th class='table-sortable:*'><?=lang::HDR_PRICE;?></th>
					<th class='table-sortable:*'><?=lang::HDR_AVAILABILITY;?></th>
					<th class="mobile-hide table-sortable:*"><?=lang::HDR_DATE_RECEIVED;?><br />
												 <?=lang::HDR_EXPIRE;?></th>
					<th><?php echo lang::HDR_HANDLING;?></th>
				</tr>
			</thead>
			<tbody>	
			<?php $total_pcs = $total_price = 0;
				$count=1;
				while($data[$count] !=NULL) {
					if($data[$count]['pcs_available'] > 0) {
						echo '<tr>
							<td class="small center">' . $count	. '</td>';
							if ($brandID == 1) echo '<td>' . $data[$count]['brand']	. '</td>';
							echo '<td class="mobile-hide">' . $data[$count]['articul']	. '</td>
							<td><a href="sales_archive.php?cosmID='.$data[$count]['id'].'" title="'.lang::HDR_SALE_ARCHIVE.'"><strong>' . $data[$count]['name']	. '</strong>, ' . $data[$count]['volume']	. '</a><br />'
								  . $data[$count]['description'] . '</td>
							<td class="center">' . $data[$count]['RRP'] . curr() .'</td>
							<td class="center">' . $data[$count]['pcs_available'] .'</td>
							<td class="mobile-hide center">' . correctDate($data[$count]['dateIn']) .'<br />';
								if (isset($data[$count]['expire'])) {
									if (strtotime($data[$count]['expire']) <= time()) echo '<p class="warning center">' . correctDate($data[$count]['expire']) . '</p>';
									else echo correctDate($data[$count]['expire']);
								}
							
							echo '</td>
							<td class="center">	
								<a title="'. $handle['change']['title'] . '" href="' . $handle['change']['link_start'] . $data[$count]['id'] . '">' . $handle['change']['button'] . '</a>';
							echo '</td>
						</tr>';
						$total_pcs = $total_pcs + $data[$count]['pcs_available'];
						$total_price = $total_price + $data[$count]['pcs_available'] * $data[$count]['RRP'];
					}
					$count++;
				} ?>
				<tfoot>
				<tr>
					<th>			</th>
					<?php if ($brandID == 1) echo '<th></th>'; ?>
					<th class='alignRight' colspan="2"><?=lang::HDR_TOTAL;?>:</th>
					<th><?=correctNumber($total_price,2) . curr();?></th>
					<th><?=$total_pcs;?></th>
					<th class="mobile-hide"></th>
					<th class="mobile-hide"></th>
				</tr>
			</tfoot>
			</tbody>	
		</table>

		<?php 
		
	} else {
		echo '<p>' . lang::ERR_NO_INFO . '</p>';
	}
	?>
</section>	
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>