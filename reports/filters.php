<?php echo '<p class="title">' . lang::SIDEBAR_FILTERS . '</p>';
	echo '<form method="get" class="filter">
		<fieldset class="noBorders" >';
			echo '<select name="month">';
				month_options('visits');
			echo '</select>';
			echo '<input type="submit" value="'. lang::BTN_SHOW.'" />';
		echo'</fieldset>';
	echo '</form>';
?>