<?php
function select_lang($selectOnly = 0) { //возвращает форму выбора языка
//Если установлен $selectOnly - выдает только поле формы
	if (!isset($_SESSION['lang_options'])) {
		$lang_options_raw = scandir($_SERVER['DOCUMENT_ROOT'] . '/lang/');
		$lang_options = array();
		foreach ($lang_options_raw as $lang) {
			if ($lang != '.' && $lang != '..') {
				$lang_options[]= str_replace ('.php','', $lang);
			}
			
		}
	$_SESSION['lang_options'] = $lang_options;
	}
	
	if ($selectOnly == 0) {
		echo '<form method="post">
			<fieldset class="noBorders">
				<div class="inline">
					<select name="lang" id="lang">';
						foreach ($_SESSION['lang_options'] as $option) {
							switch ($option) {
								case 'ru':
									echo '<option value="ru">Русский</option>';
									break;
								case 'ua':
									echo '<option value="ua">Українська</option>';
									break;
								case 'en':
									echo '<option value="en">English</option>';
									break;
								default:
									echo '<option value="'.$option.'">'.$option.'</option>';
									break;
							}
						}
					echo '</select>
					<input type="submit" value="'.lang::BTN_CHANGE.'" />
				</div>
			</fieldset>
		</form>';
	} else {
		echo '<div class="row">
			<label for="lang">'.lang::LANGUAGE.':</label>
			<select name="lang" id="lang" >';
				foreach ($_SESSION['lang_options'] as $option) {
					switch ($option) {
						case 'ru':
							echo '<option value="ru">Русский</option>';
							break;
						case 'ua':
							echo '<option value="ua">Українська</option>';
							break;
						case 'en':
							echo '<option value="en">English</option>';
							break;
						default:
							echo '<option value="'.$option.'">'.$option.'</option>';
							break;
					}
				}
			echo '</select>
		</div>';
	}
					
}


function role_options($selected=null) { // Список доступных ролей (select)
	$options=array(
		array('id' => 'godmode', 'power' => 99, 'text' => lang::LEVEL_GODMODE),
		array('id' => 'general', 'power' => 90, 'text' => lang::LEVEL_GENERAL),
		array('id' => 'user', 'power' => 10, 'text' => lang::LEVEL_ADMIN),
		array('id' => 'basic', 'power' => 1, 'text' => lang::LEVEL_BASIC)
	);
	
	echo '<option value="">'. lang::SELECT_DEFAULT . '</option>';
	foreach ($options as $option) {
		if ($_SESSION['pwr'] >= $option['power']) {
			if ($option['id'] == $selected) echo '<option value="'.$option['id'].'" selected>'. $option['text'] . '</option>';
			else echo '<option value="'.$option['id'].'">'. $option['text'] . '</option>';
		}
	}
}

function handle_rights($userRole, $userLocationIDs){
	switch (true)
	{
		case ($_SESSION['role'] == 'godmode'):
			$accessGranted = 1;
			break;
		//case ($_SESSION['role'] == 'general' && $userRole == 'general'):
		case ($_SESSION['role'] == 'general' && $userRole == 'user'):
		case ($_SESSION['role'] == 'general' && $userRole == 'basic'):
		case ($_SESSION['role'] == 'user' && $userRole == 'user'):
		case ($_SESSION['role'] == 'user' && $userRole == 'basic'):
			
			$myIDs=explode(',',$_SESSION['locationIDs']);
			$locationIDs = explode(',',$userLocationIDs);
			
			$i=0;
			$n=0;
			foreach($locationIDs as $editID) {
				foreach($myIDs as $id) {
					if($editID == $id) $i++;
				}
				$n++;
			}
			if($i >= $n) $accessGranted=1;
			break;
		
		default:
			$accessGranted = 0;
		
	}
	return $accessGranted;
	
}

function role_name($role) { //Преобразует роль в текстовое описание
	switch ($role) {
		case 'basic':
			$roleName = lang::LEVEL_BASIC;
			break;
		case 'multiuser':
		case 'user':
			$roleName = lang::LEVEL_ADMIN;
			break;
		case 'general':
			$roleName = lang::LEVEL_GENERAL;
			break;
		case 'godmode':
			$roleName = lang::LEVEL_GODMODE;
			break;
	}
	return $roleName;
}

function setLocationID() {
	switch(true)
	{
		case($_GET['loc'] !=''):
		case($_SESSION['locationSelected'] !=''):
			$locationID = $_SESSION['locationSelected'];
			break;
		case (isset($_SESSION['locationName'])):
			$locationID = $_SESSION['locationIDs'];
			break;
	}
	return $locationID;
}

// Адреса с проверкой на доступ
function location_options($select = 0, $tab = null, $checked = null, $noButtonRow = null, $filter=null) {
	/*
	1. Для категории USER выдается только SELECT c одним вариантом
	2. Если установлен $select = 0, салоны отдаются чекбоксами
	3. Если установлен $select = 1, салоны отдаются выпадающим списком
		3.1. Если установлен $tab, значение добавляется к адресу
		3.2. Если установлен $noButtonRow - отдает список для встраивания в форму, name='loc[]'
		3.3. Если установлен $filter - не показывает label в форме
	*/
	
	if(!isset($_SESSION['loc_options']))
	{
	
		require (dirname(__FILE__).'/connect.php');
		if ($_SESSION['role'] == 'godmode') {
			$q = $pdo->prepare("SELECT id, name FROM `locations` WHERE archive = 0 ORDER BY name");
			$q->execute();
			while ($id = $q->fetch(PDO::FETCH_ASSOC)) {
				$res[$id['id']]=$id['name'];
			}
		} else {
			$array = explode(',' , $_SESSION['locationIDs']);
			try {
				$stmt = $pdo->prepare("SELECT name FROM `locations` WHERE id = :option AND archive = 0");
				$stmt -> bindParam(':option', $option, PDO::PARAM_INT);
				$res = array();
				
				foreach ($array as $option) {
					$stmt->execute();
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$res[$option] = $row['name'];
					$_SESSION['loc_options'][$option] = $row['name'];
				}
				//
				asort($res);
			} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
				
		}
	} else $res = $_SESSION['loc_options'];
	
	// выпадающий список
	if ($select == 1) {	
		
		if(isset($_SESSION['locationSelected'])) $selected = $_SESSION['locationSelected'];
		
		if ($noButtonRow == null) {
			echo '<form method="get" class="filter">
				<fieldset class="noBorders">
						<select name="loc" class="filter title" id="header_loc" required>
							<option value="">'.lang::SELECT_DEFAULT.'</option>';
							foreach ($res as $key => $val) {
								echo '<option value="' . $key. '"';
								if($selected == $key) echo 'selected';
								echo'>' . $val . '</option>';
							}
						echo '</select>';
						if ($tab !=NULL) echo '<input type="hidden" name="tab" value="'. $tab.'" />';
						//echo'<input type="submit" class="filter title" value="'. lang::BTN_SHOW.'" />';
				echo'</fieldset>
			</form>';
		} else { // для встраивания в форму
			if($filter != 1) echo '<div class="row nested">';
				if($filter != 1) echo '<label for="loc">'. lang::HDR_LOCATION.'*:</label>';
				echo '<select name="loc" required>';
					if($_SESSION['locationName'] == null){
						if($filter == 1) '<option value="">'. lang::HDR_LOCATION.'</option>';
						else echo '<option value="">'. lang::SELECT_DEFAULT.'</option>';
					} 
					
					foreach ($res as $key => $val) {
								echo '<option value="' . $key . '"';
								switch (true)
								{
									case ($checked !=null && $checked == $key):
										echo 'selected';
										break;
									case ($selected !=null && $selected == $key):
										echo 'selected';
										break;
								}
								echo'>' . $val . '</option>';
							}
				echo '</select>';
			if($filter != 1) echo '</div>';
		}
			
	} else { // ЧЕК-БОКСЫ
		if($checked !='') 
		{ 
			$haystack = array();
			if (is_array($checked) == false) $haystack = explode(',',$checked);
			else {
				foreach ($checked as $k => $v)
				{
					$haystack[] = $k;
				}
			}
		}
	
		echo '<div class="row">';
				echo '<label>'. lang::HDR_LOCATION_PLURAL.'*:</label>';
				echo '<div class="flex">';
					foreach ($res as $key => $val){
						echo '<div class="inline">
							<input name="loc['. $key . ']" type="checkbox" value="' . $key .'"';
							switch(true)
							{
								case ($checked != null && in_array($key, $haystack)):
								case (isset($_SESSION['locationName']) && $_SESSION['locationIDs'] == $key):
								case ($_SESSION['locationSelected'] == $key):
									echo 'checked';
									break;
							}
							
							echo '>
							<label for="loc['.$key .']">' . $val . ' </label>';
						echo '</div>';
					}
				echo '</div>';
			echo '</div>';
	}	
}

