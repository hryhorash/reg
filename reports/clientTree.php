<?php 
$access = 90;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');


$stmt = $pdo->query("SELECT clients.id as id, clients.refClientID as parentId, CONCAT(IF(LENGTH(clients.surname), CONCAT(clients.name,' ',clients.surname), clients.name), IF(LENGTH(clients.prompt), CONCAT(' (',clients.prompt,')'), '')) AS name, sources.name as clientRef, SUM(visits.price_total) as cost
					FROM clients 
					LEFT JOIN visits ON visits.clientID = clients.id
                    LEFT JOIN sources ON clients.sourceID = sources.id
					GROUP BY clients.id
					ORDER BY parentId"	);
$array = $stmt->fetchAll(PDO::FETCH_ASSOC);


function buildTree(array &$elements, $parentId = 0) {
    $branch = array();

    foreach ($elements as $element) {
        if ($element['parentId'] == $parentId) {
            $children = buildTree($elements, $element['id']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[$element['id']] = $element;
        }
    }
    return $branch;
}

$x = buildTree($array);


$title = lang::H2_CLIENT_TREE;
//---------------------VIEW--------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, 'tree');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc($title);?>
	
	
		<table class="stripy table-autosort">
			<thead>
				<tr>
					<th class="mobile-hide center" style="width:max-content;">№</th>
					<th class="table-sortable:*"><?=lang::HDR_CLIENT_SOURCE;?></th>
					<th class="table-sortable:*"><?=lang::HDR_CLIENT_WORTH;?></th>
					<th><?=lang::HDR_LEVEL2;?></th>
					<th><?=lang::HDR_LEVEL3;?></th>
					<th class="table-sortable:numeric"><?=lang::HDR_REVENUE_TOTAL;?></th>
				</tr>
			</thead>

			<?php 
			$index3 = $index4 = $index5 = $index6 = [];
			$n = 1;
			foreach($x as $firstLevel) {
				$TotalClientCost =0;
				echo '<tr>
					<td class="mobile-hide small center">' . $n . '</td>
					<td>';
						if ($firstLevel['clientRef'] != '') echo htmlentities($firstLevel['clientRef']);
						else echo '<a href="/clients/client_edit.php?id=' . $firstLevel['id'] . '&goto=clientTree"><i class="fas fa-edit"></i></a>';
				echo '</td>
					<td><a href="/clients/client_profile.php?id=' . $firstLevel['id'] . '">' . htmlentities($firstLevel['name']) . '</a> - ' . number_format($firstLevel['cost'],0,"."," ") . '</td>
					<td>';
				$TotalClientCost = $TotalClientCost + $firstLevel['cost'];
				
				if (isset($firstLevel['children'])) {
					foreach ($firstLevel['children'] as $SecondLevel) {
						echo '<a href="/clients/client_profile.php?id=' . $SecondLevel['id'] . '">' . htmlentities($SecondLevel['name']) . '</a> - ' . number_format($SecondLevel['cost'],0,"."," ") . '<br / >'; 
						$TotalClientCost = $TotalClientCost + $SecondLevel['cost'];
						$index3[] = $SecondLevel['id'];
					}
				}
				
				echo '</td>';
				echo '<td>'; 
					if (isset($index3)) {
						foreach ($index3 as $i3) {
							$ch3 = buildTree($array, $i3);
							foreach ($ch3 as $ThirdLevel) {
								echo '<a href="/clients/client_profile.php?id=' . $ThirdLevel['id'] . '">' . htmlentities($ThirdLevel['name']) . '</a> - ' . number_format($ThirdLevel['cost'],0,"."," ") . '<br / >'; 
								$TotalClientCost = $TotalClientCost + $ThirdLevel['cost'];
								$index4[] = $ThirdLevel['id'];
							}
							
						}
						if ($index4 !=NULL) {
							foreach ($index4 as $i4) {
								$ch4 = buildTree($array, $i4);
								if ($ch4 != NULL) {
									echo '<details>
										<summary>'.lang::HDR_MORE_LVL.'</summary>';
		
										foreach ($ch4 as $FourthLevel) {
											echo '<a href="/clients/client_profile.php?id=' . $FourthLevel['id'] . '">' . htmlentities($FourthLevel['name']) . '</a> - ' . number_format($FourthLevel['cost'],0,"."," ") . '<br / >'; 
											$TotalClientCost = $TotalClientCost + $FourthLevel['cost'];
											$index5[] = $FourthLevel['id']; 
										} 
								 }
							}
							if ($index5 !=NULL) {
								foreach ($index5 as $i5) {
									$ch5 = buildTree($array, $i5);
									if ($ch5 != NULL) {
										foreach ($ch5 as $FifthLevel) {
											echo '<a href="/clients/client_profile.php?id=' . $FifthLevel['id'] . '">' . htmlentities($FifthLevel['name']) . '</a> - ' . number_format($FifthLevel['cost'],0,"."," ") . '<br / >'; 
											$TotalClientCost = $TotalClientCost + $FifthLevel['cost'];
											$index6[] = $FifthLevel['id'];
										}
									}
								}
								if ($index6 !=NULL) {
									foreach ($index6 as $i6) {
										$ch6 = buildTree($array, $i6);
										if ($ch6 != NULL) {
											foreach ($ch6 as $SixthLevel) {
												echo '<a href="/clients/client_profile.php?id=' . $SixthLevel['id'] . '">' . htmlentities($SixthLevel['name']) . '</a> - ' . number_format($SixthLevel['cost'],0,"."," ") . '<br / >'; 
												$TotalClientCost = $TotalClientCost + $SixthLevel['cost'];
												//$index6[] = $FourthLevel['id'];
											}
										}
									}
								}
							}
							echo '</details>';
						}
					}
					$index3 = $index4 = $index5 = $index6 = [];
				echo '</td>';//
				echo '<td class="center">' . correctNumber($TotalClientCost,0) . '</td>';
				echo '</tr>';
				$n++;
				
			} ?>
			</table>
			

			
		
			
		
</section>	
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>