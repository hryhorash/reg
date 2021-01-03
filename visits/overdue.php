<?php 
$access = 10;
include($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');

if($_GET['offset'] > 0) $offset = $_GET['offset'];
else $offset = 0;
$limit = 3;

	
$sql_overdue = "SELECT visits.id, visits.date, startTime, endTime, visits.state, price_total, visits.comment
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
			AND visits.state < 5
			AND visits.date < CURRENT_DATE
		GROUP BY visits.id
		ORDER BY visits.date, startTime
		LIMIT $offset, $limit";
try 
{
	$stmt = $pdo->prepare($sql_overdue);
	$stmt -> bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
	$stmt ->execute();
	$count=1;
	while ($overdue[$count] = $stmt->fetch(PDO::FETCH_ASSOC)) $count++;
} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	


		


// Кнопки управления доступом
$handle = array();

$handle['approve'] = array(
	'title'=>lang::HANDLING_APPROVE, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=clients&archive=0&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'],'button'=>'<i class="fas fa-trash-restore link"></i>'
);

$handle['change'] = array(

	'title'=>lang::HANDLING_CHANGE, 
	'link_start'=>'/visits/visit_details.php?id=',
	'link_finish'=>'&goto=dashboard',
	'button'=>'<i class="fas fa-edit"></i>'
);
$handle['cancel'] = array(
	'title'=>lang::HANDLING_NOSHOW, 
	'link_start'=>'/config/archive.php?id=',
	'link_finish'=>'&table=clients&archive=1&URL='.$_SERVER['PHP_SELF'].'&tab='.$_GET['tab'],
	'button'=>'<i class="fas fa-trash link"></i>',
	'alertMSG'=>lang::ALERT_BLOCK_DEFAULT
);


$title=lang::H2_OVERDUE_VISITS;
//---------------------VIEW--------------------------------------

echo '<div id="overdue">';

	if ($count > 1) { 
		echo $title; ?>

		<table class='stripy table-autosort table-autofilter' id="tbl_overdue">
			<thead>
				<tr>
					<th class="table-sortable:*"><?=lang::DATE;?></th>
					<th class="table-sortable:*" style="width:35%;"><?=lang::TBL_CLIENT .'<br/>'. 
													lang::HDR_WORKTYPE_CATS;?></th>
					<th class="mobile-hide"><?=lang::HDR_ACCESS_LIST;?></th>
					<th class="mobile-hide table-sortable:*" style="width:25%;"><?=lang::HDR_COMMENT;?></th>
					<th style="width: 90px;"><?php echo lang::HDR_HANDLING;?></th>
				</tr>
			</thead>
			<tbody>	
			<?php $count=1;
				while($overdue[$count] !=NULL) {
					echo '<tr>
							<td>' . correctDate($overdue[$count]['date']) . '<br />
								<span style="color:grey;">'	. $overdue[$count]['startTime'] . '-' . $overdue[$count]['endTime']; echo '</span></td>
							<td><a href="/clients/client_profile.php?id='.$overdue[$count]['clientID'].'">' . FIO($overdue[$count]['clientName'],$overdue[$count]['clientSurname'],$overdue[$count]['prompt']) . '</a><br />'
								. $overdue[$count]['services'] .'<br />
								<span style="float: right;">' . correctNumber($overdue[$count]['price_total'],2) . curr() . '</span>
							</td>
							<td class="mobile-hide">' . $overdue[$count]['staff']	. '</td>
							<td class="mobile-hide">' . $overdue[$count]['comment']	. '</td>
							<td class="center">';?>	
								<i class="fas fa-check link" id="<?=$overdue[$count]['id'];?>"></i>
								<a title="<?=$handle['change']['title']; ?>" href="<?php echo $handle['change']['link_start'] . $overdue[$count]['id'] . $handle['change']['link_finish']; ?>"><?php echo $handle['change']['button']; ?></a>
								<i class="fas fa-times link" id="<?=$overdue[$count]['id'];?>"></i>
								
								<?php 
								
								
							echo '</td>
						</tr>';
					$count++;
				} ?>
			</tbody>	
		</table>
		<?php //list_navigation_buttons($count,$offset,$limit);
	} ?>	
</div>
