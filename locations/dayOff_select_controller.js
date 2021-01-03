jQuery(document).ready(function($){
	const date = $("input[name='date']").val();
	const weekday = $("select[name='weekday']").val();
	function select_controller(date, weekday) {
		if(date == '' && weekday == '' ) {
			$("input[name='date']").prop("disabled", false);
			$("select[name='weekday']").prop("disabled", false);
		} else if (date != '') {
			$("select[name='weekday']").val('');
			$("select[name='weekday']").prop("disabled", true);
		} else if (weekday != '') {
			$("input[name='date']").val('');
			$("input[name='date']").prop("disabled", true);
		}
	}

	select_controller(date, weekday);
	
	$("input[name='date'], select[name='weekday']").change(function() {
		var new_date = $("input[name='date']").val();
		var new_weekday = $("select[name='weekday']").val();
		select_controller(new_date, new_weekday);
	});
	
});