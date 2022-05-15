<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_GET['tab'] == 'archive'){
	$archive = 1;
	$pageID = 'clt_archive';
} else {
	$archive = 0;
	$pageID = 'clt_active';
}

if (isset($_SESSION['locationSelected'])) {
	$sql = "SELECT c.id, c.name, c.surname, c.prompt, c.note, c.refClientID, c1.name as refName, c1.surname as refSurame, c1.prompt as refPrompt
		FROM clients c
		LEFT JOIN clients c1 ON c.refClientID = c1.id
		WHERE c.locationID = :locationID AND c.archive=:archive 
		ORDER BY c.name, c.surname";
	$stmt = $pdo->prepare($sql);
	try 
	{
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
		$stmt -> bindValue(':archive', $archive, PDO::PARAM_INT);
		$stmt ->execute();
		$count=1;
		while ($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC)) $count++;
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}$stmt = $pdo->prepare($sql);
	$pdo=NULL;
}

		


// Кнопки управления доступом
$handle = array();
$handle['change'] = array(
	'title'=>lang::HANDLING_CHANGE, 
	'link_start'=>'/clients/client_edit.php?id=',
	'button'=>'<i class="fas fa-edit"></i>'
);
$handle['block'] = array(
	'title'=>lang::HANDLING_ARCHIVE, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=clients&archive=1&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'],
	'button'=>'<i class="fas fa-trash"></i>',
	'alertMSG'=>lang::ALERT_BLOCK_DEFAULT
);
$handle['restore'] = array(
	'title'=>lang::HANDLING_RESTORE, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=clients&archive=0&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'],'button'=>'<i class="fas fa-trash-restore"></i>',
	'alertMSG'=>lang::HANDLING_RESTORE
);

$title=lang::MENU_CLIENTS;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';

	include('filters.php');
	echo tabs($tabs, $pageID);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc(lang::MENU_CLIENTS);

	if ($count > 1) { ?>

		<table class='stripy table-autosort table-autofilter'>
			<thead>
				<tr>
					<th style='width: 7ch;'>№</th>
					<th class="table-sortable:*"><?=lang::NAME;?></th>
					<th class="mobile-hide table-sortable:*"><?=lang::HDR_RECOMMENDATION;?></th>
					<th class="mobile-hide" style="width:30%;"><?=lang::HDR_COMMENT;?></th>
					<th style='width: 17ch;'><?php echo lang::HDR_HANDLING;?></th>
				</tr>
			</thead>
			<tbody>	
			<?php $count=1;
				while($data[$count] !=NULL) {
					echo '<tr>
							<td class="small center">'. $count .'</td>
							<td>' . FIO($data[$count]['name'],$data[$count]['surname'],$data[$count]['prompt']) . '</td>
							<td class="mobile-hide"><a href="/clients/client_profile.php?id='.$data[$count]['refClientID'].'">' . FIO($data[$count]['refName'],$data[$count]['refSurname'],$data[$count]['refPrompt']) . '</a></td>
							<td class="mobile-hide">' . $data[$count]['note']	. '</td>
							<td class="center">';?>	
								<a title="<?=lang::TOOLTIP_PROFILE;?>" href="/clients/client_profile.php?id=<?=$data[$count]['id'];?>"><i class="fas fa-eye"></i></a>
								<a title="<?=$handle['change']['title']; ?>" href="<?php echo $handle['change']['link_start'] . $data[$count]['id']; ?>"><?php echo $handle['change']['button']; ?></a>
									
								<?php if ($archive == 0) { ?>
									<a title="<?php echo $handle['block']['title'] ?>" href="<?php echo $handle['block']['link_start'] . $data[$count]['id'].$handle['block']['link_finish']; ?>" onclick='return confirm("<?php echo $handle['block']['alertMSG'] . '\"'. $data[$count]['name'] . '\"?'; ?>");'><?php echo $handle['block']['button']; ?></a>
									
								<?php } else { ?>
									<a title="<?php echo $handle['restore']['title'] ?>" href="<?php echo $handle['restore']['link_start'] . $data[$count]['id'].$handle['restore']['link_finish']; ?>" onclick='return confirm("<?php echo $handle['restore']['alertMSG'] . '\"'. $data[$count]['name'] . '\"?'; ?>");'><?php echo $handle['restore']['button']; ?></a>
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
	if ($_GET['tab'] != 'archive') echo '<a class="button" href="/clients/client_add.php">'.lang::BTN_ADD.'</a>';
	?>
</section>
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>