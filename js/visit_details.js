dragula([document.getElementById("spentLines")], {
	revertOnSpill: true,
	moves: function (el, container, handle) {
		return handle.classList.contains("handle");
	},
})
	.on("drag", function (el) {
		el.className = el.className.replace("ex-moved", "");
	})
	.on("drop", function (el) {
		el.className += " ex-moved";
	})
	.on("over", function (el, container) {
		container.className += " ex-over";
	})
	.on("out", function (el, container) {
		container.className = container.className.replace("ex-over", "");
	});

const edit = $('input[name="v_id"]').val();
if (edit > 0) {
	totalPrice();
	service_results_netto();
	salesTotals();
}
work_autocomplete();

var locationID = $('select[name="loc"]').val();
if (locationID == 0) {
	$('select[name="loc"]').parent().siblings().hide();
}
$(document).ready(function () {
	$('select[name="loc"]').change(function () {
		if ($(this).val() > 0) {
			$(this).parent().siblings().show();
			$(this).css("color", "black");
			locationID = $(this).val();
		} else {
			$(this).parent().siblings().hide();
		}
	});
});

function wage_rate(el) {
	var catID = el.siblings('input[name="catID[]"]').val(); //el = input name="work[]"
	var userID = el.siblings('select[name="staff[]"]').find("option:selected").val();
	var rate;

	var i = 0;
	while (ratesJson[i]["userID"] > 0) {
		if (ratesJson[i]["userID"] == userID && ratesJson[i]["specialtyID"] == catID)
			rate = ratesJson[i]["reward_rate"];
		i++;
	}
	if (rate >= 0) el.siblings('input[name="rate[]"]').val(rate);
	el.siblings("input[name='price[]']").focus();
}

function delete_staff_total_list(staffID) {
	var staffID_toDelete = staffID;

	//собираем перечень всех актуальных ID сотрудников
	var current_staff_IDs = [];
	var doNothing;
	$("select[name='staff[]']").each(function () {
		var toCompare = $(this).find("option:selected").val();
		if (staffID_toDelete == toCompare) {
			doNothing = 1;
		}
	});

	//удаляем из итогов сотрудника, которого уже нет в списке работ
	if (doNothing != 1) {
		$("input[name='staffID[]']").each(function () {
			toCompare = $(this).val();
			var isOld = $(this).siblings("input[name='staffRowIDs[]']").val();

			if (staffID_toDelete == toCompare) {
				if (isOld > 0) {
					$(this).val(0);
					$(this).siblings("input[name='staffPrices[]']").val(0);
					$(this).siblings("input[name='staff_wage[]']").val(0);
					$(this).siblings("input[name='staffTips[]']").val(0);
					$(this).siblings("input[name='staffName[]']").val("");
					$(this).parent().hide();
				} else {
					$(this).parent().next().detach(); // удаляем скрытый коммент
					$(this).parent().detach(); // удаляем сотрудника
				}
			}
		});
	} else {
		//пересчитываем итоги этого сотрудника
		var staff_price = 0;
		$("input[name='price[]']").each(function () {
			var staffID_this = $(this)
				.siblings("select[name='staff[]']")
				.find("option:selected")
				.val();
			if (staffID_toDelete == staffID_this) {
				staff_price += +$(this).val();
			}
		});

		$("input[name='staffPrices[]']").each(function () {
			var staffID_this = $(this).siblings("input[name='staffID[]']").val();
			if (staffID_toDelete == staffID_this) {
				$(this).val(staff_price);
			}
		});
	}
}

