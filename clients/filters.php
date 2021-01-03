<?php
echo '<p class="title">' . lang::SIDEBAR_FILTERS . '</p>';

echo '<form method="post" class="filter" action="/clients/client_profile.php">
	<fieldset class="noBorders">
		<input name="customers" class="clientProfile" placeholder="'.lang::SEARCH_CLIENT_PLACEHOLDER . ' / ' .lang::PHONE_PLACEHOLDER. '" value="'.$_POST['customers'].'" autocomplete="off">
		<input name="id" type="hidden" value="'.$_POST['id'].'">
		<input type="submit" value="'.lang::BTN_SHOW.'"">
	</fieldset>
</form>';

if($title == lang::MENU_VISITS || $title == lang::MENU_CALENDAR) {
	
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
}
?>