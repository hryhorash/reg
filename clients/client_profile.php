<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

// На входе обязателен ID клиента (id= number  при чем и GET и POST)
if($_REQUEST['id']  > 0) $id = $_REQUEST['id'];
if(isset($_GET['offset'])) $offset = (int)$_GET['offset'];
else $offset = 0;
$limit = 6;


if ($id > 0 ){
	$sql_info = "SELECT clients.id, clients.name, clients.surname, clients.prompt, phones, email, DOB,refClientID, note, sources.name as sourceName, photo,
					MAX( visits.date ) AS last_visit, 
					MIN( visits.date ) AS first_visit, 
					COUNT(DISTINCT visits.date ) AS total_visits, 
					SUM(visits.price_total) as price_total,
                    SUM(visits_staff.tips) AS total_tips,
					(SUM(case when DATE_FORMAT(visits.date, '%Y') = YEAR(CURDATE()) then visits.price_total else 0 end)) AS this_year_revenue,
					 (SUM(case when DATE_FORMAT(visits.date, '%Y') = YEAR(CURDATE()) then visits_staff.tips else 0 end)) AS this_year_tips,
                     ref.name as rname, ref.surname as rsurname, ref.prompt as rprompt
				FROM clients 
				LEFT JOIN visits ON visits.clientID = clients.id
				LEFT JOIN visits_staff ON visits.id = visits_staff.visitID
				LEFT JOIN sources ON clients.sourceID = sources.id
                LEFT JOIN ( 
                	select id, name, surname, prompt
                    from clients
				) ref ON clients.refClientID = ref.id
				WHERE clients.id = :id
					AND visits.state = 10";
	
	
	$sql_visits = "SELECT visits.id, visits.date, visits.state, visits.price_total, visits.netto, visits.comment
					,locations.name as location
					,GROUP_CONCAT(worktypes.name SEPARATOR '<br />') as workNames
					,GROUP_CONCAT(worktypes.catID) as catIDs
					,GROUP_CONCAT(DISTINCT CONCAT(users.name, ' ', users.surname)  SEPARATOR '<br />') as staffNames
				FROM visits
				LEFT JOIN locations ON visits.locationID = locations.id
				LEFT JOIN visits_works ON visits.id = visits_works.visitID
				LEFT JOIN worktypes ON visits_works.workID = worktypes.id
				LEFT JOIN users ON visits_works.userID = users.id
				WHERE visits.clientID = :id
				GROUP BY visits.id
				ORDER BY visits.date DESC
				LIMIT $offset, $limit";
				
	$sql_sales = "SELECT received.cosmID, received.dateOut, received.priceOut, received.priceIn 
					,CONCAT(brands.name, ' ', cosmetics.name, ', ', cosmetics.volume) as cosm_name
					,SUM(qtyOut) as qty
					,SUM(qtyOut) * AVG(received.priceOut) as price_total
					,SUM(qtyOut) * AVG(received.priceIn) as netto_total
				FROM received
				LEFT JOIN cosmetics ON received.cosmID = cosmetics.id
				LEFT JOIN brands ON cosmetics.brandID = brands.id
				WHERE received.soldToID = :id
				GROUP BY dateOut, cosmID
				ORDER BY dateOut DESC";
	try 
	{
		//ИНФО о клиенте
		$info = $pdo->prepare($sql_info);
		$info -> bindValue(':id', $id, PDO::PARAM_INT);
		$info ->execute();
		$client_info = $info->fetch(PDO::FETCH_ASSOC);
		
		//инфо о визитах
		$v = $pdo->prepare($sql_visits);
		$v->bindParam(':id', $id, PDO::PARAM_INT);
		$v->setFetchMode(PDO::FETCH_CLASS, 'Visit');
		$v->execute(); 
		
		$count = 1;
		$future_count = 1;
		while($visit_data = $v->fetch(PDO::FETCH_ASSOC)) {
			if(strtotime($visit_data['date']) >= strtotime(date('Y-m-d'))) {
				$future_visits[$future_count] = $visit_data;
				$future_count++;
			} else {
				$archive_visits[$count] = $visit_data;
				$count++;
			}
		}
		
		//инфо о продажах
		$s = $pdo->prepare($sql_sales);
		$s->bindParam(':id', $id, PDO::PARAM_INT);
		$s->setFetchMode(PDO::FETCH_CLASS, 'Visit');
		$s->execute(); 
		$n=1;	
		while($sales_data[$n] = $s->fetch(PDO::FETCH_ASSOC)) $n++;	
		
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}$stmt = $pdo->prepare($sql);
	$pdo=NULL;
}



