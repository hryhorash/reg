<script>
$("select[name='category']").change(function(){
		
	//При изначально установленном значении
	var xhttp = new XMLHttpRequest();
	$.ajax({
		type: "GET",
		url: "/expences/subcat_ajax.php",
		data:	{ "category": $("#category option:selected").val(), "subcategory":  $("#subcatID option:selected").val() },  
		success: function(data){
			document.getElementById("subcategory").innerHTML = data;
		  }
	});
	
	
	
	// При изменении значения	
	$("#category").change(function() {
		var xhttp = new XMLHttpRequest();
		$.ajax({
			type: "GET",
			url: "/expences/subcat_ajax.php",
			data:	{ "category": $("#category option:selected").val() },  		success: function(data){
				document.getElementById("subcategory").innerHTML = data;			  
			}
		});
	});
});
</script>