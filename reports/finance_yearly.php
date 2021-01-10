<?php 
$access = 90;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if (!isset($_GET['year'])) $years[] = date('Y', time()); 
else $years[] = $_GET['year'];
$years[] = $years[0] - 1;
$years[] = $years[0] - 2;	

$sql_services_income = "SELECT DATE_FORMAT(date, '%Y') AS year
			, (CASE WHEN DATE_FORMAT(date, '%Y') = :year
				THEN SUM(staff.price)
				ELSE 0
				END) as income_curr
			, (CASE WHEN DATE_FORMAT(date, '%Y') = :year_prev
				THEN SUM(staff.price)
				ELSE 0
				END) as income_prev
			, (CASE WHEN DATE_FORMAT(date, '%Y') = :year_pprev 
				THEN SUM(staff.price)
				ELSE 0
				END) as income_pprev
			, (CASE WHEN DATE_FORMAT(date, '%Y') = :year
				THEN SUM(staff.wage)
				ELSE 0
				END) as wages_curr
			, (CASE WHEN DATE_FORMAT(date, '%Y') = :year_prev
				THEN SUM(staff.wage)
				ELSE 0
				END) as wages_prev    
			, (CASE WHEN DATE_FORMAT(date, '%Y') = :year_pprev 
				THEN SUM(staff.wage)
				ELSE 0
				END) as wages_pprev    
		FROM visits 
		LEFT JOIN (
			SELECT DISTINCT visitID, price, wage
			FROM visits_staff
		) staff ON visits.id = staff.visitID
		
		WHERE (DATE_FORMAT(date, '%Y') = :year
			OR DATE_FORMAT(date, '%Y') = :year_prev  
			OR DATE_FORMAT(date, '%Y') = :year_pprev )
			AND locationID = :locationID
			AND visits.state = 10
		GROUP BY year 
		ORDER BY year DESC";
$stmt = $pdo->prepare($sql_services_income);
$stmt->bindValue(':year', $years[0] , PDO::PARAM_STR);
$stmt->bindValue(':year_prev', $years[1], PDO::PARAM_STR);
$stmt->bindValue(':year_pprev', $years[2], PDO::PARAM_STR);
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

$sql_sales_income = "SELECT DATE_FORMAT(dateOut, '%Y') AS year
			, (CASE WHEN DATE_FORMAT(dateOut, '%Y') = :year
				THEN SUM(received.priceOut)
				ELSE 0
				END) as income_sales_curr
			, (CASE WHEN DATE_FORMAT(dateOut, '%Y') = :year_prev
				THEN SUM(received.priceOut)
				ELSE 0
				END) as income_sales_prev
			, (CASE WHEN DATE_FORMAT(dateOut, '%Y') = :year_pprev
				THEN SUM(received.priceOut)
				ELSE 0
				END) as income_sales_pprev
			, (CASE WHEN DATE_FORMAT(dateOut, '%Y') = :year
				THEN SUM(received.priceOut) - SUM(received.priceIn)
				ELSE 0
				END) as profit_sales_curr
			, (CASE WHEN DATE_FORMAT(dateOut, '%Y') = :year_prev
				THEN SUM(received.priceOut) - SUM(received.priceIn)
				ELSE 0
				END) as profit_sales_prev
			, (CASE WHEN DATE_FORMAT(dateOut, '%Y') = :year_pprev
				THEN SUM(received.priceOut) - SUM(received.priceIn)
				ELSE 0
				END) as profit_sales_pprev
		FROM received 
		LEFT JOIN invoices ON received.invoiceID=invoices.id
		WHERE (DATE_FORMAT(dateOut, '%Y') = :year
			OR DATE_FORMAT(dateOut, '%Y') = :year_prev 
			OR DATE_FORMAT(dateOut, '%Y') = :year_pprev )
			AND locationID = :locationID
		GROUP BY year 
		ORDER BY year DESC";
$stmt2 = $pdo->prepare($sql_sales_income);
$stmt2->bindValue(':year', $years[0], PDO::PARAM_STR);
$stmt2->bindValue(':year_prev', $years[1], PDO::PARAM_STR);
$stmt2->bindValue(':year_pprev', $years[2], PDO::PARAM_STR);
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

$sql_cosm_expences = "SELECT DATE_FORMAT(datePaid, '%Y') as year,
				(CASE WHEN DATE_FORMAT(datePaid, '%Y') = :year
				THEN SUM(received.priceIn)
				ELSE 0
				END) as curr
			, (CASE WHEN DATE_FORMAT(datePaid, '%Y') = :year_prev
				THEN SUM(received.priceIn)
				ELSE 0
				END) as prev
			, (CASE WHEN DATE_FORMAT(datePaid, '%Y') = :year_pprev
				THEN SUM(received.priceIn)
				ELSE 0
				END) as pprev
		FROM received 
		LEFT JOIN invoices ON received.invoiceID=invoices.id
		WHERE (DATE_FORMAT(datePaid, '%Y') = :year OR
				DATE_FORMAT(datePaid, '%Y') = :year_prev OR
				DATE_FORMAT(datePaid, '%Y') = :year_pprev)
			AND locationID = :locationID
			AND invoices.state >=4 GROUP BY year
		ORDER BY year DESC";
