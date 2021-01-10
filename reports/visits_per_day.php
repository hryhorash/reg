<?php 
$access = 90;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if (!isset($_SESSION['monthSelected'])) $_SESSION['monthSelected'] = date('Y-m', time()); 

$sql_revenue = "SELECT visits.date, SUM(staff.price) as revenue, SUM(staff.wage) as wages
		FROM `visits`
		LEFT JOIN (SELECT DISTINCT visitID, price, wage
						FROM visits_staff
					) staff ON visits.id = staff.visitID
					WHERE visits.locationID = :locationID
			AND DATE_FORMAT(visits.date, '%Y-%m') = :month
			AND visits.state = 10
			AND visits.price_total > 0
		GROUP BY visits.date
		ORDER BY visits.date DESC";
$stmt = $pdo->prepare($sql_revenue);
$stmt->bindValue(':month', $_SESSION['monthSelected'], PDO::PARAM_STR);
$stmt->bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
$stmt->execute();
$revenue = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_revenue = $total_wages = 0;
foreach($revenue as $row) {
	$total_revenue += $row['revenue'];
	$total_wages += $row['wages'];
	
	switch(date('D', strtotime($row['date']))) {
		case 'Mon':
			$mon[] = $row['revenue'];
			break;
		case 'Tue':
			$tue[] = $row['revenue'];
			break;
		case 'Wed':
			$wed[] = $row['revenue'];
			break;
		case 'Thu':
			$thu[] = $row['revenue'];
			break;
		case 'Fri':
			$fri[] = $row['revenue'];
			break;
		case 'Sat':
			$sat[] = $row['revenue'];
			break;
		case 'Sun':
			$sun[] = $row['revenue'];
			break;
	}
}

$days_totals =array(get_sum_from_array($mon,-1),
					get_sum_from_array($tue,-1),
					get_sum_from_array($wed,-1),
					get_sum_from_array($thu,-1),
					get_sum_from_array($fri,-1),
					get_sum_from_array($sat,-1),
					get_sum_from_array($sun,-1));


$title = $_SESSION['monthSelected'] . ': '  . lang::H2_VISITS_REVENUE_PER_DAY . lang::TXT_AT;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	include('filters.php');
	echo tabs($tabs, 'work');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc($title);?>
	
	<table class="stripy" style="float:left;">
		<thead>
			<tr>
				<th><?=lang::DATE;?></th>
				<th><?=lang::HDR_REVENUE . ',' . curr();?></th>
				<th><?=lang::HDR_WAGE . ',' . curr();?></th>
			</tr>
		</thead>
		<tbody>	
			<?php 
			foreach($revenue as $row) {
				echo '<tr>
					<td><a href="/visits/visits_list.php?state=10&date='.$row['date'].'">'. correctDate($row['date']) .'</a></td>
					<td class="center">'. correctNumber($row[revenue]) .'</td>
					<td class="center">'. correctNumber($row[wages]) .'</td>
				</tr>';
			} ?>
		</tbody>	
		<tfoot>
			<tr>
				<th><?=lang::HDR_TOTAL;?>:</th>
				<th><?=correctNumber($total_revenue);?></th>
				<th><?=correctNumber($total_wages);?></th>
			</tr>
		</tfoot>
	</table>

	<table style="float: left; margin-left: 10px;">
		<tr>
			<th><?=lang::MONDAY;?></th>
			<th><?=lang::TUESDAY;?></th>
			<th><?=lang::WEDNESDAY;?></th>
			<th><?=lang::THURSDAY;?></th>
			<th><?=lang::FRIDAY;?></th>
			<th><?=lang::SATURDAY;?></th>
			<th><?=lang::SUNDAY;?></th>
		</tr>
		<tr>
			<?php
			foreach($days_totals as $day) {
					if($day == max($days_totals))
						echo '<td class="center green bold">' . correctNumber($day) . curr() . '</td>';
					elseif($day == min($days_totals))
						echo '<td class="center red bold" style="color:white;">' . correctNumber($day) . curr() . '</td>';
					else
						echo '<td class="center">' . correctNumber($day) . curr() . '</td>';
			}
			?>
			
			
		</tr>
	</table>

</section>	


<?php 

	

include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>