function update_staff_total_list() {
	var newID = $(this).val();
	var newName = $(this).find("option:selected").text();
	//собираем перечень всех актуальных ID сотрудников
	var current_staff_IDs = [];
	$("select[name='staff[]']").each(function () {
		var toAdd = $(this).find("option:selected").val();
		current_staff_IDs.push(toAdd);
	});
	//собираем перечень всех ID сотрудников из итогов
	var totals_staff_IDs = [];
	$("input[name='staffID[]']").each(function () {
		if ($(this).val() > 0) {
			var toAdd_totals = $(this).val();
			totals_staff_IDs.push(toAdd_totals);
		}
	});

	//удаляем дубликаты
	current_staff_IDs = Array.from(new Set(current_staff_IDs));

	//прогоняем через результаты
	$("input[name='staffID[]']").each(function () {
		var present = $.inArray($(this).val(), current_staff_IDs);

		if (present == -1) {
			//Нужно удалить из итогов
			$(this).parent().next().detach();
			$(this).parent().detach();
		}
		var present_in_totals = $.inArray(newID, totals_staff_IDs);
		if (present_in_totals == -1) {
			staffAdd();
			$('input[name="staffID[]"]').last().val(newID);
			$('input[name="staffName[]"]').last().val(newName);
			totals_staff_IDs.push(newID);
		}
	});
	staff_total_prices();

	//обновляем ставку
	var sib_el = $(this).siblings('input[name="workNames[]"]');
	wage_rate(sib_el);
}

function minMaxTotals() {
	var min = 0;
	var max = 0;
	$("input[name='minPrice[]']").each(function () {
		min += +$(this).val();
	});
	$("input[name='maxPrice[]']").each(function () {
		max += +$(this).val();
	});
	$("input[name='min']").val(min.toFixed(2));
	$("input[name='max']").val(max.toFixed(2));
}

function totalPrice() {
	var totalPrice = 0;
	$("input[name='price[]']").each(function () {
		totalPrice += +$(this).val();
	});
	$("input[name='price_total']").val(totalPrice.toFixed(2));
	$("input[name='totals_toPay_S']").val(totalPrice.toFixed(2));
	service_income();
}

function staff_total_prices() {
	//итоги по сотрудникам
	$("input[name='staffID[]']").each(function () {
		var el = $(this);
		var staff_total = 0;
		var staff_wage = 0;
		$("input[name='price[]']").each(function () {
			if (
				$(this)
					.siblings("select[name='staff[]']")
					.find("option:selected")
					.val() == el.val()
			) {
				staff_total += +$(this).val();

				staff_wage +=
					(+$(this).siblings("input[name='rate[]']").val() / 100) *
					$(this).val();
			}
		});
		el.siblings("input[name='staffPrices[]']").val(staff_total);
		el.siblings("input[name='staff_wage[]']").val(staff_wage);
		service_results_netto();
	});
}

function totalNetto() {
	var totalNetto = 0;
	$("input[name='nettoCost[]']").each(function () {
		totalNetto += +$(this).val();
	});
	$("input[name='netto_total']").val(totalNetto.toFixed(2));
	service_results_netto();
}

function service_results_netto() {
	var total_serv_netto = 0;
	$("input[name='staff_wage[]']").each(function () {
		if ($(this).val() != "undefined") total_serv_netto += +$(this).val();
	});

	if (
		$("input[name='totalSpentC']").val() != "undefined" &&
		$("input[name='netto_total']").val() != "undefined"
	) {
		total_serv_netto +=
			+(+$("input[name='netto_total']").val()) +
			+$("input[name='totalSpentC']").val();
	} else if ($("input[name='totalSpentC']").val() != "undefined") {
		total_serv_netto += +(+$("input[name='totalSpentC']").val());
	} else if ($("input[name='netto_total']").val() != "undefined") {
		total_serv_netto += +(+$("input[name='netto_total']").val());
	}

	$("input[name='totals_netto_S']").val(total_serv_netto.toFixed(2));
	service_income();
}

function service_income() {
	var income =
		+$("input[name='totals_toPay_S']").val() -
		+$("input[name='totals_netto_S']").val();
	$("input[name='totals_income_S']").val(income.toFixed(2));
	grand_total_income();
}

