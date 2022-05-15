<nav>
	<div class="logo">
		<a href="/user/dashboard.php"><img src="<?=$_SESSION['settings']['logoURL'];?>"></a>
	</div>
	
	<?php 
		$pos = strpos($_SERVER['REQUEST_URI'], '?');
		if ($pos === false) {
			$url = $_SERVER['REQUEST_URI'];
		} else {
			$pcs = explode ( '?', $_SERVER['REQUEST_URI'] );
			$url = $pcs[0];
		}
		
		if ($url == '/' 
		OR $url == '/index.php'
		OR $url == '/user/restore_pass.php') {
		}
		else { ?>
		
	<ul class="nav-links">
		<?php if ($_SESSION["pwr"] > 9) { ?>
			<li><a href="/clients/client_list.php"><i class="fas fa-address-book"></i><?=lang::MENU_CLIENTS;?></a></li>
		<?php } ?>
		<!--li><a href="/visits/visits_list.php"><i class="fas fa-clipboard-check"></i><!--?=lang::MENU_VISITS;?></a></li-->		
		
		<!--li onclick="menuExpand(3);"><i class="fas fa-tags"></i><!--?=lang::MENU_PRICELIST;?></li>
		<div class="drop-down" id='3'>
			<li><a href="#">Товары</a></li>
		</div-->
		
		<li><a href="/worktype/work_list.php"><i class="fas fa-tags"></i><?=lang::MENU_WORKS;?></a></li>
			
		
		<li><a href="/cosmetics/cosmetics_list.php"><i class="fas fa-star"></i><?=lang::MENU_COSMETICS;?></a></li>

		<?php if ($_SESSION["pwr"] < 10) { ?>
			<li><a href="/reports/wages.php"><i class="fas fa-coins"></i><?=lang::HDR_WAGE;?></a></li>
		<?php } ?>	
		
		<?php if ($_SESSION["pwr"] > 9) { ?>
			<li><a href="/expences/expencesList.php/"><i class="fas fa-coins"></i><?=lang::MENU_EXPENCES;?></a></li>
		<?php } ?>		
		<!--li onclick="menuExpand(2);"><i class="fas fa-file-alt"></i><!--?=lang::MENU_REPORTS;?></li>
		<div class="drop-down" id='2'>
			<li><a href="#">Косметика в работу</a></li>
			<li><a href="#">Косметика в продажу</a></li>
		</div-->
		
		
			<?php if ($_SESSION["pwr"] > 89) { ?>
				<!-- <li class="flex" onclick="menuExpand(4);"><i class="fas fa-chart-line"></i>lang::MENU_ANALYTICS;</li>
				<div class="drop-down" id='4'>
					<li><a href="/reports/finance_main.php/">lang::H2_FINANCE_REPORT;</a></li>
					<li><a href="/reports/client_value.php/">lang::MENU_CLIENT_VALUE;</a></li>
				</div> -->
				<li><a href="/reports/finance_main.php"><i class="fas fa-chart-line"></i><?=lang::MENU_ANALYTICS;?></a></li>
			<?php } 
		if ($_SESSION["pwr"] > 9) { ?>
			
			<li class="flex" onclick="menuExpand(5);"><i class="fas fa-cogs"></i><?=lang::MENU_SETTINGS;?></li>
			<div class="drop-down" id='5'>
				
				<?php if ($_SESSION["pwr"] > 89) { 
					echo '<li><a href="/locations/list.php">'. lang::MENU_LOCATIONS .'</a></li>';
				} 
					echo '<li><a href="/locations/location_daysOff.php">'. lang::MENU_LOCATION_DAYS_OFF.'</a></li>';
					echo '<li><a href="/reports/expences_works_match.php">'. lang::H2_EXPENCES_WORKS_MATCH.'</a></li>';
					
				?>
				
				<li><a href="/user/userList.php"><?=lang::MENU_USERS;?></a></li>
			</div>
		<?php }?>
					
		<li class="profile">
			<a href="/user/profile.php"><i class="fas fa-user"></i><?php echo $_SESSION["name"] . ' '. $_SESSION["surname"]?></a>
			<a href="/user/logout.php" ><i class="fas fa-sign-out-alt"></i><?php echo lang::LOGOUT;?></a>
		</li>
	</ul>
	
	<div class="burger">
		<div class="line1"></div>
		<div class="line2"></div>
		<div class="line3"></div>
	</div>
	<?php }?>
</nav>