$title=FIO($client_info['name'],$client_info['surname'],$client_info['prompt']);
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 

echo '<section class="sidebar">';
	include('filters.php');
	echo tabs($tabs);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'.$title. '<a href="/clients/client_edit.php?id='.$client_info['id'].'&goto=profile"><i class="fas fa-edit"></i></a>' .'</h2>';

	if ($id > 0) {
			echo '<h2 id="info" class="pointer">'.lang::H2_INFO_AND_STATS.'<i class="fas fa-chevron-down"></i></h2>';				
			
			echo '<div class="photo">';
				if(isset($client_info['photo']) && $client_info['photo'] !='') {
					echo '<img src="'.$client_info['photo'].'" style="max-width: -webkit-fill-available;   border-radius: 3px;" />';
					
				} 
				echo '<form method="post" action="upload.php" enctype="multipart/form-data">
						<input name="photo" type="file" style="line-height:1.2em; border:none;margin-left: -5px;" required />';
						if(isset($client_info['photo']) && $client_info['photo'] !='')
							 echo '<input type="submit" value="'.lang::BTN_UPDATE_PHOTO.'" />';
						else echo '<input type="submit" value="'.lang::BTN_UPLOAD_PHOTO.'" />';
						echo '<input type="hidden" name="id" value="'.$id.'" />';
				echo '</form>';
			echo '</div>';
			
			
			echo '<table class="stripy" style="float:left; margin-right:10px;display:none;">';
				if (isset($client_info['DOB'])) echo '<tr><td><p>'.lang::HDR_DOB.':<p></td><td>' . correctDate($client_info['DOB']) . '</td></tr>';
				if (isset($client_info['phones'])) 	echo '<tr><td><p>'.lang::HDR_PHONES.':<p></td><td>'; phones($client_info['phones']); echo '</td></tr>';
				if ($client_info['email'] !='') echo '<tr><td><p>'.lang::HDR_EMAIL.':</p></td><td><a href="mailto:' . htmlentities($client_info['email']) . '">' . htmlentities($client_info['email']) . '</a></td></tr>';
				if ($client_info['refClientID'] > 0) 	echo '<tr><td><p>'.lang::HDR_RECOMMENDATION.':<p></td><td>' . FIO($client_info['rname'],$client_info['rsurname'],$client_info['rprompt']) . '</td></tr>';
				else echo '<tr><td><p>'.lang::HDR_CLIENT_SOURCE.':<p></td><td>' . $client_info['sourceName'] . '</td></tr>';
				
				if ($client_info['total_visits'] > 0) {
					echo '<tr><td><p>'.lang::HDR_FIRST_VISIT.':<p></td><td>' . correctDate($client_info['first_visit']) . '</td></tr>';
					echo '<tr><td><p>'.lang::HDR_LAST_VISIT.':<p></td><td>' . correctDate($client_info['last_visit']) . '</td></tr>';
					if ($client_info['total_visits'] > 1) {echo '<tr><td><p>'.lang::HDR_FREQUENCY.':<p></td><td>'. visit_interval($client_info['first_visit'], $client_info['last_visit'], $client_info['total_visits']) . '</td></tr>';}
					echo '<tr><td><p>'.lang::HDR_TOTAL_VISITS.':<p></td>';
					echo '<td>' . $client_info['total_visits'] . '</td></tr>';
				}
				echo '<tr><td><p>'.lang::HDR_CLIENT_WORTH.'<br />- '.lang::HDR_WORTH_CURRENT_YEAR.':<br />- '.lang::HDR_WORTH_TOTAL.':<p></td><td><br />' . correctNumber($client_info['this_year_revenue'], 0) . ' <br /> ' . correctNumber($client_info['price_total'], 0) . '</td></tr>';
				if ($client_info['note'] != '') echo '<tr><td><p>'.lang::HDR_COMMENT.':</p></td><td>' . nl2br(htmlentities($client_info['note'])) . '</a></td></tr>';
				
			echo '</table>';
			
		
		
		//ВИЗИТЫ
		$x = 1;
		
		/*  будущие */
		echo '<h2 id="future" class="pointer">'.lang::MENU_VISITS.' ('.lang::TAB_PLANS.')<i class="fas fa-chevron-down"></i></h2>
		<div id="future_visits" class="card-container" style="display:none;">';
			if($future_count > 1) {
					$i = 1;
					while($future_visits[$i] != NULL) {
						visit_card($future_visits[$i], $i);
						$i++;
						$x++;
					}
				
			}else {
				echo '<p>' . lang::ERR_NO_INFO . '</p>';
			}
		
		echo '</div>';
		/* прошедшие */
		echo '<h2>'.lang::MENU_VISITS.' ('.lang::TAB_ARCHIVE.')</h2>';		
		if($count > 1) {
			echo '<div class="card-container">';
				$i = 1;
				while($archive_visits[$i] != NULL) {
					visit_card($archive_visits[$i], $i);
					$i++;
					$x++;
				}
			echo '</div>';
			list_navigation_buttons($x,$offset,$limit, $id);
			
		}else {
			echo '<p>' . lang::ERR_NO_INFO . '</p>';
		}
			
			
			
		//ПРОДАЖИ
		
		if($n > 1) {
			echo '<h2>'.lang::HDR_SALES_LIST.'</h2>';	?>	
			
			<table class="stripy">
			<thead>
				<tr>
					<th>№			</th>
					<th><?=lang::DATE;?></th>
					<th style="width:35%;"><?=lang::HDR_ITEM_NAME;?></th>
					<th><?=lang::HDR_COST . ', ' . curr();?></th>
					<th><?=lang::HDR_PROFIT . ', ' . curr();?></th>
				</tr>
			</thead>
			<tbody>	
				<?php $n=1;
				while($sales_data[$n] != NULL){
					echo '<tr>
						<td class="small center">'. ($n) .'</td>
						<td class="center">' . correctDate($sales_data[$n]['dateOut']) . '</td>
						<td>' . $sales_data[$n]['cosm_name'] . '</td>
						<td class="center">';
							if($sales_data[$n]['qty'] > 1) {
								echo $sales_data[$n]['priceOut'] . ' x ' . $sales_data[$n]['qty'] .' = ';
								
							}
							echo '<strong>' . correctNumber($sales_data[$n]['price_total'],2)	. '</strong>';
						echo '</td>
						<td class="center">';
							echo '<strong>' . correctNumber(($sales_data[$n]['price_total'] - $sales_data[$n]['netto_total']),2)	. '</strong>';
						echo '</td>
					</tr>';
					
					$n++;
				}?>
			</tbody>	
			</table>
			
			
		<?php	
		}
		
		
	} else {
		echo '<p>' . lang::ERR_NO_ID . '</p>';
	}
	?>
</section>
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>

<script>
$("#info").on('click', function() {
	$(this).next().toggle();
	$(this).next().next().toggle();
});	

$("#future").on('click', function() {
	$(this).next().toggle();
});	
	
function fetch_spent(visitID, counter) {
	var check_empty = $("#f"+counter).html().length;
	
	if(check_empty == 0) {
	
		var xhttp = new XMLHttpRequest();
			$.ajax({
			type: "POST",
			url: "fetch_spent_ajax.php",
			data:	{ 'id': visitID },  
			success: function(data){
				document.getElementById("f"+counter).innerHTML = data;
				$("#r"+counter).show();
				
				
			}
		});
	} else $("#r"+counter).toggle();
}
</script>