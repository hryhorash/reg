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
		
	
	if($_POST['clientID'] == '') {
		$_SESSION['error'] = lang::ERR_NO_CLIENT;
		session_write_close();
		header( 'Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
	
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


	if(isset($_POST['sold_cosmID'])) {
		$i = 0;
		foreach($_POST['sold_cosmID'] as $cosmID) {
			switch(true)
			{
				case($cosmID == 0 && $_POST['soldRowIDs'][$i] != ''):
					break;
				
					//ДОБАВЛЯЕМ ПРОДАЖУ
				case($cosmID > 0 && !isset($_POST['soldRowIDs'][$i])):
					$rows = explode(',', $_POST['sell_available'][$i]);
					sort($rows);
					$pricePerOne = $_POST['sold_price_total'][$i] / $_POST['qty'][$i];
					$count = 1;
					$s = 0;
					while($count <= $_POST['qty'][$i]){
						saleUpdate($rows[$s], $_POST['dateOut'], $pricePerOne, $_POST['clientID']); 
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


	session_write_close();
	header( 'Location: ' . $_SERVER['PHP_SELF']);
	exit;
}


$soldtemplate = '<div class="row col-5__1st_wide">
		<input name="soldName[]" type="text" class="mobile-wide" placeholder="'.lang::HDR_ITEM_NAME.'" />
		<input name="qty[]" type="number" class="short" step="1"  min="1" required  />
		<input name="priceSold[]" type="number" class="short" step="0.01" />
		<input name="sold_price_total[]" type="number" class="short" step="0.01" value="0" tabindex="-1" readonly />
		<div class="tooltip" style="position:absolute;right: 20px; width: 80px;">&nbsp;</div>
		<input name="sold_cosmID[]" type="hidden" />
		<input name="sell_netto[]" type="hidden" />
		<input name="sell_available[]" type="hidden" />
	</div>';

$title=lang::HDR_NEW_SALE;
//----------VIEW --------------------------------------------------------------------------
include($_SERVER['DOCUMENT_ROOT'].'/layout/head.php'); 
echo '<section class="sidebar">';
	echo tabs($tabs, 'sell');
echo '</section>';

echo '<section class="content">';
	include($_SERVER['DOCUMENT_ROOT'].'/config/session_messages.php');
	echo '<h2>'. $title .'</h2>';?>
	
	<template id="sold">
		<?=$soldtemplate;?>
	</template>

	<form method="post">
		<fieldset>
			<div class="row col-2 ">
				<?=location_options(1, null, $_SESSION['temp']['locationID'], 1);?>
			
				<label for="dateOut"><?=lang::DATE;?>*:</label>
				<input name="dateOut" type="date" value="<?=$_SESSION['temp']['dateOut'];?>" required />
			
				<label for="clientID"><?=lang::TBL_CLIENT;?>:</label>
				<input name="customers" class="FIO" placeholder="<?=lang::SEARCH_CLIENT_PLACEHOLDER;?>" value="<?=$_SESSION['temp']['customers'];?>" autocomplete="off" required>
				<input name="clientID" type="hidden" value="<?=$_SESSION['temp']['clientID'];?>">
			</div>
				
			<section id="salesData">
				<div class="row col-5__1st_wide">
					<input type="text" class="mobile-wide input-hdr bold" value="<?=lang::HDR_ITEM_NAME;?>" disabled />
					<input type="text" class="input-hdr bold short" value="<?=lang::PLACEHOLDER_QTY;?>" disabled />
					<input type="text" class="input-hdr bold short" value="<?=lang::HDR_PRICE;?>" disabled />
					<input type="text" class="input-hdr bold short mobile-hide" value="<?=lang::HDR_TOTAL;?>" disabled />
					<div style="width:3ch;"></div>
				</div>	
				<div id="salesLines">
					<div class="row col-5__1st_wide">
						<input name="soldName[]" type="text" class="mobile-wide sold1" placeholder="<?=lang::HDR_ITEM_NAME;?>" />
						<input name="qty[]" type="number" class="short" step="1" min="1" required />
						<input name="priceSold[]" type="number" class="short" step="0.01" />
						<input name="sold_price_total[]" type="number" class="short" step="0.01" value="0" tabindex="-1" readonly />
						<div class="tooltip" style="position:absolute;right: 20px; width: 80px;">&nbsp;</div>
						<input name="sold_cosmID[]" type="hidden" />
						<input name="sell_netto[]" type="hidden" />
						<input name="sell_available[]" type="hidden" />
					</div>
				
				</div>	
				<input type="button" value="<?=lang::BTN_ADD_SALE;?>" onclick="saleAdd();" />
				<div class="row col-5__1st_wide">
					<input type="text" class="input-hdr bold" value="<?=lang::HDR_TOTAL;?>" disabled />
					<input name="totalQty" type="number" class="input-hdr bold short" value="<?=$totalQty;?>" readonly />
					<input class="input-hdr bold short mobile-hide" disabled />
					<div class="tooltip" style="position:absolute;right: 20px; width: 80px;">&nbsp;</div>
					<input name="totalSales" type="number" class="input-hdr bold short" value="<?=$totalSales;?>" readonly />
					<div style="width:3ch;"></div>
				</div>	
					
			</section>	
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
if (locationID == 0) {
	$('select[name="loc"]').parent().siblings().hide();
}
const alert_sales_limit = '<?=lang::ALERT_EXCEED_LIMIT;?>';
const alert_sales_max = '<?=lang::ALERT_EXCEED_MAX;?>';	
const profit_lable = '<?=lang::HDR_PROFIT;?>';
const pwr = <?=$_SESSION['pwr'];?>

//пепвая строка продаж
$(".sold1").autocomplete({
		serviceUrl: "/config/autocomplete.php?sold&locationID=" + locationID,
		minChars: 2,
		autoSelectFirst: true,
		preventBadQueries: false,
		onSelect: function (suggestion) {
			var res = suggestion.data.split("--");
			$(this).siblings("input[name='sold_cosmID[]']").val(res[0]);
			$(this).siblings("input[name='qty[]']").prop("placeholder", res[1]);
			$(this).siblings("input[name='qty[]']").prop("max", res[1]);
			$(this).siblings("input[name='priceSold[]']").val(res[2]);
			$(this).siblings("input[name='sell_netto[]']").val(res[3]); //аггрегированные через запятую суммы
			$(this).siblings("input[name='sell_available[]']").val(res[4]); //аггрегированные через запятую айдишки
		},
	});


//Добавление строки продажи
var _counter_sales = 1;
var template_sales = document.querySelector("#sold");
var documentFragment_spent = template_sales.content;
function saleAdd() {
	_counter_sales++;
	var oClone_sales = template_sales.content.cloneNode(true);

	oClone_sales.id += _counter_sales + "";
	document.getElementById("salesLines").appendChild(oClone_sales);
	$("#salesLines").children("div").last().children('input[name="soldName[]"]').focus();

	//Уникальный класс для добавленного поля
	$('input[name="soldName[]"]')
		.last()
		.addClass("sold" + _counter_sales);

	$(".sold" + _counter_sales).autocomplete({
		serviceUrl: "/config/autocomplete.php?sold&locationID=" + locationID,
		minChars: 2,
		autoSelectFirst: true,
		preventBadQueries: false,
		onSelect: function (suggestion) {
			var res = suggestion.data.split("--");
			$(this).siblings("input[name='sold_cosmID[]']").val(res[0]);
			$(this).siblings("input[name='qty[]']").prop("placeholder", res[1]);
			$(this).siblings("input[name='qty[]']").prop("max", res[1]);
			$(this).siblings("input[name='priceSold[]']").val(res[2]);
			$(this).siblings("input[name='sell_netto[]']").val(res[3]); //аггрегированные через запятую суммы
			$(this).siblings("input[name='sell_available[]']").val(res[4]); //аггрегированные через запятую айдишки
		},
	});
}

function salesTotals() {
	var Qty_s = 0;
	var totalSales = 0;
	var total_sales_netto = 0;
	$("input[name='qty[]']").each(function () {
		Qty_s += +$(this).val(); //общее количество

		var cur_qty = $(this).val();
		var cur_netto_prices = $(this)
			.siblings("input[name='sell_netto[]']")
			.val()
			.split(",");
		var qty_count = 0;
		while (cur_qty >= qty_count + 1) {
			total_sales_netto += +cur_netto_prices[qty_count];
			qty_count++;
		}
	});
	$("input[name='sold_price_total[]']").each(function () {
		totalSales += +$(this).val();
	});

	$("input[name='totalQty']").val(Qty_s);
	$("input[name='totalSales']").val(totalSales.toFixed(2));
		
	
	var sales_income = totalSales - total_sales_netto;
	$("input[name='totalSales']").siblings(".tooltip")
									.attr("data-tooltip", profit_lable + ": " + sales_income.toFixed(2));

						
}

$(document).on("change", function () {
	
	//пересчет итогов по продажам
	$("input[name='qty[]'], input[name='priceSold[]").on("keyup blur", function () {
		let q = $(this).parent().children("input[name='qty[]']").val();
		let p = $(this).parent().children("input[name='priceSold[]']").val();
		let netto = $(this).siblings("input[name='sell_netto[]']").val().split(",");
		let limit = $(this)
			.parent()
			.children("input[name='qty[]']")
			.prop("max")
			.valueOf();
		let old = $(this).siblings("input[name='qtyOld[]']").val();
		if (q > limit && old > 0) {
			$(this).parent().children("input[name='qty[]']").val(limit);
			alert(alert_sales_limit);
		} else if (q > limit) {
			$(this).parent().children("input[name='qty[]']").val(limit);
			alert(alert_sales_max);
			q = limit;
		}

		if (q > 0 && p > 0) {
			$(this)
				.siblings("input[name='sold_price_total[]']")
				.val((p * q).toFixed(2));

			let i = 0;
			let totalNetto_this = 0;
			while (i < q) {
				totalNetto_this += p - netto[i];
				i++;
			}

			$(this)
				.siblings(".tooltip")
				.attr("data-tooltip", profit_lable + ": " + totalNetto_this.toFixed(2));
		}
		salesTotals();
	});

	
});
	
</script>