function spentTotals() {
	var spent_volume = 0;
	var spent_cost = 0;
	$("input[name='spentC[]']").each(function () {
		spent_cost += +$(this).val();
	});
	$("input[name='spentV[]']").each(function () {
		spent_volume += +$(this).val();
	});

	$("input[name='totalSpentV']").val(spent_volume);
	$("input[name='totalSpentC']").val(spent_cost.toFixed(2));
	service_results_netto();
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
	$("input[name='totals_toPay_sale']").val(totalSales.toFixed(2));
	$("input[name='totals_netto_sale']").val(total_sales_netto.toFixed(2));

	var sales_income = totalSales - total_sales_netto;
	$("input[name='totals_income_sale']").val(sales_income.toFixed(2));

	grand_total_income();
}

function grand_total_income() {
	var service = $("input[name='totals_income_S']").val();
	var sales = $("input[name='totals_income_sale']").val();
	var income = 0;
	if (service != "undefined" && sales != "undefined") {
		income = +service + +sales;
	} else if (service != "undefined") {
		income = service;
	} else if (sales != "undefined") {
		income = sales;
	}
	$("input[name='grand_total_income']").val(income.toFixed(2));
}

//Добавление строки работ
var _counter_WRK = $("input[name='wrk_count']").val();
var template_WRK = document.querySelector("#wrk");
var _counter_NETTO = $("input[name='netto_count']").val();
var template_NETTO = document.querySelector("#net");
var documentFragment_WRK = template_WRK.content;
function workAdd() {
	var oClone_WRK = template_WRK.content.cloneNode(true);

	oClone_WRK.id += _counter_WRK + "";
	document.getElementById("works").appendChild(oClone_WRK);

	$("#works").children("div").last().children("input[name='workNames[]']").focus();

	work_autocomplete();

	_counter_WRK++;

	/*$('.staffName'+_counter_WRK).autocomplete({
		serviceUrl: '/config/autocomplete.php?staffName',
		minChars:2,
		autoSelectFirst: true,
		preventBadQueries: false,
		onSelect: function (suggestion) {
			var res = suggestion.data.split("--");
			$(this).siblings("input[name='staffID[]']").val(res[0]);
		}
	});*/
}

//Добавление строки сотрудника
var _counter_STF = $("input[name='stf_count']").val();
var template_STF = document.querySelector("#stf");
var documentFragment_STF = template_STF.content;
function staffAdd() {
	_counter_STF++;
	var oClone_STF = template_STF.content.cloneNode(true);

	oClone_STF.id += _counter_STF + "";
	document.getElementById("employees").appendChild(oClone_STF);

	//Кликабельный коммент
	$(".fa-comment")
		.last()
		.addClass("comment" + _counter_STF);
	$(".comment" + _counter_STF).on("click", function () {
		$(this).parent().next().toggle();
	});
}

//Добавление строки расхода
var _counter_spent = $("input[name='spent_count']").val();
var template_spent = document.querySelector("#spent");
var documentFragment_spent = template_spent.content;
function spentAdd() {
	_counter_spent++;
	var oClone_spent = template_spent.content.cloneNode(true);

	oClone_spent.id += _counter_spent + "";
	document.getElementById("spentLines").appendChild(oClone_spent);
	$("#spentLines").children("div").last().children('input[name="cosmNames[]"]').focus();

	//Уникальный класс для добавленного поля
	$('input[name="cosmNames[]"]')
		.last()
		.addClass("spent" + _counter_spent);

	$(".spent" + _counter_spent).autocomplete({
		serviceUrl: "/config/autocomplete.php?spent&locationID=" + locationID,
		minChars: 2,
		autoSelectFirst: true,
		preventBadQueries: false,
		onSelect: function (suggestion) {
			var res = suggestion.data.split("--");

			var alreadySpent = 0;
			$("input[name='cosmID[]']").each(function () {
				if ($(this).val() == res[0]) {
					if ($(this).siblings("input[name='spentID[]']").val() > 0) {
						//do nothing
					} else {
						alreadySpent += +$(this).siblings("input[name='spentV[]']").val();
					}
				}
			});

			var balance = res[1] - alreadySpent;
			$(this).siblings("input[name='cosmID[]']").val(res[0]);
			$(this).siblings("input[name='spentV[]']").prop("placeholder", balance);
			$(this).siblings("input[name='spentV[]']").prop("max", balance);

			//$(this).width(input_width);
			//$(this).siblings("input[name='spentV[]']").css('width', '400px');

			$(this).siblings("input[name='balanceGr[]']").val(balance);
			$(this).siblings("input[name='cosmV[]']").val(res[2]);
			$(this).siblings("input[name='pcsOut[]']").val(res[3]);
		},
	});
}

