<?php $access = 2;
	


include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if (!isset($_SESSION['monthSelected'])) $_SESSION['monthSelected'] = date('Y-m', time()); 

if($_SESSION['pwr'] > 1 && $_SESSION['pwr'] < 90) {
	$userCond = "users.id = " . $_SESSION['userID'];
} else {
	$userCond = 1;
}
$sql = "SELECT visitID, SUM(price) as price, SUM(wage) as wage, SUM(tips) as tips
		, visits.date
        , users.id as userID, CONCAT(users.name, ' ', users.surname) as staffName
		FROM `visits_staff`
		LEFT JOIN visits ON visits_staff.visitID = visits.id
		LEFT JOIN users ON visits_staff.userID = users.id
		WHERE DATE_FORMAT(date, '%Y-%m') = :month
		AND visits.locationID = :locationID
		AND $userCond
		GROUP BY users.id
		ORDER BY price DESC";
try {
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':month', $_SESSION['monthSelected'], PDO::PARAM_STR);
	$stmt->bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
	$stmt->execute();
	$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
	include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
}

$title = $_SESSION['monthSelected'] . ': '  . lang::MENU_WAGES . lang::TXT_AT;

//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	include('filters.php');
	echo tabs($tabs, 'wage');
	
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc($title);

	if($data != null) { ?>
	

		<table class="stripy">
			<thead>
				<tr>
					<th><?=lang::HDR_NAME;?></th>
					<th><?=lang::HDR_REVENUE;?></th>
					<th><?=lang::HDR_WAGE;?></th>
					<th><?=lang::HDR_TIPS;?></th>
					<?php if($_SESSION['pwr'] >= 90) echo "<th>" . lang::HDR_PROFIT ."</th>";?>
				</tr>
			</thead>
			<tbody>	
				<?php 
				$total_price = $total_wage = $total_tips = $total_profit = 0;
				
				foreach($data as $row) { 
					$total_price  += $row['price'];
					$total_wage   += $row['wage'];
					$total_tips	  += $row['tips'];
					$total_profit += ($row['price'] - $row['wage']);
					?>
					<tr>
						<td><?=$row['staffName'];?></td>
						<td class="center"><?=correctNumber($row['price']);?></td>
						<td class="center"><?=correctNumber($row['wage']);?></td>
						<td class="center"><?=correctNumber($row['tips']);?></td>
						<?php if($_SESSION['pwr'] >= 90) echo '<td class="center">' . correctNumber(($row['price'] - $row['wage'])) . '</td>'; ?>
					</tr>
				<?php }	
			if ($_SESSION['pwr'] >=90) { ?>
			</tbody>
			<tfoot>
					<tr>
						<th><?=lang::HDR_TOTAL;?></th>
						<th><?=correctNumber($total_price);?></th>
						<th><?=correctNumber($total_wage);?></th>
						<th><?=correctNumber($total_tips);?></th>
						<th><?=correctNumber($total_profit);?></th>
					</tr>
			</tfoot>	
			<?php } ?>
		</table>
	<?php } else {
		echo lang::ERR_NO_INFO;
	} ?>
</section>	


<?php 

	

include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>