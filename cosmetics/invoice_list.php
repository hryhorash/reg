<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_GET['tab'] == 'archive') {
	$state = 'state = 5';
	$pageID = 'inv_archive';
} elseif ($_GET['tab'] == 'all'){
	$state = 1;
	$pageID = 'inv_all';
} else 	{
	$state = 'state != 5';
	$pageID = 'inv_active';
}

if($_GET['month'] != 'all' && $_GET['month'] != '') $month = 'DATE_FORMAT(date, "%Y-%m") = "'.$_GET['month']. '"';
else $month = 1;

if($_GET['brandID'] != 'all' && $_GET['brandID'] != '') $brand = 'cosmetics.brandID='.$_GET['brandID'];
else $brand = 1;

if($_GET['offset'] > 0) $offset = $_GET['offset'];
else $offset = 0;
$limit=25;

$locationID = setLocationID();		
if ($locationID != NULL) {	
	try 
	{
		$stmt = $pdo->prepare("SELECT invoices.id as id,invoices.name as invoice, invoices.date, suppliers.id as supplierID, suppliers.name as supplier, state,
			SUM(priceIn) as sum,
			COUNT(DISTINCT received.id) as items
			FROM `invoices` 
			LEFT JOIN received ON invoices.id=received.invoiceID
            LEFT JOIN suppliers ON invoices.supplierID=suppliers.id
            LEFT JOIN locations ON invoices.locationID=locations.id
			LEFT JOIN cosmetics ON received.cosmID=cosmetics.id
            WHERE locationID = :locationID 
				AND $state
				AND $month
				AND $brand
			GROUP BY invoiceID
			ORDER BY invoices.date DESC
			LIMIT $offset, $limit");		
		$stmt -> bindValue(':locationID', $locationID, PDO::PARAM_INT);
		$stmt ->execute();
		$count=1;
		while ($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC))  $count++;
		
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}$stmt = $pdo->prepare($sql);
	$pdo=NULL;
}

		


// Кнопки управления доступом
$handle = array();
$handle['details'] = array(
	'title'=>lang::HANDLING_VIEW, 
	'link_start'=>'/cosmetics/invoice_details.php?id=',
	'button'=>'<i class="fas fa-eye"></i>'
);

$btn_add = '<a class="button" href="/cosmetics/invoice_add.php">'.lang::BTN_ADD.'</a>';


$title=lang::MENU_PURCHASES;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo '<p class="title">' . lang::SIDEBAR_FILTERS . '</p>';
	echo '<form method="get" class="filter">
		<fieldset class="noBorders" >';
			echo '<select name="month">';
				month_options('invoices');
			echo '</select>';
			echo '<select name="brandID">';
				brand_select_filter();
			echo '</select>';
			echo '<input name="tab" type="hidden" value="'. $_GET['tab'].'" />';
			echo '<input type="submit" value="'. lang::BTN_SHOW.'" />';
		echo'</fieldset>';
	echo '</form>';
	
	
	echo tabs($tabs, $pageID);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc(lang::H2_PURCHASES);


	if ($count > 1) {?>
		<table class='stripy table-autosort table-autofilter'>
			<thead>
				<tr>
					<th style='max-width: 10%;'>№</th>
					<th><?=lang::DATE;?></th>
					<th class='table-sortable:*'><?php echo lang::HDR_SUPPLIER;?></th>
					<th class="mobile-hide table-sortable:*"><?php echo lang::HDR_INVOICE;?></th>
					<th class='table-sortable:*'><?php echo lang::HDR_INVOICE_STATE;?></th>
					<th class="mobile-hide"><?php echo lang::HDR_QTY;?></th>
					<th><?php echo lang::HDR_COST;?></th>
					<th><?php echo lang::HDR_HANDLING;?></th>
				</tr>
			</thead>
			<tbody>	
			<?php $count=1;
				while($data[$count] !=NULL) {
					echo '<tr>
						<td class="small center" style="max-width:10%;">' . ($count+$offset)	. '</td>
						<td>' . correctDate($data[$count]['date'])	. '</td>
						<td>'.$data[$count]['supplier'].'<br /><span class="small">'; brand_names_only($data[$count]['supplierID'],1);	echo '</span></td>
						<td class="mobile-hide">' . $data[$count]['invoice'] . '</td>
						<td>'; invoice_state_read($data[$count]['state']); echo '</td>
						<td class="mobile-hide center">' . $data[$count]['items'] . '</td>
						<td class="center">' . correctNumber($data[$count]['sum'],2) . curr() .'</td>
						<td class="center">';?>	
							<a title="<?php echo $handle['details']['title']; ?>" href="<?php echo $handle['details']['link_start'] . $data[$count]['id']; ?>"><?php echo $handle['details']['button']; ?></a>
						</td>
						</tr>
					<?php $count++;
				} ?>
			</tbody>	
		</table>
		<?php list_navigation_buttons($count,$offset,$limit);
		
	} else {
		echo '<p>' . lang::ERR_NO_INFO . '</p>';
	}
	echo $btn_add;
	?>
</section>	
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>