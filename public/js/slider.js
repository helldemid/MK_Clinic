document.addEventListener('DOMContentLoaded', function () {
	const swiper = new Swiper('.mySwiper', {
		loop: true,
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
		slidesPerView: 'auto',
		spaceBetween: 30,
		autoplay: {
			delay: 5000,
		},
	});
});