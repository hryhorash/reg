<?php 
$access = 2;
include($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

$_SESSION['gridToFill_start'] = array(
	1 => array(),
	2 => array(),
	3 => array(),
	4 => array(),
	5 => array(),
	6 => array(),
	7 => array()
);

unset($_SESSION['gridToFill_finish']);

$tabs = array();
$tabs[] =  array('id'=>'cal',		'power'=>2, 'title'=>lang::MENU_VISITS,		'tab'=>'all',		'link'=>'/user/dashboard.php?date='.$_GET['date'].'&staffID='.$_GET['staffID'],	'name'=>lang::MENU_CALENDAR);
$tabs[] =  array('id'=>'add',		'power'=>10, 'title'=>lang::MENU_VISITS,		'tab'=>'all',		'link'=>'/visits/visit_details.php?new&goto=dashboard',	'name'=>lang::HDR_NEW_VISIT);


if($_GET['date'] !='')	{
	$set_date = '"'.$_GET['date'].'"';
	$weekdays = weekdays($_GET['date']);  //ДАТЫ недели Y-m-d
} else {
	$set_date = 'NOW()';
	$weekdays = weekdays(date('Y-m-d',time()));
	
}

if ($_SESSION['pwr'] < 10)		$staff_cond = 'users.id =' . $_SESSION['userID'];
else if($_GET['staffID'] > 0)	$staff_cond = 'users.id =' . $_GET['staffID'];
else 							$staff_cond = 1;

if (isset($_SESSION['locationSelected'])) {
	
	$this_loc_off_weekdays = array();
	$this_loc_off_weekdays = loc_off_weekdays();
	
	
	if($_GET['date'] !='') $cond_date = 'visits.date = "' . $_GET['date'] . '"';
	else $cond_date = 1;
	
	if($_SESSION['pwr'] < 10 ) {
		$sql = "SELECT visits.id, visits.date, startTime, endTime, visits.state
			    , GROUP_CONCAT(DISTINCT CONCAT(users.name, ' ', users.surname) SEPARATOR '<br/>') as staff
                , clients.id as clientID, clients.name as clientName, clients.surname as clientSurname, clients.prompt
                , tbl_staff.price_total, tbl_staff.works
            FROM `visits`
			LEFT JOIN visits_staff ON visits.id = visits_staff.visitID
			LEFT JOIN users ON visits_staff.userID = users.id
			LEFT JOIN clients ON visits.clientID = clients.id
			LEFT JOIN (
                SELECT visitID, SUM(visits_works.price) as price_total, GROUP_CONCAT(DISTINCT category SEPARATOR ', ') as works
                FROM visits_works
                INNER JOIN worktypes ON visits_works.workID = worktypes.id
                INNER JOIN worktype_cat ON worktypes.catID = worktype_cat.id
                WHERE visits_works.userID = :userID
                GROUP BY visitID
            ) tbl_staff ON visits.id = tbl_staff.visitID
			WHERE visits.locationID = :locationID
				AND YEARWEEK(visits.date,1) = YEARWEEK($set_date,1)
				AND state != 8
                AND users.id = :userID
			GROUP BY visits.id
			ORDER BY visits.date ASC, startTime ASC";
	} else {
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
				AND $staff_cond
			GROUP BY visits.id
			ORDER BY visits.date ASC, startTime ASC";
	}
	$stmt = $pdo->prepare($sql);
	try 
	{
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
		if($_SESSION['pwr'] < 10 ) {
			$stmt -> bindValue(':userID', $_SESSION['userID'], PDO::PARAM_INT);
		}		
		$stmt ->execute();
		$count=1;
		while ($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$visits_days_numbers[] = date('w', strtotime($data[$count]['date']));  // 0 = вс.
			$count++;
		}
		
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	$pdo=NULL;
}

$new_array_unique = array();
if($visits_days_numbers != null) {
	$visits_days_unique = array_unique($visits_days_numbers);

	// создаем массив визитов по дням недели (1 = пн, 7 = вс.)
	foreach($visits_days_unique as $item) { 
		if ($item == 0) $new_array_unique[] = 7;
		else $new_array_unique[] = $item;
	}

	sort($new_array_unique);
} else $visits_days_numbers = null;


$nav = calendar_navigation_buttons($weekdays[1]);
	
$title=lang::MENU_CALENDAR;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	if($_SESSION['pwr'] > 9) {
		include($_SERVER['DOCUMENT_ROOT'].'/clients/filters.php');
		echo '<hr>';
		echo '<form method="get" class="filter">
			<fieldset class="noBorders">
				<select name="staffID" required>';
					echo staff_select_options($_SESSION['locationSelected'], $_GET['staffID']);
				echo '</select>
				<input type="hidden" name="date" value="'.$_GET['date'].'">
				<input type="submit" value="'.lang::BTN_SHOW.'">
			</fieldset>
		</form>';
	}
	echo tabs($tabs, 'cal');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc($title);
	if (isset($_SESSION['locationSelected'])) {
		//include($_SERVER['DOCUMENT_ROOT'].'/visits/overdue.php');
		
		echo $nav;
		echo '<div id="cal_container">
			<div class="day" style="background:initial;">
				<div class="grid-cell grid-header">
					<p class="center bold"><i class="far fa-clock" style="transform: translateY(50%);"></i></p>
				</div>';
				time_grid();		
			echo '</div>';
			$i = 1;
			foreach($weekdays as $day) {
				if (!in_array($i, $this_loc_off_weekdays) || in_array($i, $new_array_unique)) {
					echo '<div class="day">
						<div class="grid-cell grid-header">';
							// ДЛЯ ОТОБРАЖЕНИЯ ВИЗИТОВ ВНЕ РАМОК ОТОБРАЖАЕМОГО РАСПИСАНИЯ
							echo'<i class="fas fa-exclamation-triangle fa-2x" style="color:orange;position:absolute;top:5px;right:0;display:none;"></i>
							<div class="todo" style="display:none;">';
								$count = 1;
								while($data[$count] != null) {
									//начало и конец раньше времени открытия салона
									if($data[$count]['date'] == $day 
										&& mb_substr($data[$count]['startTime'],0,2) < $_SESSION['openFrom'] 
										&& mb_substr($data[$count]['endTime'],0,2) <= $_SESSION['openFrom']) {
										
										if($_SESSION['pwr'] > 9)
											echo '<p>
												<a href="/visits/visit_details.php?id='.$data[$count]['id'].'&goto=dashboard">' . FIO($data[$count]['clientName'],$data[$count]['clientSurname'],$data[$count]['prompt']) . '</a></p>';
										else echo FIO($data[$count]['clientName'],$data[$count]['clientSurname']) . ': ' . $data[$count]['works'] . '</p>';
										
									}
									$count++;
								}
							echo'</div>
							<p class="center bold">'. correctDate($day,1) .'<br>'.dayOfWeek($i).'</p>
						</div>';
						
						if($day == date('Y-m-d')) {
							echo '<hr class="timeline">';
						}
						
						$count = 1;
						while($data[$count] != null) {
							// конец визита совпадает или позже даты открытия салона (или в полночь)
							if($data[$count]['date'] == $day
								&& (mb_substr($data[$count]['endTime'],0,2) > $_SESSION['openFrom'] 
								|| (
									mb_substr($data[$count]['startTime'],0,2) > $_SESSION['openFrom'] 
									&& $data[$count]['endTime'] == '00:00'
									)
								)
							  ) {
							
								event_grid_visit($data[$count], $i);
							}
							
							$count++;
						}
					
					
					//ЗАПОЛНЯЕМ ПУСТОТЫ
					$start = $_SESSION['gridToFill_start'][$i];
					$fin = $_SESSION['gridToFill_finish'][$i];
					
					
					
					if(count($_SESSION['gridToFill_start'][$i]) > 0) {
						$max_fin[$i] = max($_SESSION['gridToFill_finish'][$i]);
					} else $max_fin[$i] = $_SESSION['gridToFill_finish'][$i][0];
					
					switch (true)
					{
						//пустой день или выходной
						case($_SESSION['gridToFill_start'][$i] == null):
							empty_cal_day($day);
							break;
						
						//пустоты до первой записи
						case($_SESSION['gridToFill_start'][$i][0] > 2):
							if($_SESSION['gridToFill_start'][$i][0] <= 6 ) {
								cal_emptyCell_taken_gap(2,$_SESSION['gridToFill_start'][$i][0]);
							} else {
								$begin = 2; //начало второй строки + № конца планируемого блока
								$visitStart = $_SESSION['gridToFill_start'][$i][0] - 4;
								
								cal_emptyCell_taken_gap($visitStart, $_SESSION['gridToFill_start'][$i][0]);
								
								
								while($begin <= $visitStart && ($visitStart - $begin) >=2) {
									cal_emptyCell_wLink($begin, $day);
									$begin = $begin+2;
								}
								
							}
							
							
						// пустоты МЕЖДУ записями
						case(count($_SESSION['gridToFill_start'][$i]) > 0):
							$start_array = $_SESSION['gridToFill_start'][$i];
							unset($start_array[0]); //удаляем старт первой записи
							
							
							$c = 1;
							foreach($start_array as $visitStart) {
								$prev_visitEnd = $_SESSION['gridToFill_finish'][$i][($c-1)];
								$gap = $visitStart - $prev_visitEnd;
								if($gap > 0) {
									if($gap <= 8) {
										cal_emptyCell_taken_gap($prev_visitEnd, $visitStart);
									} else {
										cal_emptyCell_taken_gap($prev_visitEnd, ($prev_visitEnd + 4));
										cal_emptyCell_taken_gap(($visitStart-4), $visitStart);
									}
									
									$begin = $prev_visitEnd + 4; 
										
									
									if(($begin % 2) != 0 && $begin <= ($visitStart-4)) { // если начало нечетное
										echo '<div class="grid-cell" style="grid-row: '.$begin.'/'.($begin+1).';"><b></b></div>';
										$begin = $begin+1;
									} 
									
									while($begin <= ($visitStart-4) && ($visitStart-4-$begin) >= 2) {
										cal_emptyCell_wLink($begin, $day);
										$begin = $begin+2;
									}
								}
								$c++;
							}
							
							
						//пустоты после последней записи
						case(max($_SESSION['gridToFill_finish'][$i]) < $_SESSION['grid_rows']):
							$fin_gap = $_SESSION['grid_rows'] - max($_SESSION['gridToFill_finish'][$i]);
						
							if($fin_gap <= 4 ) {
								cal_emptyCell_taken_gap(max($_SESSION['gridToFill_finish'][$i]), ($_SESSION['grid_rows']+2));
							} else {
								$begin = $max_fin[$i]; 
								cal_emptyCell_taken_gap($begin, ($begin+4));
								
								$begin = $begin + 4;
								
								if(($begin % 2) != 0) { // если начало нечетное
									echo '<div class="grid-cell gray-bg" style="grid-row: '.$begin.'/'.($begin+1).';;border:none;"><b></b></div>';
									$begin = $begin+1;
								} 
								
								while($begin <= $_SESSION['grid_rows']) {
									cal_emptyCell_wLink($begin, $day);
									$begin = $begin+2;
								}
								
							}
						break;
					}
					//конец пустот
					
					
					echo '</div>';
					
					//удаляем из списка выходных день, который оказался рабочим
					if(in_array($i, $new_array_unique)) {
						if (($key = array_search($i, $this_loc_off_weekdays)) !== false) {
							unset($this_loc_off_weekdays[$key]);
						}
					}
				} 
				$i++;
			}
		
		echo '</div>';
		echo $nav;
		
	
	}?>	
</section>
<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); 
if (isset($_SESSION['locationSelected'])) {?>
	<script>
		const number_days_off = <?=sizeof($this_loc_off_weekdays);?>;
		if(number_days_off > 0) {
			const col = 7 - number_days_off;
			$('#cal_container').css('grid-template-columns', '7ch repeat(' + col + ', minmax(8ch, 1fr))');
		} 
		
		const grid_rows = <?=$_SESSION['grid_rows'];?>;
		$('.day').css('grid-template-rows', '3em repeat('+ grid_rows + ', 1.5em)');
		
		
		//визиты вне графика работы
		$('.todo').each(function(){
			var isEmpty = $(this).children().length;
			if(isEmpty > 0) {
				$(this).siblings().show();
			}
		});
		
		$(".fa-exclamation-triangle").on('click', function() {
			$(this).siblings('.todo').toggle();
		});
		
		
		
		function approve() {
			var el = $(this);
			var visitID = $(this).prop('id');
			var confirmation = confirm('<?=lang::ALERT_CONFIRM_VISIT;?>');
			if(confirmation == true) {
				var xhttp = new XMLHttpRequest();
				$.ajax({
					type: "POST",
					url: "/visits/overdue_ajax.php",
					data:	{ 'id': visitID, 'action': 'approve' },  
					success: function(data){
						
						//document.getElementById("overdue").innerHTML = data;
						count_overdue++;
						if(count_overdue == rowCount) {
							location.reload();
						} else el.parent().parent().hide();
					}
				});
			}
		}
		
		function noshow() {
			var el = $(this);
			var visitID = $(this).prop('id');
			var confirmation = confirm('<?=lang::ALERT_NOSHOW;?>');
			if(confirmation == true) {
				var xhttp = new XMLHttpRequest();
				$.ajax({
					type: "POST",
					url: "/visits/overdue_ajax.php",
					data:	{ 'id': visitID, 'action': 'noshow' },  
					success: function(data){
						
						//document.getElementById("overdue").innerHTML = data;
						count_overdue++;
						if(count_overdue == rowCount) {
							location.reload();
						} else el.parent().parent().hide();
						
					}
				});
			}
		}
		
		var rowCount = $('#tbl_overdue').find('tr').length;
		var count_overdue = 1;
		$(".fa-check").on('click', approve);
		$(".fa-times").on('click', noshow);
		
		
		//timeline
		
		const openFrom = <?=$_SESSION['openFrom'];?>;
		const openTill = <?=$_SESSION['openTill'];?>;
		const minutsOpenTotal = (openTill - openFrom) * 60;
		const height = $('.day').height() - $('.grid-header').height();
		const timeline = $('.timeline');
		
		function draw_timeline() {
			var date = new Date();
			
			
			if(openFrom > date.getHours() || date.getHours() >= openTill) {
				timeline.hide();
			} else {
				var curMinutes = ((date.getHours() - openFrom) * 60 ) + date.getMinutes();
				var percentOfDay = curMinutes / minutsOpenTotal;
				var topLoc = Math.floor(height * percentOfDay);
				
				timeline.show();
				timeline.css("top", (topLoc + $('.grid-header').height()) + "px");
			}
		}
		draw_timeline();
		setInterval(draw_timeline, 60000);
		
		
	</script>
<?php }?>