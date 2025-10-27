document.addEventListener('DOMContentLoaded', function () {

	const promoPhrases = [
		"Certified and experienced doctors",
		"Modern equipment and comfortable environment",
		"Personalized treatment plans for every patient",
		"Convenient location and flexible scheduling",
		"Wide range of aesthetic procedures",
		"Committed to patient safety and satisfaction"
	];

	let promoIndex = 0;
	const promoText = document.getElementById('promoText');

	function rotatePromo() {
	promoText.classList.add('fade-out');

	setTimeout(() => {
		promoIndex = (promoIndex + 1) % promoPhrases.length;
		promoText.textContent = promoPhrases[promoIndex];

		promoText.classList.remove('fade-out');
		promoText.classList.add('fade-in');

		setTimeout(() => promoText.classList.remove('fade-in'), 600);
	}, 600);
	}

	setInterval(rotatePromo, 3500);

	const logoNav = document.getElementById('logo_nav');
	const p = logoNav.querySelector('p');
	const img = logoNav.querySelector('img');

	var isToggled = false;

	const hideElement = (el, callback) => {
		el.classList.remove('active');
		const onTransitionEnd = (e) => {
			if (e.propertyName === 'opacity') {
				el.style.display = 'none';
				el.removeEventListener('transitionend', onTransitionEnd);
				if (callback) callback();
			}
		};
		el.addEventListener('transitionend', onTransitionEnd);
	};

	const showElement = (el) => {
		el.style.display = 'block';
		requestAnimationFrame(() => {
			el.classList.add('active');
		});
	};

	/**
	 * Toggles visibility of logo elements
	 * @param {obj} activeEl
	 * @param {obj} hiddenEl
	 */
	const toggleElementsVisibility = (activeEl, hiddenEl) => {
		hideElement(activeEl, () => {
			showElement(hiddenEl);
		});
	};

	window.addEventListener('scroll', () => {
		if (window.scrollY > 0 && !isToggled) {
			toggleElementsVisibility(p, img);
			isToggled = true;
		} else if (window.scrollY === 0 && isToggled) {
			toggleElementsVisibility(img, p);
			isToggled = false;
		}
	});

	window.onload = function () {
		setTimeout(() => {
			const preloader = document.getElementById('preloader');
			if (!preloader) return;
			preloader.style.opacity = '0';
			preloader.style.transition = 'opacity 0.5s';
			setTimeout(() => {
				preloader.style.display = 'none';
			}, 500);
		}, 1100);
	};

	document.querySelectorAll('.question_block h3').forEach(header => {
		header.addEventListener('click', () => {
			const answer = header.nextElementSibling; // <p>
			const svg = header.querySelector('.accordion-icon-svg');
			const expand = svg.querySelector('[data-accordion-animate="expand"]');
			const collapse = svg.querySelector('[data-accordion-animate="collapse"]');

			if (answer.classList.contains('opened')) {
				// закрытие
				answer.style.maxHeight = null;
				answer.classList.remove('opened');
				collapse.beginElement();
			} else {
				// открытие
				answer.style.maxHeight = answer.scrollHeight + "px";
				answer.classList.add('opened');
				expand.beginElement();
			}
		});
	});

	/**
	 * Crates anchors for not links elements
	 * @param {dom} elements anchors DOM elements
	 * @param {*} targetId anchor target id
	 */
	function createAnchors(elements, targetId) {
		if (0 === elements.length) return;
		const target = document.getElementById(targetId);
		if (!target) return;
		elements.forEach(el => {
			el.addEventListener('click', () => {
				target.scrollIntoView({ behavior: 'smooth' });
			})
		})

	}

	document.querySelectorAll('.book_now').forEach(header => {
		header.addEventListener('click', () => {
			window.location.href = 'https://partner.pabau.com/online-bookings/mkaestheticclinic';
		});
	});

	createAnchors(document.querySelectorAll('.book_now'), 'book_now');
	createAnchors(document.querySelectorAll('.learn_more'), 'additional_information');


	const menuBtn = document.getElementById('menuBtn');
	const mobileMenu = document.getElementById('mobileMenu');

	menuBtn.addEventListener('click', () => {
		menuBtn.classList.toggle('open');
		mobileMenu.classList.toggle('open');
	});
});