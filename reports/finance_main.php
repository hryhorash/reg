<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if (isset($_SESSION['monthSelected'])) {
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

	$sql_services_income = "SELECT DATE_FORMAT(date, '%Y-%m') AS month
				, SUM(staff.price) as income
                , SUM(staff.tips) as tips
                , SUM(staff.wage) as wages
			FROM visits 
			LEFT JOIN (
           	 	SELECT DISTINCT visitID, tips, price, wage
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
	while ($visits[$count] = $stmt->fetch(PDO::FETCH_ASSOC)) $count++;
	
	$sql_sales_income = "SELECT DATE_FORMAT(dateOut, '%Y-%m') AS month
				, SUM(received.priceIn) as netto
                , SUM(received.priceOut) as sales_income
                , (SUM(received.priceOut) - SUM(received.priceIn)) as profit
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
		$count++;
	}
	
	$sql_cosm_expences = "SELECT DATE_FORMAT(invoices.datePaid, '%Y-%m') AS month
				, SUM(received.priceIn) as purchases
            FROM received 
			LEFT JOIN invoices ON received.invoiceID=invoices.id
            WHERE (DATE_FORMAT(datePaid, '%Y-%m') = :month
				OR DATE_FORMAT(datePaid, '%Y-%m') = :month_prev 
				OR DATE_FORMAT(datePaid, '%Y-%m') = :month_prev_year )
				AND locationID = :locationID
                AND invoices.state >=4
            GROUP BY month 
            ORDER BY month DESC";
	$stmt3 = $pdo->prepare($sql_cosm_expences);
	$stmt3->bindValue(':month', $_SESSION['monthSelected'], PDO::PARAM_STR);
	$stmt3->bindValue(':month_prev', $month_prev, PDO::PARAM_STR);
	$stmt3->bindValue(':month_prev_year', $month_prev_year, PDO::PARAM_STR);
	$stmt3->bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
	$stmt3->execute();
	$count=1;
	while ($cosm_expences[$count] = $stmt3->fetch(PDO::FETCH_ASSOC)) $count++;
	
	
	$sql_expences = "SELECT DATE_FORMAT(date, '%Y-%m') AS month
				, SUM(price) as expences
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
	while ($expences[$count] = $stmt4->fetch(PDO::FETCH_ASSOC)) $count++;
	
	
	$sql_stakes = "SELECT DISTINCT subcatID 
					FROM `stakes` 
					WHERE locationID = :locationID 
					ORDER BY subcatID, date DESC";
	$stmt5 = $pdo->prepare($sql_stakes);
	$stmt5->bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
	$stmt5->execute();
	while ($row = $stmt5->fetch(PDO::FETCH_ASSOC)) {
		$subcatIDs[] = $row['subcatID'];
	}
	
	
	$count = 1;
	while($visits[$count]['month'] !=null) {
		$stakes_sum[$count] = 0;
		foreach($subcatIDs as $subcatID)
		{
			$stakes_sum[$count] = $stakes_sum[$count] + stakesExpenses($subcatID, $visits[$count]['month']);
		}
		$count++;
	}
	
	//Итоги
	$count = 1;
	while($visits[$count]['month'] !=null) {
		$total_income[$count] = $visits[$count]['income'] + $sales[$count]['sales_income'];
		$total_expences[$count] = $cosm_expences[$count]['purchases'] + $expences[$count]['expences'] + $visits[$count]['wages'] + $stakes_sum[$count];
		$total_profit[$count] = $total_income[$count] - $total_expences[$count];
		
		$count++;
	}
	
	
	
	

}	



