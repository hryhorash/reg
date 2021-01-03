<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include($_SERVER['DOCUMENT_ROOT'].'/clients/tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	//print_r($_POST);
	//exit;
	
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
			$visitNew -> bindValue(':price_total',	$_POST["price_total"], PDO::PARAM_STR);
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
			default:
				break;
		}
		$i++;
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
	session_write_close();
	if($_POST['visitID'] > 0 ) {
		header( 'Location: /visits/visits_list.php?state=all&date='.$_POST['date']);
		exit;
	} else {
		header( 'Location: /visits/visits_list.php?state=all&date='.$_POST['date']);
		exit;
	}
}

if(!isset($_GET['recover'])) unset($_SESSION['temp']);
if($_GET['date'] != '') $_SESSION['temp']['date'] = $_GET['date'];

if ($_GET['id'] != '') {
	
	$visit = $pdo->prepare("SELECT visits.id, visits.date, startTime, endTime, visits.state, visits.price_total, visits.netto as total_netto, visits.comment, visits.locationID
				, GROUP_CONCAT(DISTINCT worktypes.id) as serviceIDs
                , GROUP_CONCAT(DISTINCT worktypes.name) as serviceNames
                
                , works.visits_worksIDs, works.serviceMinPrices, works.serviceMaxPrices, works.work_prices, works.catIDs, works.staffIDs4work, works.staffNames4work, works.rates
                , GROUP_CONCAT(DISTINCT CONCAT(users.name, ' ', users.surname)) as staffNames
                , staff.staffRowIDs,staff.staffIDs, staff.staffPrices, staff.staffWages, staff.staffTips, staff.staffComments
                , clients.id as clientID, clients.name as clientName, clients.surname as clientSurname, clients.prompt
			FROM `visits`
			LEFT JOIN visits_works ON visits.id = visits_works.visitID
			LEFT JOIN visits_staff ON visits.id = visits_staff.visitID
			LEFT JOIN worktypes ON visits_works.workID = worktypes.id
			LEFT JOIN users ON visits_staff.userID = users.id
			LEFT JOIN clients ON visits.clientID = clients.id
            LEFT JOIN (
            	select visitID
                , GROUP_CONCAT(id) as staffRowIDs
                , GROUP_CONCAT(userID) as staffIDs
                , GROUP_CONCAT(price) as staffPrices
                , GROUP_CONCAT(wage) as staffWages
                , GROUP_CONCAT(tips) as staffTips
                , GROUP_CONCAT(comment) as staffComments
                from visits_staff
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
		$sales = $pdo->prepare("SELECT GROUP_CONCAT(received.id) as soldRowIDs,  received.cosmID as sold_cosmID,  COUNT(qtyOut) as qty, SUM(received.priceOut) as priceSold, priceIn
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
	if (handle_rights('user', $data[1]['locationID']) != 1) 
	{		
		$_SESSION['error'] = lang::ERR_NO_RIGHTS;
		session_write_close();
		header( 'Location: /cosmetics/invoice_list.php');
		exit;
	}

	$title=FIO($_SESSION['temp']['clientName'],$_SESSION['temp']['clientSurname'],$_SESSION['temp']['prompt']);
}

if(!isset($_SESSION['locationSelected'])) $_SESSION['locationSelected'] = $_SESSION['temp']['locationID'];
if(!isset($_SESSION['staff_rates'])) get_staff_cat_wages();
	

$i = $e = $s = $n = $count = 0;
$minTotal = $maxTotal = 0;


if(isset($_GET['new'])) $title=lang::HDR_NEW_VISIT;
$_SESSION['temp']['visitID'] = $_GET['id'];

$workRowTemplate = '<div class="row nested">
		<input name="workNames[]"   type="text" 	placeholder="'.lang::HDR_WORKTYPE.'" />
		<select name="staff[]" style="margin-left:10px;">
			<!--option value=""-->' . /*lang::HDR_EMPLOYEE.*/ '<!--/option-->
		</select>
		<input name="minPrice[]" type="number"	class="short input-hdr" value="0" readonly 	 />
		<input name="maxPrice[]" type="number"	class="short input-hdr" value="0" readonly 	 />
		<input name="price[]" type="number"	class="short bold" 	 />
		<input name="catID[]"   type="hidden"  									 />
		<input name="rate[]"   type="hidden"  									 />
		<input name="price_old[]" type="hidden"	 	 />
		<input name="workID[]"   type="hidden"  									 />
		<i class="fas fa-times inline-fa work" title="'.lang::HANDLING_DELETE.'"></i>
	</div>';

$staffTemplate='<div class="row nested">
		<input name="staffName[]" type="text" class="input-hdr" />
		<input name="staffID[]" type="hidden" />
		<input name="staffPrices[]" 	type="number"   step="0.01" class="medium" readonly />
		<input name="staff_wage[]" 		type="number" 	step="0.01" class="medium" required/>
		<input name="staffTips[]" 		type="number" 	step="0.01" class="medium" />
		<i class="far fa-comment fa-2x" title="'.lang::HDR_COMMENT.'" style="margin: auto -40px auto 0;"></i>
	</div>
	<div class="row" style="display:none;">
		<textarea name="staffComments[]" placeholder="'.lang::HDR_COMMENT.'" ></textarea>
	</div>';

	
$nettoTemplate='<div class="row nested">
		<input name="nettoNames[]" 		type="text"   class="input-hdr" tabindex="-1" readonly	/>
		<input name="nettoCost[]" 		type="number" class="short"  />
		<i class="fas fa-times inline-fa netto" title="'.lang::HANDLING_DELETE.'"></i>
	</div>';

						

$spentTemplate = '<div class="row nested">
		<i class="fas fa-arrows-alt-v" style="margin: auto -13px;"></i>
		<input name="cosmNames[]" 	  type="text"   placeholder="'.lang::HDR_ITEM_NAME.'"										 />
		<input name="spentV[]" 	  type="number" min="0" step="1"	class="short" 	 />
		<input name="spentC[]" type="number" step="0.01"	class="input-hdr short" tabindex="-1" readonly />
		<input name="cosmID[]"	   type="hidden"  />
		<input name="balanceGr[]"  type="hidden"  />
		<input name="cosmV[]" 	   type="hidden"  />
		<input name="pcsOut[]" 	   type="hidden"  />
		<i class="fas fa-times inline-fa spent" title="'.lang::HANDLING_DELETE.'"></i>
	</div>';
	
$soldtemplate = '<div class="row nested">
		<input name="soldName[]" type="text" placeholder="'.lang::HDR_ITEM_NAME.'" />
		<input name="qty[]" type="number" class="short" step="1"   />
		<input name="priceSold[]" type="number" class="short" step="0.01" />
		<input name="sold_price_total[]" type="number" class="short" step="0.01" value="0" tabindex="-1" readonly/>
		<input name="sold_cosmID[]" type="hidden" />
		<input name="sell_netto[]" type="hidden" />
		<input name="sell_available[]" type="hidden" />
		<i class="fas fa-times inline-fa sales" title="'.lang::HANDLING_DELETE.'"></i>
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
		
		<form method="post">
			<fieldset>
				<p class="title"><?=lang::HDR_VISIT_DATA;?></p>
				<?=location_options(1, null, $_SESSION['temp']['locationID'], 1);?>
				<div class="row">
					<label for="date"><?=lang::DATE;?>:</label>
					<input name="date" type="date" value="<?=$_SESSION['temp']['date'];?>" required />
				</div>
				<div class="row">
					<label for="startTime"><?=lang::HDR_TIME_FROM;?>:</label>
					<select name="startTime"  value="<?=$_SESSION['temp']['startTime'];?>" required />
						<?=time_options();?>
					</select>
				</div>
				<div class="row">
					<label for="endTime"><?=lang::HDR_TIME_TO;?>:</label>
					<select name="endTime"  value="<?=$_SESSION['temp']['endTime'];?>" required />
						<?=time_options();?>
					</select>
				</div>
				<div class="row">
					<label for="clientID"><?=lang::TBL_CLIENT;?>:</label>
					<input name="customers" class="FIO" placeholder="<?=lang::SEARCH_CLIENT_PLACEHOLDER;?>" value="<?php if(isset($_SESSION['temp']['customers'])) echo $_SESSION['temp']['customers']; else echo FIO($_SESSION['temp']['clientName'],$_SESSION['temp']['clientSurname'],$_SESSION['temp']['prompt']);?>" autocomplete="off">
					<input name="clientID" type="hidden" value="<?=$_SESSION['temp']['clientID'];?>">
				</div>
				<div class="row">
					<label for="state"><?=lang::HDR_VISIT_STATE;?>:</label>
					<select name="state" required />
						<?=visit_state_select($_SESSION['temp']['state']);?>
					</select>
				</div>
				<div class="row">
					<textarea name="comment" placeholder="<?=lang::HDR_COMMENT;?>"><?=$_SESSION['temp']['comment'];?></textarea>
				</div>
				<input name="visitID" type="hidden" value="<?=$_SESSION['temp']['visitID'];?>" />
				
				<p class="title"><?=lang::HDR_WORKTYPE_LIST;?></p>
				<div class="row nested">
					<input class="input-hdr bold" 		 value="<?=lang::HDR_WORKTYPE;?>" disabled />
					<input class="input-hdr bold" 		 value="<?=lang::HDR_EMPLOYEE;?>" disabled />
					<input class="input-hdr bold short " value="min,<?=curr();?>" 		disabled />
					<input class="input-hdr bold short " value="max,<?=curr();?>" 		disabled />
					<input class="input-hdr bold short " value="<?=curr();?>" 		disabled />
				</div>
				<div id="works">
				
					<?php 
					while($work_IDs[$i] != null) {
						echo '<div class="row nested">
							<input name="workNames[]" 		type="text"   class="input-hdr"		  value="' . $work_names[$i] . '" 	tabindex="-1"  readonly	/>
							<select name="staff[]" 	class="input-hdr" readonly>
								<option value="' . $userIDs[$i] . '">' . $userNames[$i] . '</option>
							</select>
							<input name="minPrice[]" 		type="number" class="input-hdr short" value="' . $work_minPrices[$i] . '" tabindex="-1" readonly  />
							<input name="maxPrice[]" 		type="number" class="input-hdr short" value="' . $work_maxPrices[$i] . '" tabindex="-1" readonly  />
							<input name="price[]" 			type="number" class="short bold" 	  value="' . $work_prices[$i] . '" />
							<input name="catID[]" 			type="hidden" 						  value="' . $catIDs[$i] . '" 				/>
							<input name="rate[]" 			type="hidden" 						  value="' . $rates[$i] . '" 				/>
							<input name="price_old[]" 		type="hidden" 					 	  value="' . $work_prices_old[$i] . '" />
							<input name="workID[]" 			type="hidden" 						  value="' . $work_IDs[$i] . '" 				/>
							<input name="visits_worksIDs[]" type="hidden" 						  value="' . $visits_worksIDs[$i] . '" 			/>'; //id уже внесенного
							echo '<i class="fas fa-times inline-fa work" title="'.lang::HANDLING_DELETE.'"></i>';
						echo '</div>';
						$minTotal = $minTotal + $work_minPrices[$i];
						$maxTotal = $maxTotal + $work_maxPrices[$i];
						$i++;
					}
					
					if(isset($_GET['new']) && !isset($_GET['recover'])) {
						echo $workRowTemplate;
						$i++;
						/*echo '<div class="row nested">
							<input name="workNames[]"  class="workNames'.$i.'" type="text" 	placeholder="'.lang::HDR_WORKTYPE.'" />
							<select name="staffID[]" style="margin-left:10px;">
							</select>
							<input name="minPrice[]" type="number"	class="short input-hdr" value="0" readonly 	 />
							<input name="maxPrice[]" type="number"	class="short input-hdr" value="0" readonly 	 />
							<input name="price[]" type="number"	class="short bold" 	 />
							<input name="catID[]"   type="hidden"  									 />
							<input name="workID[]"   type="hidden"  									 />
							<i class="fas fa-times inline-fa work" title="'.lang::HANDLING_DELETE.'"></i>
						</div>';
						$i++;*/
					} ?>
					
				</div>
				<input type="button" value="<?=lang::BTN_ADD_WORK;?>" onclick="workAdd();" />
				<div class="row nested">
					<input class="input-hdr bold" 								 value="<?=lang::HDR_TOTAL_PRICE_RANGE;?>" 		disabled />
					<input class="input-hdr bold short" name="min" type="number" value="<?=correctNumber($minTotal,2);?>" 	readonly />
					<input class="input-hdr bold short" name="max" type="number" value="<?=correctNumber($maxTotal,2);?>" 	readonly />
					<input name="price_total" class="input-hdr short bold" style="font-size: x-large; width: 100px; margin:5px -35px 5px 0;" value="<?=$_SESSION['temp']['price_total'];?>" type="number" step="0.01" />
				</div> 
				
				<!----------------- СЕБЕСТОИМОСТЬ ---------------->
				
				
				<p class="title"><?=lang::HDR_NETTO_SEVICES;?>*</p>
				
				
				<div class="row nested">
					<input class="input-hdr bold" 		 value="<?=lang::HDR_ITEM_NAME;?>" disabled />
					<input class="input-hdr bold short " value="<?=curr();?>" 		disabled />
				</div>
				<div id="netto">
				
					<?php 
					while($_SESSION['temp']['netto'][$n] != null) {
						echo '<div class="row nested">
							<input name="nettoNames[]" 		type="text"   class="input-hdr"	value="' . $_SESSION['temp']['netto'][$n]['nettoName'] . '" readonly	/>
							<input name="nettoCost[]" 		type="number" class="short" 	value="' . $_SESSION['temp']['netto'][$n]['nettoCost'] . '" readonly />';
							echo '<i class="fas fa-times inline-fa netto" title="'.lang::HANDLING_DELETE.'"></i>';
						echo '</div>';
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
				<div class="row nested">
					<input class="input-hdr bold alignRight" 		   value="<?=lang::HDR_TOTAL;?>" disabled />
					<input class="bold short" name="netto_total" type="number" value="<?=correctNumber($_SESSION['temp']['total_netto'],2);?>" 	 />
				</div> 
				<p class="small italic">* <?=lang::EXPL_NETTO_PRICE;?></p>
				
				
				
				
				
				
				
				<div id="spentData">
					<p class="title"><?=lang::HDR_SPENT_LIST;?></p>
					<div class="row nested">
						<input type="text" class="input-hdr bold" value="<?=lang::HDR_ITEM_NAME;?>" disabled />
						<input type="text" class="input-hdr bold short" value="<?=lang::HDR_SPENT_VOLUME;?>" disabled />
						<input type="text" class="input-hdr bold short" value="<?=curr();?>" disabled />
					</div>	
					<div id="spentLines">
					
						<?php 
						while($_SESSION['temp']['spent'][$count] != null) {
							echo '<div class="row nested">
								<i class="fas fa-arrows-alt-v" style="margin: auto -13px;"></i>
								<input name="cosmNames[]" type="text" class="input-hdr" value="'.$_SESSION['temp']['spent'][$count]['cosmNames'].'" readonly/>
								<input name="spentV[]" type="number" class="short" step="1"    value="'.$_SESSION['temp']['spent'][$count]['spentV'].'" />
								<input name="spentC[]" type="number" class="short" step="0.01" value="'.$_SESSION['temp']['spent'][$count]['spentC'].'" readonly/>
								<input name="cosmID[]" type="hidden" value="'.$_SESSION['temp']['spent'][$count]['cosmID'].'" />
								<input name="spentID[]" type="hidden" value="'.$_SESSION['temp']['spent'][$count]['spentID'].'" />
								<input name="spentV_old[]" type="hidden" value="'.$_SESSION['temp']['spent'][$count]['spentV_old'].'" />
								<input name="spentC_old[]" type="hidden" value="'.$_SESSION['temp']['spent'][$count]['spentC_old'].'" />
								<input name="balanceGr[]" type="hidden" value="'.$_SESSION['temp']['spent'][$count]['balanceGr'].'" />
								<input name="cosmV[]" type="hidden" value="'.$_SESSION['temp']['spent'][$count]['cosmV'].'" />
								<input name="pcsOut[]" type="hidden" value="'.$_SESSION['temp']['spent'][$count]['pcsOut'].'" />
								<i class="fas fa-times inline-fa spent" title="'.lang::HANDLING_DELETE.'"></i>
							</div>';
							$count++;
						}?>
					</div>	
					<input type="button" value="<?=lang::BTN_ADD_SPENT;?>" onclick="spentAdd();" />
					<div class="row nested">
						<input type="text" class="input-hdr bold" value="<?=lang::HDR_TOTAL;?>" disabled />
						<input name="totalSpentV" type="number" class="input-hdr bold short" value="<?=correctNumber($_SESSION['temp']['totalSpentV'],0);?>" readonly />
						<input name="totalSpentC" type="number" class="input-hdr bold short" value="<?=correctNumber($_SESSION['temp']['totalSpentC'],0);?>" readonly />
					</div>	
				</div>
				
				<div id="salesData">
					<p class="title"><?=lang::HDR_SALES_LIST;?></p>
					<div class="row nested">
						<input type="text" class="input-hdr bold" value="<?=lang::HDR_ITEM_NAME;?>" disabled />
						<input type="text" class="input-hdr bold short" value="<?=lang::PLACEHOLDER_QTY;?>" disabled />
						<input type="text" class="input-hdr bold short" value="<?=lang::HDR_PRICE;?>" disabled />
						<input type="text" class="input-hdr bold short" value="<?=lang::HDR_TOTAL;?>" disabled />
					</div>	
					<div id="saletLines">
					
						<?php $s=0;
						while($_SESSION['temp']['sales'][$s] != null) {
							echo '<div class="row nested">
								<input name="soldName[]" type="text" class="input-hdr" value="'.$_SESSION['temp']['sales'][$s]['soldName'].'" readonly/>
								<input name="qty[]" type="number" class="short" step="1"    value="'.$_SESSION['temp']['sales'][$s]['qty'].'" max="'.$_SESSION['temp']['sales'][$s]['qty'].'" />
								<input name="priceSold[]" type="number" class="short" step="0.01" value="'.$_SESSION['temp']['sales'][$s]['priceSold'] / $_SESSION['temp']['sales'][$s]['qty'].'" />
								<input name="sold_price_total[]" type="number" class="short" step="0.01" value="'.$_SESSION['temp']['sales'][$s]['priceSold'].'" readonly/>
								<input name="sold_cosmID[]" type="hidden" value="'.$_SESSION['temp']['sales'][$s]['sold_cosmID'].'" />
								<input name="soldRowIDs[]" type="hidden" value="'.$_SESSION['temp']['sales'][$s]['soldRowIDs'].'" />
								<input name="priceSoldOld[]" type="hidden" value="'.$_SESSION['temp']['sales'][$s]['priceSoldOld'].'" />
								<input name="qtyOld[]" type="hidden" value="'.$_SESSION['temp']['sales'][$s]['qtyOld'].'" />
								<input name="sell_netto[]" type="hidden" value="'.$_SESSION['temp']['sales'][$s]['priceIn'].'"/>
								<input name="sell_available[]" type="hidden" />
								<i class="fas fa-times inline-fa sales" title="'.lang::HANDLING_DELETE.'"></i>
							</div>';
							$s++;
						}?>
					</div>	
					<input type="button" value="<?=lang::BTN_ADD_SALE;?>" onclick="saleAdd();" />
					<div class="row nested">
						<input type="text" class="input-hdr bold" value="<?=lang::HDR_TOTAL;?>" disabled />
						<input name="totalQty" type="number" class="input-hdr bold short" value="<?=$totalQty;?>" readonly />
						<input class="input-hdr bold short" disabled />
						<input name="totalSales" type="number" class="input-hdr bold short" value="<?=$totalSales;?>" readonly />
					</div>	
				</div>
				
				
				<p class="title"><?=lang::HDR_COST_PER_EMPLOYEE;?></p>
				<div class="row nested">
					<input class="input-hdr bold" 		 value="<?=lang::HDR_EMPLOYEE;?>"			  disabled />
					<input class="input-hdr bold medium" value="<?=lang::HDR_TOTAL . ', ' . curr();?>" 						  disabled />
					<input class="input-hdr bold medium" value="<?=lang::HDR_WAGE . ', ' . curr();?>" 				  disabled />
					<input class="input-hdr bold medium" value="<?=lang::HDR_TIPS . ', ' . curr();?>" disabled />
				</div>
				<div id="employees">
					
						<?php
						
						if($_GET['id'] > 0) {
							while($staffIDs[$e] != null) {
								echo '<div class="row nested">
									<input name="staffName[]" 		type="text"   class="input-hdr"		  value="' . $staffNames[$e] . '" 	  readonly	/>
									<input name="staffPrices[]" 	type="number"   step="0.01" class="medium" value="' . $staffPrices[$e] . '"   />
									<input name="staff_wage[]" 		type="number" 	step="0.01" class="medium" value="' . $staffWages[$e] . '" required />
									<input name="staffTips[]" 		type="number" 	step="0.01" class="medium" value="' . $staffTips[$e] . '"  />
									<input name="staffID[]" 		type="hidden"					 	  value="' . $staffIDs[$e] . '" 				/>
									<input name="staffRowIDs[]"		type="hidden" 						  value="' . $staffRowIDs[$e] . '" 			/>'; //id уже внесенного
									echo '<i class="far fa-comment fa-2x" title="'.lang::HDR_COMMENT.'" style="margin: auto -40px auto 0;"></i>';
								echo '</div>';
								echo '<div class="row"';
									if($staffComments[$e] == '') echo 'style="display:none"';
								echo '>
									<textarea name="staffComments[]" placeholder="'.lang::HDR_COMMENT.'" >'.$staffComments[$e].'</textarea>';
								echo '</div>';
								$e++;
							}
						} else if(isset($_GET['new']) && isset($_GET['recover'])) {
							while($staffIDs[$e] != null) {
								echo '<div class="row nested">
									<select name="staffID[]" >' . staff_select_options($_SESSION['locationSelected'], $staffIDs[$e]) . '</select>
									<i class="far fa-comment fa-2x" title="'.lang::HDR_COMMENT.'" style="margin: auto 0 auto 10px;"></i>
									<input name="staffPrices[]" 	type="number"   step="0.01" class="short" value="' . $staffPrices[$e] . '"   />
									<input name="staff_wage[]" 		type="number" 	step="0.01" class="short" value="' . $staffWages[$e] . '" required />
									<input name="staffTips[]" 		type="number" 	step="0.01" class="short" value="' . $staffTips[$e] . '"  />
									<i class="fas fa-times inline-fa staff" title="'.lang::HANDLING_DELETE.'"></i>
								</div>
								<div class="row"'; if($staffComments[$e] == '') echo 'style="display:none"'; echo '>
									<textarea name="staffComments[]" placeholder="'.lang::HDR_COMMENT.'" >'.$staffComments[$e].'</textarea>
								</div>';
								$e++;
							}
						} ?>
				</div>
				
				<div id="totals">
					<p class="title"><?=lang::HDR_VISIT_TOTALS;?></p>
					<div class="row nested">
						<input class="input-hdr bold" 		 value="<?=lang::HDR_ITEM_NAME;?>"	  disabled />
						<input class="input-hdr bold medium" value="<?=lang::HDR_PRICE;?>" 		  disabled />
						<input class="input-hdr bold medium" value="<?=lang::HDR_NETTO;?>" disabled />
						<input class="input-hdr bold medium" value="<?=lang::HDR_INCOME;?>" disabled />
					</div>
					<div class="row nested">
						<input class="input-hdr" 		 value="<?=lang::HDR_CUSTOMER_SERVICE;?>"	  readonly />
						<input name="totals_toPay_S" class="input-hdr medium" value="" 		  readonly />
						<input name="totals_netto_S" class="input-hdr medium" value="" readonly />
						<input name="totals_income_S" class="input-hdr bold medium" value="" readonly />
					</div>
					<div class="row nested">
						<input class="input-hdr" 		 value="<?=lang::HDR_SALES_LIST;?>"	  readonly />
						<input name="totals_toPay_sale" class="input-hdr medium" value="" 		  readonly />
						<input name="totals_netto_sale" class="input-hdr medium" value="" readonly />
						<input name="totals_income_sale" class="input-hdr bold medium" value="" readonly />
					</div>				
					<div class="row nested">
						<input class="input-hdr bold" 		 value="<?=lang::HDR_TOTAL;?>"	  disabled />
						<input name="grand_total_income" class="input-hdr bold medium" style="font-size: x-large; width: 100px; margin:5px -35px 5px 0;"disabled />
					</div>
					
				
				
				
					
				</div>
				
			</fieldset>
			<a class="button" href="/visits/visits_list.php?state=all&date=<?=$_GET['date'];?>" style="display: inline-block;margin: 5px 0;"><?=lang::BTN_CANCEL;?></a>
			<input type="submit" value="<?=lang::BTN_SAVE;?>" style="float: right;"/>
		</form>
		
	<?php } else {
		echo lang::ERR_NO_ID;
	}
	
	$staff_rates = array();
	foreach ($_SESSION['staff_rates'] as $rate) {
		$staff_rates[] = $rate;
	}
	?>
	
	
	
</section>

<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');?>
<script src="/js/dragula_dNd.js"></script>

  
<script>
dragula([document.getElementById('spentLines')]); //draggable container
const edit = <?php if($_GET['id'] > 0) echo $_GET['id']; else echo 0;?>;
if(edit > 0) {
	totalPrice();
	service_results_netto();
	salesTotals();
}
work_autocomplete();

var locationID = <?php if($_SESSION['locationSelected'] >0) echo $_SESSION['locationSelected']; else echo 0;?>;
if(locationID == 0) {
	$('select[name="loc"]').parent().siblings().hide();
}
$(document).ready(function () {
	$('select[name="loc"]').change(function(){
		if($(this).val() >0) {
			$(this).parent().siblings().show();
			$(this).css('color', 'black');
			locationID = $(this).val();
			
			 var xhttp_loc = new XMLHttpRequest();
				$.ajax({
				type: "POST",
				url: "set_location_ajax.php",
				data:	{ 'locationID': $(this).val() },  
				success: function(data){
				}
			});
			
		} else {
			$(this).parent().siblings().hide();
		}
	});
});


	
const ratesJson = <?=json_encode($staff_rates);?>;

function wage_rate(el){
	var catID = el.siblings('input[name="catID[]"]').val(); //el = input name="work[]"
	var userID = el.siblings('select[name="staff[]"]').find('option:selected').val();
	var rate;
	//alert(ratesJson[1]['userID']);
	
		
	var i = 0;
	while(ratesJson[i]['userID'] >0) {
		if(ratesJson[i]['userID'] == userID && ratesJson[i]['specialtyID'] == catID)
			rate = ratesJson[i]['reward_rate'];
		i++;
	}
	if (rate >= 0) el.siblings('input[name="rate[]"]').val(rate); 
	el.siblings("input[name='price[]']").focus();
}

function update_staff_total_list() {
	var newID = $(this).val();
	var newName = $(this).find('option:selected').text();
	//собираем перечень всех актуальных ID сотрудников
	var current_staff_IDs = [];
	$("select[name='staff[]']").each(function(){
		var toAdd = $(this).find('option:selected').val();
		current_staff_IDs.push(toAdd);
	});
	//собираем перечень всех ID сотрудников из итогов
	var totals_staff_IDs = [];
	$("input[name='staffID[]']").each(function(){
		var toAdd_totals = $(this).val();
		totals_staff_IDs.push(toAdd_totals);
	});
	
	//удаляем дубликаты
	current_staff_IDs = Array.from(new Set(current_staff_IDs));
	
	//прогоняем через результаты
	$("input[name='staffID[]']").each(function(){
		var present = $.inArray($(this).val(), current_staff_IDs)
		
		if (present == -1) {
			//Нужно удалить из итогов
			$(this).parent().next().remove();
			$(this).parent().remove();
		}
		var present_in_totals = $.inArray(newID, totals_staff_IDs)
		if (present_in_totals == -1) {
			staffAdd();
			$('input[name="staffID[]"]').last().val(newID);
			$('input[name="staffName[]"]').last().val(newName);
			totals_staff_IDs.push(newID);
		}
	});
	staff_total_prices();
	
	//обновляем ставку
	var sib_el = $(this).siblings('input[name="workNames[]"]');
	wage_rate(sib_el);
	
}

function minMaxTotals() {
	var min = 0;
	var max = 0;
	 $("input[name='minPrice[]']").each(function(){
		min += +$(this).val();
	});
	$("input[name='maxPrice[]']").each(function(){
		max += +$(this).val();
	});
	$("input[name='min']").val(min.toFixed(2));
	$("input[name='max']").val(max.toFixed(2));
}

function totalPrice() {
	var totalPrice = 0;
	$("input[name='price[]']").each(function(){
		totalPrice += +$(this).val();
	});
	$("input[name='price_total']").val(totalPrice.toFixed(2));
	$("input[name='totals_toPay_S']").val(totalPrice.toFixed(2));
	service_income();
}

function staff_total_prices(){ //итоги по сотрудникам
		$("input[name='staffID[]']").each(function(){
		var el = $(this);
		var staff_total = 0;
		var staff_wage = 0;
		$("input[name='price[]']").each(function(){
			if ($(this).siblings("select[name='staff[]']").find('option:selected').val() == el.val()) {
				staff_total += +$(this).val();
				
				staff_wage += + $(this).siblings("input[name='rate[]']").val()/100 * $(this).val();
				
			}
		});
		el.siblings("input[name='staffPrices[]']").val(staff_total);
		el.siblings("input[name='staff_wage[]']").val(staff_wage);
		service_results_netto();
	});
}

function totalNetto() {
	var totalNetto = 0;
	$("input[name='nettoCost[]']").each(function(){
		totalNetto += +$(this).val();
	});
	$("input[name='netto_total']").val(totalNetto.toFixed(2));
	service_results_netto();
}

function service_results_netto() {
	var total_serv_netto = 0;
	 $("input[name='staff_wage[]']").each(function(){
		if($(this).val() != 'undefined')
			total_serv_netto += +$(this).val();
	});
	
	if($("input[name='totalSpentC']").val() != 'undefined' && $("input[name='netto_total']").val() != 'undefined') {
		total_serv_netto += + +$("input[name='netto_total']").val() + +$("input[name='totalSpentC']").val();
	} else if ($("input[name='totalSpentC']").val() != 'undefined') {
		total_serv_netto += + +$("input[name='totalSpentC']").val();
	} else if ($("input[name='netto_total']").val() != 'undefined') {
		total_serv_netto += + +$("input[name='netto_total']").val();
	} 
	
	$("input[name='totals_netto_S']").val(total_serv_netto.toFixed(2));
	service_income();
}

function service_income(){
	var income = +$("input[name='totals_toPay_S']").val() - +$("input[name='totals_netto_S']").val();
	$("input[name='totals_income_S']").val(income.toFixed(2));
	grand_total_income();
}

function spentTotals() {
	var spent_volume = 0;
	var spent_cost = 0;
	$("input[name='spentC[]']").each(function(){
		spent_cost += +$(this).val();
	});
	$("input[name='spentV[]']").each(function(){
		spent_volume += +$(this).val();
	});
	
	$("input[name='totalSpentV']").val(spent_volume);
	$("input[name='totalSpentC']").val(spent_cost.toFixed(2));
	service_results_netto();
}

function salesTotals() {
	var Qty_s = 0;
	var totalSales = 0;
	var total_sales_netto = 0;
	$("input[name='qty[]']").each(function(){
		Qty_s += +$(this).val(); //общее количество
		
		var cur_qty = $(this).val();
		var cur_netto_prices = $(this).siblings("input[name='sell_netto[]']").val().split(",");
		var qty_count = 0;
		while(cur_qty >= (qty_count+1)) {
			total_sales_netto  += +cur_netto_prices[qty_count];
			qty_count++;
		}
		
		
	});
	$("input[name='sold_price_total[]']").each(function(){
		totalSales += +$(this).val();
	});
	
	$("input[name='totalQty']").val(Qty_s);
	$("input[name='totalSales']").val(totalSales.toFixed(2));
	$("input[name='totals_toPay_sale']").val(totalSales.toFixed(2));
	$("input[name='totals_netto_sale']").val(total_sales_netto.toFixed(2));
	
	var sales_income = totalSales - total_sales_netto;
	$("input[name='totals_income_sale']").val(sales_income.toFixed(2));
	
	grand_total_income();
}

function grand_total_income() {
	var service = $("input[name='totals_income_S']").val();
	var sales = $("input[name='totals_income_sale']").val();
	var income = 0;
	if (service != 'undefined' && sales != 'undefined' ) {
		income = +service + +sales;
	} else if (service != 'undefined'){
		income = service;
	} else if (sales != 'undefined') {
		income = sales;
	}
	$("input[name='grand_total_income']").val(income.toFixed(2));
}

//Добавление строки работ
var _counter_WRK = <?=$i;?>;
var template_WRK = document.querySelector("#wrk");
var _counter_NETTO = <?=$n;?>;
var template_NETTO = document.querySelector("#net");
var documentFragment_WRK = template_WRK.content;
function workAdd() {
	var oClone_WRK = template_WRK.content.cloneNode(true);
	
	oClone_WRK.id += (_counter_WRK + "");
	document.getElementById("works").appendChild(oClone_WRK);
	
	
	
	work_autocomplete();
	
	_counter_WRK++;
	
	
	/*$('.staffName'+_counter_WRK).autocomplete({
		serviceUrl: '/config/autocomplete.php?staffName',
		minChars:2,
		autoSelectFirst: true,
		preventBadQueries: false,
		onSelect: function (suggestion) {
			var res = suggestion.data.split("--");
			$(this).siblings("input[name='staffID[]']").val(res[0]);
		}
	});*/
}

//Добавление строки сотрудника
var _counter_STF = <?=$e;?>;
var template_STF = document.querySelector("#stf");
var documentFragment_STF = template_STF.content;
function staffAdd() {
	_counter_STF++;
	var oClone_STF = template_STF.content.cloneNode(true);
	
	oClone_STF.id += (_counter_STF + "");
	document.getElementById("employees").appendChild(oClone_STF);
	
	//Кликабельный коммент
	$(".fa-comment").last().addClass('comment'+_counter_STF);
	$(".comment"+_counter_STF).on('click', function() {
		$(this).parent().next().toggle();
	});	
}

//Добавление строки расхода
var _counter_spent = <?=$count;?>;
var template_spent = document.querySelector("#spent");
var documentFragment_spent = template_spent.content;
function spentAdd() {
	_counter_spent++;
	var oClone_spent = template_spent.content.cloneNode(true);
	
	oClone_spent.id += (_counter_spent + "");
	document.getElementById("spentLines").appendChild(oClone_spent);
	
	//Уникальный класс для добавленного поля
	$('input[name="cosmNames[]"]').last().addClass('spent'+_counter_spent);
			
	$('.spent'+_counter_spent).autocomplete({
		serviceUrl: '/config/autocomplete.php?spent&locationID='+locationID,
		minChars:2,
		autoSelectFirst: true,
		preventBadQueries: false,
		onSelect: function (suggestion) {
			var res = suggestion.data.split("--");
			
			
			var alreadySpent = 0;
			 $("input[name='cosmID[]']").each(function(){
				 if  ($(this).val() == res[0]) {
					 if ($(this).siblings("input[name='spentID[]']").val() > 0 ) { 
						//do nothing
					 } else {
					  alreadySpent += + $(this).siblings("input[name='spentV[]']").val();
					 }
				 }
			});
			
			var balance = res[1] - alreadySpent;
			$(this).siblings("input[name='cosmID[]']").val(res[0]);
			$(this).siblings("input[name='spentV[]']").prop('placeholder', balance);
			$(this).siblings("input[name='spentV[]']").prop('max', balance);
			
			//$(this).width(input_width);
			//$(this).siblings("input[name='spentV[]']").css('width', '400px');
			
			$(this).siblings("input[name='balanceGr[]']").val(balance);
			$(this).siblings("input[name='cosmV[]']").val(res[2]);
			$(this).siblings("input[name='pcsOut[]']").val(res[3]);
		}
	});
	
}

//Добавление строки продажи
var _counter_sales = <?=$s;?>;
var template_sales = document.querySelector("#sold");
var documentFragment_spent = template_sales.content;
function saleAdd() {
	_counter_sales++;
	var oClone_sales = template_sales.content.cloneNode(true);
	
	oClone_sales.id += (_counter_sales + "");
	document.getElementById("saletLines").appendChild(oClone_sales);
	
	//Уникальный класс для добавленного поля
	$('input[name="soldName[]"]').last().addClass('sold'+_counter_sales);
			
	$('.sold'+_counter_sales).autocomplete({
		serviceUrl: '/config/autocomplete.php?sold&locationID='+locationID,
		minChars:2,
		autoSelectFirst: true,
		preventBadQueries: false,
		onSelect: function (suggestion) {
			var res = suggestion.data.split("--");
			$(this).siblings("input[name='sold_cosmID[]']").val(res[0]);
			$(this).siblings("input[name='qty[]']").prop('placeholder', res[1]);
			$(this).siblings("input[name='qty[]']").prop('max', res[1]);
			$(this).siblings("input[name='priceSold[]']").val(res[2]);
			$(this).siblings("input[name='sell_netto[]']").val(res[3]); //аггрегированные через запятую суммы
			$(this).siblings("input[name='sell_available[]']").val(res[4]); //аггрегированные через запятую айдишки
		}
	});
		
}

//удаление для уже внесенных позиций
$("i.work").on('click', function() {
	$(this).siblings("input[name='workID[]']").val(0);
	$(this).siblings("input[name='minPrice[]']").val(0);
	$(this).siblings("input[name='maxPrice[]']").val(0);
	$(this).parent().hide();
	minMaxTotals();
	
	var isOld = $(this).siblings("input[name='visits_worksIDs[]']").val();
	if(isOld > 0) {
	} else {
		$(this).siblings().prop('disabled', true);
	}
});
$("i.staff").on('click', function() {
	$(this).siblings("input[name='staffID[]']").val(0);
	$(this).siblings("input[name='staffPrices[]']").val(0);
	$(this).siblings("input[name='staffTips[]']").val(0);
	$(this).parent().hide();
	$(this).parent().next().children("textarea").val('');
	$(this).parent().next().children("textarea").hide();
	totalPrice();
	
	var isOld = $(this).siblings("input[name='staffRowIDs[]']").val();
	if(isOld > 0) {
	} else {
		$(this).siblings().prop('disabled', true);
	}
	
});
$("i.spent").on('click', function() {
	$(this).siblings("input[name='cosmID[]']").val(0);
	$(this).siblings("input[name='spentV[]']").val(0);
	$(this).siblings("input[name='spentC[]']").val(0);
	$(this).parent().hide();
	spentTotals();
	var isOld = $(this).siblings("input[name='spentID[]']").val();
	if(isOld > 0) {
	} else {
		$(this).siblings().prop('disabled', true);
	}
});	
$("i.sales").on('click', function() {
	$(this).siblings("input[name='sold_cosmID[]']").val(0);
	$(this).siblings("input[name='qty[]']").val(0);
	$(this).siblings("input[name='priceSold[]']").val(0);
	$(this).siblings("input[name='sold_price_total[]']").val(0);
	$(this).parent().hide();
	salesTotals();
	var isOld = $(this).siblings("input[name='soldRowIDs[]']").val();
	if(isOld != '') {
	} else {
		$(this).siblings().prop('disabled', true);
	}
});	

$(".fa-comment").on('click', function() {
	$(this).parent().next().toggle();
});


$(document).on("change", function() {
	//добавление итогов по сотрудникам
	$("select[name='staff[]']").change(update_staff_total_list);
	
	
	//удаление значений min-max при пустом поле названия косметики
	$("input[name='workNames[]']").on('keyup blur', function(){
		var workName	 = $(this).val();
		if(workName == '') {
			$(this).siblings("input[name='workID[]']").val(0);
			$(this).siblings("input[name='minPrice[]']").val(0);
			$(this).siblings("input[name='maxPrice[]']").val(0);
			
		}
		minMaxTotals(); //пересчет итогов
	});
	//пересчет итогов по сотрудникам
	$("input[name='price[]']").on('keyup blur', function(){
		totalPrice();
		staff_total_prices();
	});
	//пересчет итогов по цене нетто
	$("input[name='nettoCost[]']").on('keyup blur', function(){
		totalNetto()
	});
	$("input[name='netto_total']").on('keyup blur', function(){
		service_results_netto();
	});
	
	
	
	//пересчет итогов по расходной косметике
	$("input[name='spentV[]']").on('blur', function(){
		var currentV = $(this).val();
			
		//старая ли запись?
		if ($(this).siblings("input[name='spentV_old[]']").val() > 0) {
			//старая
			var oldV = $(this).siblings("input[name='spentV_old[]']").val();
			var oldC = $(this).siblings("input[name='spentC_old[]']").val();
			var costPerGram = oldC / oldV;
			var spentC = costPerGram * currentV;
			$(this).siblings("input[name='spentC[]']").val(spentC);
			
		} else { // новая
			var id = $(this).siblings("input[name='cosmID[]']").val();
			var volume = $(this).siblings("input[name='cosmV[]']").val();
			var balance = $(this).prop('max').valueOf();
			var pcsOut = $(this).siblings("input[name='pcsOut[]']").val();
			var thisPCsRemainGr = balance % volume;
			
			var input = $(this);
			
			if(currentV > 0) {
				var xhttp;    
				var xhttp2;    
				xhttp = new XMLHttpRequest();
				$.ajax({
					type: "GET",
					url: 'spent_price_ajax.php?locationID='+locationID+'&id='+id+'&pcsOut='+pcsOut,
					success: function(data){
						var costPerGram1 = data / volume;
						
						if(currentV <= thisPCsRemainGr) {
							var spentC = costPerGram1 * currentV;
							input.siblings("input[name='spentC[]']").val(spentC.toFixed(2));
						} else {
							var spentV1 = thisPCsRemainGr;
							var spentV2 = currentV - thisPCsRemainGr;
							var spentC1 = costPerGram1 * thisPCsRemainGr;
							
							xhttp2 = new XMLHttpRequest();
							$.ajax({
								type: "GET",
								url: 'spent_price_ajax.php?locationID='+locationID+'&id='+id+'&pcsOut='+pcsOut+'offset=ceil',
								success: function(data2){
									var costPerGram2 = data2 / volume;
									var spentC2 = costPerGram2 * spentV2;
									spentC = spentC1 + spentC2;
									input.siblings("input[name='spentC[]']").val(spentC.toFixed(2));
								}
							});
						}	
					}
				});
			}
		}
		spentTotals(); 
	});
	//пересчет итогов по продажам
	$("input[name='qty[]'], input[name='priceSold[]").on('keyup blur', function(){
		var q = $(this).parent().children("input[name='qty[]']").val();
		var p = $(this).parent().children("input[name='priceSold[]']").val();
		var limit = $(this).parent().children("input[name='qty[]']").prop('max').valueOf();
		var old = $(this).siblings("input[name='qtyOld[]']").val();
		if(q > limit && old > 0) {
			$(this).parent().children("input[name='qty[]']").val(limit);
			alert('<?=lang::ALERT_EXCEED_LIMIT;?>');
		}
		
		if(q > 0 && p > 0) {
			$(this).parent().children("input[name='sold_price_total[]']").val((p*q).toFixed(2));
		}
		salesTotals(); 
		
	});
	
	//апдейт итогов при ручном обновлении з/п
	$("input[name='staff_wage[]']").on('change', function(){
		service_results_netto(); 
	})
	
	
	
	
	//$("input[name='price_total'], input[name='netto_total'], input[name='totalSpentC'], input[name='totalSales']")	
	//	var sales_total_netto = 0;
	//	$("input[name='sell_netto[]']").each(function(){
	//		var salesNettoPrice = $(this).val();
	//		var qty = $(this).siblings("input[name='qty[]']").val();
	//		sales_total_netto += + salesNettoPrice * qty;
	//	});
	//	
	//	var service_income = $("input[name='price_total']").val() - $("input[name='netto_total']").val() - $("input[name='totalSpentC']").val();
	//	var sales_income = $("input[name='totalSales']").val() - sales_total_netto;
	//	
	//	
	//	//ставки сотрудников
	//	var catIDs = [];
	//	$("input[name='price[]']").each(function(){
	//		if ($(this).val() > 0) { 
	//			var catID = $(this).siblings("input[name='catID[]']").val();
	//			catIDs.push(catID);
	//		}
	//	});
	//	
	//	//$("select[name='staffID[]']").each(function(){
	//	//	var staffID = $(this).val();
	//	//	
	//	//});
	});	
	
	
	
	
	
	//удаление для новых позиций
	$("i.work").on('click', function() {
		$(this).siblings("input[name='visits_worksIDs[]']").val(0);
		$(this).siblings("input[name='minPrice[]']").val(0);
		$(this).siblings("input[name='maxPrice[]']").val(0);
		$(this).parent().hide();
		minMaxTotals();
	});
	$("i.spent").on('click', function() {
		$(this).siblings("input[name='cosmID[]']").val(0);
		$(this).siblings("input[name='spentV[]']").val(0);
		$(this).siblings("input[name='spentC[]']").val(0);
		$(this).parent().hide();
		spentTotals();
	});	
	$("i.sales").on('click', function() {
		$(this).siblings("input[name='sold_cosmID[]']").val(0);
		$(this).siblings("input[name='qty[]']").val(0);
		$(this).siblings("input[name='priceSold[]']").val(0);
		$(this).siblings("input[name='sold_price_total[]']").val(0);
		$(this).parent().hide();
		salesTotals();
	});	

	
	//Статус визита 
	var currTime = Date.now();
	$("input[name='date'], select[name='state']").on('change', function(){
		var visit_state = $("select[name='state']").val();
		var datefull = $("input[name='date']").val();
		date = datefull.split("-");
		var time = Date.UTC(date[0],date[1]-1,date[2]);
		
		if(currTime > time)	{
			if (visit_state == 10 ) {
				$("#spentData").show();
			} else {
				$("#spentData").hide();
			}
		}
		else  { //в будущем
			$("#spentData").hide();
			if (visit_state == 10) {
				alert('<?=lang::ALERT_WRONG_DATE_STATE;?>');
				$("#spentData").hide();
				$("select[name='state']").val('');
			}
		}


		$("select[name='state']").change(function(){
			var visit_state = $("select[name='state']").val();
			if (visit_state == 10 ) {
				$("#spentData").show();
				$("#totals").show();
			} else {
				$("#spentData").hide();
				$("#totals").hide();
			}
		});
});

function work_autocomplete() {
	//Уникальный класс для добавленного поля
	$('input[name="workNames[]"]').last().addClass('workName'+_counter_WRK);
	
	
	$('.workName'+_counter_WRK).autocomplete({
		serviceUrl: '/config/autocomplete.php?workName',
		minChars:2,
		autoSelectFirst: true,
		preventBadQueries: false,
		onSelect: function (suggestion) {
			var res = suggestion.data.split("--");
			var el = $(this);
			el.siblings("input[name='workID[]']").val(res[0]);
			el.siblings("input[name='minPrice[]']").val(res[1]);
			el.siblings("input[name='maxPrice[]']").val(res[2]);
			el.siblings("input[name='catID[]']").val(res[5]);
			
			//Сотрудники в выбранной категории
			var catID = res[5];
			
			//проверка, выбран ли уже сотрудник в данной услуге (нужно для редактирования услуги)
			if( el.siblings('select[name="staff[]"]').val() > 0) {
				var lastSelectVal = el.siblings('select[name="staff[]"]').val();
			} else {
				var lastSelectVal = el.parent().prev().children('select[name="staff[]"]').val();
			}
			var xhttp3 = new XMLHttpRequest();
			$.ajax({
				type: "GET",
				url: 'staff_data_ajax.php?locationID='+locationID+'&catID='+catID+'&lastSelected='+lastSelectVal,
				success: function(options){
					el.siblings('select[name="staff[]"]').empty().append(options);
					
					//Добавляем строку итога по сотруднику
						var name = el.siblings('select[name="staff[]"]').find('option:selected').text();
						var staff_id = el.siblings('select[name="staff[]"]').find('option:selected').val();
						if(_counter_WRK > 1 ) { //фактически = 0, т.к. это колбек функция
							var abort = 0;
							$("input[name='staffName[]']").each(function(){
								if( $(this).val() == name ) abort = 1;
							});
							if (abort == 0) {
								staffAdd();
								$('input[name="staffName[]"]').last().val(name);
								$('input[name="staffID[]"]').last().val(staff_id);
							}
						} else {
							staffAdd();
							$('input[name="staffName[]"]').last().val(name);
							$('input[name="staffID[]"]').last().val(staff_id);
						}
						
					//ставка з/п
					wage_rate(el);
					
			
				}
			});
			
			
			//себестоимость работ по данной услуге
			var newNettoNames = res[3].split("|");
			var newNettoCosts = res[4].split("|");
			
			
			var netName = null;
			var netCost = null;
			var x = 0;
			while(newNettoNames[x] != null) {
				var iterations = 0;
				var notEqual = 0;
				
				
				$("input[name='nettoNames[]']").each(function(){
					var exist = $(this).val();
					if (newNettoNames[x] != exist) {
						netName = newNettoNames[x];
						netCost = newNettoCosts[x];
						notEqual++;
					}
					iterations++;
				});
				
				if(notEqual == iterations) {
					var oClone_NETTO = template_NETTO.content.cloneNode(true);
	
					oClone_NETTO.id += (_counter_NETTO + "");
					document.getElementById("netto").appendChild(oClone_NETTO);
					
					$('input[name="nettoNames[]"]').last().val(netName);
					$('input[name="nettoCost[]"]').last().val(netCost);
					totalNetto();
					
					_counter_NETTO++;
				} 
				
				netName = null;
				netCost = null;
				x++;
			}
		}
	});
}
</script>
