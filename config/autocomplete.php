<?php require ('connect.php');
$key="%".$_GET['query']."%";

if (isset($_GET['city'])) {
	$sql="SELECT city
		  FROM locations
 		  WHERE city LIKE :key
		  GROUP BY city
 		  ORDER BY city";
	include('autocomplete-pdo.php');
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$reply['suggestions'][]= $row['city'];
		
	}
}

if (isset($_GET['worktype_cat'])) {
	$sql="SELECT DISTINCT category, id 
		  FROM worktype_cat
		  WHERE category LIKE :key  AND archive = 0
		  ORDER BY category";
	include('autocomplete-pdo.php');
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		//$reply['suggestions'][]=$row['category'];
		$reply['suggestions'][]= array('value' => $row["category"], 'data' => $row["id"]);
	}
}

if (isset($_GET['catID'])) {
	$sql="SELECT DISTINCT category, expences_cat.id 
		  FROM expences_cat
		  LEFT JOIN expences_subcat ON expences_subcat.catID = expences_cat.id
		  WHERE category LIKE :key AND expences_subcat.archive = 0
		  ORDER BY category";
	include('autocomplete-pdo.php');
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$reply['suggestions'][]= array('value' => $row["category"], 'data' => $row["id"]);
	}
}

if (isset($_GET['FIO'])) {
	$sql="SELECT id,
			  CASE 
				WHEN LENGTH(surname) AND LENGTH(prompt) THEN CONCAT(name,' ',surname,' (',prompt,')')
				WHEN LENGTH(surname) THEN CONCAT(name,' ',surname)
				WHEN LENGTH(prompt) THEN CONCAT(name,' (',prompt,')')
				ELSE name
			   END AS client_name
      	  FROM `clients`
 		  WHERE name LIKE :key OR surname LIKE :key OR prompt LIKE :key
 		  ORDER BY client_name";
	include('autocomplete-pdo.php');
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$reply['suggestions'][]=array('value' => $row['client_name'], 'data' => $row["id"]);
	}
}

if (isset($_GET['clientProfile'])) {
	$sql="SELECT id,
			  CASE 
				WHEN LENGTH(surname) AND LENGTH(prompt) AND LENGTH(phones) THEN CONCAT(name,' ',surname,' (',prompt,') ',phones)
				WHEN LENGTH(surname) AND LENGTH(prompt) THEN CONCAT(name,' ',surname,' (',prompt,')')
				WHEN LENGTH(surname) AND LENGTH(phones) THEN CONCAT(name,' ',surname, ' ',phones)
				WHEN LENGTH(prompt) AND LENGTH(phones) THEN CONCAT(name, ' (',prompt,') ', phones)
				WHEN LENGTH(surname) THEN CONCAT(name,' ',surname)
				WHEN LENGTH(prompt) THEN CONCAT(name,' (',prompt,')')
                WHEN LENGTH(phones) THEN CONCAT(name,' ',phones)
				ELSE name
			   END AS client_name
      	  FROM `clients`
 		  WHERE name LIKE :key OR surname LIKE :key OR prompt LIKE :key OR phones LIKE :key
 		  ORDER BY client_name";
	include('autocomplete-pdo.php');
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$reply['suggestions'][]=array('value' => $row['client_name'], 'data' => $row["id"]);
	}
}

if (isset($_REQUEST['supplierID'])) {
	if ($_REQUEST['supplierID'] !='') $supplierID='supplier_brands.supplierID='.$_REQUEST['supplierID'];
	else $supplierID=1;
	$sql="SELECT DISTINCT cosmetics.id, cosmetics.RRP, cosmetics.archive,
			  CASE 
				WHEN LENGTH(articul) THEN CONCAT(articul,', ', brands.name, ' ', cosmetics.name,', ',volume)
				ELSE CONCAT(brands.name, ' ', cosmetics.name,', ',volume)
			   END AS cosm_name
      	  FROM `cosmetics`
          LEFT JOIN brands ON cosmetics.brandID=brands.id
          LEFT JOIN supplier_brands ON brands.id = supplier_brands.brandID
 		  WHERE (cosmetics.name LIKE :key OR brands.name LIKE :key OR articul LIKE :key) AND $supplierID
 		  ORDER BY cosm_name";
	include('autocomplete-pdo.php');
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$reply['suggestions'][]=array('value' => $row['cosm_name'], 'data' => $row["id"] . '--' . $row['RRP'] . '--' . $row['archive']);
	}
}

