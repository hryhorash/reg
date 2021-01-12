<?php 
$access = 90;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if (!isset($_SESSION['monthSelected'])) $_SESSION['monthSelected'] = date('Y-m', time()); 
$pieces = explode("-", $_SESSION['monthSelected']);
$y=$pieces[0];
$m=$pieces[1];
$y_prev = $y-1;
$m_prev = $m-1;
if ($m_prev <10) {$m_prev = '0' . $m_prev;}
$month_prev_year = $y_prev . '-' . $m;

if ($m == '01') {
	$month_prev = $y_prev . '-12';
} else $month_prev = $y . '-' . $m_prev;

$months = array($_SESSION['monthSelected'], $month_prev, $month_prev_year);

$sql_services_income = "SELECT DATE_FORMAT(date, '%Y-%m') AS month
			, (CASE WHEN DATE_FORMAT(date, '%Y-%m') = :month
				THEN SUM(staff.price)
				ELSE 0
				END) as income_curr
			, (CASE WHEN DATE_FORMAT(date, '%Y-%m') = :month_prev
				THEN SUM(staff.price)
				ELSE 0
				END) as income_prev
			, (CASE WHEN DATE_FORMAT(date, '%Y-%m') = :month_prev_year 
				THEN SUM(staff.price)
				ELSE 0
				END) as income_pprev
			, (CASE WHEN DATE_FORMAT(date, '%Y-%m') = :month
				THEN SUM(staff.wage)
				ELSE 0
				END) as wages_curr
			, (CASE WHEN DATE_FORMAT(date, '%Y-%m') = :month_prev
				THEN SUM(staff.wage)
				ELSE 0
				END) as wages_prev    
			, (CASE WHEN DATE_FORMAT(date, '%Y-%m') = :month_prev_year 
				THEN SUM(staff.wage)
				ELSE 0
				END) as wages_pprev    
		FROM visits 
		LEFT JOIN (
			SELECT DISTINCT visitID, price, wage
			FROM visits_staff
		) staff ON visits.id = staff.visitID
		
		WHERE (DATE_FORMAT(date, '%Y-%m') = :month
			OR DATE_FORMAT(date, '%Y-%m') = :month_prev  
			OR DATE_FORMAT(date, '%Y-%m') = :month_prev_year )
			AND locationID = :locationID
			AND visits.state = 10
		GROUP BY month 
		ORDER BY month DESC";
$stmt = $pdo->prepare($sql_services_income);
$stmt->bindValue(':month', $_SESSION['monthSelected'], PDO::PARAM_STR);
$stmt->bindValue(':month_prev', $month_prev, PDO::PARAM_STR);
$stmt->bindValue(':month_prev_year', $month_prev_year, PDO::PARAM_STR);
$stmt->bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
$stmt->execute();
$count=1;
while ($visits[$count] = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$revenue_work_curr[] = $visits[$count]['income_curr'];
	$wages_curr[] = $visits[$count]['wages_curr'];
	$revenue_work_prev[] = $visits[$count]['income_prev'];
	$wages_prev[] = $visits[$count]['wages_prev'];
	$revenue_work_pprev[] = $visits[$count]['income_pprev'];
	$wages_pprev[] = $visits[$count]['wages_pprev'];
	$count++;
}

$sql_sales_income = "SELECT DATE_FORMAT(dateOut, '%Y-%m') AS month
			, (CASE WHEN DATE_FORMAT(dateOut, '%Y-%m') = :month
				THEN SUM(received.priceOut)
				ELSE 0
				END) as income_sales_curr
			, (CASE WHEN DATE_FORMAT(dateOut, '%Y-%m') = :month_prev
				THEN SUM(received.priceOut)
				ELSE 0
				END) as income_sales_prev
			, (CASE WHEN DATE_FORMAT(dateOut, '%Y-%m') = :month_prev_year
				THEN SUM(received.priceOut)
				ELSE 0
				END) as income_sales_pprev
			, (CASE WHEN DATE_FORMAT(dateOut, '%Y-%m') = :month
				THEN SUM(received.priceOut) - SUM(received.priceIn)
				ELSE 0
				END) as profit_sales_curr
			, (CASE WHEN DATE_FORMAT(dateOut, '%Y-%m') = :month_prev
				THEN SUM(received.priceOut) - SUM(received.priceIn)
				ELSE 0
				END) as profit_sales_prev
			, (CASE WHEN DATE_FORMAT(dateOut, '%Y-%m') = :month_prev_year
				THEN SUM(received.priceOut) - SUM(received.priceIn)
				ELSE 0
				END) as profit_sales_pprev
		FROM received 
		LEFT JOIN invoices ON received.invoiceID=invoices.id
		WHERE (DATE_FORMAT(dateOut, '%Y-%m') = :month
			OR DATE_FORMAT(dateOut, '%Y-%m') = :month_prev 
			OR DATE_FORMAT(dateOut, '%Y-%m') = :month_prev_year )
			AND locationID = :locationID
		GROUP BY month 
		ORDER BY month DESC";
