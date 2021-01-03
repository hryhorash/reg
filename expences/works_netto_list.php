<?php $access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

try {
	$stmt = $pdo->prepare("SELECT service_netto.id, service_netto.name, service_netto.cost
		FROM `service_netto` 
		ORDER BY service_netto.name
		");
	$stmt->execute();
	$count = 1;
	while($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC)) $count++;
} catch (PDOException $ex){
	include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
}


$pdo = NULL;

// Кнопки управления доступом
$handle = array();
$handle['change'] = array(
	'title'=>lang::HANDLING_CHANGE, 
	'link_start'=>'/expences/works_netto_edit.php?id=',
	'link_finish'=>'&tab='. $_GET['tab'],
	'button'=>'<i class="fas fa-edit"></i>'
);

$title = lang::HDR_SERVICE_NETTO;
//----------------------------VIEW-------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, 'net_active');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>' . lang::HDR_SERVICE_NETTO . '</h2>';

	if ($count > 1) { ?>
		
		<table class="stripy table-autosort table-autofilter">
			<thead>
			<tr>
				<th style='max-width: 10%;'>№</th>
				<th class='table-sortable:*'><?=lang::HDR_ITEM_NAME;?></th>
				<th><?=lang::HDR_COST . ', ' . curr();?></th>
				<th><?=lang::HDR_HANDLING;?></th>
			</tr>
			</thead>
		<? $count = 1;
		while($data[$count] != NULL) { 
			echo '<tr>
				<td class="small center">'. $count .'</td>
				<td>' . $data[$count]['name'] . '</td>
				<td class="center">' . $data[$count]['cost'] . '</td>
				<td class="center">';?>
					<a title="<?php echo $handle['change']['title']; ?>" href="<?php echo $handle['change']['link_start'] . $data[$count]['id'] . $handle['change']['link_finish']; ?>"><?php echo $handle['change']['button']; ?></a>
						
					
				<?php echo '</td>
			</tr>';
			$count++;
		}
		echo "</table>";
	} else {
		echo '<p>' . lang::ERR_NO_INFO . '</p>';
	}

	echo '<a class="button" href="works_netto_add.php">'.lang::BTN_ADD.'</a>';
echo '</section>';
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>