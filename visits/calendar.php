<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include($_SERVER['DOCUMENT_ROOT'].'/clients/tabs.php');

if($_GET['date'] !='')	{
	$set_date = '"'.$_GET['date'].'"';
	$weekdays = weekdays($_GET['date']);
} else {
	$set_date = 'NOW()';
	$weekdays = weekdays(date(strtotime("Y-m-d")));
	
}


if (isset($_SESSION['locationSelected'])) {
	
	$this_loc_off_weekdays = loc_off_weekdays();
	
	
	if($_GET['date'] !='') $cond_date = 'visits.date = "' . $_GET['date'] . '"';
	else $cond_date = 1;
	$sql = "SELECT visits.id, visits.date, startTime, endTime, visits.state, price_total
			    , GROUP_CONCAT(DISTINCT CONCAT(users.name, ' ', users.surname) SEPARATOR '<br/>') as staff
                , clients.id as clientID, clients.name as clientName, clients.surname as clientSurname, clients.prompt
            FROM `visits`
			LEFT JOIN visits_staff ON visits.id = visits_staff.visitID
			LEFT JOIN users ON visits_staff.userID = users.id
			LEFT JOIN clients ON visits.clientID = clients.id
			WHERE visits.locationID = :locationID
				AND YEARWEEK(visits.date,1) = YEARWEEK($set_date,1)
				AND state != 8
			GROUP BY visits.id
			ORDER BY visits.date ASC, startTime ASC";
	$stmt = $pdo->prepare($sql);
	try 
	{
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
		$stmt ->execute();
		$count=1;
		while ($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC)) $count++;
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}$stmt = $pdo->prepare($sql);
	$pdo=NULL;
}

		



$title=lang::MENU_CALENDAR;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	include($_SERVER['DOCUMENT_ROOT'].'/clients/filters.php');
	echo tabs($tabs, $pageID);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc($title);
	if (isset($_SESSION['locationSelected'])) {
		echo '<div id="cal_container">
			<div class="day">
				<div class="grid-cell" style="background-color:#e3e9ff;">
					<p class="center bold"><i class="far fa-clock" style="transform: translateY(50%);"></i></p>
				</div>';
				time_grid();		
			echo '</div>';
			$i = 1;
			foreach($weekdays as $day) {
				if (!in_array($i, $this_loc_off_weekdays)) {
					echo '<div class="day">
						<div class="grid-cell" style="background-color:#e3e9ff;">
							<p class="center bold">'. correctDate($day,1) .'<br>'.dayOfWeek($i).'</p>
						</div>';
						$count = 1;
						while($data[$count] != null) {
							if($data[$count]['date'] == $day) {
								//echo '<p class="border"><a href="/visits/visit_details.php?id='.$data[$count]['id'].'" target="_blank">' . FIO($data[$count]['clientName'],$data[$count]['clientSurname'],$data[$count]['prompt']) . '</a></p>';
				
			
								event_grid_visit($data[$count]);
							}
							
							$count++;
						}
					echo '</div>';
				}
				$i++;
			}
		
		echo '</div>';
		list_navigation_buttons($count,$offset,$limit);
		echo '<a class="button" href="/visits/visit_details.php?new&date='.$_GET['date'] .'" >'.lang::BTN_ADD.'</a>';
	
	}?>	
</section>
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); 
if (isset($_SESSION['locationSelected'])) {?>
	<script>
		const number_days_off = <?=sizeof($this_loc_off_weekdays);?>;
		if(number_days_off > 0) {
			const col = 7 - number_days_off;
			$('#cal_container').css('grid-template-columns', '7ch repeat(' + col + ', minmax(12ch, 1fr))');
		} 
		
		const grid_rows = <?=$_SESSION['grid_rows'];?>;
		$('.day').css('grid-template-rows', '3em repeat('+ grid_rows + ', 1fr)');
	</script>
<?php }?>