<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');
if ($_GET['tab'] == 'archive'){
	$archive = 1;
	$pageID = 'day_archive';
} else {
	$archive = 0;
	$pageID = 'day_active';
}


if (isset($_SESSION['locationSelected'])) {
	try 
	{
		$sql = "SELECT locations_vacations.id, city, locations.name, locations_vacations.date, weekday, comment
			FROM locations 
			LEFT JOIN locations_vacations ON locations.id=locations_vacations.locationID
			WHERE archive=:archive AND locationID = :id
			ORDER BY city, locations.name";
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':archive', $archive, PDO::PARAM_INT);
		$stmt -> bindValue(':id', $_SESSION['locationSelected'], PDO::PARAM_INT);
		$stmt ->execute();
		$count = 1;
		while($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC))	{
			$dayOff[$count] = dayOff($data[$count]['date'], $data[$count]['weekday']);
			$count++;
		}
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}

}
$pdo=NULL;

// Кнопки управления доступом
$handle = array();
$handle['change'] = array(
	'title'=>lang::HANDLING_CHANGE, 
	'link_start'=>'/locations/location_dayOff_edit.php?id=',
	'button'=>'<i class="fas fa-edit"></i>'
);
$handle['delete'] = array(
	'title'=>lang::HANDLING_DELETE, 
	'link_start'=>'/config/delete.php?id=',
	'link_finish'=>'&table=locations_vacations&URL='.$_SERVER['PHP_SELF'],
	'button'=>'<i class="fas fa-trash"></i>',
	'alertMSG'=>lang::ALERT_DELETE
);

$title=lang::H2_DAYS_OFF;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, $pageID);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc(lang::H2_DAYS_OFF);

	if ($count > 1) {
	?>
		<table class='stripy table-autosort table-autofilter'>
			<thead>
				<tr>
					<th style='max-width: 10%;'>№			</th>
					<th class='table-sortable:*'><?=lang::HDR_CITY;?></th>
					<th class='table-sortable:*'><?=lang::HDR_DAY_OFF;?></th>
					<th							><?=lang::HDR_COMMENT;?></th>
					<th><?php echo lang::HDR_HANDLING;?></th>
				</tr>
			</thead>
			<tbody>	
			<?php $count=1;
				while($data[$count] !=NULL) {
					echo '<tr>
							<td class="small center">'. $count .'</td>
							<td>' . $data[$count]['city']	. '</td>
							<td class="center">' . $dayOff[$count] . '</td>
							<td>' . $data[$count]['comment']	. '</td>';?>
							<td class="center">	
								<a title="<?php echo $handle['change']['title']; ?>" href="<?php echo $handle['change']['link_start'] . $data[$count]['id']; ?>"><?php echo $handle['change']['button']; ?></a>
								<a title="<?php echo $handle['delete']['title'] ?>" href="<?php echo $handle['delete']['link_start'] . $data[$count]['id'].$handle['delete']['link_finish']; ?>" onclick='return confirm("<?php echo $handle['delete']['alertMSG'] . '\"'. $dayOff[$count] . '\"?'; ?>");'><?php echo $handle['delete']['button']; ?></a>
							</td>
					<?php echo' </tr>';
					$count++;
				} ?>
			</tbody>	
		</table>

		<?php 
		
	} else {
		echo '<p>' . lang::ERR_NO_INFO . '</p>';
	}
	echo '<a class="button" href="/locations/location_dayOff_add.php">'.lang::BTN_ADD.'</a>';
	?>
</section>	
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>