function location_names_only($IDs, $coma = null) {// Отдает названия построчно или через запятую
	require (dirname(__FILE__).'/connect.php');
	
	if($_SESSION['loc_db'] == null) {
		try {
			$stmt = $pdo->prepare("SELECT id, name FROM `locations`");
			$stmt->execute();
			$loc_db = array();
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$loc_db[$row['id']] = $row['name'];
			}
			$_SESSION['loc_db'] = $loc_db;
		} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	}
	
	if ($IDs == 'all') {
		$result = 'все';
	} else {
		
		$array = explode(',' , $IDs);
		
		$i = '';
		$result = '';
		foreach ($array as $id){
			
			$result = $result . $i . $_SESSION['loc_db'][$id];
			if ($coma ==null) $i = '<br />';
			else $i = ', ';
		}
	}
	return $result;
}

function location_URL($location) {
	if(isset($_SESSION['locationName'])) $loc=$_SESSION['locationIDs'];
	else $loc = $location;
	return $loc;
}



function dayOff($date, $weekday, $even=null){
	switch(true){
		case ($date != null):
			$dayOff = correctDate($date);
			break;
		case ($even != null):
			if($even == 0)	$dayOff = lang::HDR_ODD;
			else			$dayOff = lang::HDR_EVEN;
			break;
		case ($weekday != null):
			$dayOff = dayOfWeek($weekday);
			break;
		default:
			$dayOff = '';
			break;
	}
	return $dayOff;
}