if (isset($_GET['sale'])) {
	$sql="SELECT cosmetics.id, 
			  CASE 
				WHEN LENGTH(articul) THEN CONCAT(articul,', ', brands.name, ' ', cosmetics.name,', ',volume)
				ELSE CONCAT(brands.name, ' ', cosmetics.name,', ',volume)
			   END AS cosm_name
      	  FROM `cosmetics`
          LEFT JOIN brands ON cosmetics.brandID=brands.id
          WHERE (cosmetics.name LIKE :key OR brands.name LIKE :key OR articul LIKE :key)
			AND purpose IN (1,2)
 		  ORDER BY cosm_name";
	include('autocomplete-pdo.php');
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$reply['suggestions'][]=array('value' => $row['cosm_name'], 'data' => $row['id']);
	}
}

if (isset($_GET['workName'])) {
	$sql="SELECT worktypes.id, worktypes.name, minPrice, maxPrice, worktypes.catID
			, GROUP_CONCAT(service_netto.name SEPARATOR '|') as nettoName
			, GROUP_CONCAT(service_netto.cost SEPARATOR '|') as nettoCost
		FROM `worktypes` 
		LEFT JOIN worktype_netto ON worktypes.catID = worktype_netto.catID
		LEFT JOIN service_netto ON worktype_netto.nettoID = service_netto.id
		WHERE worktypes.name LIKE :key
		GROUP BY worktypes.id";
	include('autocomplete-pdo.php');
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$reply['suggestions'][]=array('value' => $row['name'], 'data' => $row["id"] . '--' . $row["minPrice"] . '--' . $row["maxPrice"] . '--' . $row['nettoName']  . '--' . $row['nettoCost']  . '--' . $row['catID']);
	}	
}


if (isset($_GET['staffName'])) {
	$sql="SELECT DISTINCT users.id
			  , CONCAT (users.name, ' ', users.surname) AS user
              , GROUP_CONCAT(DISTINCT users_specialty.specialtyID) as workCatIDs
      	  FROM `users`
 		   LEFT JOIN users_locations ON users.id = users_locations.userID
 		   LEFT JOIN users_specialty ON users.id = users_specialty.userID
 		   WHERE locationID = :locationID
			AND name LIKE :key OR surname LIKE :key
 		    AND specialtyID IS NOT NULL
           GROUP BY users.id";
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':key', $key, PDO::PARAM_STR);
	$stmt->bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
	$stmt->execute();
	$reply = array();
	$reply['query'] = $type_in;
	$reply['suggestions'] = array();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$reply['suggestions'][]=array('value' => $row['user'], 'data' => $row["id"].'--'.$row['workCatIDs']);
	}
}

if (isset($_GET['spent'])) {
	//if($_GET['locationID'] > 0) $locationID = ;
	$sql="SELECT DISTINCT cosmetics.id, CONCAT(brands.name, ' ', cosmetics.name) as cosm_name, volume AS cosm_volume
		, tbl_in.pcsIn
        , (CASE
                WHEN tbl_out.vol_Out IS NULL THEN 0
                ELSE (tbl_out.vol_Out / cosmetics.volume) 
           END) as pcsOut
        , SUM(received.qtyOut) as add_out
        , (CASE
                WHEN tbl_out.vol_Out IS NULL THEN (tbl_in.pcsIn * cosmetics.volume)
                ELSE (tbl_in.pcsIn * cosmetics.volume - tbl_out.vol_Out)
           END) as balance
        FROM `cosmetics` 
		LEFT JOIN brands ON cosmetics.brandID = brands.id
		LEFT JOIN received ON cosmetics.id = received.cosmID
		LEFT JOIN invoices ON received.invoiceID = invoices.id
		LEFT JOIN locations ON invoices.locationID = locations.id
        LEFT JOIN (
       	 	select cosmID, SUM(qtyIn) as pcsIn
            from received
            left join invoices on received.invoiceID=invoices.id
            WHERE invoices.locationID = :locationID  
            GROUP BY cosmID
        ) tbl_in ON cosmetics.id = tbl_in.cosmID
        LEFT JOIN (
       	 	select cosmID, SUM(volume) as vol_Out
            from spent
            left join visits on spent.visitID = visits.id
            where visits.locationID = :locationID  
            GROUP BY cosmID
        ) tbl_out ON cosmetics.id = tbl_out.cosmID
		WHERE cosmetics.purpose IN (0,2)
			AND cosmetics.archive = 0
			AND invoices.state >= 4
			AND locationID = :locationID  
			AND (cosmetics.name LIKE :key OR brands.name LIKE :key)
		GROUP BY cosmetics.id
		ORDER BY cosm_name";
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':key', $key, PDO::PARAM_STR);
	$stmt->bindValue(':locationID', $_GET['locationID'], PDO::PARAM_INT);
	$stmt->execute();
	$reply = array();
	$reply['query'] = $type_in;
	$reply['suggestions'] = array();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$pcsOut = $row["pcsOut"] + $row["add_out"]; // для "универсальной" косметики
		$balance = $row["balance"] - $row["add_out"] * $row["cosm_volume"];
		$reply['suggestions'][]=array('value' => $row['cosm_name'], 'data' => $row["id"] .'--'. round($balance,0) . '--' . $row["cosm_volume"] . '--' . $pcsOut);
	}
}

