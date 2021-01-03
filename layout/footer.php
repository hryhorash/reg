	</main>
			
	<div id="footer">
	</div>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
	<script src="/js/menu.js"></script>
		
	<?php if ($_SESSION['loggedin'] == true) { ?>
		<script src="/js/jquery.maskedinput.min.js"></script>
		<script src="/js/jquery.autocomplete.min.js"></script>
		<script src="/js/table-sort.js"></script>
		<script src="/js/my.js"></script>
	<?php } ?>
</body>
</html>
<script>
// защита от случайного двойного нажатия на кнопку отправки формы
// почему-то не работает внутри my.js
document.querySelectorAll('form').forEach((form) => {
	form.addEventListener('submit', (e) => {
		if (form.classList.contains('is-submitting')) {
			e.preventDefault();
			e.stopPropagation();
			return false;	
		};

		form.classList.add('is-submitting');
	});
});
</script>