function user_select($locationID, $selected=null, $filter=null) {
	require (dirname(__FILE__).'/connect.php');
	
	try {
		$stmt = $pdo->prepare("SELECT DISTINCT users.id,
			  CONCAT (users.name, ' ', users.surname) AS user
			  FROM `users`
			  LEFT JOIN users_locations ON users.id = users_locations.userID
			  WHERE locationID = :locationID
			  ORDER BY user");
		$stmt -> bindParam(':locationID', $locationID, PDO::PARAM_INT);
		$stmt->execute();
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	
	
	
	if ($filter !=1) {
		echo '<div class="row">
		<label for="userID">' . lang::HDR_EMPLOYEE . ':</label>';
	}
		echo'<select name="userID" required>';
			if ($filter !=1) echo '<option value="">' . lang::SELECT_DEFAULT. '</option>';
			else echo '<option value="all">' . lang::HDR_EMPLOYEE . lang::SELECT_ADD_ALL. '</option>';
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				echo '<option value="'.$row['id'].'" ';
					if($row['id'] == $selected && $selected != '') echo 'selected';
				echo '>' . $row['user'] . '</option>';
			}
		echo '</select>';
if ($filter !=1) echo '</div>';
}

function staff_select_options($locationID, $selected=null) {
	require (dirname(__FILE__).'/connect.php');
	
	try {
		$stmt = $pdo->prepare("SELECT DISTINCT users.id,
			  CONCAT (users.name, ' ', users.surname) AS user
			  FROM `users`
			  LEFT JOIN users_specialty ON users.id = users_specialty.userID
              LEFT JOIN users_locations ON users.id = users_locations.userID
			  WHERE locationID = :locationID
              	AND users_specialty.specialtyID IS NOT NULL
				AND users.archive = 0
			  ORDER BY user");
		$stmt -> bindParam(':locationID', $locationID, PDO::PARAM_INT);
		$stmt->execute();
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	
	
		$options = '<option value="">' . lang::HDR_EMPLOYEE. '</option>';
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$options = $options . '<option value="'.$row['id'].'" ';
				if($row['id'] == $selected && $selected != '') $options = $options . 'selected';
			$options = $options . '>' . htmlspecialchars($row['user']) . '</option>';
		}
		return $options;
}

function get_staff_cat_wages() {
	require (dirname(__FILE__).'/connect.php');
	try {
		$stmt = $pdo->prepare("SELECT DISTINCT users_specialty.id, users_specialty.userID, specialtyID, reward_rate 
			FROM `users_specialty` 
			LEFT JOIN users_locations ON users_specialty.userID = users_locations.userID
			WHERE locationID IN (:locationIDs) AND reward_rate >= 0");
		$stmt -> bindParam(':locationIDs', $_SESSION['locationIDs'], PDO::PARAM_STR);
		$stmt->execute();
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	while($_SESSION['staff_rates'][$i] = $stmt->fetch(PDO::FETCH_ASSOC)) $i++;
}

function even_select($selected=null) {
	$days = array(lang::HDR_ODD,lang::HDR_EVEN);
	
echo '<div class="row">
		<label for="even">' . lang::HDR_ODD_OR_EVEN . ':</label>
		<select name="even">';
			echo '<option value="">' . lang::SELECT_DEFAULT . '</option>';
			foreach($days as $k => $v) {
				echo '<option value="'.$k.'" ';
					if($k == $selected && $selected != '') echo 'selected';
				echo '>' . $v . '</option>';
			}
		echo '</select>';
echo '</div>';
}


function visit_state_read($selected) {
	$states = array(
		0	=>	lang::HDR_VISIT_STATE0,
		1	=>	lang::HDR_VISIT_STATE1, 
		8	=>	'<span class="warning">' . lang::HDR_VISIT_STATE8 . '</span>', 
		9	=>	'<span class="warning">' . lang::HDR_VISIT_STATE9 . '</span>', 
		10	=>	lang::HDR_VISIT_STATE10
	);
	
	foreach($states as $k => $v) {
		if($selected == $k) $state = $v;
	}
	
	return $state;
}

function visit_state_select($selected, $filter = null) {
	$options = array(
		0	=>	lang::HDR_VISIT_STATE0,
		1	=>	lang::HDR_VISIT_STATE1, 
		2	=>	lang::HDR_VISIT_STATE2, 
		8	=>	'<span class="warning">' . lang::HDR_VISIT_STATE8 . '</span>', 
		9	=>	'<span class="warning">' . lang::HDR_VISIT_STATE9 . '</span>', 
		10	=>	lang::HDR_VISIT_STATE10
	);
	
	if($filter != null) {
		echo '<option value="all"';
		if($selected == 'all') echo ' selected';
		echo '>'.lang::HDR_VISIT_STATE . lang::SELECT_ADD_ALL.'</option>';
	
	}// else echo '<option value="">'.lang::SELECT_DEFAULT.'</option>';
	
	foreach($options as $k => $v){
		 echo '<option value="'.$k.'"';
		 if($selected !=null && $selected !='all' && $selected == $k) echo ' selected';
			echo '>'.$v.'</option>';
	}
}

function priceIn($cosmID, $pcsOut, $offset = 'floor') {
		require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
		if ($offset == 'floor') {
			$offset = floor($pcsOut);
		} else $offset = ceil($pcsOut+0.000000001);

		$sql = "SELECT (priceIn / qtyIn) as priceIn
			FROM received
			LEFT JOIN invoices ON received.invoiceID = invoices.id
			WHERE received.cosmID = :id 
				AND locationID = :locationID
				AND received.qtyOut = 0
			LIMIT 1 OFFSET :offset";  
		
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':id', $cosmID, PDO::PARAM_INT);
		$stmt->bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
		$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch();
		if ($row['priceIn'] != NULL) return $row['priceIn'];
		else  return 0;			
	}

// Номенклатура услуг
/*function work_options($array) { //для worktype/work_list
	switch(true) {
		case($array == ''):
			break;
		default:
			$options = json_decode($array);
		
			foreach ($options as $key => $subarray)
			{
				if (is_array($subarray) == false)
				{
					echo $key . ' - ' . $subarray . '<br />';
				
				} else 
				{
					echo '<strong>'. $key . ':</strong><br />';
					
					$i = 0;
					while ($subarray[$i] != NULL)
					{
						foreach ($subarray[$i] as $k => $v)
						{
							echo $k . ': ' . $v . '<br />';
						}
						$i++;
					}
				}
			}
	}
}*/

//Для кого услуга
function work_target($name) {
	switch ($name)
	{
		case 1:
			echo lang::TARGET_MALE;
			break;
		case 2:
			echo lang::TARGET_FEMALE;
			break;
		case 3:
			echo lang::TARGET_CHILD;
			break;
		default:
			echo lang::TARGET_GENERAL;
			break;
	}
}

function target_select($selected=null, $filter=null) {
	$options = array(lang::TARGET_GENERAL, lang::TARGET_MALE, lang::TARGET_FEMALE, lang::TARGET_CHILD);
	
	
	if($filter != null) {
		echo '<option value="all"';
		if($selected == 'all') echo ' selected';
		echo '>'.lang::HDR_WORKTYPE_TARGET . lang::SELECT_ADD_ALL.'</option>';
	
	} else echo '<option value="">'.lang::SELECT_DEFAULT.'</option>';
	
	foreach($options as $k => $v){
		 echo '<option value="'.$k.'"';
		 if($selected !='all' && $selected == $k) echo ' selected';
			echo '>'.$v.'</option>';
	}
	
}

function work_cat_select($check = null, $selected = null, $filter=null, $userID = null) {
	require (dirname(__FILE__).'/connect.php');
	try {
		$stmt = $pdo->prepare("SELECT worktype_cat.id, category, GROUP_CONCAT(userID) as userIDs, GROUP_CONCAT(reward_rate) as reward_rates
			FROM `worktype_cat` 
			LEFT JOIN users_specialty ON worktype_cat.id = users_specialty.specialtyID
			WHERE archive=0
			GROUP BY category
            ORDER BY category");
		$stmt->execute();
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
		
	if ($check == null)
	{
		if($filter != null) echo '<option value="all">'.lang::HDR_WORKTYPE_CAT . lang::SELECT_ADD_ALL .'</option>';
		else 				echo '<option value="">'.lang::SELECT_DEFAULT.'</option>';
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			echo '<option value="'.$row['id'].'"';
				if($selected == $row['id']) echo ' selected';
				echo '>'. $row['category'].		'</option>';
		}
	} else // ЧЕК-БОКСЫ
	{
		if($selected != null) $haystack = explode(',',$selected);
		if(isset($_SESSION['temp']['specialty'])) $haystack=$_SESSION['temp']['specialty'];
		
		echo '<div class="flex-row">';
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			echo '<div class="inline half">
				<input name="specialty[]" type="checkbox" value="' . $row['id'].'"';
				
				if(isset($haystack) && in_array($row['id'], $haystack)) echo 'checked';
				
				echo '>';
				
				//названия категорий для записи с сайта
				$_SESSION['categories'][$row['id']] = $row['category'];
				
				
				echo '<label for="specialty[]">' . $row['category'] . ' </label>';
				//ставка
				if($userID != null) {
					$users = explode(',',$row['userIDs']);
					$rates = explode(',',$row['reward_rates']);
					$i=0;
					while($users[$i] != null) {
						if($users[$i] == $userID) {
							echo '<input name="reward_rate[]" class="short"  type="number" min="0" max="100" step="1" placeholder="' . lang::HDR_RATE_PLACEHOLDER . '" value="' . $rates[$i].'" style="margin-right:10px;" />';
							echo '<input name="reward_rate_old[]" type="hidden" value="' . $rates[$i].'" />';
						}
						$i++;
					}
				}
			echo '</div>';
			
		
		}
		echo '</div>';
	}	
}


//КЛИЕНТЫ
function FIO($name, $surname=null, $prompt=null) {
	switch (true)
	{
		case ($surname !=null && $prompt != null):
			$FIO = $name . ' ' . $surname . ' (' . $prompt . ')';
			break;
		case ($surname !=null && $prompt == null):
			$FIO = $name . ' ' . $surname;
			break;
		case ($surname ==null && $prompt != null):
			$FIO = $name . ' (' . $prompt . ')';
			break;
		case ($surname ==null && $prompt == null):
			$FIO = $name;
			break;
	}
	return htmlspecialchars($FIO);
}

function client_source_select($selected=null) {
	require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
	$stmt = $pdo->prepare("SELECT id, name
			FROM sources 
			WHERE archive=0");
	$stmt->execute();
	echo '<select name="sourceID" required>';
		echo '<option value="">' . lang::SELECT_DEFAULT . '</option>';
			
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			echo '<option value="'.$row["id"].'"';
				if($selected == $row["id"]) echo 'selected';
			
			echo '>' . $row["name"] . '</option>';
			
		}
	echo '</select>';
}


//ФОРМА добавления телефонов
function phones_add($array=null){
	if(isset($_SESSION['temp']['phones']) || isset($array)) {
		if (isset($_SESSION['temp']['phones'])) $array=$_SESSION['temp']['phones'];
		
		$i=0;
		foreach($array as $phone) {
			echo '<div class="row">
				<label for="phones[]">'. lang::HDR_PHONE .':</label>
				<input name="phones[]" type="tel" placeholder="'. lang::PHONE_PLACEHOLDER_PATTERN .'" minlength="7" maxlength="12" value="' . $phone .'" />';
				if($i == 0) echo '<i class="fas fa-plus inline-fa" onclick="phoneAdd();"></i>';
				$i++;	
			echo '</div>';
		} 
	} else {
		echo '<div class="row">
			<label for="phones[]">'. lang::HDR_PHONE .':</label>
			<input name="phones[]" type="tel" placeholder="'. lang::PHONE_PLACEHOLDER_PATTERN .'" minlength="7" maxlength="12" />
			<i class="fas fa-plus inline-fa" onclick="phoneAdd();"></i>
		</div>';
	}
	
	echo '<template id="phone">
		<div class="row">
			<label for="phones[]">'. lang::HDR_PHONE .':</label>
			<input name="phones[]" type="tel" placeholder="'.lang::PHONE_PLACEHOLDER_PATTERN.'" minlength="7" maxlength="12" />
					
		</div>
	</template>
	
	<script>
		var _counter = 0;
	var template = document.querySelector("#phone");
	var documentFragment = template.content;
	function phoneAdd() {
		_counter++;
		var oClone = template.content.cloneNode(true);
		oClone.id += (_counter + "");
		document.getElementById("morePhones").appendChild(oClone);
	}</script>';
}


// Добавление в базу
function phonesSQL($array=null){
	$phones = '';
	if(isset($array))
	{
		foreach ($array as $phone)
		{
			if (strlen($phone) == 10)
			{
					$res[]='38'.$phone;
			} else	$res[] = $phone;
		}
		$phones = implode(",", $res);
	}
	return $phones;
}

//Отображение телефонов

function phones($string) {
	$array = explode(',', $string);
	
	foreach($array as $phone) {
		echo $phone . '<br />';
	}
}

function visit_interval($first_visit, $last_visit, $total_visits) {
	if ((strtotime($last_visit) - strtotime($first_visit)) / $total_visits / 604800 /*weeks in sec */ > 5) {
		return round((strtotime($last_visit) - strtotime($first_visit)) / $total_visits / 2592000, 1) . ' мес.';
	} else {
		return round((strtotime($last_visit) - strtotime($first_visit)) / $total_visits / 604800, 0) . ' нед.';
	} 
}

function curr() // валюта
{
	return ' '.$_SESSION['settings']['currency'];
}


function brand_select_filter($brands = null, $archive = 0) {
	if (!isset($brands) || isset($_GET['brandID'])) {
		require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
		$stmt = $pdo->prepare("SELECT brands.name,  brands.id
				FROM brands
				RIGHT JOIN cosmetics ON brands.id=cosmetics.brandID
                WHERE cosmetics.archive=:archive
                GROUP BY brands.name
                ORDER by brands.name");
		$stmt -> bindValue(':archive', $archive, PDO::PARAM_INT);
		$stmt->execute();
		$count=1;
		while ($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$brands[$data[$count]['id']]=$data[$count]['name'];
			$count++;
		}
	}
	
	$array=array_unique($brands);
	asort($array);
	
	echo'<option value="all">' . lang::HDR_BRAND . lang::SELECT_ADD_ALL .'</option>';
	foreach ($array as $key => $val) {
		echo '<option value="'.$key.'"';
			if($_SESSION['brandID'] == $key) echo 'selected';
		echo '>' . $val . '</option>';
	}
			
}

function brand_select($selected=null) {
	require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
		$stmt = $pdo->prepare("SELECT id, name
				FROM brands
				ORDER by brands.name");
		$stmt->execute();
		echo '<div class="row">
			<label>'.lang::HDR_BRAND.':*</label>
			<select name="brandID" required>
				<option value="">' . lang::SELECT_DEFAULT . '</option>';
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					echo '<option value="'.$row['id'].'"';
						if($_GET['brandID'] == $row['id'] || $_SESSION['temp']['brandID'] == $row['id'] || $selected == $row['id']) echo 'selected';
					echo '>' . $row['name'] . '</option>';
				}
			echo '</select>
			<a class="form-inline" title="'.lang::MENU_NEW.'" href="/cosmetics/brand_add.php?backTo=cosmetics_add"><i class="fas fa-plus inline-fa"></i></a>
		</div>';
}

function brand_multiselect($checked=null) {
	require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
		$stmt = $pdo->prepare("SELECT id, name
				FROM brands
				ORDER by brands.name");
		$stmt->execute();
		while ($id = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$res[$id['id']]=$id['name'];
			}
		if($checked !=null || isset($_SESSION['temp']['brandIDs'])) 
		{ 
			$haystack = array();
			if($_SESSION['temp']['brandIDs']) $haystack = $_SESSION['temp']['brandIDs'];
				else {
					if (is_array($checked) == false) $haystack = explode(',',$checked);
					else {
						foreach ($checked as $k => $v)
						{
							$haystack[] = $k;
						}
					}
				}
		}
	
		echo '<div class="row">';
				echo '<label>'. lang::HDR_BRANDS.'*:</label>';
				echo '<div class="flex-row">';
					foreach ($res as $key => $val){
						echo '<div class="inline half">
							<input name="brandIDs['. $key . ']" type="checkbox" value="' . $key .'"';
							if ($checked != null && in_array($key, $haystack)) {
								echo 'checked';
							}
							if (isset($_SESSION['temp']['brandIDs']) && in_array($key, $haystack)) {
								echo 'checked';
							}
							
							echo '>
							<label for="brandIDs['.$key .']">' . $val . ' </label>';
						echo '</div>';
					}
				echo '</div>';
			echo '</div>';
}


function brand_names_only($supplierID, $coma = null) {// Отдает названия построчно или через запятую
	require (dirname(__FILE__).'/connect.php');
	$brands_db = array();
	if(!isset($_SESSION['brands_db'][$supplierID])) {
		try {
			$stmt = $pdo->prepare("SELECT brands.id, brands.name FROM `brands`
				LEFT JOIN supplier_brands ON brands.id=supplier_brands.brandID
				LEFT JOIN suppliers ON supplier_brands.supplierID=suppliers.id
				WHERE suppliers.id=:supplierID");
			$stmt -> bindValue(':supplierID', $supplierID, PDO::PARAM_INT);
			$stmt->execute();
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$brands_db[] = $row['name'];
			}
			sort($brands_db);
			$_SESSION['brands_db'][$supplierID] = $brands_db;
	
		} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	}
	
	$brands_db = $_SESSION['brands_db'][$supplierID];
	
	$i = '';
	foreach ($brands_db as $name){
		
		echo $i . $name;
		if ($coma ==null) $i = '<br />';
		else $i = ', ';
	}
}


function cosm_purpose($value) {
	switch ($value)
	{
		case '0':
			return lang::HDR_PURPOSE_WORK;
			break;
		case '1':
			return lang::HDR_PURPOSE_SALE;
			break;
		case '2':
			return lang::HDR_PURPOSE_BOTH;
			break;
		case '3':
			return lang::HDR_PURPOSE_ACCOUNT;
			break;
	}
}

function cosm_purpose_select($selected=null, $filter=null){
	$purposes=array(lang::HDR_PURPOSE_WORK,lang::HDR_PURPOSE_SALE,lang::HDR_PURPOSE_BOTH,lang::HDR_PURPOSE_ACCOUNT);
	if($filter == null) echo '<div class="row">
			<label>'.lang::HDR_PURPOSE.':*</label>';
			echo '<select name="purpose"'; if($filter == null) echo ' required'; echo '>';
				if($filter != null) {
					echo '<option value="all"';
						if($selected == 'all') echo ' selected';
					echo '>' . lang::HDR_PURPOSE . lang::SELECT_ADD_ALL .'</option>';
				} else echo '<option value="">' . lang::SELECT_DEFAULT . '</option>';
				foreach ($purposes as $key => $val) {
					echo '<option value="'.$key.'"';
						if ($_SESSION['temp']['purpose'] !='' && $_SESSION['temp']['purpose'] == $key) echo 'selected';
						if ($selected != 'all' && $selected == $key) echo 'selected';
					echo '>'.$val.'</option>';
				}
			echo '</select>';
		if($filter == null) echo '</div>';
}

function VAT_select($selected=null){
	$VAT=array(lang::VAT_NO,lang::VAT_YES);
	echo '<div class="row">
			<label>'.lang::HDR_VAT.':*</label>
			<select name="VAT" required>
				<option value="">' . lang::SELECT_DEFAULT . '</option>';
				foreach ($VAT as $key => $val) {
					echo '<option value="'.$key.'"';
						if ($_SESSION['temp']['VAT'] !=null && $_SESSION['temp']['VAT'] == $key) echo 'selected';
						if (isset($selected) && $selected == $key) echo 'selected';
					echo '>'.$val.'</option>';
				}
			echo '</select>
		</div>';
}

function VAT_read($value) {
	switch ($value)
	{
		case '0':
			echo lang::VAT_NO;
			break;
		case '1':
			echo lang::VAT_YES;
			break;
	}
}

function VAT_subtract($number) {  // ВАЖНО! Должна быть установлена переменная $_SESSION['supplierVAT'] сессии
	if ($_SESSION['supplierVAT'] == 0) {
	
		$result = $number - ($number / (100 + $_SESSION['settings']['VAT']) * $_SESSION['settings']['VAT']);
		$result = round($result, 2);
	} else {
		$result=$number;
	}
		
	return $result;
}

function VAT_only($number) {
	$result = $number / (100 + $_SESSION['settings']['VAT']) * $_SESSION['settings']['VAT'];
	$result = round($result, 2);
	return $result;
}

function supplier_select($selected=null){
	require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
	$stmt = $pdo->prepare("SELECT id, name
			FROM suppliers 
			WHERE archive=0 ");
	$stmt->execute();
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$res[$row['id']]=$row['name'];
	}
	echo '<div class="row">
			<label>'.lang::HDR_SUPPLIER.':*</label>
			<select name="supplier" required>
				<option value="">' . lang::SELECT_DEFAULT . '</option>';
				foreach ($res as $key => $val) {
					echo '<option value="'.$key.'"';
						if ($_SESSION['temp']['supplier'] !=null && $_SESSION['temp']['supplier'] == $key) echo 'selected';
						if (isset($selected) && $selected == $key) echo 'selected';
					echo '>'.$val.'</option>';
				}
			echo '</select>
		</div>';
}

function invoice_state_select($selected=null){
	$states=array(
		lang::PURCHASE_STATE0,
		lang::PURCHASE_STATE1,
		lang::PURCHASE_STATE2,
		lang::PURCHASE_STATE3,
		lang::PURCHASE_STATE4,
		lang::PURCHASE_STATE5
	);
	
	echo '<div class="row">
			<label>'.lang::HDR_PURCHASE_STATE.':*</label>
			<select name="state" required>
				<option value="">' . lang::SELECT_DEFAULT . '</option>';
				foreach ($states as $key => $val) {
					echo '<option value="'.$key.'"';
						if (isset($selected) && $selected == $key) echo 'selected';
					echo '>'.$val.'</option>';
				}
			echo '</select>
		</div>';
	
}

function invoice_state_read($value){
	switch($value)
	{
		case '0':
			echo lang::PURCHASE_STATE0;
			break;
		case '1':
			echo lang::PURCHASE_STATE1;
			break;
		case '2':
			echo lang::PURCHASE_STATE2;
			break;
		case '3':
			echo lang::PURCHASE_STATE3;
			break;
		case '4':
			echo lang::PURCHASE_STATE4;
			break;
		case '5':
			echo lang::PURCHASE_STATE5;
			break;	
	}
	
}


// СТАВКИ ЗАТРАТ
function cat_list ($inmenu = 1, $selected=null, $filter=null) { // категории затрат
	require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
	$stmt = $pdo->prepare("SELECT DISTINCT category, expences_cat.id
			FROM expences_cat 
			LEFT JOIN expences_subcat ON expences_cat.id = expences_subcat.catID
			WHERE expences_cat.archive=0 AND expences_subcat.archive=0 AND inmenu=:inmenu");
	$stmt->bindValue(':inmenu', $inmenu, PDO::PARAM_INT);
	$stmt->execute();
	if($filter != null) echo '<option value="all">' . lang::HDR_CATEGORY . lang::SELECT_ADD_ALL . '</option>';
	else				echo '<option value="">' . lang::SELECT_DEFAULT . '</option>';
		
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		echo '<option value="'.$row["id"].'"';
			if($selected == $row["id"]) echo 'selected';
		
		echo '>' . $row["category"] . '</option>';
		
	}
}


//// Расходы с заданными ставками (для фин.отчета)
function stakesExpenses($subcatID, $month) {
	require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
	
	$stmt = $pdo->prepare("SELECT date 
							FROM visits
							WHERE locationID = :locationID
								AND DATE_FORMAT(visits.date, '%Y-%m') = :month
								AND price_total > 0 
								AND state = 10
							GROUP BY date
							");
	$stmt->bindValue(':month', $month, PDO::PARAM_STR);
	$stmt->bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
	$stmt->execute();
	
	$total_subcategory = 0;
	
	while($row_date=$stmt->fetch(PDO::FETCH_ASSOC)){
		$stmt2 = $pdo->prepare("SELECT date, unitPrice, monthlyPrice
								FROM stakes
								WHERE subcatID = :subcatID
									AND locationID = :locationID
									AND date BETWEEN '1970-01-01' AND :date
								ORDER BY date DESC
								LIMIT 1");
		$stmt2->bindParam(':subcatID', $subcatID, PDO::PARAM_INT);
		$stmt2->bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
		$stmt2->bindParam(':date', $row_date['date'], PDO::PARAM_STR);
		$stmt2->execute();
		$row = $stmt2->fetch();
		if (isset($row['date']) && $row['unitPrice'] > 0)
			$total_subcategory = $total_subcategory + $row['unitPrice'];
		else {
			$total_subcategory = $row['monthlyPrice'];
		}
	}
	return $total_subcategory;
}

function work_netto_services_options($checked = null) {
	require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
	
	if($checked != null) 
	{ 
		$haystack = array();
		if (is_array($checked) == false) $haystack = explode(',',$checked);
		else {
			foreach ($checked as $k => $v)
			{
				$haystack[] = $k;
			}
		}
	}
	
	$stmt = $pdo->prepare("SELECT service_netto.id, service_netto.name
		FROM `service_netto` 
		ORDER BY service_netto.name
		");
	$stmt->execute();
	
	echo '<div class="row">';
		echo '<label>'. lang::HDR_SERVICES_NETTO.':</label>';
		echo '<div class="flex">';
		$count = 0;
		while($row[$count] = $stmt->fetch(PDO::FETCH_ASSOC))  {
			 $key = $row[$count]['id'];
			 $val = $row[$count]['name'];
			
			echo '<div class="inline">
				<input name="serv_work[]" type="checkbox" value="' . $key .'"';
				switch(true)
				{
					case ($checked != null && in_array($key, $haystack)):
						echo 'checked';
						break;
				}
				
				echo '>
				<label for="serv_work[]">' . $val . ' </label>';
			echo '</div>';
			
			$count++;
		}
		echo '</div>';
	echo '</div>';
	
}




// Заголовок страницы с выбором локации. Останавливает выполнение сценария, если не указана локация
function header_loc($text = null) { 
	echo '<h2 class="form">' . $text . '';
  
	if ($_SESSION['role'] != 'godmode') {
		
		// Если у пользователя несколько салонов, нужно определиться сначала
		if(isset($_SESSION['locationName'])) {
			echo ' ' . $_SESSION['locationName'] . '</h2>';
		} else {
			echo location_options(1, 'active');
			if ($_GET['loc'] != '' || $_SESSION['locationSelected'] !='') { // проверяем, выбран ли салон 
			} else {	include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');
				exit;
			}	
		}
	} else {  //если роль == godmode
		echo  location_options(1, 'active');
	}
	echo '</h2>';
}

// TABS
function tabs($tabs, $active=null) {
	$delimiter = '<span class="delimiter">&nbsp;&nbsp;|&nbsp;&nbsp;</span>';
	
	$title = '';
	foreach ($tabs as $tab) {
		if($_SESSION['pwr'] >= $tab['power']) {
			
			if($title != $tab['title']) {
				$title = $tab['title'];
				echo '<p class="title">'.$tab['title'].':</p>';
			}
			
			switch (true) 
			{
				case($tab['id'] == $active):
					echo '<span class="active">'. $tab['name'] .'</span>' . $delimiter;
					break;
				default:
					echo '<a href='.$tab['link'].' class="inactive">'.$tab['name'].'</a>' . $delimiter;
					break;
			}
		}
	}
}

// День недели
function dayOfWeek ($date) {
	if (strlen($date) == 1) {
		switch ($date) {
			case '1':
				$day = lang::MONDAY;
				break;
			case '2':
				$day = lang::TUESDAY;
				break;
			case '3':
				$day = lang::WEDNESDAY;
				break;
			case '4':
				$day = lang::THURSDAY;
				break;
			case '5':
				$day = lang::FRIDAY;
				break;
			case '6':
				$day = lang::SATURDAY;
				break;
			case '7':
				$day = lang::SUNDAY;
				break;
		}
	} else {
		switch (date('D', strtotime($date))) {
			case 'Mon':
				$day = lang::MONDAY;
				break;
			case 'Tue':
				$day = lang::TUESDAY;
				break;
			case 'Wed':
				$day = lang::WEDNESDAY;
				break;
			case 'Thu':
				$day = lang::THURSDAY;
				break;
			case 'Fri':
				$day = lang::FRIDAY;
				break;
			case 'Sat':
				$day = '<span class="warning">' .lang::SATURDAY . '</span>';
				break;
			case 'Sun':
				$day = '<span class="warning">' .lang::SUNDAY . '</span>';
				break;
		}
	}
	return $day;
}

function weekday_select($selected=null){
	$days = array(
		1 => lang::MONDAY,
		2 => lang::TUESDAY,
		3 => lang::WEDNESDAY,
		4 => lang::THURSDAY,
		5 => lang::FRIDAY,
		6 => lang::SATURDAY,
		7 => lang::SUNDAY
	);
	
echo '<div class="row">
		<label for="weekday">' . lang::HDR_WEEKDAY . ':</label>
		<select name="weekday">';
			echo '<option value="">' . lang::SELECT_DEFAULT . '</option>';
			foreach($days as $k => $v) {
				echo '<option value="'.$k.'" ';
					if($k == $selected && $selected != '') echo 'selected';
				echo '>' . $v . '</option>';
			}
		echo '</select>';
echo '</div>';
	
}

function weekdays($date) {
	switch (date('D', strtotime($date))) {
		case 'Mon':
			$mon = $date;
			break;
		case 'Tue':
			$mon = date('Y-m-d', strtotime($date. ' - 1 days'));
			break;
		case 'Wed':
			$mon = date('Y-m-d', strtotime($date. ' - 2 days'));
			break;
		case 'Thu':
			$mon = date('Y-m-d', strtotime($date. ' - 3 days'));
			break;
		case 'Fri':
			$mon = date('Y-m-d', strtotime($date. ' - 4 days'));
			break;
		case 'Sat':
			$mon = date('Y-m-d', strtotime($date. ' - 5 days'));
			break;
		case 'Sun':
			$mon = date('Y-m-d', strtotime($date. ' - 6 days'));
			break;
	}
	
	$weekdays = array (
		1 => date('Y-m-d', strtotime($mon)),
		2 => date('Y-m-d', strtotime($mon. ' + 1 days')),
		3 => date('Y-m-d', strtotime($mon. ' + 2 days')),
		4 => date('Y-m-d', strtotime($mon. ' + 3 days')),
		5 => date('Y-m-d', strtotime($mon. ' + 4 days')),
		6 => date('Y-m-d', strtotime($mon. ' + 5 days')),
		7 => date('Y-m-d', strtotime($mon. ' + 6 days'))
	);

	return $weekdays;
}

function loc_off_weekdays() {
	$locID = $_SESSION['locationSelected'];
	$days_off = array();
	
	//if(!isset($_SESSION['days_off'][$locID])) {
		require (dirname(__FILE__).'/connect.php');
		$stmt = $pdo->prepare("SELECT weekday FROM `locations_vacations` WHERE weekday is not null AND locationID=:locationID");
		$stmt->bindValue(':locationID', $locID, PDO::PARAM_INT);
		$stmt->execute();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$days_off[]=$row['weekday'];  //номер (7 = вс)
		}
		//$_SESSION['days_off'][$locID] = $days_off;
		
		
	//} else $days_off = $_SESSION['days_off'][$locID];
		
	return $days_off;
}

function month_options($table) {
	require (dirname(__FILE__).'/connect.php');
	$stmt = $pdo->prepare("SELECT DATE_FORMAT(date, '%Y-%m') AS month FROM $table GROUP BY month ORDER BY month DESC");
	$stmt->execute();
	echo '<option value="all"';
		if($_GET['month'] == 'all') echo ' selected';
		echo '>'.lang::HDR_MONTH . lang::SELECT_ADD_ALL .'</option>';
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		echo '<option value="' . $row['month'] . '"';
			if (isset($_SESSION['monthSelected']) &&  $row['month'] == $_SESSION['monthSelected']) echo 'selected';
			echo '>' . $row['month'] . 
		'</option>';
	}
}

function correctDate($date, $small = 0) {
	if ($date != NULL) {
		if($small == 0) $res = date('d.m.Y', strtotime($date));
		else			$res = date('d.m.y', strtotime($date));
	} else $res = "";
	return $res;
}

function defaultDate() {
	if (isset($_SESSION['temp']['date'])) echo $_SESSION['temp']['date'];
	else echo date('Y-m-d');
}

function correctNumber($number, $decimal=0){
	return number_format($number, $decimal,'.',' ');
}


// Календарь
function operating_hours($selected = null, $openTime = 0,$closeTime = 24){
	
	
	for ($i = $openTime; $i < $closeTime; $i++) {
		if ($i<10) $n= '0' . $i;
		else $n = $i;
		
		echo '<option value="' . $i .'"';
			if(mb_substr($selected,0,2) == $n ) echo ' selected';
		echo'>' . $n .':00' . '</option>';
	}
}

function time_options($selected = null, $site = 0, $openTime = 0,$closeTime = 24){
	if ($site == 1) {
		if(isset($_SESSION['openFrom']) && $openTime == 0) $openTime = $_SESSION['openFrom'];	
		if(isset($_SESSION['openTill'])) $closeTime = $_SESSION['openTill'];	
	
	} else {	
		if(isset($_SESSION['openFrom']) && $openTime == 0) $openTime = $_SESSION['openFrom']-2;	//+2 часа зазор для нестанд. клиентов
		if(isset($_SESSION['openTill'])) $closeTime = $_SESSION['openTill']+2;	//+2 часа зазор для нестанд. клиентов
	}

	
	$minutes = array(':00',':15',':30',':45');
	
	echo '<option value="00:00">' . lang::SELECT_DEFAULT . '</option>';
	for ($i = $openTime; $i < $closeTime; $i++) {
		if ($i<10) $n= '0' . $i;
		else $n = $i;
		foreach ($minutes as $min) {
			if($i < 24) {
				echo '<option value="' . $n .$min .'"';
					if(($n .$min) == $selected) echo ' selected';
				echo '>' . $n . $min . '</option>';
			}
		}
	}
	if($i < 24) echo '<option value="' . $i . ':00">' . $i . ':00</option>';
	else echo '<option value="00:00">00:00</option>';
	
}

function time_grid($site=null){
	require (dirname(__FILE__).'/connect.php');
	$stmt = $pdo->prepare("SELECT openFrom, openTill FROM locations WHERE id=:locationID");
	$stmt->bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
	if($site == null) $openTill = $row['openTill'];
	else			  $openTill = $row['openTill'] - 1;
	
	$grid_row = 2;  // с учетом того, что первый ряд уже занят заголовком
	for ($i = $row['openFrom']; $i < $openTill; $i++) {
		if ($i<10) $n= '0' . $i;
		else $n = $i;
		
		// полный час
		echo '<div class="grid-cell" style="grid-row: '.$grid_row.'/'.($grid_row+2).';border:none;">
			<p class="center small time">' . $n .':00' . '</p>
		</div>';
		
		//половинка
		echo '<div class="grid-cell" style="grid-row: '.($grid_row+2).'/'.($grid_row+4).'">
			<p class="center small time"></p>
		</div>';
		
		$grid_row = $grid_row + 4;
		
	}
	$_SESSION['openFrom'] = $row['openFrom'];
	$_SESSION['openTill'] = $openTill;
	$_SESSION['grid_rows'] = $grid_row - 2;  //ряд для времени окончания не отображаем
}

function event_grid_visit($visitData, $weekday = null, $forSite = null) {
	//$_SESSION['openTill'] для сайта тут уже уменьшена на час (см. time_grid())
	
	switch(true) {
		case (mb_substr($visitData['startTime'],0,2) < $_SESSION['openFrom']
		      && mb_substr($visitData['endTime'],0,2) > $_SESSION['openFrom']):
			$visit_start = $_SESSION['openFrom'];
			$visit_end	 = $visitData['endTime'];
			$notice = '<i class="fas fa-exclamation-triangle attention" title="'.lang::TOOLTIP_VISIT_START . $visitData['startTime'] .'"></i>';
			break;
			
		case ((mb_substr($visitData['endTime'],0,2) >$_SESSION['openTill'] || $visitData['endTime'] == '00:00')
				&& mb_substr($visitData['startTime'],0,2) >= $_SESSION['openFrom']):
			$visit_start = $visitData['startTime'];
			$visit_end	 = $_SESSION['openTill'];
			$notice = '<i class="fas fa-exclamation-triangle attention" title="'.lang::TOOLTIP_VISIT_END . $visitData['endTime'] .'"></i>';
			break;
		
		default:
			$visit_start = $visitData['startTime'];
			$visit_end	 = $visitData['endTime'];
			break;
	}
	
	//для сайта: проверяем, не позже ли заканчивается визит по сравнению с временем, отображаемым на сайте
	//if(mb_substr($visit_end,0,2) >= $openTill) $visit_end = $openTill . ':00';
	
	$grid_start = event_duration_slots($_SESSION['openFrom'], $visit_start) +2;
	$slots_number = event_duration_slots($visit_start, $visit_end);
	$grid_end = $grid_start+$slots_number;
	
	if($grid_end > $_SESSION['grid_rows']) $grid_end = $_SESSION['grid_rows'];  // для сайта с укороченным графиком "до"
	
	if ($grid_start < $grid_end) { //для сайта с укороченным графиком "до"
		echo '<div class="grid-cell'; 
			if($weekday != null) {
				if($visitData['state'] == 2 && $forSite == null) echo ' accent-bg"';
				else echo ' gray-bg"';
			}
		echo ' style="grid-row: '. $grid_start .'/'.$grid_end.';">';
				if($forSite == 1) {
					echo '<p>'.lang::HDR_CALENDAR_TAKEN.'</p>';
				} else {
					if($_SESSION['pwr'] > 9)
						echo '<p class="grid-cell">
							<a href="/visits/visit_details.php?id='.$visitData['id'].'&goto=dashboard">' . FIO($visitData['clientName'],$visitData['clientSurname'],$visitData['prompt']) . '</a>';
					else echo FIO($visitData['clientName'],$visitData['clientSurname']) . ': ' . $visitData['works'];
					
					echo $notice . '</p>
					<p class="cal-price';
						//отмечаем визиты, которые еще не финализированы, но уже в прошлом
						if(strtotime($visitData['date']) < strtotime(date('d-m-Y')) &&$visitData['state'] < 8 ) {
							echo ' accent-bg';
						}
					
					echo '">'.$visitData['price_total'].curr().'</p>';
				}
		echo '</div>';
	}
	
	// для заполения пустот в календаре
	if($weekday != null) {
		$_SESSION['gridToFill_start'][$weekday][] = $grid_start;
		$_SESSION['gridToFill_finish'][$weekday][] = $grid_end;
	}
		
}

function event_duration_slots($startTime, $endTime){
	$start = explode(":", $startTime);
	$end = explode(":", $endTime);
	
	if ($end[0] < $start[0]) { //для перехода через полночь
		$end[0] = $end[0] + 24;
	}
	
	$h = $end[0] - $start[0];
	
	if ($end[1] < $start[1]) { //время окончания меньше времени начала
		$h = $h - 1;
		$end[1] = $end[1] + 60;
	}
	$m = $end[1] - $start[1];
	
	$result = round(($h *4 + $m / 15),0);
	
	
	return $result;	
}

function cal_emptyCell_wLink($gridRowFrom, $date, $site=null) {
	$gap = $gridRowFrom - 2 + $_SESSION['openFrom'] * 4;
	$timeFrom = event_duration_read($gap);
	if(strlen($timeFrom) == 4) $timeFrom = '0' . $timeFrom;
	
	if($site != null) {
			$url = '/site/visit.php?lang='. $_SESSION['lang'];
			$plusSign = '<p class="center" style="font-weight: bold;padding: 0;color: white;">+</p>';
	} else	{
		$url = '/visits/visit_details.php?new';
		$plusSign = '<i class="fas fa-plus center"></i>';
	}
	
	$emptyCell = '<div class="grid-cell" style="grid-row: '.$gridRowFrom.'/'.($gridRowFrom+2).';grid-column:1 / 10;" title="'.$timeFrom.'"><a href="'.$url.'&date='.$date.'&timeFrom='.$timeFrom.'&goto=dashboard" class="fill-div">'.$plusSign.'</a></div>';
	
	//скрываем пустое время для сайта, если оно "сегодня" и уже в прошлом
	if(date('Y-m-d') == $date && $site != null) {
		switch(date("i")) {
			case (date("i") < 15):
				$m_now = '00';
				break;
			
			case (date("i") < 30):
				$m_now = '15';
				break;
			
			case (date("i") < 45):
				$m_now = '30';
				break;
			case (date("i") < 60):
				$m_now = '45';
				break;
		}
		
		 if (strtotime(date('Y-m-d H:' . $m_now)) >= strtotime(date('Y-m-d' . $timeFrom))) {
			cal_emptyCell_taken_gap($gridRowFrom, ($gridRowFrom+2));
		} else {
			echo $emptyCell;
		}	
		
	} else 	echo $emptyCell;	
}

function cal_emptyCell_taken_gap($gridRowFrom, $gridRowTill) {
	if ($gridRowFrom <= $_SESSION['grid_rows']) {		
		if($gridRowTill > ($_SESSION['grid_rows']+2)) $gridRowTill = $_SESSION['grid_rows']+2;
		echo '<div class="grid-cell gray-bg" style="grid-row: '.$gridRowFrom.'/'.$gridRowTill.';grid-column: 1 /10;border:none;"><b></b></div>';
	}
}

function empty_cal_day($date, $site=null) {
	require (dirname(__FILE__).'/connect.php');
	$stmt = $pdo->prepare("SELECT date FROM locations_vacations WHERE locationID=:locationID AND date=:date");
	$stmt->bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
	$stmt->bindValue(':date', $date, PDO::PARAM_STR);
	$stmt->execute();
	
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
	if ($row['date'] == $date) {
		echo '<div class="grid-cell gray-bg" style="grid-row: 2/'.($_SESSION['grid_rows']+2).';">
			<p>'.lang::HDR_CALENDAR_TAKEN.'</p>
		</div>';
	} else {
		$begin = 2;
		while($_SESSION['grid_rows'] >= $begin) {
			if($site != null) {
					cal_emptyCell_wLink($begin, $date, 1);
			} else	cal_emptyCell_wLink($begin, $date);
			
			$begin = $begin+2;
		}
	}
	
}
	
function event_duration($startTime, $endTime){
	$start = explode(":", $startTime);
	$end = explode(":", $endTime);
	
	if ($end[0] < $start[0]) { //для перехода через полночь
		$end[0] = $end[0] + 24;
	}
	
	$h = $end[0] - $start[0];
	
	if ($end[1] < $start[1]) { //время окончания меньше времени начала
		$h = $h - 1;
		$end[1] = $end[1] + 60;
	}
	$m = $end[1] - $start[1];
	
	$result = $h . ':' . $m;
	
	
	return $result;	
}

function get_std_end_time($startTime) {
	$start = explode(":", $startTime);
	$end = $start[0]+1;
	if($end < 10 )  $result = '0' . $end . ':' . $start[1];
	else		    $result = $end . ':' . $start[1];
	return $result;
}

function event_duration_select($selected = null){
	$h=0;
	$m=15;
	$n=1;
	echo '<option value="">'.lang::SELECT_DEFAULT.'</option>';
	while($h < 9) {
		while($m < 60) {
			echo '<option value="' . $n .'"'; if ($n == $selected) echo 'selected'; echo '>';
				echo $h . ':';
				if ($m == 0) echo '0'; echo $m;
			echo '</option>';
			$m = $m+15;
			$n++;
			
		}
		$h++;
		$m = 0;
	}
	echo '<option value="' . $n .'"';
		if ($n == $selected) echo 'selected';
		echo '>' . $h . ':0' . $m . '</option>'; // для ровного счета :)
}

function event_duration_read($number = null){
	$h = floor($number / 4);
	$m = $number * 15 - $h * 60;
	if ($m == 0) $m = '00';
	$result = $h . ':' . $m;
	return $result; 
}



function slots_array($startTime, $endTime){
	$start = explode(":", $startTime);
	$end = explode(":", $endTime);
	
	if ($start[1] >0) $startingSlot = $start[0] * 4 + $start[1] / 15;
	else $startingSlot = $start[0] * 4;
	
	if ($end[1] >0) $endingSlot = $end[0] * 4 + $end[1] / 15;
	else $endingSlot = $end[0] * 4;
	
	$count = $startingSlot;
	
	if ($startingSlot > $endingSlot) {
		while ($startingSlot <= 96) {
			$slots_array[] = $startingSlot;
			$startingSlot++;
		}
		$count = 1;
	}
	
	while ($count < $endingSlot) {
		$slots_array[] = $count;
		$count++;
	}
	return $slots_array;
	
}

function list_navigation_buttons($count,$offset,$limit, $id = null, $purpose = null) {
	$args = '';
	if($_REQUEST['tab'] != '') $args = $args . '&tab=' . $_REQUEST['tab'];
	if ($id != null) $args = $args . '&id=' . $id;
	
	//для истории косметики 
	if($_REQUEST['cosmID'] != '') $args = $args . '&cosmID=' . $_REQUEST['cosmID'];
	if($_REQUEST['purpose'] != '') $args = $args . '&purpose=' . $_REQUEST['purpose'];
	
	if($purpose != null) {
			if($purpose == 'work' && $_REQUEST['offset_sale'] != '') $args = $args . '&offset_sale=' . $_REQUEST['offset_sale'];
			if($purpose == 'sale' && $_REQUEST['offset_work'] != '') $args = $args . '&offset_work=' . $_REQUEST['offset_work'];
		   $offset_label = 'offset_'.$purpose;
	} else $offset_label = 'offset';
	// конец истории косметики
	
	if(($count-1) < $limit && $offset == 0) {
	} else {
		echo '<div style="margin: 10px 0;height: 2.3em;">';
			 if($offset > 0) {
				echo '<a class="button back" href="' . $_SERVER['PHP_SELF'] .'?'.$offset_label.'=' . ($offset-$limit) . $args .'"> < < < </a>';
			}
			if($limit == ($count-1)) {
				echo '<a class="button fwd" href="' . $_SERVER['PHP_SELF'] .'?'.$offset_label.'=' . ($offset+$limit) . $args .'"> > > ></a>';
			}
		echo '</div>';
	}
		
}

function calendar_navigation_buttons($date, $noArchive = null) {
	$prev = date('Y-m-d', strtotime($date. ' - 7 days'));
	$next = date('Y-m-d', strtotime($date. ' + 7 days'));
	$currentWeek = date('W', time());
	$requestedWeek = date('W', strtotime($date));
	$week_diff = $currentWeek - $requestedWeek;
	
	$args = '';
	if($_GET['tab'] != '') $args = $args . '&tab=' . $_GET['tab'];
	if ($_GET['staffID'] != null) $args = $args . '&staffID=' . $_GET['staffID'];
	$args = $args . '&lang=' . $_SESSION['lang'];
	
	$div_open = '<div style="margin: 10px 0;height: 2.3em;">';
		$btn_prev = '<a class="button back" href="' . $_SERVER['PHP_SELF'] .'?date=' . $prev . $args .'"> < < < </a>';
		$btn_next = '<a class="button fwd" href="' . $_SERVER['PHP_SELF'] .'?date=' . $next . $args .'"> > > ></a>';
			
	$div_close = '</div>';
	
	switch(true) {
		case ($week_diff > 30 && $noArchive != null):
			$result = 	$div_open . $btn_prev . $btn_next . $div_close;
			break;
		
		case ($week_diff >=0 && $noArchive != null):
			$result = 	$div_open . 			$btn_next . $div_close;
			break;
			
		default:
			$result = 	$div_open . $btn_prev . $btn_next . $div_close;
			break;
	}
	
	return $result;
}

function visit_card ($array, $count) {
	echo '<div class="card'; 
		if($array['state'] == 8 || $array['state'] == 9) echo ' visit-cancelled';
	echo '">';
		echo '<p class="title">' .correctDate($array['date']) . ' ('.dayOfWeek($array['date']).')  |  ' . $array['location'] . '</p>
		<table class="card">
			<tr>
				<td>'. $array['staffNames'] .'</td>
				<td>'. $array['workNames'] .'</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:right;';
					if($array['state'] == 8 || $array['state'] == 9) echo ' text-decoration: line-through;';
					echo '">'. $array['price_total'] . curr() . '
				</td>
			</tr>';
			if(strtotime($array['date']) < strtotime(date('d-m-Y'))) 
				echo '<tr id="r' . $count .'" style="display:none;">
					<td colspan="2" id="f'. $count .'" ></td>
				</tr>';
			
			if ($array['comment'] != '')
			echo '<tr>
				<td colspan="2">' . $array['comment'] .'</td>
			</tr>';
		echo '</table>
		<div class="row" style="width: -webkit-fill-available;
								position: absolute;
								bottom: 10px;
								margin-right: 20px;">';
			$catIDs = explode(',',$array['catIDs']);			// ТОЛЬКО ДЛЯ ЛЕНЫ!!! УДАЛИТЬ ДЛЯ ПРОДАКШН
			if(in_array(7,$catIDs) && strtotime($array['date']) < strtotime(date('d-m-Y'))) { 							// ТОЛЬКО ДЛЯ ЛЕНЫ!!! УДАЛИТЬ ДЛЯ ПРОДАКШН
				echo '<a class="button center" style="flex: 1;margin-right: 10px;" href="#" onclick="fetch_spent('.$array['id'].','. $count .');return false;" ><i class="fas fa-info-circle"></i>'.lang::HDR_FORMULA.'</a>';	
			} 
			echo '<a class="button center" style="flex: 1;" href="/visits/visit_details.php?id='. $array['id'] .'&goto=profile" ><i class="fas fa-eye"></i>'. lang::HANDLING_VIEW .'</a>';
		echo '</div>
	</div>';
}

function cosm_history_work($cosmID, $offset=0, $limit=10) {
	require (dirname(__FILE__).'/connect.php');
	try 
	{
		$stmt = $pdo->prepare("SELECT spent.id as spentID
								, spent.volume as volumeS,  spent.cost, spent.visitID
								, visits.date, visits.clientID
								, clients.id as clientID, clients.name as client_name, clients.surname, clients.prompt
									FROM spent
									LEFT JOIN visits ON spent.visitID = visits.id
									LEFT JOIN clients ON visits.clientID = clients.id
									WHERE spent.cosmID = :cosmID
										AND visits.locationID = :locationID
									ORDER BY visits.date DESC
									LIMIT $offset, $limit");		
		$stmt -> bindValue(':cosmID', $cosmID, PDO::PARAM_INT);
		$stmt -> bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
		$stmt ->execute();
		$count=1;
		while ($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC))  $count++;
		
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	$pdo=null;
	
	echo '<div>
		<h2>'.lang::H2_HISTORY_WORK.'</h2>';
		if($count > 1) {
			echo '<table class="stripy">
				<thead>
					<tr>
						<th>'.lang::DATE.'</th>
						<th>'.lang::HDR_NAME.'</th>
						<th>'.lang::H2_HISTORY_WORK.'</th>
						<th>'.lang::HDR_COST.'</th>
					</tr>
				</thead>
				<tbody>';
				$count=1;
				while($data[$count] !=NULL) {
					echo '<tr>
						<td class="center">' . correctDate($data[$count]['date'])	. '</td>
						<td><a href="/clients/client_profile.php?id='.$data[$count]['clientID'].'" title="'.lang::HDR_CLIENT_PROFILE.'">' . FIO($data[$count]['client_name'],$data[$count]['surname'],$data[$count]['prompt'])	. '</a></td>
						<td class="center"><a href="/cosmetics/cosmetics_spent_edit.php?id='.$data[$count]['spentID'].'" title="'.lang::HANDLING_CHANGE.'">' . $data[$count]['volumeS'] . '</a></td>
						<td class="center">' . $data[$count]['cost']	. curr() . '</td>
					</tr>';
					$count++;
				}
				echo '</tbody>	
			</table>';
			echo list_navigation_buttons($count,$offset,$limit, null, 'work');
		} else echo '<p>' . lang::ERR_NO_INFO . '</p>';	
	echo '</div>';
}

function cosm_history_sales($cosmID, $offset=0, $limit=10) {
	require (dirname(__FILE__).'/connect.php');
	try 
	{
		$stmt = $pdo->prepare("SELECT received.id as soldID
			, invoices.date, received.priceIn,  received.expire, received.soldToID, received.dateOut, received.priceOut
			, clients.id as clientID, clients.name as client_name, clients.surname, clients.prompt
				FROM received
				LEFT JOIN invoices ON received.invoiceID = invoices.id
				LEFT JOIN clients ON received.soldToID = clients.id
				WHERE received.cosmID = :cosmID
					AND invoices.locationID = :locationID
					AND received.dateOut is not null
				ORDER BY received.dateOut DESC
				LIMIT $offset, $limit");		
		$stmt -> bindValue(':cosmID', $cosmID, PDO::PARAM_INT);
		$stmt -> bindValue(':locationID', $_SESSION['locationSelected'], PDO::PARAM_INT);
		$stmt ->execute();
		$total_profit = 0;
		$count=1;
		while ($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC))  {
			$total_profit += $data[$count]['priceOut'] - $data[$count]['priceIn'];
			$count++;
		}
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	
	echo '<div>
		<h2>'.lang::HDR_SALES_LIST.'</h2>';
		if($count > 1) {
			echo '<table class="stripy">
				<thead>
					<tr>
						<th>'.lang::DATE.'</th>
						<th>'.lang::HDR_NAME.'</th>
						<th>'.lang::HDR_PRICE.'</th>
						<th>'.lang::HDR_PROFIT.'</th>
					</tr>
				</thead>
				<tbody>';
				$count=1;
				while($data[$count] !=NULL) {
						echo '<tr>
							<td class="center">' . correctDate($data[$count]['dateOut']) . '</td>
							<td><a href="/clients/client_profile.php?id='.$data[$count]['clientID'].'" title="'.lang::HDR_CLIENT_PROFILE.'">' . FIO($data[$count]['client_name'],$data[$count]['surname'],$data[$count]['prompt'])	. '</a></td>
							<td class="center">' . correctNumber($data[$count]['priceOut']) . '</td>
							<td class="center">' . correctNumber($data[$count]['priceOut'] - $data[$count]['priceIn']) . curr()	. '</td>
						</tr>';
						$count++;
					}
						echo '<tr>
						<th colspan="3" style="text-align:right;">'.lang::HDR_TOTAL.':</th>
						<th>'.correctNumber($total_profit) . curr()	. '</th>
					</tr>
				</tbody>	
			</table>';
			echo list_navigation_buttons($count,$offset,$limit, null, 'sale');
		} else echo '<p>' . lang::ERR_NO_INFO . '</p>';
	echo '</div>';
}

?>