if (isset($_GET['sold'])) {
	$sql="SELECT GROUP_CONCAT(received.id) as rowIDs, RRP, GROUP_CONCAT(priceIn) as pricesNetto
			, cosmetics.id as cosmID 
			, CASE 
				WHEN LENGTH(articul) THEN CONCAT(articul,', ', brands.name, ' ', cosmetics.name,', ',volume)
				ELSE CONCAT(brands.name, ' ', cosmetics.name,', ',volume)
			  END AS cosm_name
            , CASE 
            	WHEN tbl_pcs_out.minus_qty_pcs is not null
            	THEN tbl_pcs_out.minus_qty_pcs
            	ELSE 0
            	END as minus_qty_pcs 
			, SUM(received.qtyIn) as qtyIn
			, CASE 
            	WHEN (SUM(received.qtyOut) + tbl_out.vol_Out / cosmetics.volume) is not null
            	THEN CEIL((SUM(received.qtyOut) + tbl_out.vol_Out / cosmetics.volume))
            	ELSE 0
            	END  as pcsOut
		FROM `received`
		LEFT JOIN cosmetics ON received.cosmID = cosmetics.id
		LEFT JOIN brands ON cosmetics.brandID = brands.id
		LEFT JOIN invoices ON received.invoiceID = invoices.id
		LEFT JOIN (
					select cosmID, SUM(volume) as vol_Out
					from spent
					left join visits on spent.visitID = visits.id
					where visits.locationID = :locationID
					GROUP BY cosmID
				) tbl_out ON cosmetics.id = tbl_out.cosmID
        LEFT JOIN (
           select cosmID, SUM(qtyIn) as minus_qty_pcs
            from received
            left join invoices on received.invoiceID = invoices.id
            where invoices.locationID = :locationID
               and qtyIn < 1
        ) tbl_pcs_out ON received.cosmID = tbl_pcs_out.cosmID
		WHERE received.qtyOut = 0
			AND cosmetics.purpose IN (1,2)
			AND invoices.state >=4
			AND received.qtyIn = 1
			AND invoices.locationID = :locationID
			AND (cosmetics.name LIKE :key OR brands.name LIKE :key OR articul LIKE :key)
		GROUP BY cosmetics.id";
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':key', $key, PDO::PARAM_STR);
	$stmt->bindValue(':locationID', $_GET['locationID'], PDO::PARAM_INT);
	$stmt->execute();
	$reply = array();
	$reply['query'] = $type_in;
	$reply['suggestions'] = array();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$balance = $row['qtyIn'] - $row['pcsOut'] - $row['minus_qty_pcs'];
		$reply['suggestions'][]=array('value' => $row['cosm_name'], 'data' => $row["cosmID"] .'--'. round($balance,0) .'--'. $row['RRP'] . '--' . $row["pricesNetto"] . '--' . $row["rowIDs"]);
	}
}


echo json_encode($reply);
$pdo = NULL;?>