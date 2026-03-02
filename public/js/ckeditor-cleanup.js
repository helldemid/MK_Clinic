(() => {
	const removeNotifications = (root = document) => {
		root.querySelectorAll('.cke_notifications_area').forEach((node) => node.remove());
	};

	const boot = () => {
		removeNotifications();

		const observer = new MutationObserver((mutations) => {
			for (const mutation of mutations) {
				for (const node of mutation.addedNodes) {
					if (!(node instanceof Element)) {
						continue;
					}

					if (node.matches('.cke_notifications_area')) {
						node.remove();
						continue;
					}

					removeNotifications(node);
				}
			}
		});

		observer.observe(document.body, {
			childList: true,
			subtree: true,
		});
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
