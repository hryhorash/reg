<?php
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	//var_dump($_POST);exit;

	$check = "SELECT expences_works.id, expences_works.worktypeCatID as selected
			FROM `worktype_cat` 
			LEFT JOIN expences_works ON expences_works.worktypeCatID = worktype_cat.id
			WHERE expences_works.expencesCatID=:expencesCatID
			ORDER BY category";
	
	
	$sql = "INSERT INTO expences_works (expencesCatID, worktypeCatID)
	VALUES (:expencesCatID, :worktypeCatID)";
	
	
	try {

		// проверяем уже сохраненные значения
		$read = $pdo->prepare($check);
		$read->bindValue(':expencesCatID', $_POST["category"], PDO::PARAM_INT); 
		$read->execute();

		while($row = $read->fetch()) {
			$prev[$row['id']] = $row['selected'];
		} 
		
		//var_dump($prev);exit;
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':expencesCatID', $_POST["category"], PDO::PARAM_INT); 
		$stmt->bindParam(':worktypeCatID', $worktypeCatID, PDO::PARAM_INT); 
		

		function delete($id) {
			require($_SERVER['DOCUMENT_ROOT'].'/config/connect.php');
			$delete = $pdo->prepare("
								DELETE FROM expences_works WHERE id = :id
							");
			$delete -> bindValue(':id', $id, PDO::PARAM_INT);
			$delete -> execute();
		}
		if($prev != null){  //были галки, установленные ранее
			
			// удаляем данные, если галку отжали
			switch(true) {
				case ($_POST["specialty"] == null):
					foreach($prev as $key => $item) {
						delete($key);
					}
					break;

				case ($_POST["specialty"] > 0):
					foreach($prev as $key => $item) {
						if($item != $_POST["specialty"]) delete($key);
					}

					foreach($_POST["specialty"] as $worktypeCatID) {
						if (!in_array($_POST["specialty"], $prev)) {
							$stmt->execute();
						}
					}
					break;

				default:
					foreach($prev as $key => $item) {
						if (!in_array($item, $_POST["specialty"])) {
							delete($key);
						}
					}
					
					foreach($_POST["specialty"] as $worktypeCatID) {
						if (!in_array($worktypeCatID, $prev)) {
							$stmt->execute();
						}
					}

			}
			

		} else { // галки устанавливаются впервые
			if($_POST["specialty"] != null) {
				foreach($_POST["specialty"] as $worktypeCatID) {
					$stmt->execute();
				}
			}
			
		}
		
		
		
		$_SESSION['success'] = lang::SUCCESS_GENERAL_ADD;
		session_write_close();
		//header( 'Location: /expences/expencesList.php');
		//exit;
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = lang::ERR_GENERAL;
	}
}

$pdo = NULL;
$title = lang::H2_EXPENCES_WORKS_MATCH;
//----------------------------VIEW-------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, $pageID);
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>' . lang::H2_EXPENCES_WORKS_MATCH . '</h2>';?>

	<form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
		<fieldset>
			<div class="row col-2e">
				<div class="row">
					<label for="category"><?=lang::MENU_EXPENCES_CAT;?>*:</label>
					<select name="category" id="category">
						<?=cat_list(1,$_SESSION['temp']['category']); ?>
					</select>
				</div>
				<div class="row" id="ajax">
				
					<label for="catID"><?=lang::HDR_WORKTYPE_CAT;?>*:</label>
					<?=work_cat_select(1, $_SESSION['temp']['catID']);?>
					
				</div>
			</div>
		</fieldset>

		<input id="button" type="submit" value="<?=lang::BTN_ADD;?>" />
		
	</form>

	<div id="test"></div>
</section>


<?php include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php'); ?>

<script>
//$("select[name='category']").change(function(){
		
	//При изначально установленном значении
	/* var xhttp = new XMLHttpRequest();
	$.ajax({
		type: "GET",
		url: "expences_works_match_ajax.php",
		data:	{ "category": $("#category option:selected").val(), "subcategory":  $("#subcatID option:selected").val() },  
		success: function(data){
			alert(data);
		  }
	}); */
	
	
	
	// При изменении значения	
	$("#category").change(function() {
		var xhttp = new XMLHttpRequest();
		$.ajax({
			type: "GET",
			url: "expences_works_match_ajax.php",
			data:	{ "category": $("#category option:selected").val() },
			success: function(data){

				// очищаем галочки
				$("input[name='specialty[]']").each(function() {
					$(this).prop("checked", false);
				});
				
				// устанавливаем галочки
				data = JSON.parse(data);
				if (jQuery.isEmptyObject(data) == false) {
					
					data.forEach(function(selectedWorkID){
						$("input[name='specialty[]']").each(function() {
							let curr = $(this).val();
							

							if (curr == selectedWorkID) {
								$(this).prop("checked", true);
								//alert('success');
							}
							
						
					
						});


					});
				}
			}
		});
	});
//});
</script>