$stmt3 = $pdo->prepare($sql_cosm_expences);
$stmt3->bindValue(':year', $years[0], PDO::PARAM_STR);
$stmt3->bindValue(':year_prev', $years[1], PDO::PARAM_STR);
$stmt3->bindValue(':year_pprev', $years[2], PDO::PARAM_STR);
$stmt3->bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
$stmt3->execute();
$count=1;
while ($cosm_expences[$count] = $stmt3->fetch(PDO::FETCH_ASSOC)) {
	$purchases_curr[]  = $cosm_expences[$count]['curr'];
	$purchases_prev[]  = $cosm_expences[$count]['prev'];
	$purchases_pprev[] = $cosm_expences[$count]['pprev'];
	$count++;
}

$sql_expences = "SELECT DATE_FORMAT(date, '%Y') AS year
			, (CASE WHEN DATE_FORMAT(date, '%Y') = :year
				THEN SUM(price)
				ELSE 0
				END) as curr
			, (CASE WHEN DATE_FORMAT(date, '%Y') = :year_prev
				THEN SUM(price)
				ELSE 0
				END) as prev
			, (CASE WHEN DATE_FORMAT(date, '%Y') = :year_pprev
				THEN SUM(price)
				ELSE 0
				END) as pprev
		FROM expences 
		WHERE (DATE_FORMAT(date, '%Y') = :year
			OR DATE_FORMAT(date, '%Y') = :year_prev 
			OR DATE_FORMAT(date, '%Y') = :year_pprev )
			AND locationID = :locationID
		GROUP BY year 
		ORDER BY year DESC";
$stmt4 = $pdo->prepare($sql_expences);
$stmt4->bindValue(':year', $years[0], PDO::PARAM_STR);
$stmt4->bindValue(':year_prev', $years[1], PDO::PARAM_STR);
$stmt4->bindValue(':year_pprev', $years[2], PDO::PARAM_STR);
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
while(isset($years[$count])) {
	$stakes_sum[$count] = 0;
	$i = 0;
	foreach($subcatIDs as $subcatID)
	{
		$subcatID_sum = stakesExpenses_year($subcatID, $years[$count]);
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
	




$title = $years[0] . ': '  . lang::H2_FINANCE_YEARLY . lang::TXT_AT;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	include('filters.php');
	echo tabs($tabs, 'year');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc($title);?>
	<table class="stripy">
		<thead>
			<tr>
				<th></th>
				<th><?=$years[0];?></th>
				<th><?=$years[1];?></th>
				<th><?=$years[2];?></th>
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
				<td class="center"><?=get_sum_from_array($revenue_work_curr);?></td>
				<td class="center"><?=get_sum_from_array($revenue_work_prev);?></td>
				<td class="center"><?=get_sum_from_array($revenue_work_pprev);?></td>
			</tr>
			<tr>
				<td>- <?=lang::HDR_REVENUE_SALES;?></td>
				<td class="center"><?=get_sum_from_array($revenue_sales_curr);?></td>
				<td class="center"><?=get_sum_from_array($revenue_sales_prev);?></td>
				<td class="center"><?=get_sum_from_array($revenue_sales_pprev);?></td>
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
				<td class="center tooltip" data-tooltip="<?php foreach($stakes_details[2] as $item) echo $item; ?>">
						<a href="#"><?=correctNumber($stakes_sum[2]);?></a>
				</td>
			</tr>
			<tr>
				<td>- <?=lang::HDR_EXPENCES_OPERATIONAL;?></td>
				<td class="center"><?=get_sum_from_array($expences_curr);?></td>
				<td class="center"><?=get_sum_from_array($expences_prev);?></td>
				<td class="center"><?=get_sum_from_array($expences_pprev);?></td>
			</tr>
			<tr>
				<td>- <?=lang::HDR_EXPENCES_COSMETICS;?></td>
				<td class="center"><?=get_sum_from_array($purchases_curr);?></td>
				<td class="center"><?=get_sum_from_array($purchases_prev);?></td>
				<td class="center"><?=get_sum_from_array($purchases_pprev);?></td>
			</tr>
			<tr>
				<td>- <?=lang::HDR_EXPENCES_WAGES;?></td>
				<td class="center"><?=get_sum_from_array($wages_curr);?></td>
				<td class="center"><?=get_sum_from_array($wages_prev);?></td>
				<td class="center"><?=get_sum_from_array($wages_pprev);?></td>
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
				<td class="center"><?=get_sum_from_array($profit_sales_curr);?></td>
				<td class="center"><?=get_sum_from_array($profit_sales_prev);?></td>
				<td class="center"><?=get_sum_from_array($profit_sales_pprev);?></td>
			</tr>

			<tr>
				<th colspan="4"><?=lang::HDR_ANALYSIS;?>:</th>
			<tr>
				<td><?=lang::HDR_EXPENCES_TO_REVENUE;?>:</td>
				<td class="center"><?php if($total_revenue[0] > 0) echo correctNumber($total_expences[0]/$total_revenue[0]*100) . '%'; else echo '100%';?></td>
				<td class="center"><?php if($total_revenue[1] > 0) echo correctNumber($total_expences[1]/$total_revenue[1]*100) . '%'; else echo '100%';?></td>
				<td class="center"><?php if($total_revenue[2] > 0) echo correctNumber($total_expences[2]/$total_revenue[2]*100) . '%'; else echo '100%';?></td>
			</tr>
			<tr>
				<td><?=lang::HDR_PROFIT_PER_MONTH_AVG;?>:</td>
				<td class="center"><?=correctNumber($total_profit[0]/12);?></td>
				<td class="center"><?=correctNumber($total_profit[1]/12);?></td>
				<td class="center"><?=correctNumber($total_profit[2]/12);?></td>
			</tr>
			
		</tbody>	
	</table>

	


</section>	


<?php 

	

include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>