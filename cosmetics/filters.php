<?php
echo '<p class="title">' . lang::SIDEBAR_FILTERS . '</p>';

echo '<form method="post" class="filter" action="/cosmetics/history.php">
	<fieldset class="noBorders">
		<input name="cosmHistory" placeholder="'.lang::HDR_ITEM_NAME. '" value="'.$_POST['cosmHistory'].'" autocomplete="off">
		<input name="cosmID" type="hidden" value="'.$_POST['cosmID'].'" required >
		<input type="submit" value="'.lang::BTN_SHOW.'"">
	</fieldset>
</form>';

/*if($title == lang::MENU_VISITS || $title == lang::MENU_CALENDAR) {
	
	echo '<hr>';

	echo '<form method="get" class="filter" action="/visits/visits_list.php">
		<fieldset class="noBorders">
			<input name="date" type="date" value="'.$_GET['date'].'"/>
			<select name="state">';
				visit_state_select($_GET['state'], 1);
			echo '</select>
			<input type="submit" value="'.lang::BTN_SHOW.'"">
		</fieldset>
	</form>';
}*/
?>