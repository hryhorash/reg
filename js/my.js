$(document).ready(function () {
	// ВЫБРАТЬ САЛОН!
	var loc = $("#header_loc").val();
	if (loc == "") document.getElementsByName("loc")[0].style.color = "orange";
});

$("#header_loc").change(function () {
	if ($(this).val() > 0) {
		const locationID_header = $(this).val();

		xhttp_loc = new XMLHttpRequest();
		$.ajax({
			type: "POST",
			data: { loc: locationID_header },
			success: function (data) {
				location.reload();
			},
		});
	}
});

if (
	$('input[name="date"]').length &&
	$('select[name="weekday"]').length &&
	$('select[name="even"]').length
) {
	$('input[name="date"], select[name="weekday"], select[name="even"]').on(
		"change",
		function () {
			$(this).siblings().val("");
		}
	);
}

// AUTOCOMPLETE

$("input[name='city']").autocomplete({
	serviceUrl: "/config/autocomplete.php?city",
	minChars: 2,
	autoSelectFirst: true,
	preventBadQueries: false,
	onSelect: function (suggestion) {
		//alert('You selected: ' + suggestion.value + ', ' + suggestion.data);
	},
});

$(".worktype_cat").autocomplete({
	serviceUrl: "/config/autocomplete.php?worktype_cat",
	autoSelectFirst: true,
	preventBadQueries: false,
	onSelect: function (suggestion) {},
});

$(".expences_cat").autocomplete({
	serviceUrl: "/config/autocomplete.php?catID",
	autoSelectFirst: true,
	preventBadQueries: false,
	onSelect: function (suggestion) {
		$("input[name='catID_autocomplete']").val(suggestion.data);
	},
});

$(".FIO").autocomplete({
	serviceUrl: "/config/autocomplete.php?FIO",
	minChars: 2,
	autoSelectFirst: true,
	preventBadQueries: false,
	onSelect: function (suggestion) {
		$("input[name='clientID']").val(suggestion.data);
	},
});

$(".clientProfile").autocomplete({
	serviceUrl: "/config/autocomplete.php?clientProfile",
	minChars: 2,
	autoSelectFirst: true,
	preventBadQueries: false,
	onSelect: function (suggestion) {
		$("input[name='id']").val(suggestion.data);
	},
});

$('input[name="workNames[]"]').autocomplete({
	serviceUrl: "/config/autocomplete.php?workName",
	minChars: 2,
	autoSelectFirst: true,
	preventBadQueries: false,
	onSelect: function (suggestion) {
		var res = suggestion.data.split("--");
		$(this).siblings("input[name='workID[]']").val(res[0]);
		//$(this).siblings("input[name='work_cat']").val(res[1]);
		$(this).siblings("input[name='minPrice[]']").val(res[2]);
		$(this).siblings("input[name='maxPrice[]']").val(res[3]);
		$(this).siblings("input[name='workID_change[]']").val(1);
	},
});

$(".sale").autocomplete({
	serviceUrl: "/config/autocomplete.php?sale",
	minChars: 2,
	autoSelectFirst: true,
	preventBadQueries: false,
	onSelect: function (suggestion) {
		$("input[name='cosmID']").val(suggestion.data);
	},
});

$('input[name="cosmHistory"]').autocomplete({
	serviceUrl: "/config/autocomplete.php?supplierID",
	minChars: 2,
	autoSelectFirst: true,
	preventBadQueries: false,
	onSelect: function (suggestion) {
		let cosmID = suggestion.data.split("--")[0];
		$("input[name='cosmID']").val(cosmID);
	},
});
