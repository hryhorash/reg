<?php $access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');
if ($_GET['tab'] == 'archive'){
	$archive = 1;
	$pageID = 'cat_archive';
} else {
	$archive = 0;
	$pageID = 'cat_active';
}

try {
	$stmt = $pdo->prepare("SELECT expences_subcat.id, category, subcategory, expences_subcat.id as subcatID, inmenu 
		FROM expences_cat 
		LEFT JOIN expences_subcat ON expences_subcat.catID = expences_cat.id
		WHERE expences_subcat.archive =:archive
		ORDER BY category, subcategory");
	$stmt->bindValue(':archive', $archive, PDO::PARAM_INT);
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
	'link_start'=>'/expences/cat_edit.php?id=',
	'link_finish'=>'&tab='. $_GET['tab'],
	'button'=>'<i class="fas fa-edit"></i>'
);
$handle['block'] = array(
	'title'=>lang::HANDLING_BLOCK, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=expences_subcat&archive=1&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'],
	'button'=>'<i class="fas fa-trash"></i>',
	'alertMSG'=>lang::ALERT_BLOCK_DEFAULT
);
$handle['restore'] = array(
	'title'=>lang::HANDLING_RESTORE, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=expences_subcat&archive=0&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'],
	'button'=>'<i class="fas fa-trash-restore"></i>',
	'alertMSG'=>lang::HANDLING_RESTORE
);

$title = lang::MENU_EXPENCES_CAT;
//----------------------------VIEW-------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, $pageID);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>' . lang::MENU_EXPENCES_CAT . '</h2>';

	if ($count > 1) { ?>
		
		<table class="stripy table-autosort table-autofilter">
			<thead>
			<tr>
				<th style='max-width: 10%;'>№</th>
				<th class='table-sortable:*'><?=lang::HDR_CATEGORY;?></th>
				<th class='table-sortable:*'><?=lang::HDR_SUBCATEGORY;?></th>
				<th><?=lang::HDR_SHOW_IN_MENU;?></th>
				<th><?=lang::HDR_HANDLING;?></th>
			</tr>
			</thead>
		<? $count = 1;
		while($data[$count] != NULL) { 
			echo '<tr>
				<td class="small center">'. $count .'</td>
				<td><a title="'.lang::CHANGE_ITEM_NAME .'" href="/expences/catName_edit.php?name='.$data[$count]['category'].'">' . $data[$count]['category'] . '</a></td>
				<td>' . $data[$count]['subcategory'] . '</td>
				<td class="center">';
					if ($data[$count]['inmenu'] == 0)
						echo '<i class="fas fa-times" style="color:red;"></i>';
					else echo'<i class="fas fa-check" style="color:green;"></i>';
				echo '</td>
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

	if ($_GET['tab'] != 'archive') echo '<a class="button" href="/expences/cat_add.php?tab=active">'.lang::BTN_ADD.'</a>';
echo '</section>';
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>