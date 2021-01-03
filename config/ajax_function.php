<script>
$(document).ready(function () {
	//При изначально установленном значении
	var xhttp;    
	  xhttp = new XMLHttpRequest();
		$.ajax({
				type: "GET",
				url: "/expences/subcat_ajax.php",
				data:	{ "category": $("#category option:selected").val(), "subcategory": "<?php if(isset($_SESSION['temp']['subcatID'])) echo $_SESSION['temp']['subcatID']; else echo $data['subcatID'];?>" },  
				success: function(data){
					document.getElementById("subcategory").innerHTML = data;
				  }
			});
	
	
	
	// При изменении значения	$("#category").change(function() {	  var xhttp;    	  xhttp = new XMLHttpRequest();		$.ajax({				type: "GET",				url: "/expences/subcat_ajax.php",				data:	{ "category": $("#category option:selected").val() },  				success: function(data){					document.getElementById("subcategory").innerHTML = data;				  }			});	});
});</script>