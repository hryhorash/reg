<?php 
$access = 10;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
include('tabs.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	$_SESSION["locationSelected"] = $_POST['loc'];
	
	$_SESSION['temp'] = array(
		'dateOut' 			=> $_POST['dateOut'],
		'customers' 		=> $_POST['customers'],
		'clientID' 			=> $_POST['clientID'],
		'soldName' 			=> $_POST['soldName'],
		'sold_cosmID' 		=> $_POST['sold_cosmID'],
		'qty' 				=> $_POST['qty'],
		'priceSold' 		=> $_POST['priceSold'],
		'sold_price_total' 	=> $_POST['sold_price_total'],
		'price_total' 		=> $_POST['price_total'],
		'totals_income_sale'=> $_POST['totals_income_sale'],
		'sell_netto'		=> $_POST['sell_netto'],
		'sell_available'	=> $_POST['sell_available']
	);
		
	
	if($_POST['clientID'] == '') {
		$_SESSION['error'] = lang::ERR_NO_CLIENT;
		session_write_close();
		header( 'Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
	
	$availableIDs = explode(',', $_POST['sell_available']);
	
	$sql = "UPDATE received 
			SET dateOut 	= :dateOut,
				priceOut   	= :priceOut, 
				soldToID   	= :soldToID, 
				qtyOut   	= 1, 
				`timestamp`	= null, 
				author		= :author
			WHERE id = :id";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(':dateOut', $_POST['dateOut'], PDO::PARAM_STR);
		$stmt -> bindValue(':priceOut', $_POST['priceSold'], PDO::PARAM_STR);
		$stmt -> bindValue(':soldToID', $_POST['clientID'], PDO::PARAM_INT);
		$stmt -> bindValue(':author', $_SESSION['userID'], PDO::PARAM_INT);
		
		
		$count = 0;
		while($count < $_POST['qty']) {
			$stmt -> bindValue(':id', $availableIDs[$count], PDO::PARAM_INT);
			$stmt ->execute();
			$count++;
		}
		unset($_SESSION['temp']);
		
		
	} catch (PDOException $ex){
		include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
		$_SESSION['error'] = $ex;
	}
	session_write_close();
	header( 'Location: ' . $_SERVER['PHP_SELF']);
	exit;
}

$title=lang::HDR_NEW_SALE;
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, 'sell');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'. $title .'</h2>';?>
	
	<form method="post">
		<fieldset>
			<?=location_options(1, null, $_SESSION['temp']['locationID'], 1);?>
			<div class="row">
				<label for="dateOut"><?=lang::DATE;?>*:</label>
				<input name="dateOut" type="date" value="<?=$_SESSION['temp']['dateOut'];?>" required />
			</div>
			<div class="row">
				<label for="clientID"><?=lang::TBL_CLIENT;?>:</label>
				<input name="customers" class="FIO" placeholder="<?=lang::SEARCH_CLIENT_PLACEHOLDER;?>" value="<?=$_SESSION['temp']['customers'];?>" autocomplete="off">
				<input name="clientID" type="hidden" value="<?=$_SESSION['temp']['clientID'];?>">
			</div>
				
				
			<div class="row nested">
				<input type="text" class="mobile-wide input-hdr bold" value="<?=lang::HDR_ITEM_NAME;?>" disabled />
				<input type="text" class="input-hdr bold short" value="<?=lang::PLACEHOLDER_QTY;?>" disabled />
				<input type="text" class="input-hdr bold short" value="<?=lang::HDR_PRICE;?>" disabled />
				<input type="text" class="input-hdr bold short" value="<?=lang::HDR_TOTAL;?>" disabled />
			</div>	
			
			<div class="row nested">
				<input name="soldName" type="text" class="sold mobile-wide" placeholder="<?=lang::HDR_ITEM_NAME;?>" value="<?=$_SESSION['temp']['soldName'];?>" />
				<input name="qty" type="number" class="short" step="1" value="<?=$_SESSION['temp']['qty'];?>"  />
				<input name="priceSold" type="number" class="short" step="0.01" value="<?=$_SESSION['temp']['priceSold'];?>" />
				<input name="sold_price_total" type="number" class="short" step="0.01" value="<?=$_SESSION['temp']['sold_price_total'];?>" tabindex="-1" readonly />
				<input name="sold_cosmID" value="<?=$_SESSION['temp']['sold_cosmID'];?>" type="hidden" />
				<input name="sell_netto" type="hidden" />
				<input name="sell_available" type="hidden" />
			</div>
			
			<div class="row nested">
				<input type="text" class="input-hdr bold mobile-wide" value="<?=lang::HDR_PROFIT_TOTAL;?>" disabled />
				<input name="totals_income_sale" type="number" class="input-hdr bold short" value="<?=$_SESSION['temp']['totals_income_sale'];?>" readonly />
			</div>	
		</fieldset>
		<input type="submit" value="<?php echo lang::BTN_SELL; ?>" />
	</form>
</section>

<?php 
include($_SERVER['DOCUMENT_ROOT'].'/layout/footer.php');	
unset($_SESSION['temp']);
?> 
<script>
	var locationID = $('select[name="loc"]').val();
	$(document).ready(function () {
		$('select[name="loc"]').change(function(){
			locationID = $(this).val();
		});
	});

	$('.sold').autocomplete({
		serviceUrl: '/config/autocomplete.php?sold&locationID='+locationID,
		minChars:2,
		autoSelectFirst: true,
		preventBadQueries: false,
		onSelect: function (suggestion) {
			var res = suggestion.data.split("--");
			$(this).siblings("input[name='sold_cosmID']").val(res[0]);
			$(this).siblings("input[name='qty']").prop('placeholder', res[1]);
			$(this).siblings("input[name='qty']").prop('max', res[1]);
			$(this).siblings("input[name='priceSold']").val(res[2]);
			$(this).siblings("input[name='sell_netto']").val(res[3]); //аггрегированные через запятую суммы
			$(this).siblings("input[name='sell_available']").val(res[4]); //аггрегированные через запятую айдишки
		}
	});
	
	function salesTotals() {
		var qty = $("input[name='qty']").val();
		var totalSales = qty * $("input[name='priceSold']").val();
		var total_sales_netto = 0;
			
			var cur_netto_prices = $(this).siblings("input[name='sell_netto']").val().split(",");
			var qty_count = 0;
			while(qty >= (qty_count+1)) {
				total_sales_netto  += +cur_netto_prices[qty_count];
				qty_count++;
			}
			
			
		var sales_income = totalSales - total_sales_netto;
		$("input[name='totals_income_sale']").val(sales_income.toFixed(2));
		$("input[name='sold_price_total']").val(totalSales.toFixed(2));
	}
	
	$("input[name='qty'], input[name='priceSold").on('keyup blur', salesTotals);
</script>
