<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_GET['tab'] == 'archive'){
	$archive = 1;
	$pageID = 'spl_archive';
} else {
	$archive = 0;
	$pageID = 'spl_active';
}

if(isset($_GET['brandID']) && $_GET['brandID'] != 'all' && $_GET['brandID'] != '') $brandID='brands.id = ' . $_GET['brandID'];
else $brandID = 1;


try 
{
	$stmt = $pdo->prepare("SELECT suppliers.id,suppliers.name, VAT, contact, position, phones, email, site, comment,GROUP_CONCAT(brands.name SEPARATOR ', ') as brandNames
		FROM suppliers
        LEFT JOIN supplier_brands ON suppliers.id=supplier_brands.supplierID
        LEFT JOIN brands ON supplier_brands.brandID=brands.id
		WHERE suppliers.archive = :archive AND $brandID
        GROUP BY suppliers.id
		ORDER by suppliers.name");		
	$stmt -> bindValue(':archive', $archive, PDO::PARAM_INT);
	$stmt ->execute();
	$count=1;
	while ($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC))  $count++;
	
} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}$stmt = $pdo->prepare($sql);
$pdo=NULL;

// Кнопки управления доступом
$handle = array();
$handle['change'] = array(
	'title'=>lang::HANDLING_CHANGE, 
	'link_start'=>'/cosmetics/supplier_edit.php?id=',
	'button'=>'<i class="fas fa-edit"></i>'
);
$handle['block'] = array(
	'title'=>lang::HANDLING_ARCHIVE, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=suppliers&archive=1&URL='.$_SERVER['PHP_SELF'],
	'button'=>'<i class="fas fa-trash"></i>',
	'alertMSG'=>lang::ALERT_BLOCK_DEFAULT
);
$handle['restore'] = array(
	'title'=>lang::HANDLING_RESTORE, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=suppliers&archive=0&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'],'button'=>'<i class="fas fa-trash-restore"></i>',
	'alertMSG'=>lang::HANDLING_RESTORE
);
if ($brandID != 1)	$title=lang::H2_SUPPLIERS . ' ' . $data[1]['brandNames'];
else				$title=lang::H2_SUPPLIERS;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo '<p class="title">' . lang::SIDEBAR_FILTERS . '</p>';
	echo '<form method="get" class="filter">
		<fieldset class="noBorders">
			<select name="brandID" required>';
				brand_select_filter($brands, $archive);
			echo '</select>
			<input name="tab" type="hidden" value="'.$_GET['tab'].'">
			<input type="submit" value="'.lang::BTN_SHOW.'"">
		</fieldset>
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
					<th class='table-sortable:*'><?=lang::HDR_ENTITY_NAME;?></th>
					<?php if ($brandID == 1) echo '<th>' . lang::TAB_BRANDS .'</th>'; ?>
					<th><?php echo lang::HDR_VAT;?></th>
					<th><?php echo lang::HDR_CONTACTS;?></th>
					<th><?php echo lang::HDR_COMMENT;?></th>
					<th><?php echo lang::HDR_HANDLING;?></th>
				</tr>
			</thead>
			<tbody>	
			<?php $count=1;
				while($data[$count] !=NULL) {
					echo '<tr>
						<td class="small center" style="max-width:10%;">' . $count	. '</td>
						<td>' . $data[$count]['name']	. '</td>';
						if ($brandID == 1) echo '<td>'. $data[$count]['brandNames'] . '</td>';
						echo '<td>'; VAT_read($data[$count]['VAT']); echo '</td>
						<td>' . $data[$count]['contact'] . '<br />'.$data[$count]['position'] . '<br />'; phones($data[$count]['phones']); echo $data[$count]['email'] . '<br />'. $data[$count]['site']	. '</td>
						<td>' . $data[$count]['comment']	. '</td>
						<td class="center">';?>	
							<a title="<?php echo $handle['change']['title']; ?>" href="<?php echo $handle['change']['link_start'] . $data[$count]['id']; ?>"><?php echo $handle['change']['button']; ?></a>
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
	if ($_GET['tab'] != 'archive') echo '<a class="button" href="/cosmetics/supplier_add.php">'.lang::BTN_ADD.'</a>';
?>
</section>
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>