<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');
if ($_GET['tab'] == 'archive'){
	$archive = 1;
} else $archive = 0;

$i = 1;
		
if ($_SESSION['role'] == 'godmode') {
	try {
		$stmt = $pdo->prepare("SELECT id,username,name,surname,email,role,phones FROM `users` WHERE role='godmode' and archive=:archive");
		$stmt -> bindValue(':archive', $archive, PDO::PARAM_INT);
		$stmt ->execute();
		while($users[$i] = $stmt->fetch(PDO::FETCH_ASSOC))	$i++;
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
} 


$locationID = setLocationID();		
if ($locationID != NULL) {		
	/*$sql = "SELECT
			  users.id, username, users.name, users.surname, users.email, users.locationIDs, users.role, users.phones,users.note,users.specialty,
			  SUBSTRING_INDEX(SUBSTRING_INDEX(users.locationIDs, ',', numbers.n), ',', -1) locationID
			FROM
			  (SELECT 1 n UNION ALL SELECT 2
			   UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10) numbers 
			INNER JOIN users
			  ON CHAR_LENGTH(users.locationIDs)
				 -CHAR_LENGTH(REPLACE(users.locationIDs, ',', ''))>=numbers.n-1
			INNER JOIN locations ON CHAR_LENGTH(users.locationIDs) -CHAR_LENGTH(REPLACE(users.locationIDs, ',', ''))>=numbers.n-1 = locations.id
			WHERE SUBSTRING_INDEX(SUBSTRING_INDEX(users.locationIDs, ',', numbers.n), ',', -1) = :locationID
				AND users.archive = :archive"; */
				
	$sql = "SELECT
			  users.id, username, users.name, surname, email, role, phones, note,
			  GROUP_CONCAT(locations.id) as locationIDs,
              GROUP_CONCAT(locations.name SEPARATOR ', ') as locationNames
			FROM users
            LEFT JOIN users_locations ON users.id=users_locations.userID
			LEFT JOIN locations ON users_locations.locationID = locations.id
			WHERE users.archive = :archive 
				AND locations.id=:locationID
			GROUP BY users.id
            ORDER BY users.name, users.surname";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':locationID', $locationID, PDO::PARAM_INT);
		$stmt -> bindValue(':archive', $archive, PDO::PARAM_INT);
		$stmt ->execute();
		$count = $i;
		while($users[$count] = $stmt->fetch(PDO::FETCH_ASSOC))	$count++;
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	
}

$pdo=NULL;

// Кнопки управления доступом
$handle = array();
$handle['change'] = array(
	'title'=>lang::HANDLING_CHANGE, 
	'link_start'=>'/user/user_edit.php?id=',
	'button'=>'<i class="fas fa-edit"></i>'
);
$handle['block'] = array(
	'title'=>lang::HANDLING_BLOCK, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=users&archive=1&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'],
	'button'=>'<i class="fas fa-trash"></i>',
	'alertMSG'=>lang::ALERT_BLOCK
);
$handle['restore'] = array(
	'title'=>lang::HANDLING_RESTORE, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=users&archive=0&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'],
	'button'=>'<i class="fas fa-trash-restore"></i>',
	'alertMSG'=>lang::HANDLING_RESTORE
);

$title=lang::HDR_ACCESS_LIST;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 

echo '<section class="sidebar">';
	echo tabs($tabs, 'usr_active');
echo '</section>';
echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc(lang::HDR_ACCESS_LIST);

	if ($count > 1 || ($_SESSION['role'] == 'godmode' && $_GET['tab'] != 'archive')) {
	?>



		<table class='stripy table-autosort table-autofilter'>
			<thead>
				<tr>
					<th style='max-width: 10%;'>№			</th>
					<th class='table-sortable:*'><?php echo lang::HDR_NAME;?></th>
					<th class='table-sortable:*'><?php echo lang::USERNAME;?></th>
					<th class='table-sortable:*'><?php echo lang::HDR_EMAIL;?></th>
					<th class='table-sortable:*'><?php echo lang::HDR_PHONES;?></th>
					<th class='table-sortable:*'><?php echo lang::HDR_ROLE;?></th>
					<th><?php echo lang::HDR_COMMENT;?></th>
					<th><?php echo lang::HDR_HANDLING;?></th>
				</tr>
			</thead>
			<tbody>	
			<?php $count=1;
				while($users[$count] !=NULL) {
					echo '<tr>
							<td class="small center">'. $count .'</td>
							<td>' . $users[$count]['name'] . ' ' .$users[$count]['surname'] 		. '</td>
							<td>' . $users[$count]['username'] 	. '</td>
							<td>' . $users[$count]['email'] 	. '</td>
							<td>'; phones($users[$count]['phones']); 	echo '</td>
							<td>' . role_name($users[$count]['role']) 	. '</td>
							<td>' . $users[$count]['note'] 	. '</td>
							<td class="center">';	
								if ($archive == 0 && handle_rights($users[$count]['role'], $users[$count]['locationIDs']) == 1) { ?>
									<a title="<?php echo $handle['change']['title']; ?>" href="<?php echo $handle['change']['link_start'] . $users[$count]['id'] .'&tab=active'; ?>"><?php echo $handle['change']['button']; ?></a>
									<a title="<?php echo $handle['block']['title'] ?>" href="<?php echo $handle['block']['link_start'] . $users[$count]['id'] . $handle['block']['link_finish']; ?>" onclick='return confirm("<?php echo $handle['block']['alertMSG']. '\"'.$users[$count]['name'].' '.$users[$count]['surname'].'\"?'; ?>");'><?php echo $handle['block']['button']; ?></a>
									
								<?php } else if ($archive == 1 && handle_rights($users[$count]['role'], $users[$count]['locationIDs']) == 1) { ?>
									<a title="<?php echo $handle['change']['title']; ?>" href="<?php echo $handle['change']['link_start'] . $users[$count]['id'] . $handle['change']['link_finish'].'&tab=archive'; ?>"><?php echo $handle['change']['button']; ?></a>
									<a title="<?php echo $handle['restore']['title'] ?>" href="<?php echo $handle['restore']['link_start'] . $users[$count]['id'] . $handle['restore']['link_finish']; ?>" onclick='return confirm("<?php echo $handle['restore']['alertMSG']. '\"'.$users[$count]['name'].' '.$users[$count]['surname'].'\"?'; ?>");'><?php echo $handle['restore']['button']; ?></a>
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
	if ($_GET['tab'] != 'archive') echo '<a class="button" href="/user/user_add.php">'.lang::BTN_ADD.'</a>';
echo '</section>';
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>