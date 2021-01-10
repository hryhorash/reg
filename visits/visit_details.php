<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include($_SERVER['DOCUMENT_ROOT'].'/clients/tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	
	$_SESSION["locationSelected"] = $_POST['loc'];
	
	if($_POST['del_visit'] === 'true') {
		$del_v = $pdo -> prepare("DELETE FROM `visits` WHERE id = :visitID");
		$del_v -> bindValue(':visitID', $_POST['visitID'], PDO::PARAM_INT);
		if($del_v -> execute() == true) {
			$del_w = $pdo -> prepare("DELETE FROM `visits_works` WHERE visitID = :visitID");
			$del_w -> bindValue(':visitID', $_POST['visitID'], PDO::PARAM_INT);
			$del_w -> execute();
			
			$del_s = $pdo -> prepare("DELETE FROM `visits_staff` WHERE visitID = :visitID");
			$del_s -> bindValue(':visitID', $_POST['visitID'], PDO::PARAM_INT);
			$del_s -> execute();
			
			$del_с = $pdo -> prepare("DELETE FROM `spent` WHERE visitID = :visitID");
			$del_с -> bindValue(':visitID', $_POST['visitID'], PDO::PARAM_INT);
			$del_с -> execute();
		}
		
	} else {
	
		$_SESSION['temp'] = array(
			'date' 				=> $_POST['date'],
			'startTime' 		=> $_POST['startTime'],
			'endTime' 			=> $_POST['endTime'],
			'customers' 		=> $_POST['customers'],
			'clientID' 			=> $_POST['clientID'],
			'state' 			=> $_POST['state'],
			'price_total' 		=> $_POST['price_total'],
			'comment' 			=> $_POST['comment'],
			'visitID' 			=> $_POST['visitID'],
			
			'visits_worksIDs'	=> $_POST['visits_worksIDs'],
			'serviceNames' 		=> $_POST['workNames'],
			'serviceIDs' 		=> $_POST['workID'],
			'serviceMinPrices'	=> $_POST['minPrice'],
			'serviceMaxPrices'	=> $_POST['maxPrice'],
			'work_prices'		=> $_POST['price'],
			'work_prices_old'	=> $_POST['price_old'],
			'catIDs'			=> $_POST['catID'],
			
			'staffRowIDs'		=> $_POST['staffRowIDs'],
			'staffNames'		=> $_POST['staffName'],
			'staffIDs' 			=> $_POST['staffID'],
			'staffPrices' 		=> $_POST['staffPrices'],
			'staffWages' 		=> $_POST['staffWages'],
			'staffTips' 		=> $_POST['staffTips'],
			'staffComments' 	=> $_POST['staffComments'],
			'totalSpentV'		=> $_POST['totalSpentV'],
			'totalSpentC'		=> $_POST['totalSpentC'],
			'totalNetto'		=> $_POST['netto_total']
		);
		$c=0;
		while ($_POST['nettoName'][$c] != null) {
			$_SESSION['temp']['netto'][$c] = array(
				'nettoName' 		=> $_POST['nettoNames'][$c],
				'nettoCost' 		=> $_POST['nettoCost'][$c]
			);
			$c++;
		}
		
		$c=0;
		while ($_POST['cosmID'][$c] != null) {
			$_SESSION['temp']['spent'][$c] = array(
				'cosmNames' 		=> $_POST['cosmNames'][$c],
				'spentV' 			=> $_POST['spentV'][$c],
				'spentC' 			=> $_POST['spentC'][$c],
				'cosmID' 			=> $_POST['cosmID'][$c],
				'spentID' 			=> $_POST['spentID'][$c],
				'spentV_old' 		=> $_POST['spentV_old'][$c],
				'spentC_old' 		=> $_POST['spentC_old'][$c],
				'balanceGr' 		=> $_POST['balanceGr'][$c],
				'cosmV' 			=> $_POST['cosmV'][$c],
				'pcsOut' 			=> $_POST['pcsOut'][$c]
			);
			$c++;
		}
		$c=0;
		while ($_POST['sold_cosmID'][$c] != null) {
			$_SESSION['temp']['sales'][$c] = array(
				'soldName' 			=> $_POST['soldName'][$c],
				'sold_cosmID' 		=> $_POST['sold_cosmID'][$c],
				'qty' 				=> $_POST['qty'][$c],
				'priceSold' 		=> $_POST['sold_price_total'][$c],
				'soldRowIDs' 		=> $_POST['soldRowIDs'][$c],
				'priceSoldOld'		=> $_POST['priceSoldOld'][$c],
				'qtyOld'			=> $_POST['qtyOld'][$c],
				'sell_netto'		=> $_POST['sell_netto'][$c],
				'sell_available'	=> $_POST['sell_available'][$c]
			);
			$c++;
		}
		
		
		function redirect_recover() {
			if($_POST['visitID'] > 0) // признак нового заказа
					header( 'Location: ' . $_SERVER['PHP_SELF'].'?id='.$_POST['visitID'].'&recover');
			else 	header( 'Location: ' . $_SERVER['PHP_SELF'].'?new&recover');
		}
		
		if($_POST['clientID'] == '') {
			$_SESSION['error'] = lang::ERR_NO_CLIENT;
				session_write_close();
				redirect_recover();
				exit;
		}
		
		if ($_POST['state'] == 10) {
			foreach ($_POST['workID'] as $work) {
				if ($work > 0) $workOK = 1;
			}
			if($workOK != 1) {
				$_SESSION['error'] = lang::ERR_SELECT_SPECIALTY;
					session_write_close();
					redirect_recover();
					exit;
			} 

			if(isset($_POST['staffID'])) {
				foreach ($_POST['staffID'] as $staff) {
					if ($staff > 0) $staffOK = 1;
				}
			} else $staffOK = 0;
			if($staffOK != 1) {
				$_SESSION['error'] = lang::ERR_NO_STAFF;
					session_write_close();
					redirect_recover();
					exit;
			} 
		}
		
		
		// Данные о визите
		if($_POST['visitID'] > 0) {
			try {
				$visitChange = $pdo->prepare("
							UPDATE `visits` 
							SET locationID		= :locationID,
								date			= :date,
								startTime		= :startTime,
								endTime			= :endTime,
								clientID		= :clientID,
								state			= :state,
								price_total		= :price_total,
								netto			= :netto,
								comment			= :comment,
								`timestamp`		= :timestamp, 
								author			= :author
							WHERE id = :id");
				$visitChange -> bindValue(':locationID',$_POST["loc"], PDO::PARAM_INT);
				$visitChange -> bindValue(':date', 		$_POST["date"], PDO::PARAM_STR);
				$visitChange -> bindValue(':startTime', $_POST["startTime"], PDO::PARAM_STR);
				$visitChange -> bindValue(':endTime', 	$_POST["endTime"], PDO::PARAM_STR);
				$visitChange -> bindValue(':clientID', 	$_POST["clientID"], PDO::PARAM_INT);
				$visitChange -> bindValue(':state', 	$_POST["state"], PDO::PARAM_INT);
				$visitChange -> bindValue(':price_total', $_POST["price_total"], PDO::PARAM_STR);
				$visitChange -> bindValue(':netto',		$_POST["netto_total"], PDO::PARAM_STR);
				$visitChange -> bindValue(':comment', 	$_POST["comment"], PDO::PARAM_STR);
				$visitChange -> bindValue(':timestamp', null, PDO::PARAM_INT);
				$visitChange -> bindValue(':author', 	$_SESSION['userID'], PDO::PARAM_INT);
				$visitChange -> bindValue(':id', 		$_POST["visitID"], PDO::PARAM_INT);
				$visitChange -> execute();
			} catch (PDOException $ex){
				include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
				$_SESSION['error'] = $ex;
				session_write_close();
				header( 'Location: ' . $_SERVER['PHP_SELF'].'id='.$_POST["visitID"]);
				exit;
			}
		} else { //новый визит
			try {
				$visitNew = $pdo->prepare("
							INSERT INTO visits (locationID, date, startTime, endTime, clientID, state, price_total, netto, comment,  author) 
							VALUES(:locationID, :date, :startTime, :endTime, :clientID, :state, :price_total, :netto, :comment, :author)");
				$visitNew -> bindValue(':locationID', 	$_POST["loc"], PDO::PARAM_INT);
				$visitNew -> bindValue(':date', 		$_POST["date"], PDO::PARAM_STR);
				$visitNew -> bindValue(':startTime', 	$_POST["startTime"], PDO::PARAM_STR);
				$visitNew -> bindValue(':endTime', 		$_POST["endTime"], PDO::PARAM_STR);
				$visitNew -> bindValue(':clientID', 	$_POST["clientID"], PDO::PARAM_INT);
				$visitNew -> bindValue(':state', 		$_POST["state"], PDO::PARAM_INT);
				if($_POST["price_total"] !='')
					$visitNew -> bindValue(':price_total',	$_POST["price_total"], PDO::PARAM_STR);
				else $visitNew -> bindValue(':price_total',	0, PDO::PARAM_INT);
				$visitNew -> bindValue(':netto',		$_POST["netto_total"], PDO::PARAM_STR);
				$visitNew -> bindValue(':comment', 		$_POST["comment"], PDO::PARAM_STR);
				$visitNew -> bindValue(':author', 		$_SESSION['userID'], PDO::PARAM_INT);
				$visitNew -> execute();
				$visitID = $pdo->lastInsertId();
				$_SESSION['temp']['visitID'] = $visitID;
				
			} catch (PDOException $ex){
				include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
				$_SESSION['error'] = $ex;
				session_write_close();
				header( 'Location: ' . $_SERVER['PHP_SELF'].'?new&recover');
				exit;
			}
		}
		
		//список работ
		function workAdd($workID, $minPrice, $maxPrice, $userID, $price, $reward_rate) {
			require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
			$workAdd = $pdo->prepare("
						INSERT INTO visits_works (visitID, workID, minPrice, maxPrice, userID, price, reward_rate,  author) 
						VALUES(:visitID, :workID, :minPrice, :maxPrice, :userID, :price, :reward_rate, :author)
					");
			$workAdd -> bindValue(':visitID', $_SESSION['temp']['visitID'], PDO::PARAM_INT);
			$workAdd -> bindValue(':workID', $workID, PDO::PARAM_INT);
			$workAdd -> bindValue(':minPrice', $minPrice, PDO::PARAM_STR);
			$workAdd -> bindValue(':maxPrice', $maxPrice, PDO::PARAM_STR);
			$workAdd -> bindValue(':userID', $userID, PDO::PARAM_INT);
			$workAdd -> bindValue(':price', $price, PDO::PARAM_STR);
			$workAdd -> bindValue(':reward_rate', $reward_rate, PDO::PARAM_INT);
			$workAdd -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
			$workAdd -> execute();
		}
		
		function workUpdate($id, $price) {
			require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
			$workUpdate = $pdo->prepare("
						UPDATE visits_works 
						SET price 		= :price, 
							`timestamp`	= null,
							author		= :author
						WHERE id = :id
					");
			$workUpdate -> bindValue(':id', $id, PDO::PARAM_INT);
			$workUpdate -> bindValue(':price', $price, PDO::PARAM_STR);
			$workUpdate -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
			$workUpdate -> execute();
		}
		
		function workDelete($id) {
			require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
			$workDelete = $pdo->prepare("
						DELETE FROM visits_works WHERE id = :id
					");
			$workDelete -> bindValue(':id', $id, PDO::PARAM_INT);
			$workDelete -> execute();
		}
		$i = 0;
		
		if(is_array($_POST['workID']) == true) {
			foreach($_POST['workID'] as $workID) {
				switch(true)
				{
					case($workID == 0 && $_POST['visits_worksIDs'][$i] > 0):
						workDelete($_POST['visits_worksIDs'][$i]);
						break;
					case($workID > 0 && $_POST['visits_worksIDs'][$i] == ''):
						if($_POST['price'][$i] > 0) $price = $_POST['price'][$i];
						else $price = 0;
						workAdd($workID, $_POST['minPrice'][$i], $_POST['maxPrice'][$i], $_POST['staff'][$i], $price, $_POST['rate'][$i]);
						break;
					case($workID > 0 && $_POST['visits_worksIDs'][$i] > 0 && $_POST['price'][$i] != $_POST['price_old'][$i]):
						if($_POST['price'][$i] > 0) $price = $_POST['price'][$i];
						else $price = 0;
						workUpdate($_POST['visits_worksIDs'][$i], $price);
						break;
					default:
						break;
				}
				$i++;
			}
		}
		
		//список сотрудников
		function staffAdd($userID, $price, $wage, $tips, $comment) {
			if($tips == '') $tips = 0;
			require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
			$staffAdd = $pdo->prepare("
						INSERT INTO visits_staff (visitID, userID, price, wage, tips, comment, author) 
						VALUES(:visitID, :userID, :price, :wage, :tips, :comment, :author)
					");
			$staffAdd -> bindValue(':visitID', $_SESSION['temp']['visitID'], PDO::PARAM_INT);
			$staffAdd -> bindValue(':userID', $userID, PDO::PARAM_INT);
			$staffAdd -> bindValue(':price', $price, PDO::PARAM_STR);
			$staffAdd -> bindValue(':wage', $wage, PDO::PARAM_STR);
			
			$staffAdd -> bindValue(':tips', $tips, PDO::PARAM_STR);
			$staffAdd -> bindValue(':comment', $comment, PDO::PARAM_STR);
			$staffAdd -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
			$staffAdd -> execute();
		}
		
		function staffDelete($id) {
			require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
			$staffDelete = $pdo->prepare("
						DELETE FROM visits_staff WHERE id = :id
					");
			$staffDelete -> bindValue(':id', $id, PDO::PARAM_INT);
			$staffDelete -> execute();
		}
		
		function staffUpdate($id, $price, $wage, $tips, $comment) {
			if($tips == '') $tips = 0;
			require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
			$staffUpd = $pdo->prepare("
						UPDATE visits_staff 
							SET price		= :price, 
								wage		= :wage, 
								tips		= :tips, 
								comment		= :comment, 
								timestamp	= :timestamp, 
								author		= :author
							WHERE id = :id
					");
			$staffUpd -> bindValue(':price', $price, PDO::PARAM_STR);
			$staffUpd -> bindValue(':wage', $wage, PDO::PARAM_STR);
			
			$staffUpd -> bindValue(':tips', $tips, PDO::PARAM_STR);
			$staffUpd -> bindValue(':timestamp', null, PDO::PARAM_STR);
			$staffUpd -> bindValue(':comment', $comment, PDO::PARAM_STR);
			$staffUpd -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
			$staffUpd -> bindValue(':id', $id, PDO::PARAM_INT);
			$staffUpd -> execute();
		}
		
		if(is_array($_POST['staffID']) == true) {
		
			$i = 0;
			foreach($_POST['staffID'] as $staffID) {
				switch(true)
				{
					case($staffID == 0 && $_POST['staffRowIDs'][$i] > 0):
						staffDelete($_POST['staffRowIDs'][$i]);
						break;
					case($staffID > 0 && $_POST['staffRowIDs'][$i] == ''):
						staffAdd($staffID, $_POST['staffPrices'][$i], $_POST['staff_wage'][$i], $_POST['staffTips'][$i], $_POST['staffComments'][$i]);
						break;
						
					case($staffID > 0 && $_POST['staffRowIDs'][$i] > 0):
						staffUpdate($_POST['staffRowIDs'][$i], $_POST['staffPrices'][$i], $_POST['staff_wage'][$i], $_POST['staffTips'][$i], $_POST['staffComments'][$i]);
						break;
					default:
						break;
				}
				$i++;
			}
		}
		
		// внесение косметики
		function spentAdd($cosmID, $spentV, $cost) {
			require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
			
			
			$spentAdd = $pdo->prepare("
						INSERT INTO spent (visitID, cosmID, volume, cost, author) 
						VALUES(:visitID, :cosmID, :volume, :cost, :author)
					");
			$spentAdd -> bindValue(':visitID', $_SESSION['temp']['visitID'], PDO::PARAM_INT);
			$spentAdd -> bindValue(':cosmID', $cosmID, PDO::PARAM_INT);
			$spentAdd -> bindValue(':volume', $spentV, PDO::PARAM_INT);
			$spentAdd -> bindValue(':cost', $cost, PDO::PARAM_STR);
			$spentAdd -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
			$spentAdd -> execute();
		}
		
		function spentUpdate($id, $volumeOld, $volumeNew, $costOld) {
			$pricePerGram = $volumeOld / $costOld;
			$cost = $volumeNew * $pricePerGram;
			require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
			$spentUpdate = $pdo->prepare("
						UPDATE spent 
						SET volume 		= :volume,
							cost   		= :cost, 
							`timestamp`	= :timestamp, 
							author		= :author
						WHERE id = :id");
			$spentUpdate -> bindValue(':volume', $volumeNew, PDO::PARAM_INT);
			$spentUpdate -> bindValue(':cost', $cost, PDO::PARAM_STR);
			$spentUpdate -> bindValue(':timestamp', null, PDO::PARAM_STR);
			$spentUpdate -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
			$spentUpdate -> bindValue(':id', $id, PDO::PARAM_INT);
			$spentUpdate -> execute();
			
		}
		
		function spentDelete($id) {
			require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
			$spentDelete = $pdo->prepare("
						DELETE FROM spent WHERE id = :id
					");
			$spentDelete -> bindValue(':id', $id, PDO::PARAM_INT);
			$spentDelete -> execute();
		}
		
		if(isset($_POST['cosmID'])) {
			$i=0;
			foreach($_POST['cosmID'] as $cosmID) {
				switch(true)
				{
					case($cosmID == 0 && $_POST['spentID'][$i] > 0):
						spentDelete($_POST['spentID'][$i]);
						break;
					case($cosmID > 0 && $_POST['spentID'][$i] == ''):
					case($cosmID > 0 && !isset($_POST['spentID'][$i])):
						spentAdd($cosmID, $_POST['spentV'][$i], $_POST['spentC'][$i]);
						break;
					case($_POST['spentV'][$i] != $_POST['spentV_old'][$i]):
						spentUpdate($_POST['spentID'][$i], $_POST['spentV_old'][$i], $_POST['spentV'][$i], $_POST['spentC'][$i]);
						break;
					
					default:
						break;
				}
				$i++;
			}
		}
		
		// Продажи
		function saleUpdate($id, $dateOut, $priceOut, $soldToID) {
			require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
			$saleUpdate = $pdo->prepare("
						UPDATE received 
						SET dateOut 	= :dateOut,
							priceOut   	= :priceOut, 
							soldToID   	= :soldToID, 
							qtyOut   	= 1, 
							`timestamp`	= null, 
							author		= :author
						WHERE id = :id");
			$saleUpdate -> bindValue(':dateOut', $dateOut, PDO::PARAM_STR);
			$saleUpdate -> bindValue(':priceOut', $priceOut, PDO::PARAM_STR);
			$saleUpdate -> bindValue(':soldToID', $soldToID, PDO::PARAM_INT);
			$saleUpdate -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
			$saleUpdate -> bindValue(':id', $id, PDO::PARAM_INT);
			$saleUpdate -> execute();
		}
		
		function saleDelete($id) {
			require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
			$saleDelete = $pdo->prepare("
						UPDATE received 
						SET dateOut 	= null,
							priceOut   	= null, 
							qtyOut 		= 0,
							soldToID   	= null, 
							`timestamp`	= null, 
							author		= :author
						WHERE id = :id");
			$saleDelete -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
			$saleDelete -> bindValue(':id', $id, PDO::PARAM_INT);
			$saleDelete -> execute();
		}
		
		function saleAdd ($dateOut, $priceOut, $soldToID) {
			require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
			
		}
		
		
		if(isset($_POST['sold_cosmID'])) {
			$i = 0;
			foreach($_POST['sold_cosmID'] as $cosmID) {
				switch(true)
				{
					case($cosmID == 0 && $_POST['soldRowIDs'][$i] != ''):
						$rows = explode(',', $_POST['soldRowIDs'][$i]);
						foreach($rows as $row) {
							saleDelete($row);
						}
						break;
					case($cosmID > 0 && $_POST['qty'][$i] <= $_POST['qtyOld'][$i]):
						$rows = explode(',', $_POST['soldRowIDs'][$i]);
						sort($rows);
						$pricePerOne = $_POST['sold_price_total'][$i] / $_POST['qty'][$i];
						$count = 1;
						foreach($rows as $row) {
							if($count <= $_POST['qty'][$i]) {
								if ($pricePerOne != $_POST['priceSoldOld'][$i]) 
									saleUpdate($row, $_POST['date'], $pricePerOne, $_POST['clientID']);
							} else {
								saleDelete($row);
							}
							$count++;
						}
						break;
						
					//ДОБАВЛЯЕМ ПРОДАЖУ
					case($cosmID > 0 && !isset($_POST['soldRowIDs'][$i])):
						$rows = explode(',', $_POST['sell_available'][$i]);
						sort($rows);
						$pricePerOne = $_POST['sold_price_total'][$i] / $_POST['qty'][$i];
						$count = 1;
						$s = 0;
						while($count <= $_POST['qty'][$i]){
							saleUpdate($rows[$s], $_POST['date'], $pricePerOne, $_POST['clientID']); 
							$count++;
							$s++;
						}
						
						break;
					
					
					default:
						break;
				}
				$i++;
			}
		}
		
		//END POST
		unset($_SESSION['temp']);
		session_write_close();
	}
	
	if($_POST['goto'] == 'dashboard') {
		header( 'Location: /user/dashboard.php?date='.$_POST['date']);
		exit;
	}
	if($_POST['goto'] == 'profile') {
		header( 'Location: /clients/client_profile.php?id='.$_POST['clientID']);
		exit;
	}
	
	header( 'Location: /visits/visits_list.php?state=all&date='.$_POST['date']);
	exit;
	
}

get_staff_cat_wages();

if(!isset($_GET['recover'])) unset($_SESSION['temp']);
if($_GET['date'] != '') $_SESSION['temp']['date'] = $_GET['date'];
if($_GET['timeFrom'] !='') $_SESSION['temp']['startTime'] = $_GET['timeFrom'];

if ($_GET['id'] != '') { 
	
	$visit = $pdo->prepare("SELECT visits.id, visits.date, startTime, endTime, visits.state, visits.price_total, visits.netto as total_netto, visits.comment, visits.locationID
				
                , works.visits_worksIDs, works.serviceMinPrices, works.serviceMaxPrices, works.work_prices, works.catIDs, works.staffIDs4work, works.staffNames4work, works.rates, works.serviceIDs, works.serviceNames
                , staff.staffRowIDs,staff.staffIDs, staff.staffPrices, staff.staffWages, staff.staffTips, staff.staffComments, staff.staffNames
                , clients.id as clientID, clients.name as clientName, clients.surname as clientSurname, clients.prompt
			FROM `visits`
			LEFT JOIN clients ON visits.clientID = clients.id
            LEFT JOIN (
            	select visitID
                , GROUP_CONCAT(visits_staff.id) as staffRowIDs
                , GROUP_CONCAT(visits_staff.userID) as staffIDs
                , GROUP_CONCAT(CONCAT(users.name, ' ', users.surname)) as staffNames
                , GROUP_CONCAT(price) as staffPrices
                , GROUP_CONCAT(wage) as staffWages
                , GROUP_CONCAT(tips) as staffTips
                , GROUP_CONCAT(visits_staff.comment) as staffComments
                from visits_staff
                LEFT JOIN users ON visits_staff.userID = users.id
                GROUP BY visitID
            ) staff ON visits.id = staff.visitID
             LEFT JOIN (
            	select visitID
                , GROUP_CONCAT(visits_works.id) as visits_worksIDs
                , GROUP_CONCAT(visits_works.minPrice) as serviceMinPrices
                , GROUP_CONCAT(visits_works.maxPrice) as serviceMaxPrices
                , GROUP_CONCAT(price) as work_prices
                , GROUP_CONCAT(catID) as catIDs
                , GROUP_CONCAT(visits_works.userID) as staffIDs4work  								
                , GROUP_CONCAT(CONCAT(users.name, ' ', users.surname)) as staffNames4work			
                , GROUP_CONCAT(visits_works.reward_rate) as rates
                , GROUP_CONCAT(worktypes.id) as serviceIDs											
                , GROUP_CONCAT(worktypes.name) as serviceNames										
                 from visits_works
                 LEFT JOIN worktypes ON visits_works.workID = worktypes.id
                 LEFT JOIN users ON visits_works.userID = users.id
                GROUP BY visitID
            ) works ON visits.id = works.visitID
			WHERE visits.id = :id
			GROUP BY visits.id");
	$visit -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
	$visit ->execute();
	$_SESSION['temp']=$visit->fetch(PDO::FETCH_ASSOC);
	
	
	
	
	$netto = $pdo->prepare("SELECT DISTINCT service_netto.name as nettoName, service_netto.cost as nettoCost
			FROM `service_netto` 
			LEFT JOIN worktype_netto ON service_netto.id = worktype_netto.nettoID
			LEFT JOIN worktype_cat ON worktype_netto.catID = worktype_cat.id
			LEFT JOIN worktypes ON worktype_cat.id = worktypes.catID
			LEFT JOIN visits_works ON worktypes.id = visits_works.workID
			LEFT JOIN visits ON visits_works.visitID = visits.id
			WHERE visits.id = :id
			ORDER BY service_netto.name");
	$netto -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
	$netto -> execute();
	$count = 0;
	$_SESSION['temp']['totalNetto'] = 0;
	while($_SESSION['temp']['netto'][$count] = $netto->fetch(PDO::FETCH_ASSOC)) {
		$_SESSION['temp']['totalNetto'] = $_SESSION['temp']['totalNetto'] + $_SESSION['temp']['netto'][$count]['cost'];
		$count++;
	}
	
	
	$sql="SELECT spent.id as spentID, spent.volume as spentV, spent.volume as spentV_old, spent.cost as spentC, spent.cost as spentC_old, cosmID, CONCAT(brands.name, ' ', cosmetics.name) as cosmNames
		FROM `spent` 
		LEFT JOIN cosmetics ON spent.cosmID=cosmetics.id
		LEFT JOIN brands ON cosmetics.brandID=brands.id
		WHERE visitID=:id
		";
	$stmt = $pdo->prepare($sql);
	$stmt -> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
	$stmt ->execute();
	$_SESSION['temp']['totalSpentV'] = 0;
	$_SESSION['temp']['totalSpentC'] = 0;
	$count = 0;
	while($_SESSION['temp']['spent'][$count] = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$_SESSION['temp']['totalSpentV'] = $_SESSION['temp']['totalSpentV'] + $_SESSION['temp']['spent'][$count]['spentV'];
		$_SESSION['temp']['totalSpentC'] = $_SESSION['temp']['totalSpentC'] + $_SESSION['temp']['spent'][$count]['spentC'];
		$count++;
	}
	
	if($_SESSION['temp']['clientID'] > 0) {
		$totalQty = 0;
		$totalSales = 0;
		$sales = $pdo->prepare("SELECT GROUP_CONCAT(received.id) as soldRowIDs,  received.cosmID as sold_cosmID,  COUNT(qtyOut) as qty, SUM(received.priceOut) as priceSold, GROUP_CONCAT(priceIn) as priceIn
									, CONCAT(brands.name, ' ', cosmetics.name) as soldName
								FROM `received` 
								LEFT JOIN cosmetics ON received.cosmID = cosmetics.id
								LEFT JOIN brands ON cosmetics.brandID = brands.id
								WHERE received.dateOut = :date
									AND soldToID = :clientID
								GROUP BY received.cosmID");
		$sales -> bindValue(':date', $_SESSION['temp']['date'], PDO::PARAM_STR);
		$sales -> bindValue(':clientID', $_SESSION['temp']['clientID'], PDO::PARAM_INT);
		$sales ->execute();
		$count = 0;
		while($_SESSION['temp']['sales'][$count] = $sales->fetch(PDO::FETCH_ASSOC)) {
			$_SESSION['temp']['sales'][$count]['priceSoldOld'] = $_SESSION['temp']['sales'][$count]['priceSold'] / $_SESSION['temp']['sales'][$count]['qty'];
			$_SESSION['temp']['sales'][$count]['qtyOld'] = $_SESSION['temp']['sales'][$count]['qty'];
			$totalQty 	= $totalQty + $_SESSION['temp']['sales'][$count]['qty'];
			$totalSales = $totalSales + $_SESSION['temp']['sales'][$count]['priceSold'];
			$count++;
		}
	}
	
	//проверить права на редактирование воизбежание простого перебора id-шками
	if (handle_rights('user', $_SESSION['temp']['locationID']) != 1) 
	{		
		$_SESSION['error'] = lang::ERR_NO_RIGHTS;
		session_write_close();
		header( 'Location: /user/dashboard.php');
		exit;
	}

	$title=FIO($_SESSION['temp']['clientName'],$_SESSION['temp']['clientSurname'],$_SESSION['temp']['prompt']);
}

if(!isset($_SESSION['locationSelected'])) $_SESSION['locationSelected'] = $_SESSION['temp']['locationID'];
//if(!isset($_SESSION['staff_rates'])) get_staff_cat_wages();
	

$i = $e = $s = $n = $count = 0;
$minTotal = $maxTotal = 0;


if(isset($_GET['new'])) $title=lang::HDR_NEW_VISIT;
$_SESSION['temp']['visitID'] = $_GET['id'];

$workRowTemplate = '<div class="row col-6__2wide">
		<input name="workNames[]"   type="text" 	placeholder="'.lang::HDR_WORKTYPE.'" />
		<select name="staff[]" class="mobile-2-span3">
			<!--option value=""-->' . /*lang::HDR_EMPLOYEE.*/ '<!--/option-->
		</select>
		<input name="minPrice[]" type="number"	class="short input-hdr" value="0" tabindex="-1" readonly />
		<input name="maxPrice[]" type="number"	class="short input-hdr" value="0" tabindex="-1" readonly />
		<input name="price[]" type="number"	class="short bold" 	 />
		<input name="catID[]"   type="hidden"  									 />
		<input name="rate[]"   type="hidden"  									 />
		<input name="price_old[]" type="hidden"	 	 />
		<input name="workID[]"   type="hidden"  									 />
		<i class="fas fa-times  work" title="'.lang::HANDLING_DELETE.'"></i>
	</div>';

$staffTemplate='<div class="row col-5__1st_wide">
		<input name="staffName[]" type="text" class="input-hdr" />
		<i class="far fa-comment fa-2x" title="'.lang::HDR_COMMENT.'"></i>
		<input name="staffID[]" type="hidden" />
		<input name="staffPrices[]" 	type="number"   step="0.01" class="medium" readonly />
		<input name="staff_wage[]" 		type="number" 	step="0.01" class="medium" required/>
		<input name="staffTips[]" 		type="number" 	step="0.01" class="medium" />
	</div>
	<div class="row" style="display:none;">
		<textarea name="staffComments[]" placeholder="'.lang::HDR_COMMENT.'" ></textarea>
	</div>';

	
$nettoTemplate='
		<input name="nettoNames[]" 		type="text"   class="input-hdr" tabindex="-1" readonly	/>
		<input name="nettoCost[]" 		type="number" class="short"  />';

						

$spentTemplate = '<div class="row col-5__1wide">
		<i class="fas fa-arrows-alt-v"></i>
		<input name="cosmNames[]" 	  type="text"  class="mobile-wide" placeholder="'.lang::HDR_ITEM_NAME.'"										 />
		<input name="spentV[]" 	  type="number" min="0" step="1"	class="short" 	 />
		<input name="spentC[]" type="number" step="0.01"	class="input-hdr short" tabindex="-1" readonly';
			if($_SESSION['pwr'] < 90) $spentTemplate = $spentTemplate . 'style="display:none"';
		$spentTemplate = $spentTemplate . ' />
		<input name="cosmID[]"	   type="hidden"  />
		<input name="balanceGr[]"  type="hidden"  />
		<input name="cosmV[]" 	   type="hidden"  />
		<input name="pcsOut[]" 	   type="hidden"  />
		<i class="fas fa-times  spent" title="'.lang::HANDLING_DELETE.'"></i>
	</div>';
	
$soldtemplate = '<div class="row col-5__1st_wide">
		<input name="soldName[]" type="text" class="mobile-wide" placeholder="'.lang::HDR_ITEM_NAME.'" />
		<input name="qty[]" type="number" class="short" step="1"   />
		<input name="priceSold[]" type="number" class="short" step="0.01" />
		<input name="sold_price_total[]" type="number" class="short" step="0.01" value="0" tabindex="-1" readonly/>
		<input name="sold_cosmID[]" type="hidden" />
		<input name="sell_netto[]" type="hidden" />
		<input name="sell_available[]" type="hidden" />
		<i class="fas fa-times  sales" title="'.lang::HANDLING_DELETE.'"></i>
	</div>';

if(!isset($_GET['recover'])) {
	$visits_worksIDs 	= explode(',', $_SESSION['temp']['visits_worksIDs']);
	$work_IDs 			= explode(',', $_SESSION['temp']['serviceIDs']);
	$work_names 		= explode(',', $_SESSION['temp']['serviceNames']);
	$work_minPrices 	= explode(',', $_SESSION['temp']['serviceMinPrices']);
	$work_maxPrices 	= explode(',', $_SESSION['temp']['serviceMaxPrices']);
	$work_prices		= explode(',', $_SESSION['temp']['work_prices']);
	$work_prices_old	= explode(',', $_SESSION['temp']['work_prices']);
	$catIDs				= explode(',', $_SESSION['temp']['catIDs']);
	$userIDs			= explode(',', $_SESSION['temp']['staffIDs4work']);
	$userNames			= explode(',', $_SESSION['temp']['staffNames4work']);
	$rates				= explode(',', $_SESSION['temp']['rates']);
	
	$staffRowIDs	 	= explode(',', $_SESSION['temp']['staffRowIDs']);
	$staffIDs	 		= explode(',', $_SESSION['temp']['staffIDs']);
	$staffNames 		= explode(',', $_SESSION['temp']['staffNames']);
	$staffPrices 		= explode(',', $_SESSION['temp']['staffPrices']);
	$staffWages 		= explode(',', $_SESSION['temp']['staffWages']);
	$staffTips 			= explode(',', $_SESSION['temp']['staffTips']);
	$staffComments 		= explode(',', $_SESSION['temp']['staffComments']);
	
} else {
	$visits_worksIDs 	= $_SESSION['temp']['visits_worksIDs'];
	$work_IDs 			= $_SESSION['temp']['serviceIDs'];
	$work_names 		= $_SESSION['temp']['serviceNames'];
	$work_minPrices 	= $_SESSION['temp']['serviceMinPrices'];
	$work_maxPrices 	= $_SESSION['temp']['serviceMaxPrices'];
	$work_prices		= $_SESSION['temp']['work_prices'];
	$work_prices_old	= $_SESSION['temp']['work_prices_old'];
	$catIDs				= $_SESSION['temp']['catIDs'];
	$userIDs			= $_SESSION['temp']['staffIDs4work'];
	$userNames			= $_SESSION['temp']['staffNames4work'];
	$rates				= $_SESSION['temp']['rates'];
	
	$staffRowIDs	 	= $_SESSION['temp']['staffRowIDs'];
	$staffIDs	 		= $_SESSION['temp']['staffIDs'];
	$staffNames 		= $_SESSION['temp']['staffNames'];
	$staffPrices 		= $_SESSION['temp']['staffPrices'];
	$staffWages 		= $_SESSION['temp']['staffWages'];
	$staffTips 			= $_SESSION['temp']['staffTips'];
	$staffComments 		= $_SESSION['temp']['staffComments'];
	
	//$nettoNames 		= $_SESSION['temp']['nettoName'];
	//$nettoCost	 		= $_SESSION['temp']['nettoCost'];
	
}

if($_GET['timeFrom'] !='') {
	$_SESSION['temp']['startTime'] = $_GET['timeFrom'];	
	$start = explode(':', $_SESSION['temp']['startTime']);
	$_SESSION['temp']['endTime'] = get_std_end_time($_GET['timeFrom']);
}


//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	if (isset($_GET['new']))	echo tabs($tabs, 'vst_add');
	else echo tabs($tabs);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	if($_GET['id'] > 0 || isset($_GET['new'])) {?>
		<h2><?=$title;?></h2>
		
		
		<template id="wrk">
			<?=$workRowTemplate;?>
		</template>
		
		<template id="stf">
			<?=$staffTemplate;?>
		</template>
		
		<template id="net">
			<?=$nettoTemplate;?>
		</template>
		
		
		<template id="spent">
			<?=$spentTemplate;?>
		</template>
		
		<template id="sold">
			<?=$soldtemplate;?>
		</template>
		
		<form method="post" id="form">
			<fieldset>
				<p class="title"><?=lang::HDR_VISIT_DATA;?></p>
				<div class="row col-2">
				<?=location_options(1, null, $_SESSION['temp']['locationID'], 1);?>
					<label for="date"><?=lang::DATE;?>:</label>
					<input name="date" type="date" value="<?=$_SESSION['temp']['date'];?>" required />
					<label for="startTime"><?=lang::HDR_TIME_FROM;?>:</label>
					<select name="startTime" required />
						<?=time_options($_SESSION['temp']['startTime']);?>
					</select>
					<label for="endTime"><?=lang::HDR_TIME_TO;?>:</label>
					<select name="endTime" required />
						<?=time_options($_SESSION['temp']['endTime'], 0, $start[0]);?>
					</select>
					<label for="clientID"><?=lang::TBL_CLIENT;?>:</label>
					<input name="customers" class="FIO" placeholder="<?=lang::SEARCH_CLIENT_PLACEHOLDER;?>" value="<?php if(isset($_SESSION['temp']['customers'])) echo htmlspecialchars($_SESSION['temp']['customers']); else echo FIO($_SESSION['temp']['clientName'],$_SESSION['temp']['clientSurname'],$_SESSION['temp']['prompt']);?>" autocomplete="off">
					<input name="clientID" type="hidden" value="<?=$_SESSION['temp']['clientID'];?>">
					<label for="state"><?=lang::HDR_VISIT_STATE;?>:</label>
					<select name="state" required />
						<?=visit_state_select($_SESSION['temp']['state']);?>
					</select>
				</div>
				<div class="row">
					<textarea name="comment" placeholder="<?=lang::HDR_COMMENT;?>"><?php echo htmlspecialchars($_SESSION['temp']['comment']);?></textarea>
				</div>
				<input name="visitID" type="hidden" value="<?=$_SESSION['temp']['visitID'];?>" />
				<?php if(isset($_GET['goto'])) echo '<input name="goto" type="hidden" value="'.$_GET['goto'].'" />';?>
				
				<p class="title"><?=lang::HDR_WORKTYPE_LIST;?></p>
				<div id="works">
					<div class="row col-6__2wide">
						<input class="input-hdr bold" 		 value="<?=lang::HDR_WORKTYPE;?>" disabled />
						<input class="input-hdr bold mobile-2-span2" value="<?=lang::HDR_EMPLOYEE;?>" disabled />
						<input class="input-hdr bold short" value="min,<?=curr();?>" 	disabled />
						<input class="input-hdr bold short" value="max,<?=curr();?>" 	disabled />
						<input class="input-hdr bold short" value="<?=curr();?>" 		disabled />
						<div style="width:2ch;"></div>
					</div>
				
					<?php 
					while($work_IDs[$i] != null) {
						echo '<div class="row col-6__2wide">
							<input name="workNames[]" 		type="text"   class="input-hdr"		  value="' . htmlspecialchars($work_names[$i]) . '" 	tabindex="-1"  readonly	/>
							<select name="staff[]" 	class="input-hdr mobile-2-span3" readonly>
								<option value="' . $userIDs[$i] . '">' . htmlspecialchars($userNames[$i]) . '</option>
							</select>
							<input name="minPrice[]" 		type="number" class="input-hdr short" value="' . $work_minPrices[$i] . '" tabindex="-1" readonly  />
							<input name="maxPrice[]" 		type="number" class="input-hdr short" value="' . $work_maxPrices[$i] . '" tabindex="-1" readonly  />
							<input name="price[]" 			type="number" class="short bold" 	  value="' . $work_prices[$i] . '" />
							<input name="catID[]" 			type="hidden" 						  value="' . $catIDs[$i] . '" />
							<input name="rate[]" 			type="hidden" 						  value="' . $rates[$i] . '" />
							<input name="price_old[]" 		type="hidden" 					 	  value="' . $work_prices_old[$i] . '" />
							<input name="workID[]" 			type="hidden" 						  value="' . $work_IDs[$i] . '" />
							<input name="visits_worksIDs[]" type="hidden" 						  value="' . $visits_worksIDs[$i] . '" />'; //id уже внесенного
							echo '<i class="fas fa-times  work" title="'.lang::HANDLING_DELETE.'"></i>
						</div>';
						$minTotal = $minTotal + $work_minPrices[$i];
						$maxTotal = $maxTotal + $work_maxPrices[$i];
						$i++;
					}
						
					if(isset($_GET['new']) && !isset($_GET['recover'])) {
						echo $workRowTemplate;
						$i++;
					} ?>
				</div>
				<input type="button" value="<?=lang::BTN_ADD_WORK;?>" onclick="workAdd();" />
				<div class="row col-6__2wide">
					<input class="input-hdr bold" style="grid-column: 1 / 3;"    value="<?=lang::HDR_TOTAL_PRICE_RANGE;?>" 		disabled />
					<input class="input-hdr bold short" name="min" type="number" value="<?=correctNumber($minTotal,2);?>" 	readonly />
					<input class="input-hdr bold short" name="max" type="number" value="<?=correctNumber($maxTotal,2);?>" 	readonly />
					<input name="price_total" class="mobile-wide input-hdr short bold" class="results" value="<?=$_SESSION['temp']['price_total'];?>" type="number" step="0.01" />
					<div style="width: 2ch;"></div>
				</div> 
				
				<!----------------- СЕБЕСТОИМОСТЬ ---------------->
				
				<section id="nettoData">
					<p class="title"><?=lang::HDR_NETTO_SEVICES;?>*</p>
					
					
					<div id="netto" class="row col-2__1wide">
						<input class="input-hdr bold" 		 value="<?=lang::HDR_ITEM_NAME;?>" disabled />
						<input class="input-hdr bold short " value="<?=curr();?>" 		disabled />
					
						<?php 
						while($_SESSION['temp']['netto'][$n] != null) {
								echo '<input name="nettoNames[]" 		type="text"   class="input-hdr"	value="' . $_SESSION['temp']['netto'][$n]['nettoName'] . '" readonly	/>
								<input name="nettoCost[]" 		type="number" class="short" 	value="' . $_SESSION['temp']['netto'][$n]['nettoCost'] . '" readonly />';
								
							$totalNetto = $totalNetto + $_SESSION['temp']['netto'][$n]['nettoCost'];
							$n++;
						}
						
						if(isset($_GET['new']) && !isset($_GET['recover'])) {
							echo '
								<input name="nettoNames[]"	type="hidden"	disabled />
								<input name="nettoCost[]"	type="hidden"	disabled />
							';
						}
						
						?>
						
					</div>
					<div class="row col-2__1wide">
						<input class="input-hdr bold alignRight mobile-wide" 	value="<?=lang::HDR_TOTAL;?>" disabled />
						<input class="bold short" name="netto_total" type="number" value="<?=correctNumber($_SESSION['temp']['total_netto'],2);?>" 	 />
					</div> 
					<p class="small italic">* <?=lang::EXPL_NETTO_PRICE;?></p>
				</section>
				
				<section id="spentData">
					<p class="title"><?=lang::HDR_SPENT_LIST;?></p>
					<div id="spentLines">
						<div class="row col-5__1wide">
							<div style="width:2ch"></div>
							<input type="text" class="mobile-wide input-hdr bold" value="<?=lang::HDR_ITEM_NAME;?>" disabled />
							<input type="text" class="input-hdr bold short" value="<?=lang::HDR_SPENT_VOLUME;?>" disabled />
							<input type="text" class="input-hdr bold short" value="<?=curr();?>" disabled <?php if($_SESSION['pwr'] < 90) echo 'style="display:none"';?> />
							<div style="width:3ch"></div>
						</div>
						<?php 
						while($_SESSION['temp']['spent'][$count] != null) {
							echo '<div class="row col-5__1wide">
								<i class="fas fa-arrows-alt-v"></i>
								<input name="cosmNames[]" type="text" class="mobile-wide input-hdr" value="'.htmlspecialchars($_SESSION['temp']['spent'][$count]['cosmNames']).'" readonly/>
								<input name="spentV[]" type="number" class="short" step="1"    value="'.$_SESSION['temp']['spent'][$count]['spentV'].'" />
								<input name="spentC[]" type="number" class="short" step="0.01" value="'.$_SESSION['temp']['spent'][$count]['spentC'].'" readonly '; 
									if($_SESSION['pwr'] < 90) echo 'style="display:none"';
								echo '/>
								<input name="cosmID[]" type="hidden" value="'.$_SESSION['temp']['spent'][$count]['cosmID'].'" />
								<input name="spentID[]" type="hidden" value="'.$_SESSION['temp']['spent'][$count]['spentID'].'" />
								<input name="spentV_old[]" type="hidden" value="'.$_SESSION['temp']['spent'][$count]['spentV_old'].'" />
								<input name="spentC_old[]" type="hidden" value="'.$_SESSION['temp']['spent'][$count]['spentC_old'].'" />
								<input name="balanceGr[]" type="hidden" value="'.$_SESSION['temp']['spent'][$count]['balanceGr'].'" />
								<input name="cosmV[]" type="hidden" value="'.$_SESSION['temp']['spent'][$count]['cosmV'].'" />
								<input name="pcsOut[]" type="hidden" value="'.$_SESSION['temp']['spent'][$count]['pcsOut'].'" />
								<i class="fas fa-times  spent" title="'.lang::HANDLING_DELETE.'"></i>
							</div>';
							$count++;
						}?>
					</div>	
					<input type="button" value="<?=lang::BTN_ADD_SPENT;?>" onclick="spentAdd();" />
					<div class="row col-5__1wide"  
						<?php if($_SESSION['pwr'] < 90) echo 'style="display:none"';?>
					>
						<div></div>
						<input type="text" class="input-hdr bold" value="<?=lang::HDR_TOTAL;?>" disabled />
						<input name="totalSpentV" type="number" class="input-hdr bold short" value="<?=correctNumber($_SESSION['temp']['totalSpentV'],0);?>" readonly />
						<input name="totalSpentC" type="number" class="input-hdr bold short" value="<?=correctNumber($_SESSION['temp']['totalSpentC'],0);?>" readonly />
						<div style="width:3ch;"></div>
					</div>	
				</section>
				
				<section id="salesData">
					<p class="title"><?=lang::HDR_SALES_LIST;?></p>
					<div class="row col-5__1st_wide">
						<input type="text" class="mobile-wide input-hdr bold" value="<?=lang::HDR_ITEM_NAME;?>" disabled />
						<input type="text" class="input-hdr bold short" value="<?=lang::PLACEHOLDER_QTY;?>" disabled />
						<input type="text" class="input-hdr bold short" value="<?=lang::HDR_PRICE;?>" disabled />
						<input type="text" class="input-hdr bold short mobile-hide" value="<?=lang::HDR_TOTAL;?>" disabled />
						<div style="width:3ch;"></div>
					</div>	
					<div id="salesLines">
					
						<?php $s=0;
						while($_SESSION['temp']['sales'][$s] != null) {
							echo '<div class="row col-5__1st_wide">
								<input name="soldName[]" type="text" class="mobile-wide input-hdr" value="'.htmlspecialchars($_SESSION['temp']['sales'][$s]['soldName']).'" readonly/>
								<input name="qty[]" type="number" class="short" step="1"    value="'.$_SESSION['temp']['sales'][$s]['qty'].'" max="'.$_SESSION['temp']['sales'][$s]['qty'].'" />
								<input name="priceSold[]" type="number" class="short" step="0.01" value="'.$_SESSION['temp']['sales'][$s]['priceSold'] / $_SESSION['temp']['sales'][$s]['qty'].'" />
								<input name="sold_price_total[]" type="number" class="short" step="0.01" value="'.$_SESSION['temp']['sales'][$s]['priceSold'].'" readonly/>
								<input name="sold_cosmID[]" type="hidden" value="'.$_SESSION['temp']['sales'][$s]['sold_cosmID'].'" />
								<input name="soldRowIDs[]" type="hidden" value="'.$_SESSION['temp']['sales'][$s]['soldRowIDs'].'" />
								<input name="priceSoldOld[]" type="hidden" value="'.$_SESSION['temp']['sales'][$s]['priceSoldOld'].'" />
								<input name="qtyOld[]" type="hidden" value="'.$_SESSION['temp']['sales'][$s]['qtyOld'].'" />
								<input name="sell_netto[]" type="hidden" value="'.$_SESSION['temp']['sales'][$s]['priceIn'].'"/>
								<input name="sell_available[]" type="hidden" />
								<i class="fas fa-times  sales" title="'.lang::HANDLING_DELETE.'"></i>
							</div>';
							$s++;
						}?>
					</div>	
					<input type="button" value="<?=lang::BTN_ADD_SALE;?>" onclick="saleAdd();" />
					<div class="row col-5__1st_wide">
						<input type="text" class="input-hdr bold" value="<?=lang::HDR_TOTAL;?>" disabled />
						<input name="totalQty" type="number" class="input-hdr bold short" value="<?=$totalQty;?>" readonly />
						<input class="input-hdr bold short mobile-hide" disabled />
						<input name="totalSales" type="number" class="input-hdr bold short" value="<?=$totalSales;?>" readonly />
						<div style="width:3ch;"></div>
					</div>	
				</section>
				
				<section id="employeeData">
					<p class="title" <?php if($_SESSION['pwr'] < 90) echo 'style="display:none"';?> ><?=lang::HDR_COST_PER_EMPLOYEE;?></p>
					<div class="row col-5__1st_wide" <?php if($_SESSION['pwr'] < 90) echo 'style="display:none"';?>>
						<input class="input-hdr bold" value="<?=lang::HDR_EMPLOYEE;?>"		   disabled />
						<div style="width:5ch;"></div>
						<input class="input-hdr bold medium" value="<?=lang::HDR_TOTAL . ', ' . curr();?>" disabled />
						<input class="input-hdr bold medium" value="<?=lang::HDR_WAGE . ', ' . curr();?>"  disabled />
						<input class="input-hdr bold medium" value="<?=lang::HDR_TIPS . ', ' . curr();?>"  disabled />
					</div>
					<div id="employees" <?php if($_SESSION['pwr'] < 90) echo 'style="display:none"';?>>
						
							<?php
							
							if($_GET['id'] > 0) {
								while($staffIDs[$e] != null) {
									echo '<div class="row col-5__1st_wide">
										<input name="staffName[]" 		type="text"   class="mobile-wide input-hdr"		  value="' . htmlspecialchars($staffNames[$e]) . '" 	  readonly	/>
										<i class="far fa-comment fa-2x" title="'.lang::HDR_COMMENT.'"></i>
										<input name="staffPrices[]" 	type="number"   step="0.01" class="medium" value="' . $staffPrices[$e] . '"   />
										<input name="staff_wage[]" 		type="number" 	step="0.01" class="medium" value="' . $staffWages[$e] . '" required />
										<input name="staffTips[]" 		type="number" 	step="0.01" class="medium" value="' . $staffTips[$e] . '"  />
										<input name="staffID[]" 		type="hidden"					 	  value="' . $staffIDs[$e] . '" 				/>
										<input name="staffRowIDs[]"		type="hidden" 						  value="' . $staffRowIDs[$e] . '" 			/>'; //id уже внесенного
									echo '</div>';
									echo '<div class="row"';
										if($staffComments[$e] == '') echo 'style="display:none"';
									echo '>
										<textarea name="staffComments[]" placeholder="'.lang::HDR_COMMENT.'" >'.htmlspecialchars($staffComments[$e]).'</textarea>';
									echo '</div>';
									$e++;
								}
							} else if(isset($_GET['new']) && isset($_GET['recover'])) {
								while($staffIDs[$e] != null) {
									echo '<div class="row col-5__1st_wide">
										<select name="staffID[]" class="mobile-wide">' . staff_select_options($_SESSION['locationSelected'], $staffIDs[$e]) . '</select>
										<i class="far fa-comment fa-2x" title="'.lang::HDR_COMMENT.'"></i>
										<input name="staffPrices[]" 	type="number"   step="0.01" class="short" value="' . $staffPrices[$e] . '"   />
										<input name="staff_wage[]" 		type="number" 	step="0.01" class="short" value="' . $staffWages[$e] . '" required />
										<input name="staffTips[]" 		type="number" 	step="0.01" class="short" value="' . $staffTips[$e] . '"  />
									</div>
									<div class="row"'; if($staffComments[$e] == '') echo 'style="display:none"'; echo '>
										<textarea name="staffComments[]" placeholder="'.lang::HDR_COMMENT.'" >'.htmlspecialchars($staffComments[$e]).'</textarea>
									</div>';
									$e++;
								}
							} ?>
					</div>
				</section>
				<section id="totals" <?php if($_SESSION['pwr'] < 90) echo 'style="display:none"';?> >
					<p class="title"><?=lang::HDR_VISIT_TOTALS;?></p>
					<div class="row col-4__1st_wide">
						<input class="input-hdr bold" value="<?=lang::HDR_ITEM_NAME;?>"	  disabled />
						<input class="input-hdr bold medium" value="<?=lang::HDR_PRICE;?>" 		  disabled />
						<input class="input-hdr bold medium" value="<?=lang::HDR_NETTO;?>" disabled />
						<input class="input-hdr bold medium" value="<?=lang::HDR_PROFIT;?>" disabled />
					</div>
					<div class="row col-4__1st_wide">
						<input class="input-hdr" value="<?=lang::HDR_CUSTOMER_SERVICE;?>"	  readonly />
						<input name="totals_toPay_S" class="input-hdr medium" value="" 		  readonly />
						<input name="totals_netto_S" class="input-hdr medium" value="" readonly />
						<input name="totals_income_S" class="input-hdr bold medium" value="" readonly />
					</div>
					<div class="row col-4__1st_wide">
						<input class="input-hdr" value="<?=lang::HDR_SALES_LIST;?>"	  readonly />
						<input name="totals_toPay_sale" class="input-hdr medium" value="" 		  readonly />
						<input name="totals_netto_sale" class="input-hdr medium" value="" readonly />
						<input name="totals_income_sale" class="input-hdr bold medium" value="" readonly />
					</div>				
					<div class="row col-4__1st_wide">
						<input class="input-hdr bold" 		 value="<?=lang::HDR_TOTAL;?>"	  disabled />
						<input class="input-hdr medium" 	  disabled />
						<input class="input-hdr medium" 	  disabled />
						<input name="grand_total_income" class="input-hdr bold medium results" class="results" disabled />
					</div>
					
				</section>
				<input type="hidden" name="del_visit">
			</fieldset>
			<div class="row col-3">
				<?php
				if($_GET['goto'] == 'profile') {
					echo '<a class="button" href="/clients/client_profile.php?id=' . $_SESSION['temp']['clientID'];
				} else if($_GET['goto'] == 'dashboard') {
					echo '<a class="button" href="/user/dashboard.php?date='.$_SESSION['temp']['date'];
				} else {
					echo '<a class="button" href="/visits/visits_list.php?state=all&date='.$_SESSION['temp']['date'];
				}
				
				echo '" >'. lang::BTN_CANCEL .'</a>';
				
				if($_SESSION['pwr'] > 89 && !isset($_GET['new'])) {
					echo '<button class="del_visit" style="place-self: center;"><i class="fas fa-times" title="'.lang::HANDLING_DELETE.'"></i>'.lang::HANDLING_DELETE.'</button>';
				} else echo '<b></b>';
				
				echo '<input type="submit" id="submit" value="'.lang::BTN_SAVE.'" style="justify-self: end;"/>
			</div>
		</form>';
		
	 } else {
		echo lang::ERR_NO_ID;
	}
	
	$staff_rates = array();
		foreach ($_SESSION['staff_rates'] as $rate) {
		$staff_rates[] = $rate;
	}
	
	if($_GET["id"] > 0) $id = $_GET["id"]; 
	else $id = 0;
	
	?>
	
	
	
	<!-- 4 js only-->
	<input type="hidden" name="v_id" value="<?=$id;?>" />
	<input type="hidden" name="wrk_count" value="<?=$i;?>" />	
	<input type="hidden" name="netto_count" value="<?=$n;?>" />
	<input type="hidden" name="stf_count" value="<?=$e;?>" />	
	<input type="hidden" name="spent_count" value="<?=$count;?>" />
	<input type="hidden" name="sales_count" value="<?=$s;?>" />
</section>

<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');?>
<script src="/js/dragula_dNd.js"></script>
<script>
	const ratesJson = <?=json_encode($staff_rates);?>;
	const alert_txt = '<?=lang::ALERT_DELETE;?>';
	const alert_future_date = '<?=lang::ALERT_WRONG_DATE_STATE;?>';
	
</script>
<script src = "/js/visit_details.js"></script>
