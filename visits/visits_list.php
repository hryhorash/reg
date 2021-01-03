<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include($_SERVER['DOCUMENT_ROOT'].'/clients/tabs.php');

if($_GET['state'] !='' && $_GET['state'] !='all') {
	$cond_state = 'visits.state =' . $_GET['state'];
	if($_GET['state'] > 5) $pageID = 'vst_archive';
	else $pageID = 'vst_active';
} else if($_GET['state'] == 'all') {
	$pageID = 'vst_all';
	$cond_state = 1;
		
}else {
	if ($_GET['tab'] == 'archive'){
		$archive = 1;
		$cond_state = 'visits.state > 5';
		$pageID = 'vst_archive';
	} else {
		$cond_state = 'visits.state < 5';
		$pageID = 'vst_active';
	}
}	
if($_GET['offset'] > 0) $offset = $_GET['offset'];
else $offset = 0;
$limit = 10;

//get_staff_cat_wages(); // Заполняем матрицу сотрудник-категория работ - ставка. Будет использоваться при внесении визита для подстчета итогов


if (isset($_SESSION['locationSelected'])) {
	
	if($_GET['date'] !='') $cond_date = 'visits.date = "' . $_GET['date'] . '"';
	else $cond_date = 1;
	$sql = "SELECT visits.id, visits.date, startTime, endTime, visits.state, price_total, visits.comment
				, GROUP_CONCAT(DISTINCT worktypes.name  SEPARATOR ', ') as services
                , GROUP_CONCAT(DISTINCT CONCAT(users.name, ' ', users.surname) SEPARATOR '<br/>') as staff
                , clients.id as clientID, clients.name as clientName, clients.surname as clientSurname, clients.prompt
            FROM `visits`
			INNER JOIN visits_works ON visits.id = visits_works.visitID
			INNER JOIN worktypes ON visits_works.workID = worktypes.id
			INNER JOIN visits_staff ON visits.id = visits_staff.visitID
			INNER JOIN users ON visits_staff.userID = users.id
			INNER JOIN clients ON visits.clientID = clients.id
			WHERE visits.locationID = :locationID
				AND $cond_state
				AND $cond_date
			GROUP BY visits.id
			ORDER BY visits.date DESC, startTime ASC
			LIMIT $offset, $limit";
	$stmt = $pdo->prepare($sql);
	try 
	{
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
		$stmt ->execute();
		$count=1;
		while ($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC)) $count++;
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	$pdo=NULL;
}

		


// Кнопки управления доступом
$handle = array();
$handle['change'] = array(
	'title'=>lang::HANDLING_CHANGE, 
	'link_start'=>'/visits/visit_details.php?id=',
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

$title=lang::MENU_VISITS;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';

	include($_SERVER['DOCUMENT_ROOT'].'/clients/filters.php');
	echo tabs($tabs, $pageID);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc($title);

	if ($count > 1) { ?>

		<table class='stripy table-autosort table-autofilter'>
			<thead>
				<tr>
					<th>№			</th>
					<th class="table-sortable:*"><?=lang::DATE;?></th>
					<th class="table-sortable:*" style="width:35%;"><?=lang::TBL_CLIENT .'<br/>'. 
													lang::HDR_WORKTYPE_CATS;?></th>
					<th class="mobile-hide"><?=lang::HDR_ACCESS_LIST;?></th>
					<th class="table-sortable:*"><?=lang::HDR_VISIT_STATE;?></th>
					<th class="mobile-hide table-sortable:*" style="width:25%;"><?=lang::HDR_COMMENT;?></th>
					<th style="width: 90px;"><?php echo lang::HDR_HANDLING;?></th>
				</tr>
			</thead>
			<tbody>	
			<?php $count=1;
				while($data[$count] !=NULL) {
					echo '<tr>
							<td class="small center">'. ($count+$offset) .'</td>
							<td>' . correctDate($data[$count]['date']) . '<br />
								<span style="color:grey;">'	. $data[$count]['startTime'] . '-' . $data[$count]['endTime']; echo '</span></td>
							<td><a href="/clients/client_profile.php?id='.$data[$count]['clientID'].'">' . FIO($data[$count]['clientName'],$data[$count]['clientSurname'],$data[$count]['prompt']) . '</a><br />'
								. $data[$count]['services'] .'<br />
								<span style="float: right;">' . correctNumber($data[$count]['price_total'],2) . curr() . '</span>
							</td>
							<td class="mobile-hide">' . $data[$count]['staff']	. '</td>
							<td class="center">' . visit_state_read($data[$count]['state']) . '</td>
							<td class="mobile-hide">' . $data[$count]['comment']	. '</td>
							<td class="center">';?>	
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
		<?php list_navigation_buttons($count,$offset,$limit);
	} else {
		echo '<p>' . lang::ERR_NO_INFO . '</p>';
	}
	echo '<a class="button" href="/visits/visit_details.php?new&date='.$_GET['date'] .'" >'.lang::BTN_ADD.'</a>';
	?>	
</section>
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>