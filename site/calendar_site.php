<?php 
$access = 1;
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
unset($_SESSION['temp']);

if($_GET['date'] !='')	{
	$set_date = '"'.$_GET['date'].'"';
	$weekdays = weekdays($_GET['date']);
} else {
	$set_date = 'NOW()';
	$weekdays = weekdays(date('Y-m-d',time()));
	
}

if (isset($_SESSION['locationSelected'])) {
	
	$this_loc_off_weekdays = array();
	$this_loc_off_weekdays = loc_off_weekdays();
	
	
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


$nav = calendar_navigation_buttons($weekdays[1], 'noArchive');
	
$title=lang::MENU_CALENDAR; 
//---------------------VIEW--------------------------------------?>
<!DOCTYPE html><html lang="<?=$_SESSION['locale'];?>">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/layout/css/main.css" type="text/css" media="screen" />
</head>
<body style="background-color:white; margin: 0 1px;">
<style>
	.time-close {
		transform: translateY(25%);
	}
	
	@media screen and (max-width: 768px) {
		body {
			width: 96%;
			margin: auto;
		}
	}
</style>

	<?php include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	//echo '<h2>' . $title . '</h2>';
	
	if (isset($_SESSION['locationSelected'])) {
		
		echo $nav;
		echo '<div id="cal_container">
			<div class="day" style="background:initial;">
				<div class="grid-cell grid-header">
					<p class="center bold"><i class="far fa-clock" style="transform: translateY(50%);"></i></p>
				</div>';
				time_grid('site');		
			echo '</div>';
			
			$grid_end = $_SESSION['grid_rows']+2;
			$i = 1;
			foreach($weekdays as $day) {
				if (!in_array($i, $this_loc_off_weekdays) || in_array($i, $new_array_unique)) {
					echo '<div class="day">
						<div class="grid-cell grid-header">
							<p class="center bold">'. correctDate($day,1) .'<br>'.dayOfWeek($i).'</p>
						</div>';
							
						if(strtotime($day) < strtotime(date('Y-m-d'))) {
							cal_emptyCell_taken_gap(2, $grid_end);
									
						} else {
						
							//линия времени сегодня
							if($day == date('Y-m-d')) {
								echo '<hr class="timeline">';
							}
							
							$count = 1;
							while($data[$count] != null) {
								if($data[$count]['date'] == $day) {
									if (mb_substr($data[$count]['endTime'],0,2) > $_SESSION['openFrom'] 
									|| (mb_substr($data[$count]['startTime'],0,2) > $_SESSION['openFrom'] && $data[$count]['endTime'] == '00:00'
										)) {
										event_grid_visit($data[$count], $i, 1);
									}
								}
								
								$count++;
							}
							
							
							//ЗАПОЛНЯЕМ ПУСТОТЫ
							if(count($_SESSION['gridToFill_start'][$i]) > 0) {
								$max_fin[$i] = max($_SESSION['gridToFill_finish'][$i]);
							} else $max_fin[$i] = $_SESSION['gridToFill_finish'][$i][0];
							
							if ($max_fin[$i] > $grid_end) $max_fin[$i] = $grid_end;
							
							switch (true)
							{
								//пустой день или выходной
								case($_SESSION['gridToFill_start'][$i] == null):
									empty_cal_day($day, 1);
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
											cal_emptyCell_wLink($begin, $day, 1);
											$begin = $begin+2;
										}
										
									}
									
									
								// пустоты МЕЖДУ записями
								case(count($_SESSION['gridToFill_start'][$i]) > 0):
									$start_array = $_SESSION['gridToFill_start'][$i];
									unset($start_array[0]); //удаляем старт первой записи
									
									
									$c = 1;
									foreach($start_array as $visitStart) {
										if($visitStart < $grid_end) {
											$prev_visitEnd = $_SESSION['gridToFill_finish'][$i][($c-1)];
												
											$gap = $visitStart - $prev_visitEnd;
											if($gap > 0) {
												if($gap <= 8) {
													cal_emptyCell_taken_gap($prev_visitEnd, $visitStart);
													//$begin = $_SESSION['gridToFill_finish'][$i][($c)]; 
												} else {
													cal_emptyCell_taken_gap($prev_visitEnd, ($prev_visitEnd + 4));
													
													cal_emptyCell_taken_gap(($visitStart-4), $visitStart);
													//$begin = $prev_visitEnd + 4; 
												}
												$begin = $prev_visitEnd + 4; 
												if(($begin % 2) != 0) { // если начало нечетное
													echo '<div class="grid-cell" style="grid-row: '.$begin.'/'.($begin+1).';"><b></b></div>';
													$begin = $begin+1;
												} 
												
												while($begin <= ($visitStart-4) && ($visitStart-4-$begin) >= 2) {
													cal_emptyCell_wLink($begin, $day, 1);
													$begin = $begin+2;
												}
											}
										} else {
											$prev_visitEnd = $_SESSION['gridToFill_finish'][$i][($c-1)];
											$gap = $grid_end - $prev_visitEnd;
											
											if($gap <= 8) {
												cal_emptyCell_taken_gap($prev_visitEnd, $grid_end);
											} else {
												//определяем, насколько позже начинается визит
												$end_gap = $visitStart - $grid_end;
												if($end_gap <= 4 && $end_gap > 0) {
													cal_emptyCell_taken_gap(($grid_end-$end_gap), $grid_end);
												}
											}
											
											$begin = $grid_end;
										}
										$c++;
									}
									if($begin >= $grid_end) break; // избегаем двойного заполнения после последней записи
									
								//пустоты после последней записи
								case($max_fin[$i] < $_SESSION['grid_rows']):
									$fin_gap = $_SESSION['grid_rows'] - $max_fin[$i];
								
									if($fin_gap > 0 && $fin_gap <= 4) {
										cal_emptyCell_taken_gap($max_fin[$i], $grid_end);
									} else {
										$begin = $max_fin[$i]; 
										cal_emptyCell_taken_gap($begin, ($begin+4));
										
										$begin = $begin + 4;
										
										if(($begin % 2) != 0) { // если начало нечетное
											echo '<div class="grid-cell gray-bg" style="grid-row: '.$begin.'/'.($begin+1).';border:none;"><b></b></div>';
											$begin = $begin+1;
										} 
										
										while($begin <= $_SESSION['grid_rows']) {
											cal_emptyCell_wLink($begin, $day, 1);
											$begin = $begin+2;
										}
										
									}
									break;
	
							}
						//конец пустот
						}
						
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
		//echo $nav;
		
	
	}?>	
<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>

<?php 
if (isset($_SESSION['locationSelected'])) {?>	
	<script>
		const number_days_off = <?=sizeof($this_loc_off_weekdays);?>;
		if(number_days_off > 0) {
			const col = 7 - number_days_off;
			$('#cal_container').css('grid-template-columns', '7ch repeat(' + col + ', minmax(8ch, 1fr))');
		} 
		
		const grid_rows = <?=$_SESSION['grid_rows'];?>;
		$('.day').css('grid-template-rows', '3em repeat('+ grid_rows + ', 0.75em)');
		
		const openFrom = <?=$_SESSION['openFrom'];?>;
		const openTill = <?=$_SESSION['openTill'];?>;
		const minutsOpenTotal = (openTill - openFrom) * 60;
		const height = $('.day').height() - $('.grid-cell').height();
		const timeline = $('.timeline');
			
		function draw_timeline() {
			var date = new Date();
			
			
			if(openFrom > date.getHours() || date.getHours() >= openTill) {
				timeline.hide();
			} else {
				var curMinutes = ((date.getHours() - openFrom ) * 60 ) + date.getMinutes();
				var percentOfDay = curMinutes / minutsOpenTotal;
				var topLoc = Math.floor(height * percentOfDay);
				
				timeline.show();
				timeline.css("top", (topLoc + $('.grid-cell').height()) + "px");
			}
		}
		draw_timeline();
		setInterval(draw_timeline, 60000);
	</script>
<?php }?>
</body>
</html>