<?php 
$access = 90;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if (isset($_SESSION['locationSelected'],$_GET['dateFrom'],$_GET['dateTo'],$_GET['service'])) {	
	$total_cost = $total_payroll = $total_expences = 0;
	
	try {
			$stmt = $pdo->prepare('SELECT visits.date
										, SUM(visits_works.price) as price
                                        , t.wage
                                        
									FROM visits
									LEFT JOIN visits_works ON visits.id = visits_works.visitID
									LEFT JOIN worktypes ON visits_works.workID = worktypes.id
									LEFT JOIN expences_works ON worktypes.catID = expences_works.worktypeCatID
                                    LEFT JOIN(
                                            SELECT visits.date
                                       			 , SUM(visits_staff.wage) as wage
										FROM visits
                                        LEFT JOIN visits_staff ON visits.id = visits_staff.visitID
                                        GROUP BY visits.date
                                    ) t
                                    on visits.date = t.date 
                                    WHERE expences_works.expencesCatID = :service
										AND visits.locationID = :location
										AND (visits.date BETWEEN :dateFrom AND :dateTo)
									GROUP BY visits.date
                                    ORDER BY visits.date DESC');
		$stmt->bindValue(':service', $_GET['service'], PDO::PARAM_INT);
		$stmt->bindValue(':dateFrom', $_GET['dateFrom'], PDO::PARAM_STR);
		$stmt->bindValue(':dateTo', $_GET['dateTo'], PDO::PARAM_STR);
		$stmt->bindValue(':location', $_SESSION['locationSelected'], PDO::PARAM_INT);
		$stmt->execute();
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}	
	
	$count1 = 0;
	while($services[$count1] = $stmt->fetch()) {
		$total_cost = $total_cost + $services[$count1]['price'];
		$total_payroll 	= $total_payroll + $services[$count1]['wage'];
		$count1++;
	}
	 
	try {
		$stmt2 = $pdo->prepare("SELECT expences.date, SUM(expences.price)/2 as spent
							FROM expences
							LEFT JOIN expences_subcat ON expences_subcat.id = expences.subcatID
							LEFT JOIN expences_works ON expences_subcat.catID = expences_works.expencesCatID
                            WHERE expences_works.expencesCatID = :service
								AND expences.locationID = :location
								AND (expences.date BETWEEN :dateFrom AND :dateTo)
							GROUP BY expences.date
                            ORDER BY expences.date DESC");
		$stmt2->bindValue(':service', $_GET['service'], PDO::PARAM_INT);
		$stmt2->bindValue(':dateFrom', $_GET['dateFrom'], PDO::PARAM_STR);
		$stmt2->bindValue(':dateTo', $_GET['dateTo'], PDO::PARAM_STR);
		$stmt2->bindValue(':location', $_SESSION['locationSelected'], PDO::PARAM_INT);
		$stmt2->execute();	
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	
	
	$count2=0;
	while($expences[$count2] = $stmt2->fetch()) {
		$total_expences = $total_expences + $expences[$count2]['spent'];
		$count2++;
	}	
	$pdo=NULL;	
	
	$total_balance = correctNumber(($total_cost - $total_payroll - $total_expences)) . curr();
	
	if ($total_balance <= 0 ) $total_balance = '<span style="color:red;">' . $total_balance. '</span>';
}
$title = lang::H2_FINANCE_CAT . lang::TXT_AT;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	include('filters.php');
	echo tabs($tabs, 'cat');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc($title);

	$dateFrom = $dateTo = '';
	if (isset($_GET['dateFrom'])) $dateFrom = $_GET['dateFrom'];

	if(isset($_GET['dateTo']))  $dateTo = $_GET['dateTo'];
	else $dateTo = date('Y-m-d');?>

	<form action="<?=$_SERVER['PHP_SELF'];?>" method="get" name="show">
		<fieldset>
			<div class="row col-2">
				<label for="dateFrom"><?=lang::HDR_OPEN_FROM;?>* :</label>
				<input name="dateFrom" type="date" value="<?=$dateFrom;?>" />
			
				<label for="dateTo"><?=lang::HDR_OPEN_TILL;?>* :</label>
				<input name="dateTo" type="date" value="<?=$dateTo;?>" required />
			
				<label for="service"><?=lang::HDR_CATEGORY;?>* :</label>
				<select name="service"><?=cat_list_fin($_GET['service']);?></select>
			</div>
			<input id="button" style="margin-bottom: var(--padding-std);" type="submit" value="<?=lang::BTN_SHOW;?>" />
		</fieldset>
	</form>	



	<?php  if ($total_balance != NULL) {
		echo '<p>' . lang::TXT_BALANCE . ' ' . correctDate($_GET['dateFrom']) . ' - ' . correctDate($_GET['dateTo']) . ' = <strong>' . $total_balance . '</strong></p>
		<div class="row col-2e">
			<div>
				<h2>'. lang::HDR_REVENUE .'</h2>';
				if ($count1 > 0) {
					echo "<table class='stripy table-autosort'>
							<thead>
							<tr>
								<th style='width: 13ch;'>" . lang::DATE ."</th>
								<th class='table-sortable:*'>". lang::HDR_REVENUE ."</th>
								<th class='table-sortable:*'>". lang::HDR_WAGE ."</th>
								<th class='table-sortable:*'>". lang::HDR_PROFIT ."</th>
							</tr>
							</thead>";
						$count1 = 0;
						while ($services[$count1] != NULL) {
							if($services[$count1]['price'] != 0 OR $services[$count1]['wage'] != 0) {
								echo '<tr>
									<td><a href="/visits/visits_list.php?state=10&date='. $services[$count1]['date'] .'">' . correctDate($services[$count1]['date']) .'</a></td>					
									<td class="center">' . correctNumber($services[$count1]['price']) . '</td>
									<td class="center">' . correctNumber($services[$count1]['wage']) . '</td>
									<td class="center bold">' . correctNumber(($services[$count1]['price'] - $services[$count1]['wage'])) . '</td>
								</tr>';
							}
							$count1++;
						}
							echo '<tfoot>
								<tr>
									<th>' . lang::HDR_WORKDAY . ': ' . $count1 . '</th>
									<th>' . correctNumber($total_cost) . '</th>
									<th>' . correctNumber($total_payroll) . '</th>
									<th>' . correctNumber(($total_cost - $total_payroll)) . '</th>
									
								</tr>
							</tfoot>';
						echo '</table>';
				} else echo lang::ERR_NO_INFO;
			echo '</div>';
			
			echo '<div style="align-self: normal;">
				<h2>' . lang::H2_HISTORY_WORK . '</h2>';
				if ($count2 > 0) {
					echo '<table class="stripy table-autosort">	
						<thead>
							<tr>
								<th>' . lang::DATE .'</th>
								<th class="table-sortable:*">' . lang::HDR_COST . '</th>
							</tr>
						<thead>';
						$count2 = 0;	
						while ($expences[$count2] != NULL) {
							echo '<tr> 
								<td class="center"><a href="/expences/expencesList.php/?month='. date("Y-m", strtotime($expences[$count2]["date"])) .'&categoryID=' . $_GET["service"] .'">' . correctDate($expences[$count2]['date']) . '</a></td>
								<td class="center">' . correctNumber($expences[$count2]['spent']) . '</td>
							</tr>';
							$count2++;
						}
						echo '<tfoot>
							<tr>
								<th>'. lang::HDR_TOTAL .'</th>
								<th>' . correctNumber($total_expences) . '</th>
							</tr>
						</tfoot>
					</table>';
					
				} else echo lang::ERR_NO_INFO;
			echo '</div>';
			
		echo "</div>";
	}?>
</section>	


<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>