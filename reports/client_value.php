<?php 
$access = 90;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');


	$sql = "SELECT 
			clientID, clients.name, clients.surname, clients.prompt
			, SUM(case when DATE_FORMAT(visits.date, '%Y') = YEAR(CURDATE()) then visits.price_total else 0 end) as y_cur
            , COUNT(CASE WHEN DATE_FORMAT(visits.date, '%Y') = YEAR(CURDATE()) then 1 ELSE NULL END) as y_cur_v
            
            , SUM(case when DATE_FORMAT(visits.date, '%Y') = YEAR(CURDATE())-1 then visits.price_total else 0 end) as y_prev
            , COUNT(CASE WHEN DATE_FORMAT(visits.date, '%Y') = YEAR(CURDATE())-1 then 1 ELSE NULL END) as y_prev_v
                     
            , SUM(case when DATE_FORMAT(visits.date, '%Y') = YEAR(CURDATE())-2 then visits.price_total else 0 end) as y_pprev
			, COUNT(CASE WHEN DATE_FORMAT(visits.date, '%Y') = YEAR(CURDATE())-2 then 1 ELSE NULL END) as y_pprev_v
           
		FROM `visits` 
		RIGHT JOIN clients ON visits.clientID = clients.id
		WHERE visits.locationID = :locationID
			AND visits.state = 10
		GROUP BY clientID
		ORDER BY y_cur DESC";
	$stmt = $pdo->prepare($sql);
	$stmt -> bindValue(":locationID", $_SESSION['locationSelected'], PDO::PARAM_INT);
	$stmt->execute();
	$data = $stmt->fetchAll(PDO::FETCH_ASSOC);





$title = lang::MENU_CLIENT_VALUE;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, 'cl_val');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc($title);?>
	
	
		<table class="stripy table-autosort">
			<thead>
				<tr>
					<th style="width: max-content;">№</th>
					<th><?=lang::TBL_CLIENT;?></th>
					<th class="table-sortable:numeric"><?=date('Y');?></th>
					<th class="table-sortable:numeric"><?=(date('Y')-1);?></th>
					<th class="table-sortable:numeric"><?=(date('Y')-2);?></th>
					<th class="table-sortable:numeric"><?=lang::HDR_TOTAL;?></th>
					<th class="table-sortable:numeric"><?=lang::HDR_HANDLING;?></th>
				</tr>
			</thead>
			<tbody>	
				<?php $count=0;
				$total_curr = $total_y_prev = $total_y_pprev = 0;
				$total_curr_v = $total_y_prev_v = $total_y_pprev_v = 0;
				while($data[$count] != null) {
					if($data[$count]['archive'] != 1) {
						echo '<tr>
							<td class="center small">' . ($count+1) . '</td>
							<td><a href="/clients/client_profile.php?id='.$data[$count]['clientID'].'" title="'.lang::HDR_CLIENT_PROFILE.'">' . FIO($data[$count]['name'],$data[$count]['surname'],$data[$count]['prompt'])	. '</a></td>
							<td class="center tooltip" data-tooltip="'.lang::MENU_VISITS . ': ' .$data[$count]['y_cur_v'].'">' . 
								correctNumber($data[$count]['y_cur']) . curr() . '
							</td>
							<td class="center tooltip" data-tooltip="'.lang::MENU_VISITS . ': ' .$data[$count]['y_prev_v'].'">' . 
								correctNumber($data[$count]['y_prev']) . curr() .  '
							</td>
							<td class="center tooltip" data-tooltip="'.lang::MENU_VISITS . ': ' .$data[$count]['y_pprev_v'].'">' . 
								correctNumber($data[$count]['y_pprev']) . curr() .  '
							</td>
							<td class="center bold">' . correctNumber($data[$count]['y_cur'] + $data[$count]['y_prev'] + $data[$count]['y_pprev']) .  curr() . '</td>
							<td class="center"><a title="'. lang::HANDLING_ARCHIVE .'" href="/config/archive.php?id=' . $data[$count]['clientID'].'&table=clients&archive=1&URL='.$_SERVER['PHP_SELF'].'" onclick=\'return confirm("'. lang::ALERT_BLOCK_DEFAULT . '\"'. FIO($data[$count]['name'],$data[$count]['surname'],$data[$count]['prompt']) . '\"?");\'><i class="fas fa-trash"></i></a></td>
						</tr>';
					}
					$total_curr += $data[$count]['y_cur'];
					$total_y_prev += $data[$count]['y_prev'];
					$total_y_pprev += $data[$count]['y_pprev'];
					$total_curr_v += $data[$count]['y_cur_v'];
					$total_y_prev_v += $data[$count]['y_prev_v'];
					$total_y_pprev_v += $data[$count]['y_pprev_v'];
					$count++;
				}
				
				
				
				
				?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="2"><?=lang::HDR_TOTAL;?>:</th>
					<th class="center tooltip" data-tooltip="<?=lang::MENU_VISITS . ': ' . $total_curr_v;?>"><?=correctNumber($total_curr) . curr();?></th>
					<th class="center tooltip" data-tooltip="<?=lang::MENU_VISITS . ': ' . $total_y_prev_v;?>"><?=correctNumber($total_y_prev) . curr();?></th>
					<th class="center tooltip" data-tooltip="<?=lang::MENU_VISITS . ': ' . $total_y_pprev_v;?>"><?=correctNumber($total_y_pprev) . curr();?></th>
					<th></td>
					<th></th>
				</tr>
			</tfoot>
		</table>
		
			
		
</section>	
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>