$stmt2 = $pdo->prepare($sql_sales_income);
$stmt2->bindValue(':month', $_SESSION['monthSelected'], PDO::PARAM_STR);
$stmt2->bindValue(':month_prev', $month_prev, PDO::PARAM_STR);
$stmt2->bindValue(':month_prev_year', $month_prev_year, PDO::PARAM_STR);
$stmt2->bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
$stmt2->execute();
$count=1;
while ($sales[$count] = $stmt2->fetch(PDO::FETCH_ASSOC)) {
	$revenue_sales_curr[]  = $sales[$count]['income_sales_curr'];
	$revenue_sales_prev[]  = $sales[$count]['income_sales_prev'];
	$revenue_sales_pprev[] = $sales[$count]['income_sales_pprev'];
	
	$profit_sales_curr[]  = $sales[$count]['profit_sales_curr'];
	$profit_sales_prev[]  = $sales[$count]['profit_sales_prev'];
	$profit_sales_pprev[] = $sales[$count]['profit_sales_pprev'];
	$count++;
}

$sql_cosm_expences = "SELECT DATE_FORMAT(datePaid, '%Y-%m') as month,
				(CASE WHEN DATE_FORMAT(datePaid, '%Y-%m') = :month
				THEN SUM(received.priceIn)
				ELSE 0
				END) as curr
			, (CASE WHEN DATE_FORMAT(datePaid, '%Y-%m') = :month_prev
				THEN SUM(received.priceIn)
				ELSE 0
				END) as prev
			, (CASE WHEN DATE_FORMAT(datePaid, '%Y-%m') = :month_prev_year
				THEN SUM(received.priceIn)
				ELSE 0
				END) as pprev
		FROM received 
		LEFT JOIN invoices ON received.invoiceID=invoices.id
		WHERE (DATE_FORMAT(datePaid, '%Y-%m') = :month OR
				DATE_FORMAT(datePaid, '%Y-%m') = :month_prev OR
				DATE_FORMAT(datePaid, '%Y-%m') = :month_prev_year)
			AND locationID = :locationID
			AND invoices.state >=4 GROUP BY month
		ORDER BY month DESC";
$stmt3 = $pdo->prepare($sql_cosm_expences);
$stmt3->bindValue(':month', $_SESSION['monthSelected'], PDO::PARAM_STR);
$stmt3->bindValue(':month_prev', $month_prev, PDO::PARAM_STR);
$stmt3->bindValue(':month_prev_year', $month_prev_year, PDO::PARAM_STR);
$stmt3->bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
$stmt3->execute();
$count=1;
while ($cosm_expences[$count] = $stmt3->fetch(PDO::FETCH_ASSOC)) {
	$purchases_curr[]  = $cosm_expences[$count]['curr'];
	$purchases_prev[]  = $cosm_expences[$count]['prev'];
	$purchases_pprev[] = $cosm_expences[$count]['pprev'];
	$count++;
}

$sql_expences = "SELECT DATE_FORMAT(date, '%Y-%m') AS month
			, (CASE WHEN DATE_FORMAT(date, '%Y-%m') = :month
				THEN SUM(price)
				ELSE 0
				END) as curr
			, (CASE WHEN DATE_FORMAT(date, '%Y-%m') = :month_prev
				THEN SUM(price)
				ELSE 0
				END) as prev
			, (CASE WHEN DATE_FORMAT(date, '%Y-%m') = :month_prev_year
				THEN SUM(price)
				ELSE 0
				END) as pprev
		FROM expences 
		WHERE (DATE_FORMAT(date, '%Y-%m') = :month
			OR DATE_FORMAT(date, '%Y-%m') = :month_prev 
			OR DATE_FORMAT(date, '%Y-%m') = :month_prev_year )
			AND locationID = :locationID
		GROUP BY month 
		ORDER BY month DESC";
$stmt4 = $pdo->prepare($sql_expences);
$stmt4->bindValue(':month', $_SESSION['monthSelected'], PDO::PARAM_STR);
$stmt4->bindValue(':month_prev', $month_prev, PDO::PARAM_STR);
$stmt4->bindValue(':month_prev_year', $month_prev_year, PDO::PARAM_STR);
$stmt4->bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
$stmt4->execute();
$count=1;
while ($expences[$count] = $stmt4->fetch(PDO::FETCH_ASSOC)) {
	$expences_curr[]  = $expences[$count]['curr'];
	$expences_prev[]  = $expences[$count]['prev'];
	$expences_pprev[] = $expences[$count]['pprev'];

	$count++;
}

