<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');


// Заголовки
$tabs = array();
$tabs[] =  array('tab'=>'active','link'=>$_SERVER['PHP_SELF'] . '?tab=active','name'=>lang::TAB_ACTIVE);
$tabs[] =  array('tab'=>'archive','link'=>$_SERVER['PHP_SELF'] . '?tab=archive','name'=>lang::TAB_ARCHIVE);
$tabs[] =  array('tab'=>'add','link'=>'/worktype/work_add.php?tab=active','name'=>lang::BTN_ADD);	
$tabs[] =  array('tab'=>'worktype_cat','link'=>'/worktype/cat_list.php?tab=active','name'=>lang::MENU_WORKTYPE_CAT);


if ($_GET['tab'] == 'archive'){
	$archive = 1;
} else $archive = 0;

		

$sql = "SELECT worktypes.id, category, worktypes.name, target, minPrice, maxPrice, duration 
		FROM worktypes 
		LEFT JOIN worktype_cat ON worktypes.catID = worktype_cat.id
		WHERE worktypes.archive=:archive 
		ORDER BY category, name";
$stmt = $pdo->prepare($sql);
try 
	{
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':archive', $archive, PDO::PARAM_INT);
		$stmt ->execute();
		$count=1;
		while ($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
		//	$target_arr[]= $data[$count]['target'];
			$count++;
		}
		//$target_array=array_unique($target_arr);
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}$stmt = $pdo->prepare($sql);
				
				
		
$pdo=NULL;

// Кнопки управления доступом
$handle = array();
$handle['change'] = array(
	'title'=>lang::HANDLING_CHANGE, 
	'link_start'=>'/worktype/work_edit.php?id=',
	'button'=>'<i class="fas fa-edit"></i>'
);
$handle['block'] = array(
	'title'=>lang::HANDLING_BLOCK, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=worktypes&archive=1&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'],
	'button'=>'<i class="fas fa-trash"></i>',
	'alertMSG'=>lang::ALERT_BLOCK_DEFAULT
);
$handle['restore'] = array(
	'title'=>lang::HANDLING_RESTORE, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=worktypes&archive=0&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'],
	'button'=>'<i class="fas fa-trash-restore"></i>',
	'alertMSG'=>lang::HANDLING_RESTORE
);

$title=lang::MENU_WORKS;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');

echo '<h2>'.lang::MENU_WORKS.'</h2>';

echo tabs($tabs);

if ($count > 1) {
?>

	<table class='stripy table-autosort table-autofilter'>
		<thead>
			<tr>
				<th style='max-width: 10%;'>№</th>
				<th class='table-sortable:*'><?=lang::HDR_WORKTYPE_CAT;?></th>
				<th class='table-sortable:*'><?=lang::HDR_ITEM_NAME;?></th>
				<th class='table-sortable:*'><?=lang::HDR_AVG_DURATION;?></th>
				<th class='table-sortable:*'><?=lang::HDR_WORKTYPE_TARGET;?></th>
				<th class='table-sortable:*'><?=lang::HDR_WORKTYPE_PRICE_RANGE;?></th>
				<th><?php echo lang::HDR_HANDLING;?></th>
			</tr>
		</thead>
		<tbody>	
		<?php 
		
			/*foreach ($target_array as $target)
			{
				echo '<tr>
						<td class="collapsible" colspan="5"><i class="fas fa-eye"></i>'; echo work_target($target); echo '</td>
					 </tr>';
			*/
				$count=1;
				while($data[$count] !=NULL) {
				//	if ($data[$count]['target'] == $target) {
						echo '<tr>
							<td class="small center">'. $count .'</td>
							<td>' . $data[$count]['category']	. '</td>
							<td>' . $data[$count]['name']	. '</td>
							<td class="center">' . event_duration_read($data[$count]['duration'])	. '</td>
							<td>'; echo work_target($data[$count]['target']); echo '</td>
							<td>' . $data[$count]['minPrice'] .'-'.	$data[$count]['maxPrice'] . ' '. $_SESSION['settings']['currency'] .'</td>
							<td class="center">';	?>
								<a title="<?php echo $handle['change']['title']; ?>" href="<?php echo $handle['change']['link_start'] . $data[$count]['id'] .'&tab=active'; ?>"><?php echo $handle['change']['button']; ?></a>
								<?php if ($archive == 0) { ?>
									<a title="<?php echo $handle['block']['title'] ?>" href="<?php echo $handle['block']['link_start'] . $data[$count]['id'].$handle['block']['link_finish']; ?>" onclick='return confirm("<?php echo $handle['block']['alertMSG'] . '\"'.$data[$count]['name'] . '\"?'; ?>");'><?php echo $handle['block']['button']; ?></a>
									
								<?php } else { ?>
									<a title="<?php echo $handle['restore']['title'] ?>" href="<?php echo $handle['restore']['link_start'] . $data[$count]['id'].$handle['restore']['link_finish']; ?>" onclick='return confirm("<?php echo $handle['restore']['alertMSG'] . '\"'.$data[$count]['name'] . '\"?'; ?>");'><?php echo $handle['restore']['button']; ?></a>
								<?php }
								
									
								echo '</td>
						</tr>';
					//}
					$count++;
				}
			//} ?>
		</tbody>	
	</table>

	<?php 
	
} else {
	echo '<p>' . lang::ERR_NO_INFO . '</p>';
}
if ($_GET['tab'] != 'archive') echo '<a class="button" href="/worktype/work_add.php?tab=active">'.lang::BTN_ADD.'</a>';
?>
	
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>