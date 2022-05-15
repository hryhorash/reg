const date = $("input[name='date']").val();
const weekday = $("select[name='weekday']").val();
const even = $("select[name='even']").val();

function select_controller(date, weekday, even) {
	if(date == '' && weekday == '' && even == '') {
		$("input[name='date']").prop("disabled", false);
		$("select[name='weekday']").prop("disabled", false);
		$("select[name='even']").prop("disabled", false);
	} else if (date != '') {
		$("select[name='weekday']").val('');
		$("select[name='weekday']").prop("disabled", true);
		$("select[name='even']").val('');
		$("select[name='even']").prop("disabled", true);
	} else if (weekday != '') {
		$("input[name='date']").val('');
		$("input[name='date']").prop("disabled", true);
		$("select[name='even']").val('');
		$("select[name='even']").prop("disabled", true);
	} else if (even != '') {
		$("input[name='date']").val('');
		$("input[name='date']").prop("disabled", true);
		$("select[name='weekday']").val('');
		$("select[name='weekday']").prop("disabled", true);
	}
}

$(document).ready(function(){
	select_controller(date, weekday, even);
	
	$("input[name='date'], select[name='weekday'], select[name='even']").change(function() {
		var new_date = $("input[name='date']").val();
		var new_weekday = $("select[name='weekday']").val();
		var new_even = $("select[name='even']").val();
		select_controller(new_date, new_weekday, new_even);
	});
	
});