$title = $visits[1]['month'] . ': '  . lang::H2_FINANCE_REPORT . lang::TXT_AT;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	include('filters.php');
	echo tabs($tabs, 'fin');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc($title);
	
	if (isset($_SESSION['monthSelected'])) {?>
	
	
		<div class="row" style="justify-content: space-between;">
			<table class="stripy">
				<thead>
					<tr>
						<th><?=lang::HDR_ENTITY_NAME;?></th>
						<th><?=$visits[1]['month'];?></th>
						<th><?=$visits[2]['month'];?></th>
						<th><?=$visits[3]['month'];?></th>
					</tr>
				</thead>
				<tbody>	
					<tr class="bold">
						<td><?=lang::HDR_REVENUE_TOTAL;?>:</td>
						<td class="center"><?=correctNumber($total_income[1],0);?></td>
						<td class="center"><?=correctNumber($total_income[2],0);?></td>
						<td class="center"><?=correctNumber($total_income[3],0);?></td>
					</tr>
					<tr>
						<td>- <?=lang::HDR_REVENUE_SERVICES;?></td>
						<td class="center"><?=correctNumber($visits[1]['income'],0);?></td>
						<td class="center"><?=correctNumber($visits[2]['income'],0);?></td>
						<td class="center"><?=correctNumber($visits[3]['income'],0);?></td>
					</tr>
					<tr>
						<td>- <?=lang::HDR_REVENUE_SALES;?></td>
						<td class="center"><a href="/reports/sales.php"><?=correctNumber($sales[1]['sales_income'],0);?></a></td>
						<td class="center"><a href="/reports/sales.php?month=<?=$visits[2]['month'];?>"><?=correctNumber($sales[2]['sales_income'],0);?></a></td>
						<td class="center"><a href="/reports/sales.php?month=<?=$visits[3]['month'];?>"><?=correctNumber($sales[3]['sales_income'],0);?></a></td>
					</tr>
					<tr class="bold">
						<td><?=lang::HDR_EXPENCES_TOTAL;?>:</td>
						<td class="center"><?=correctNumber($total_expences[1],0);?></td>
						<td class="center"><?=correctNumber($total_expences[2],0);?></td>
						<td class="center"><?=correctNumber($total_expences[3],0);?></td>
					</tr>
					<tr>
						<td>- <?=lang::HDR_EXPENCES_FIXED;?></td>
						<td class="center"><?=correctNumber($stakes_sum[1],0);?></td>
						<td class="center"><?=correctNumber($stakes_sum[2],0);?></td>
						<td class="center"><?=correctNumber($stakes_sum[3],0);?></td>
					</tr>
					<tr>
						<td>- <?=lang::HDR_EXPENCES_OPERATIONAL;?></td>
						<td class="center"><?=correctNumber($expences[1]['expences'],0);?></td>
						<td class="center"><?=correctNumber($expences[2]['expences'],0);?></td>
						<td class="center"><?=correctNumber($expences[3]['expences'],0);?></td>
					</tr>
					<tr>
						<td>- <?=lang::HDR_EXPENCES_COSMETICS;?></td>
						<td class="center"><?=correctNumber($cosm_expences[1]['purchases'],0);?></td>
						<td class="center"><?=correctNumber($cosm_expences[2]['purchases'],0);?></td>
						<td class="center"><?=correctNumber($cosm_expences[3]['purchases'],0);?></td>
					</tr>
					<tr>
						<td>- <?=lang::HDR_EXPENCES_WAGES;?></td>
						<td class="center"><?=correctNumber($visits[1]['wages'],0);?></td>
						<td class="center"><?=correctNumber($visits[2]['wages'],0);?></td>
						<td class="center"><?=correctNumber($visits[3]['wages'],0);?></td>
					</tr>
					<tr>
						<th><?=lang::HDR_PROFIT_TOTAL;?>:</th>
						<th style="font-size:large;"><?=correctNumber($total_profit[1],0);?></th>
						<th style="font-size:large;"><?=correctNumber($total_profit[2],0);?></th>
						<th style="font-size:large;"><?=correctNumber($total_profit[3],0);?></th>
					</tr>
					<tr>
						<td>- <?=lang::HDR_PROFIT_SALES;?></td>
						<td class="center"><?=correctNumber($sales[1]['profit'],0);?></td>
						<td class="center"><?=correctNumber($sales[2]['profit'],0);?></td>
						<td class="center"><?=correctNumber($sales[3]['profit'],0);?></td>
					</tr>
				</tbody>	
			</table>
		
			
		</div>
	<?php } else {
		echo lang::EXP_USE_FILTER;
	}
	
	?>
</section>	
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>