//Добавление строки продажи
var _counter_sales = $('input[name="sales_count"]').val();
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

$(".fa-comment").on("click", function () {
	$(this).parent().next().toggle();
});

$(document).on("change", function () {
	//добавление итогов по сотрудникам
	$("select[name='staff[]']").change(update_staff_total_list);

	//удаление значений min-max при пустом поле названия косметики
	$("input[name='workNames[]']").on("keyup blur", function () {
		var workName = $(this).val();
		if (workName == "") {
			$(this).siblings("input[name='workID[]']").val(0);
			$(this).siblings("input[name='minPrice[]']").val(0);
			$(this).siblings("input[name='maxPrice[]']").val(0);
		}
		minMaxTotals(); //пересчет итогов
	});
	//пересчет итогов по сотрудникам
	$("input[name='price[]']").on("keyup blur", function () {
		totalPrice();
		staff_total_prices();
	});
	//пересчет итогов по цене нетто
	$("input[name='nettoCost[]']").on("keyup blur", function () {
		totalNetto();
	});
	$("input[name='netto_total']").on("keyup blur", function () {
		service_results_netto();
	});

	//пересчет итогов по расходной косметике
	$("input[name='spentV[]']").on("blur", function () {
		var currentV = $(this).val();

		//старая ли запись?
		if ($(this).siblings("input[name='spentV_old[]']").val() > 0) {
			//старая
			var oldV = $(this).siblings("input[name='spentV_old[]']").val();
			var oldC = $(this).siblings("input[name='spentC_old[]']").val();
			var costPerGram = +oldC / +oldV;
			var spentC = costPerGram * +currentV;
			$(this).siblings("input[name='spentC[]']").val(spentC);
		} else {
			// новая
			var id = $(this).siblings("input[name='cosmID[]']").val();
			var volume = $(this).siblings("input[name='cosmV[]']").val();
			var balance = $(this).prop("max").valueOf();
			var pcsOut = $(this).siblings("input[name='pcsOut[]']").val();
			var thisPCsRemainGr = balance % +volume; /// 0 = новый тюбик

			if (thisPCsRemainGr == 0) thisPCsRemainGr = +volume; // для нового тюбика

			var input = $(this);

			if (currentV > 0) {
				var xhttp = new XMLHttpRequest();
				$.ajax({
					type: "GET",
					url:
						"spent_price_ajax.php?locationID=" +
						locationID +
						"&id=" +
						id +
						"&pcsOut=" +
						pcsOut,
					success: function (data) {
						var costPerGram1 = +data / +volume; // !!!!!!

						if (currentV <= thisPCsRemainGr) {
							var spentC = costPerGram1 * +currentV;
							input
								.siblings("input[name='spentC[]']")
								.val(spentC.toFixed(2));
						} else {
							var spentV1 = +thisPCsRemainGr;
							var spentV2 = currentV - +thisPCsRemainGr;
							var spentC1 = costPerGram1 * +thisPCsRemainGr;

							var xhttp2 = new XMLHttpRequest();
							$.ajax({
								type: "GET",
								url:
									"spent_price_ajax.php?locationID=" +
									locationID +
									"&id=" +
									id +
									"&pcsOut=" +
									pcsOut +
									"offset=ceil",
								success: function (data2) {
									var costPerGram2 = +data2 / +volume;
									var spentC2 = +costPerGram2 * +spentV2;
									spentC = +spentC1 + +spentC2;
									input
										.siblings("input[name='spentC[]']")
										.val(spentC.toFixed(2));
								},
							});
						}
						spentTotals();
					},
				});
			}
		}
		spentTotals();
	});
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

	//апдейт итогов при ручном обновлении з/п
	$("input[name='staff_wage[]']").on("change", function () {
		service_results_netto();
	});
});

