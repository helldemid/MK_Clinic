(() => {
	const nav = document.querySelector('[data-price-nav]');
	if (!nav) {
		return;
	}

	const links = Array.from(nav.querySelectorAll('a[href^="#"]'));
	const navInner = nav.querySelector('.price-nav__inner');
	const sections = links
		.map((link) => document.querySelector(link.getAttribute('href')))
		.filter(Boolean);

	if (!links.length || !sections.length) {
		return;
	}

	const scrollNavToLink = (link) => {
		if (!navInner) {
			return;
		}
		const containerRect = navInner.getBoundingClientRect();
		const linkRect = link.getBoundingClientRect();
		const offset = linkRect.left - containerRect.left - (containerRect.width / 2 - linkRect.width / 2);
		navInner.scrollTo({
			left: navInner.scrollLeft + offset,
			behavior: 'smooth'
		});
	};

	const scrollToSection = (section) => {
		const header = document.querySelector('header');
		const headerHeight = header ? header.offsetHeight : 0;
		const navHeight = nav.offsetHeight;
		const offset = 16;
		const top = section.getBoundingClientRect().top + window.scrollY - headerHeight - navHeight - offset;
		window.scrollTo({ top, behavior: 'smooth' });
	};

	const setActive = (id) => {
		links.forEach((link) => {
			const isActive = link.getAttribute('href') === `#${id}`;
			link.classList.toggle('is-active', isActive);
			if (isActive) {
				link.setAttribute('aria-current', 'true');
				scrollNavToLink(link);
			} else {
				link.removeAttribute('aria-current');
			}
		});
	};

	const setActiveFromHash = () => {
		if (!window.location.hash) {
			setActive(sections[0].id);
			return;
		}
		const targetId = window.location.hash.replace('#', '');
		const hasMatch = links.some((link) => link.getAttribute('href') === `#${targetId}`);
		setActive(hasMatch ? targetId : sections[0].id);
	};

	const observer = new IntersectionObserver(
		(entries) => {
			const visible = entries
				.filter((entry) => entry.isIntersecting)
				.sort((a, b) => b.intersectionRatio - a.intersectionRatio);

			if (visible.length > 0) {
				setActive(visible[0].target.id);
			}
		},
		{
			rootMargin: '-40% 0px -55% 0px',
			threshold: [0, 0.2, 0.6, 1]
		}
	);

	sections.forEach((section) => observer.observe(section));

	nav.addEventListener('click', (event) => {
		const link = event.target.closest('a[href^="#"]');
		if (!link) {
			return;
		}
		event.preventDefault();
		const targetId = link.getAttribute('href').replace('#', '');
		const targetSection = document.getElementById(targetId);
		if (!targetSection) {
			return;
		}
		history.pushState(null, '', `#${targetId}`);
		setActive(targetId);
		scrollToSection(targetSection);
	});

	window.addEventListener('hashchange', setActiveFromHash);
	setActiveFromHash();
})();