$sql_stakes = "SELECT DISTINCT subcatID, subcategory
				FROM `stakes` 
				LEFT JOIN expences_subcat ON stakes.subcatID = expences_subcat.id
				WHERE locationID = :locationID 
				ORDER BY subcatID, date DESC";
$stmt5 = $pdo->prepare($sql_stakes);
$stmt5->bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
$stmt5->execute();
while ($row = $stmt5->fetch(PDO::FETCH_ASSOC)) {
	$subcatIDs[] = $row['subcatID'];
	$subcatNames[] = $row['subcategory'];
}


$count = 0;
while(isset($months[$count])) {
	$stakes_sum[$count] = 0;
	$i = 0;
	foreach($subcatIDs as $subcatID)
	{
		$subcatID_sum = stakesExpenses($subcatID, $months[$count]);
		$stakes_sum[$count] += $subcatID_sum;
		//$stakes_details[$count][] = '<p><span class="bold">' . $subcatNames[$i] . ':</span> ' . correctNumber($subcatID_sum) . '</p>';
		$stakes_details[$count][] = $subcatNames[$i] . ': ' . correctNumber($subcatID_sum) . "\n";
		$i++;
	}
	$count++;
}

//Итоги
$total_revenue[] = get_sum_from_array($revenue_work_curr,-1)  + get_sum_from_array($revenue_sales_curr,-1);
$total_revenue[] = get_sum_from_array($revenue_work_prev,-1)  + get_sum_from_array($revenue_sales_prev,-1); 
$total_revenue[] = get_sum_from_array($revenue_work_pprev,-1) + get_sum_from_array($revenue_sales_pprev,-1);  

$total_expences[] = get_sum_from_array($purchases_curr,-1)  + get_sum_from_array($expences_curr,-1)  + get_sum_from_array($wages_curr,-1)  + $stakes_sum[0];
$total_expences[] = get_sum_from_array($purchases_prev,-1)  + get_sum_from_array($expences_prev,-1)  + get_sum_from_array($wages_prev,-1)  + $stakes_sum[1];
$total_expences[] = get_sum_from_array($purchases_pprev,-1) + get_sum_from_array($expences_pprev,-1) + get_sum_from_array($wages_pprev,-1) + $stakes_sum[2];


$total_profit[] = $total_revenue[0] - $total_expences[0];
$total_profit[] = $total_revenue[1] - $total_expences[1];
$total_profit[] = $total_revenue[2] - $total_expences[2];
		




