<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if (isset($_SESSION['monthSelected']) && isset($_SESSION['locationSelected'])) {
	if($_GET['categoryID'] !='' && $_GET['categoryID'] !='all') $cond_category = 'expences_cat.id = ' . $_GET['categoryID'];
	else $cond_category = 1;
	
	
	$sql = "SELECT expences.id,expences.date, category,subcategory,item,price,comment, locations.name as location
			FROM expences
			LEFT JOIN expences_subcat ON expences.subcatID = expences_subcat.id
			LEFT JOIN expences_cat ON expences_subcat.catID = expences_cat.id
			LEFT JOIN locations ON expences.locationID = locations.id        
			WHERE DATE_FORMAT(expences.date, '%Y-%m') = :month 
				AND expences.locationID= :location
				AND $cond_category
			ORDER BY date DESC, category, subcategory";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':month', $_SESSION['monthSelected'], PDO::PARAM_STR);
		$stmt->bindValue(':location', $_SESSION['locationSelected'], PDO::PARAM_INT);
		$stmt->execute();
		$count = 1;
		$total = 0;
		while($data[$count] = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$total = $total + $data[$count]['price'];
			$count++;
		}
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
	}
	$pdo = NULL;
}


// Кнопки управления доступом
$handle = array();
$handle['change'] = array(
	'title'=>lang::HANDLING_CHANGE, 
	'link_start'=>'/expences/expences_edit.php?id=',
	'button'=>'<i class="fas fa-edit"></i>'
);
$handle['delete'] = array(
	'title'=>lang::HANDLING_DELETE, 
	'link_start'=>'/config/delete.php?id=',
	'link_finish'=>'&table=expences&URL='.$_SERVER['PHP_SELF'],
	'button'=>'<i class="fas fa-trash"></i>',
	'alertMSG'=>lang::ALERT_DELETE
);

$button_add='<a href="/expences/expences_add.php" class="button">'.lang::BTN_ADD.'</a>';


if(isset($_SESSION['monthSelected'])) $title = lang::MENU_EXPENCES_MONTHLY . ' ' . date('Y.m',strtotime($_SESSION['monthSelected'])) . lang::TXT_AT;
else $title = lang::MENU_EXPENCES_MONTHLY . lang::TXT_AT;

//----------------------------VIEW-------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo '<p class="title">'. lang::SIDEBAR_FILTERS . '</p>';
	echo '<form method="get" class="filter">
		<fieldset class="noBorders">';
			echo '<select name="month">';
				month_options('expences');
			echo '</select>';
			echo '<select name="categoryID">';
				cat_list(1, $_GET['categoryID'], 1);
			echo '</select>';
			
			
			echo '<input type="submit" value="'. lang::BTN_SHOW.'" />';
		echo'</fieldset>';
	echo '</form>';echo tabs($tabs, 'exp_report');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo header_loc ($title);

	if($data[1] != NULL) {
		echo $button_add; ?>
		
		<table class="stripy">
			<tr>
				<th><?=lang::DATE;?></th>
				<th class="mobile-hide"><?=lang::HDR_CATEGORY;?></th>
				<th class="mobile-hide"><?=lang::HDR_SUBCATEGORY;?></th>
				<th><?=lang::HDR_ITEM_NAME;?></th>
				<th style="width:10%;"><?=lang::HDR_PRICE;?></th>
				<th class="mobile-hide"><?=lang::HDR_COMMENT;?></th>
				<th><?=lang::HDR_HANDLING;?></th>
			</tr>
		<?php $count=1;
		while($data[$count] != NULL) {
			echo "<tr>
				<td>" . correctDate($data[$count]['date']) . "</td>
				<td class='mobile-hide'>" . $data[$count]['category'] . "</td>
				<td class='mobile-hide'>" . $data[$count]['subcategory'] . "</td>
				<td>" . $data[$count]['item'] . "</td>
				<td class='center'>" . $data[$count]['price'] . curr() .  "</td>
				<td class='mobile-hide'>" . $data[$count]['comment'] . "</td>
				<td class='center'>"; ?>
					<a title="<?php echo $handle['change']['title']; ?>" href="<?php echo $handle['change']['link_start'] . $data[$count]['id']; ?>"><?php echo $handle['change']['button']; ?></a>
					<a title="<?php echo $handle['delete']['title'] ?>" href="<?php echo $handle['delete']['link_start'] . $data[$count]['id'] . $handle['delete']['link_finish']; ?>" onclick='return confirm("<?php echo $handle['delete']['alertMSG']. '\"'.$data[$count]['item'].'\"?'; ?>");'><?php echo $handle['delete']['button']; ?></a>
				</td>
			</tr>
			<?php $count++;
		}
			echo '<tr>
				<th colspan="2">'.lang::HDR_TOTAL.':</th>
				<th class="mobile-hide" colspan="2"></th>
				<th colspan="3"><strong>' . $total . curr() . '</strong></th>
			</tr>
		</table>';
		echo $button_add;
	} else {
		echo '<p>' . lang::ERR_NO_INFO . '</p>';
		echo $button_add;
	}	
	
echo '<section>';

 include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>