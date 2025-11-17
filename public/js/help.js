class HelpPage {
	constructor() {
		this.menuItems = document.querySelectorAll('#help-menu .menu-item');

		if (!this.menuItems.length) {
			console.warn('Help menu not found.');
			return;
		}

		this.init();
	}

	init() {
		this.menuItems.forEach(item => {
			item.addEventListener('click', () => this.onMenuClick(item));
		});
	}

	async onMenuClick(item) {
		const slug = item.dataset.slug;
		if (!slug) {
			console.error('Menu item missing slug attribute');
			return;
		}

		if (item.classList.contains('active')) return;

		this.setActive(item);
		if (item.dataset.loaded == 1) {
			this.toggleContentBlocksVisibility(this.getContentBlock(slug));
			return;
		}
		await this.loadSection(slug);
		item.dataset.loaded = 1;
	}

	setActive(item) {
		this.menuItems.forEach(i => i.classList.remove('active'));
		item.classList.add('active');
	}

	loadSection(slug) {
		ApiService.get(`/help/load/${slug}`)
			.then(data => {
				if (!data) {
					this.showError("No data returned from API");
					return;
				}

				this.updateContent(data, slug);
			})
			.catch(err => {
				console.error('Help section load error:', err);
				this.showError("Failed to load content.");
			});
	}

	toggleContentBlocksVisibility(activeBlock) {
		document.querySelectorAll('.help-content > *').forEach(node => {
			node.style.display = 'none'
		});
		activeBlock.style.display = 'block';
	}

	getContentBlock(slug = '') {
		return document.getElementById('content-' + slug);
	}

	updateContent(data, slug = '') {
		const contentNode = this.getContentBlock(slug);
		if (!contentNode) return;

		this.toggleContentBlocksVisibility(contentNode);

		const html = data.content;

		if (html && html.trim() !== "") {
			contentNode.innerHTML = html;
		} else {
			contentNode.innerHTML = "<p>No content available.</p>";
		}
	}

	showError(message) {
		AlertService.error(message);
	}
}


document.addEventListener('DOMContentLoaded', () => {
	new HelpPage();
});
