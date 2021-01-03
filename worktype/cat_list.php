<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_GET['tab'] == 'archive'){
	$archive = 1;
	$pageID = 'cat_archive';
} else {
	$archive = 0;
	$pageID = 'cat_active';
}
	

$sql = "SELECT worktype_cat.id, category, GROUP_CONCAT(DISTINCT service_netto.name SEPARATOR '<br/>') as serv_netto
		FROM worktype_cat 
		LEFT JOIN worktype_netto ON worktype_cat.id = worktype_netto.catID
		LEFT JOIN service_netto ON  worktype_netto.nettoID = service_netto.id
		WHERE archive=:archive 
		GROUP BY category
		ORDER BY category";
try 
	{
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':archive', $archive, PDO::PARAM_INT);
		$stmt ->execute();
		$count=1;
		while ($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC)) $count++;
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}$stmt = $pdo->prepare($sql);
				
				
		
$pdo=NULL;

// Кнопки управления доступом
$handle = array();
$handle['change'] = array(
	'title'=>lang::HANDLING_CHANGE, 
	'link_start'=>'/worktype/cat_edit.php?id=',
	'button'=>'<i class="fas fa-edit"></i>'
);
$handle['block'] = array(
	'title'=>lang::HANDLING_BLOCK, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=worktype_cat&archive=1&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'],'button'=>'<i class="fas fa-trash"></i>',
	'alertMSG'=>lang::ALERT_BLOCK_DEFAULT
);
$handle['restore'] = array(
	'title'=>lang::HANDLING_RESTORE, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=worktype_cat&archive=0&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'],
	'button'=>'<i class="fas fa-trash-restore"></i>',
	'alertMSG'=>lang::HANDLING_RESTORE
);

$title=lang::MENU_WORKTYPE_CAT;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 

echo '<section class="sidebar">';
	echo tabs($tabs, $pageID);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'.lang::MENU_WORKTYPE_CAT.'</h2>';

	if ($count > 1) {
	?>



		<table class='stripy table-autosort table-autofilter'>
			<thead>
				<tr>
					<th style='max-width: 10%;'>№			</th>
					<th class='table-sortable:*'><?=lang::HDR_WORKTYPE_CAT;?></th>
					<th class='table-sortable:*'><?=lang::HDR_SERVICES_NETTO;?></th>
					<th><?php echo lang::HDR_HANDLING;?></th>
				</tr>
			</thead>
			<tbody>	
			<?php $count=1;
				while($data[$count] !=NULL) {
					echo '<tr>
							<td class="small center">'. $count .'</td>
							<td>' . $data[$count]['category']	. '</td>
							<td>' . $data[$count]['serv_netto']	. '</td>
							<td class="center">'; ?>	
								<a title="<?php echo $handle['change']['title']; ?>" href="<?php echo $handle['change']['link_start'] . $data[$count]['id'] .'&tab=active'; ?>"><?php echo $handle['change']['button']; ?></a>
								
								<?php if ($archive == 0) { ?>
									<a title="<?php echo $handle['block']['title'] ?>" href="<?php echo $handle['block']['link_start'] . $data[$count]['id'].$handle['block']['link_finish']; ?>" onclick='return confirm("<?php echo $handle['block']['alertMSG'] . $data[$count]['category'] . '?'; ?>");'><?php echo $handle['block']['button']; ?></a>
									
								<?php } else { ?>
									<a title="<?php echo $handle['restore']['title'] ?>" href="<?php echo $handle['restore']['link_start'] . $data[$count]['id'].$handle['restore']['link_finish']; ?>" onclick='return confirm("<?php echo $handle['restore']['alertMSG'] . $data[$count]['category'] . '?'; ?>");'><?php echo $handle['restore']['button']; ?></a>
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
	if ($_GET['tab'] != 'archive') echo '<a class="button" href="/worktype/cat_add.php?tab=active">'.lang::BTN_ADD.'</a>';
	?>
</section>
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>