//Статус визита
function visit_state_toggle() {
	var visit_state = $("select[name='state']").val();
	if (visit_state == 10) {
		$("#nettoData").show();
		$("#spentData").show();
		$("#salesData").show();
		$("#employeeData").show();
		$("#totals").show();
	} else {
		$("#nettoData").hide();
		$("#spentData").hide();
		$("#salesData").hide();
		$("#employeeData").hide();
		$("#totals").hide();
	}
}

var currTime = Date.now();
visit_state_toggle();

$("input[name='date'], select[name='state']").on("change", function () {
	var visit_state = $("select[name='state']").val();
	var datefull = $("input[name='date']").val();
	date = datefull.split("-");
	var time = Date.UTC(date[0], date[1] - 1, date[2]);

	if (currTime > time) {
		visit_state_toggle();
	} else {
		//в будущем
		if (visit_state > 8) {
			alert(alert_future_date);
			$("select[name='state']").val(0);
		}
	}

	$("select[name='state']").change(visit_state_toggle);
});

function work_autocomplete() {
	//Уникальный класс для добавленного поля
	$('input[name="workNames[]"]')
		.last()
		.addClass("workName" + _counter_WRK);

	$(".workName" + _counter_WRK).autocomplete({
		serviceUrl: "/config/autocomplete.php?workName",
		minChars: 2,
		autoSelectFirst: true,
		preventBadQueries: false,
		onSelect: function (suggestion) {
			var res = suggestion.data.split("--");
			var el = $(this);
			el.siblings("input[name='workID[]']").val(res[0]);
			el.siblings("input[name='minPrice[]']").val(res[1]);
			el.siblings("input[name='maxPrice[]']").val(res[2]);
			el.siblings("input[name='catID[]']").val(res[5]);

			//Сотрудники в выбранной категории
			var catID = res[5];

			//проверка, выбран ли уже сотрудник в данной услуге (нужно для редактирования услуги)
			if (el.siblings('select[name="staff[]"]').val() > 0) {
				var lastSelectVal = el.siblings('select[name="staff[]"]').val();
			} else {
				var lastSelectVal = el
					.parent()
					.prev()
					.children('select[name="staff[]"]')
					.val();
			}
			var xhttp3 = new XMLHttpRequest();
			$.ajax({
				type: "GET",
				url:
					"staff_data_ajax.php?locationID=" +
					locationID +
					"&catID=" +
					catID +
					"&lastSelected=" +
					lastSelectVal,
				success: function (options) {
					el.siblings('select[name="staff[]"]').empty().append(options);

					//Добавляем строку итога по сотруднику
					var name = el
						.siblings('select[name="staff[]"]')
						.find("option:selected")
						.text();
					var staff_id = el
						.siblings('select[name="staff[]"]')
						.find("option:selected")
						.val();
					if (_counter_WRK > 1) {
						//фактически = 0, т.к. это колбек функция
						var abort = 0;
						$("input[name='staffName[]']").each(function () {
							if ($(this).val() == name) abort = 1;
						});
						if (abort == 0) {
							staffAdd();
							$('input[name="staffName[]"]').last().val(name);
							$('input[name="staffID[]"]').last().val(staff_id);
						}
					} else {
						document.getElementById("employees").innerHTML = ""; // очищаем блок на всякий случай
						staffAdd();
						$('input[name="staffName[]"]').last().val(name);
						$('input[name="staffID[]"]').last().val(staff_id);
					}

					//ставка з/п
					wage_rate(el);
				},
			});

			//себестоимость работ по данной услуге
			var newNettoNames = res[3].split("|");
			var newNettoCosts = res[4].split("|");

			var netName = null;
			var netCost = null;
			var x = 0;
			while (newNettoNames[x] != null) {
				var iterations = 0;
				var notEqual = 0;

				$("input[name='nettoNames[]']").each(function () {
					var exist = $(this).val();
					if (newNettoNames[x] != exist) {
						netName = newNettoNames[x];
						netCost = newNettoCosts[x];
						notEqual++;
					}
					iterations++;
				});

				if (notEqual == iterations) {
					var oClone_NETTO = template_NETTO.content.cloneNode(true);

					oClone_NETTO.id += _counter_NETTO + "";
					document.getElementById("netto").appendChild(oClone_NETTO);

					$('input[name="nettoNames[]"]').last().val(netName);
					$('input[name="nettoCost[]"]').last().val(netCost);
					totalNetto();

					_counter_NETTO++;
				}

				netName = null;
				netCost = null;
				x++;
			}
		},
	});
}

