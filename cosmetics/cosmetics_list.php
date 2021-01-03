<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_GET['tab'] == 'archive'){
	$archive = 1;
	$pageID = 'csm_archive';
} else {
	$archive = 0;
	$pageID = 'csm_active';
}

if($_GET['brandID'] != 'all' && $_GET['brandID'] != '') $brandID='brandID = ' . $_GET['brandID'];
else $brandID = 1;

if($_GET['purpose'] != 'all' && $_GET['purpose'] != '') {
	if($_GET['purpose'] == 3) 
		 $purpose='purpose =' . $_GET['purpose'];
	else $purpose='purpose in (' . $_GET['purpose'] . ',2)';
}
else $purpose = 1;

$sql = "SELECT cosmetics.id, brands.name as brand, cosmetics.name, articul, volume, purpose, cosmetics.brandID
	FROM `cosmetics` 
	LEFT JOIN brands ON cosmetics.brandID = brands.id
	WHERE cosmetics.archive = :archive 
		AND $brandID
		AND $purpose";
$stmt = $pdo->prepare($sql);
try 
{
	$stmt = $pdo->prepare($sql);
	$stmt -> bindValue(':archive', $archive, PDO::PARAM_INT);
	$stmt ->execute();
	$count=1;
	while ($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$brands[$data[$count]['brandID']]=$data[$count]['brand'];
		$count++;
	}
} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}$stmt = $pdo->prepare($sql);
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

if ($brandID != 1)	$title=lang::MENU_COSMETICS . ' ' . $data[1]['brand'];
else 				$title=lang::MENU_COSMETICS;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	include($_SERVER['DOCUMENT_ROOT'].'/cosmetics/filters.php'); 
	echo '<hr>';
	echo '<form method="get" class="filter">
		<fieldset class="noBorders">
			<select name="brandID">';
				brand_select_filter($brands, $archive);
			echo '</select>';
			
			cosm_purpose_select($_GET['purpose'], 1);
			
			echo '<input name="tab" type="hidden" value="'.$_GET['tab'].'">
			<input type="submit" value="'.lang::BTN_SHOW.'"">';
			
			
		echo '</fieldset>
	</form>';
	
	
	echo tabs($tabs, $pageID);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'.$title.'</h2>';

	if ($count > 1) {?>
		<table class='stripy table-autosort table-autofilter'>
			<thead>
				<tr>
					<th style='max-width: 10%;'>№			</th>
					<?php if ($brandID == 1) echo '<th class="table-sortable:*">'. lang::HDR_BRAND .'</th>'; ?>
					<th class='table-sortable:*'><?=lang::HDR_ITEM_NAME;?></th>
					<th class='table-sortable:*'><?=lang::HDR_VOLUME;?></th>
					<th class="mobile-hide table-sortable:*"><?=lang::HDR_PURPOSE;?></th>
					<th class="mobile-hide table-sortable:*"><?=lang::HDR_ARTICUL;?></th>
					<th><?php echo lang::HDR_HANDLING;?></th>
				</tr>
			</thead>
			<tbody>	
			<?php $count=1;
				while($data[$count] !=NULL) {
					echo '<tr>
							<td class="small center" style="max-width:10%;">' . $count	. '</td>';
							if ($brandID == 1) echo '<td>' . $data[$count]['brand']	. '</td>';
							echo '<td><a href="/cosmetics/history.php?cosmID='.$data[$count]['id'].'">' . $data[$count]['name']	. '</a></td>
							<td>' . $data[$count]['volume']	. '</td>
							<td class="mobile-hide">' . cosm_purpose($data[$count]['purpose']). '</td>
							<td class="mobile-hide">' . $data[$count]['articul']	. '</td>
							<td class="center">	
								<a title="'. $handle['change']['title'] . '" href="' . $handle['change']['link_start'] . $data[$count]['id'] . '">' . $handle['change']['button'] . '</a>';
									
								if ($archive == 0) { ?>
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
	if ($_GET['tab'] != 'archive') echo '<a class="button" href="/cosmetics/cosmetics_add.php">'.lang::BTN_ADD.'</a>';
	?>
</section>	
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>