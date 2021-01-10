<?php $access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_GET['tab'] == 'archive'){
	$archive = 1;
	$pageID = 'rate_archive';
} else {
	$archive = 0;
	$pageID = 'rate_active';
}

if (isset($_SESSION['locationSelected'])) {	
	try {
		$stmt = $pdo->prepare("SELECT stakes.id , category, subcategory, date, unitPrice, monthlyPrice, locations.name as location, stakes.locationID 
			FROM stakes
			LEFT JOIN expences_subcat ON stakes.subcatID = expences_subcat.id
			LEFT JOIN expences_cat ON expences_subcat.catID = expences_cat.id
			LEFT JOIN locations ON stakes.locationID = locations.id
			WHERE stakes.archive=:archive
				AND locationID = :locID
			ORDER BY subcategory ASC, stakes.date DESC");
		$stmt->bindValue(':archive', $archive, PDO::PARAM_INT);
		$stmt->bindValue(':locID', $_SESSION['locationSelected'], PDO::PARAM_INT);
		$stmt->execute();
		$count = 1;
		while($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC))	$count++;
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
	}
}
$pdo = NULL;

// Кнопки управления доступом
$handle = array();
$handle['change'] = array(
	'title'=>lang::HANDLING_CHANGE, 
	'link_start'=>'/expences/stakes_edit.php?id=',
	'link_finish'=>'&loc='. location_URL($_GET['loc']),
	'button'=>'<i class="fas fa-edit"></i>'
);
$handle['block'] = array(
	'title'=>lang::HANDLING_BLOCK, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=stakes&archive=1&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'],
	'button'=>'<i class="fas fa-trash"></i>',
	'alertMSG'=>lang::ALERT_BLOCK_DEFAULT
);
$handle['restore'] = array(
	'title'=>lang::HANDLING_RESTORE, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=stakes&archive=0&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'],
	'button'=>'<i class="fas fa-trash-restore"></i>',
	'alertMSG'=>lang::HANDLING_RESTORE
);

$title = lang::MENU_STAKES;
//----------------------------VIEW-------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, $pageID);
echo '</section>';

echo '<section class="content">';
	include_once($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc(lang::MENU_STAKES);

	if ($count > 1) { ?>
		
		<table class="stripy table-autosort table-autofilter">
			<thead>
			<tr>
				<th style='max-width: 10%;'>№</th>
				<th class='table-sortable:*'><?=lang::HDR_ITEM_NAME;?></th>
				<th class='table-sortable:*'><?=lang::HDR_LOCATION;?></th>
				<th class='table-sortable:*'><?=lang::HDR_PRICE;?></th>
				<th><?=lang::HDR_ACTIVE_FROM;?></th>
				<th><?=lang::HDR_HANDLING;?></th>
			</tr>
			</thead>
		<? $count = 1;
		while($data[$count] != NULL) {
			echo '<tr>
				<td class="small center">'. $count .'</td>
				<td>' . $data[$count]['subcategory'] . '</td>
				<td>' . $data[$count]['location'] . '</td>
				<td>';
					if ($data[$count]['unitPrice'] != 0) 
					{
						echo $data[$count]['unitPrice']; echo curr(); echo ' '. lang::PER_PIECE_PLACEHOLDER;
					} else 
					{
						echo $data[$count]['monthlyPrice']; echo curr(); echo lang::PER_MONTH .'</td>';
					}
				echo '<td class="center">' . correctDate($data[$count]['date']) . '</td>
				<td class="center">';?>
					<a title="<?php echo $handle['change']['title']; ?>" href="<?php echo $handle['change']['link_start'] . $data[$count]['id'] . $handle['change']['link_finish']; ?>"><?php echo $handle['change']['button']; ?></a>
						
					<?php if ($archive == 0) { ?>
						<a title="<?php echo $handle['block']['title'] ?>" href="<?php echo $handle['block']['link_start'] . $data[$count]['id'] . $handle['block']['link_finish']; ?>" onclick='return confirm("<?php echo $handle['block']['alertMSG']. '\"'.$data[$count]['subcategory'].'\"?'; ?>");'><?php echo $handle['block']['button']; ?></a>
						
					<?php } else { ?>
						<a title="<?php echo $handle['restore']['title'] ?>" href="<?php echo $handle['restore']['link_start'] . $data[$count]['id'] . $handle['restore']['link_finish']; ?>" onclick='return confirm("<?php echo $handle['restore']['alertMSG']. '\"'.$data[$count]['subcategory'].'\"?'; ?>");'><?php echo $handle['restore']['button']; ?></a>
					<?php }
				echo '</td>
			</tr>';
			$count++;
		}
		echo "</table>";
	} else {
		echo '<p>' . lang::ERR_NO_INFO . '</p>';
	}

	if ($_GET['tab'] != 'archive') echo '<a class="button" href="/expences/stakes_add.php?tab=active">'.lang::BTN_ADD.'</a>';
echo '</section>';
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>