$("#works").on("click", "i.work", deleteWork);
$("#spentData").on("click", "i.spent", deleteSpent);
$("#salesData").on("click", "i.sales", deleteSale);

function deleteWork() {
	var staffID = $(this).siblings("select[name='staff[]']").val();
	var isOld = $(this).siblings("input[name='visits_worksIDs[]']").val();

	if (isOld > 0) {
		$(this).siblings("input[name='workID[]']").val(0);
		$(this)
			.siblings("select[name='staff[]']")
			.append($("<option>").val(0).text("del")); //добавляем опцию с нулем
		$(this).siblings("select[name='staff[]']").val(0); //устанавливаем 0
		$(this).siblings("input[name='minPrice[]']").val(0);
		$(this).siblings("input[name='maxPrice[]']").val(0);
		$(this).siblings("input[name='price[]']").val(0);
		$(this).parent().hide();
	} else {
		$(this).parent().detach();
	}

	delete_staff_total_list(staffID);
	totalPrice();
	minMaxTotals();
}

function deleteSale() {
	$(this).siblings("input[name='sold_cosmID[]']").val(0);
	$(this).siblings("input[name='qty[]']").val(0);
	$(this).siblings("input[name='priceSold[]']").val(0);
	$(this).siblings("input[name='sold_price_total[]']").val(0);
	$(this).parent().hide();
	salesTotals();
	var isOld = $(this).siblings("input[name='soldRowIDs[]']").val();
	if (isOld != "") {
	} else {
		$(this).siblings().prop("disabled", true);
	}
}

function deleteSpent() {
	$(this).siblings("input[name='cosmID[]']").val(0);
	$(this).siblings("input[name='spentV[]']").val(0);
	$(this).siblings("input[name='spentC[]']").val(0);
	$(this).parent().hide();
	spentTotals();
	var isOld = $(this).siblings("input[name='spentID[]']").val();
	if (isOld > 0) {
	} else {
		$(this).siblings().prop("disabled", true);
	}
}

function delete_visit() {
	let action = confirm(alert_txt + "?");
	if (action === true) {
		$("input[name='del_visit']").val("true");
		form.submit();
	} else {
		return false;
	}
}

$(".del_visit").on("click", delete_visit);

$("#netto_header").on("click", function () {
	$("#netto").toggle();
	$("#netto_totals").toggle();
});

$("#sales_header").on("click", function () {
	$("#sales_hide").toggle();
});