$title = $_SESSION['monthSelected'] . ': '  . lang::H2_FINANCE_REPORT . lang::TXT_AT;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	include('filters.php');
	echo tabs($tabs, 'fin');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc($title);?>
		
	<table class="stripy">
		<thead>
			<tr>
				<th><?=lang::HDR_ENTITY_NAME;?></th>
				<th><?=$_SESSION['monthSelected'];?></th>
				<th><?=$month_prev;?></th>
				<th><?=$month_prev_year;?></th>
			</tr>
		</thead>
		<tbody>	
			<tr class="bold">
				<td><?=lang::HDR_REVENUE_TOTAL;?>:</td>
				<td class="center"><?=correctNumber($total_revenue[0]);?></td>
				<td class="center"><?=correctNumber($total_revenue[1]);?></td>
				<td class="center"><?=correctNumber($total_revenue[2]);?></td>
			</tr>
			<tr>
				<td>- <?=lang::HDR_REVENUE_SERVICES;?></td>
				<td class="center"><a href="/reports/visits_per_day.php?month=<?=$months[0];?>"><?=get_sum_from_array($revenue_work_curr);?></a></td>
				<td class="center"><a href="/reports/visits_per_day.php?month=<?=$months[1];?>"><?=get_sum_from_array($revenue_work_prev);?></a></td>
				<td class="center"><a href="/reports/visits_per_day.php?month=<?=$months[2];?>"><?=get_sum_from_array($revenue_work_pprev);?></a></td>
			</tr>
			<tr>
				<td>- <?=lang::HDR_REVENUE_SALES;?></td>
				<td class="center"><a href="/reports/sales.php"><?=get_sum_from_array($revenue_sales_curr);?></a></td>
				<td class="center"><a href="/reports/sales.php?month=<?=$month_prev;?>"><?=get_sum_from_array($revenue_sales_prev);?></a></td>
				<td class="center"><a href="/reports/sales.php?month=<?=$month_prev_year;?>"><?=get_sum_from_array($revenue_sales_pprev);?></a></td>
			</tr>
			<tr class="bold">
				<td><?=lang::HDR_EXPENCES_TOTAL;?>:</td>
				<td class="center"><?=correctNumber($total_expences[0]);?></td>
				<td class="center"><?=correctNumber($total_expences[1]);?></td>
				<td class="center"><?=correctNumber($total_expences[2]);?></td>
			</tr>
			<tr>
				<td>- <?=lang::HDR_EXPENCES_FIXED;?></td>
				<td class="center tooltip" data-tooltip="<?php foreach($stakes_details[0] as $item) echo $item; ?>">
						<a href="#"><?=correctNumber($stakes_sum[0]);?></a>
				</td>
				<td class="center tooltip" data-tooltip="<?php foreach($stakes_details[1] as $item) echo $item; ?>">
						<a href="#"><?=correctNumber($stakes_sum[1]);?></a>
				</td>
				<td class="center tooltip" data-tooltip="<?php foreach($stakes_details[2] as $item) echo $item ; ?>">
						<a href="#"><?=correctNumber($stakes_sum[2]);?></a>
				</td>
			</tr>
			<tr>
				<td>- <?=lang::HDR_EXPENCES_OPERATIONAL;?></td>
				<td class="center"><a href="/expences/expencesList.php?month=<?=$months[0];?>"><?=get_sum_from_array($expences_curr);?></a></td>
				<td class="center"><a href="/expences/expencesList.php?month=<?=$months[1];?>"><?=get_sum_from_array($expences_prev);?></a></td>
				<td class="center"><a href="/expences/expencesList.php?month=<?=$months[2];?>"><?=get_sum_from_array($expences_pprev);?></a></td>
			</tr>
			<tr>
				<td>- <?=lang::HDR_EXPENCES_COSMETICS;?></td>
				<td class="center"><a href="/cosmetics/invoice_list.php?tab=archive&month=<?=$months[0];?>"><?=get_sum_from_array($purchases_curr);?></a></td>
				<td class="center"><a href="/cosmetics/invoice_list.php?tab=archive&month=<?=$months[1];?>"><?=get_sum_from_array($purchases_prev);?></a></td>
				<td class="center"><a href="/cosmetics/invoice_list.php?tab=archive&month=<?=$months[2];?>"><?=get_sum_from_array($purchases_pprev);?></a></td>
			</tr>
			<tr>
				<td>- <?=lang::HDR_EXPENCES_WAGES;?></td>
				<td class="center"><a href="/reports/visits_per_day.php?month=<?=$months[0];?>"><?=get_sum_from_array($wages_curr);?></a></td>
				<td class="center"><a href="/reports/visits_per_day.php?month=<?=$months[1];?>"><?=get_sum_from_array($wages_prev);?></a></td>
				<td class="center"><a href="/reports/visits_per_day.php?month=<?=$months[2];?>"><?=get_sum_from_array($wages_pprev);?></a></td>
			</tr>
			<tr>
				<th><?=lang::HDR_PROFIT_TOTAL;?>:</th>
				<th style="font-size:large;"><?=correctNumber($total_profit[0]);?></th>
				<th style="font-size:large;"><?=correctNumber($total_profit[1]);?></th>
				<th style="font-size:large;"><?=correctNumber($total_profit[2]);?></th>
			</tr>
			<tr>
				<td>- <?=lang::HDR_REVENUE_SERVICES;?></td>
				<td class="center"><?=correctNumber($total_profit[0] - get_sum_from_array($profit_sales_curr,-1));?></td>
				<td class="center"><?=correctNumber($total_profit[1] - get_sum_from_array($profit_sales_prev,-1));?></td>
				<td class="center"><?=correctNumber($total_profit[2] - get_sum_from_array($profit_sales_pprev,-1));?></td>
			</tr>
			<tr>
				<td>- <?=lang::HDR_PROFIT_SALES;?></td>
				<td class="center"><a href="/reports/sales.php?month=<?=$months[0];?>"><?=get_sum_from_array($profit_sales_curr);?></a></td>
				<td class="center"><a href="/reports/sales.php?month=<?=$months[1];?>"><?=get_sum_from_array($profit_sales_prev);?></a></td>
				<td class="center"><a href="/reports/sales.php?month=<?=$months[2];?>"><?=get_sum_from_array($profit_sales_pprev);?></a></td>
			</tr>
		</tbody>	
	</table>
</section>	


<?php 

	

include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>