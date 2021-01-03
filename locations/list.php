<?php 
$access = 90;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_GET['tab'] == 'archive'){
	$archive = 1;
	$pageID = 'loc_archive';
} else {
	$archive = 0;
	$pageID = 'loc_active';
}

$i = 1;
		

switch (true)
{
	case(isset($_SESSION['locationName'])):
		$sql = "SELECT * from locations WHERE id=:id AND archive=:archive";
		try 
			{
				$stmt = $pdo->prepare($sql);
				$stmt -> bindValue(':id', $_SESSION['locationIDs'], PDO::PARAM_INT);
				$stmt -> bindValue(':archive', $archive, PDO::PARAM_INT);
				$stmt ->execute();
				$locations[1] = $stmt->fetch(PDO::FETCH_ASSOC);
				$count=2; 									// для отображения результата нужно
			} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}$stmt = $pdo->prepare($sql);
				
		break;
	default:
		$locationIDs = explode(',',$_SESSION['locationIDs']);
		$sql = "SELECT * from locations WHERE id=:id AND archive=:archive ORDER BY city, name";
		$stmt = $pdo->prepare($sql);
		$count = 1;
		
		foreach ($locationIDs as $id)
		{
			try 
			{
				$stmt -> bindParam(':id', $id, PDO::PARAM_INT);
				$stmt -> bindValue(':archive', $archive, PDO::PARAM_INT);
				$stmt ->execute();
				while($locations[$count] = $stmt->fetch(PDO::FETCH_ASSOC))	$count++;
			} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}

		}
}
		
$pdo=NULL;

// Кнопки управления доступом
$handle = array();
$handle['change'] = array(
	'title'=>lang::HANDLING_CHANGE, 
	'link_start'=>'/locations/location_edit.php?id=',
	'button'=>'<i class="fas fa-edit"></i>'
);
$handle['block'] = array(
	'title'=>lang::HANDLING_BLOCK, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=locations&archive=1&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'],
	'button'=>'<i class="fas fa-trash"></i>',
	'alertMSG'=>lang::ALERT_BLOCK_LOCATION
);
$handle['restore'] = array(
	'title'=>lang::HANDLING_RESTORE, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=locations&archive=0&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'],'button'=>'<i class="fas fa-trash-restore"></i>',
	'alertMSG'=>lang::ALERT_RESTORE_LOCATION
);

$title=lang::H2_LOCATIONS;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, $pageID);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'.lang::H2_LOCATIONS.'</h2>';

	if ($count > 1) {
	?>



		<table class='stripy table-autosort table-autofilter'>
			<thead>
				<tr>
					<th style='max-width: 10%;'>№			</th>
					<th class='table-sortable:*'><?=lang::HDR_CITY;?></th>
					<th>						 <?=lang::HDR_LOCATION;?></th>
					<th class='table-sortable:*'><?=lang::HDR_OPERATING_HOURS;?></th>
					<th><?php echo lang::HDR_HANDLING;?></th>
				</tr>
			</thead>
			<tbody>	
			<?php $count=1;
				while($locations[$count] !=NULL) {
					echo '<tr>
							<td class="small center">'. $count .'</td>
							<td>' . $locations[$count]['city']	. '</td>
							<td>' . $locations[$count]['name'] 	. '</td>
							<td class="center">' . $locations[$count]['openFrom'] .' - ' .$locations[$count]['openTill'] . '</td>
							<td class="center">';?>	
								<a title="<?php echo $handle['change']['title']; ?>" href="<?php echo $handle['change']['link_start'] . $locations[$count]['id'] .'&tab=active'; ?>"><?php echo $handle['change']['button']; ?></a>
									
								<?php if ($archive == 0) { ?>
									<a title="<?php echo $handle['block']['title'] ?>" href="<?php echo $handle['block']['link_start'] . $locations[$count]['id'].$handle['block']['link_finish']; ?>" onclick='return confirm("<?php echo $handle['block']['alertMSG'] . '\"'. $locations[$count]['name'] . '\"?'; ?>");'><?php echo $handle['block']['button']; ?></a>
									
								<?php } else { ?>
									<a title="<?php echo $handle['restore']['title'] ?>" href="<?php echo $handle['restore']['link_start'] . $locations[$count]['id'].$handle['restore']['link_finish']; ?>" onclick='return confirm("<?php echo $handle['restore']['alertMSG'] . '\"'. $locations[$count]['name'] . '\"?'; ?>");'><?php echo $handle['restore']['button']; ?></a>
								<?php }
								
								
							echo '</td>
						</tr>';
					$count++;
				} ?>
			</tbody>	
		</table>

		<?php 
		
	} else {
		echo '<p>' . lang::ERR_NO_INFO . '</p>';
	}
	if ($_GET['tab'] != 'archive') echo '<a class="button" href="/locations/location_add.php?tab=active">'.lang::BTN_ADD.'</a>';?>
</section>
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>