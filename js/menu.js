window.onload=function(){
	const navSlide = () => {
	const burger = document.querySelector('.burger');
	const nav = document.querySelector('.nav-links');
	const navLinks = document.querySelectorAll('.nav-links li');
	
		burger.addEventListener('click', () => {
			// Toggle Nav
			nav.classList.toggle('nav-active');
			
			//Animate links
			navLinks.forEach((link) => {
				if  (link.style.animation)
					 link.style.animation = '';
				else link.style.animation = `fadeIn ease 1s`;
			});
			burger.classList.toggle('toggle');
		});
	}
	
	navSlide();
}

function menuExpand(id) {
	var dropDown = document.getElementById(id);
	dropDown.classList.toggle('drop-down-active');
	
	/*var header = $(this).prev('li').children('a').val();
	alert(header);
	header.classList.toggle('bold');*/
}
	