<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');
if ($_GET['tab'] == 'archive'){
	$archive =  1;
	$condition = '(users_workdays.archive = 1  OR date < CURDATE())';
} else {
	$archive = 0;
	$condition = 'users_workdays.archive = 0 AND (date >= CURDATE() OR date IS NULL)';
}

if ($_GET['userID'] > 0) $condition2 = 'users_workdays.userID = ' .$_GET['userID'];
else $condition2 = 1;

$locationID = setLocationID();		
if ($locationID != NULL) {		
	$sql = "SELECT users_workdays.id, CONCAT(users.name, ' ', users.surname) as user, users_workdays.date, weekday, even, comment, users_workdays.locationID, locations.name as location
			FROM users_workdays 
			LEFT JOIN users ON users_workdays.userID = users.id
            LEFT JOIN locations ON users_workdays.locationID=locations.id
			WHERE users_workdays.locationID = :locationID
				AND $condition
				AND $condition2
			ORDER BY user";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':locationID', $locationID, PDO::PARAM_INT);
		$stmt ->execute();
		$count = 1;
		while($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC))	{
			$workingDays[$count] = dayOff($data[$count]['date'], $data[$count]['weekday'], $data[$count]['even']);
			$count++;
		}
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	
}

$pdo=NULL;

// Кнопки управления доступом
$handle = array();
$handle['change'] = array(
	'title'=>lang::HANDLING_CHANGE, 
	'link_start'=>'/user/workdays_edit.php?id=',
	'button'=>'<i class="fas fa-edit"></i>'
);
$handle['archive'] = array(
	'title'=>lang::HANDLING_ARCHIVE, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=users_workdays&archive=1&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'],
	'button'=>'<i class="fas fa-trash"></i>',
	'alertMSG'=>lang::HANDLING_ARCHIVE
);
$handle['restore'] = array(
	'title'=>lang::HANDLING_RESTORE, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=users_workdays&archive=0&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'],
	'button'=>'<i class="fas fa-trash-restore"></i>',
	'alertMSG'=>lang::HANDLING_RESTORE
);

$title=lang::TAB_WORKDAYS_USERS;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 


echo '<section class="sidebar">';
	echo'<p class="title">'.lang::SIDEBAR_FILTERS.'</p>';
	echo '<form method="get" class="filter">
		<fieldset class="noBorders">';
			user_select(setLocationID(), $_GET['userID'], 1);
			if ($tab !=NULL) echo '<input type="hidden" name="tab" value="'. $tab.'" />';
			echo '<input type="submit" value="'. lang::BTN_SHOW.'" />';
		echo'</fieldset>';
	echo '</form>';
	
	echo tabs($tabs,'wrk_active');

	
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc(lang::TAB_WORKDAYS_USERS);


	if ($count > 1) {
	?>
		<table class='stripy table-autosort table-autofilter'>
			<thead>
				<tr>
					<th style='max-width: 10%;'>№			</th>
					<th class='table-sortable:*'><?php echo lang::HDR_NAME;?></th>
					<th class='table-sortable:*'><?php echo lang::HDR_WORKDAY;?></th>
					<th><?php echo lang::HDR_COMMENT;?></th>
					<th><?php echo lang::HDR_HANDLING;?></th>
				</tr>
			</thead>
			<tbody>	
			<?php $count=1;
				while($data[$count] !=NULL) {
					echo '<tr>
							<td class="small center">'. $count .'</td>
							<td>' . $data[$count]['user']	. '</td>
							<td>' . $workingDays[$count] 	. '</td>
							<td>' . $data[$count]['comment'] 	. '</td>
							<td class="center">';?>
								<a title="<?php echo $handle['change']['title']; ?>" href="<?php echo $handle['change']['link_start'] . $data[$count]['id']; ?>"><?php echo $handle['change']['button']; ?></a>
								<?php if ($archive == 0) { ?>
									
									<a title="<?php echo $handle['archive']['title'] ?>" href="<?php echo $handle['archive']['link_start'] . $data[$count]['id'] . $handle['archive']['link_finish']; ?>" onclick='return confirm("<?php echo $handle['archive']['alertMSG']. '\"'.$workingDays[$count].'\"?'; ?>");'><?php echo $handle['archive']['button']; ?></a>
									
								<?php } else if ($archive == 1 && $data[$count]['date'] == null) { ?>
									<a title="<?php echo $handle['restore']['title'] ?>" href="<?php echo $handle['restore']['link_start'] . $data[$count]['id'] . $handle['restore']['link_finish']; ?>" onclick='return confirm("<?php echo $handle['restore']['alertMSG']. '\"'.$workingDays[$count].'\"?'; ?>");'><?php echo $handle['restore']['button']; ?></a>
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
	if ($_GET['tab'] != 'archive') echo '<a class="button" href="/user/workdays_add.php">'.lang::BTN_ADD.'</a>';
echo '</section>';

include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>