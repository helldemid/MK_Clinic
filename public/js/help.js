class HelpPage {
	constructor() {
		this.menuItems = document.querySelectorAll('#help-menu .menu-item');
		this.menuWrapper = document.querySelector('.side-menu-wrapper');
		this.errorText = 'Something went wrong, please try reloading the page';
		this.isLoading = false;

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

		this.focusActiveMenuItem();
	}

	async onMenuClick(item) {
		const slug = item.dataset.slug;
		if (!slug) {
			console.error('Menu item missing slug attribute');
			return;
		}

		if (item.classList.contains('active') && item.dataset.loaded == 1) return;
		if (this.isLoading) return;

		this.setActive(item);
		if (item.dataset.loaded == 1) {
			this.toggleContentBlocksVisibility(this.getContentBlock(slug));
			return;
		}

		const contentNode = this.getContentBlock(slug);
		if (!contentNode) {
			console.error('Help content block not found for slug:', slug);
			return;
		}

		this.toggleContentBlocksVisibility(contentNode);
		this.showLoader(contentNode);

		try {
			this.isLoading = true;
			const data = await this.loadSection(slug);
			this.updateContent(data, slug);
			item.dataset.loaded = 1;
		} catch (err) {
			console.error('Help section load error:', err);
			this.showInlineError(contentNode);
		} finally {
			this.isLoading = false;
		}
	}

	setActive(item) {
		this.menuItems.forEach(i => i.classList.remove('active'));
		item.classList.add('active');
		this.focusMenuItem(item);
	}

	focusActiveMenuItem() {
		const activeItem = Array.from(this.menuItems).find(item => item.classList.contains('active'));
		if (!activeItem) {
			return;
		}

		this.focusMenuItem(activeItem, false);
	}

	focusMenuItem(item, smooth = true) {
		if (!this.menuWrapper || window.innerWidth > 780) {
			return;
		}

		const wrapperRect = this.menuWrapper.getBoundingClientRect();
		const itemRect = item.getBoundingClientRect();
		const offset = itemRect.left - wrapperRect.left - (wrapperRect.width / 2 - itemRect.width / 2);

		this.menuWrapper.scrollTo({
			left: this.menuWrapper.scrollLeft + offset,
			behavior: smooth ? 'smooth' : 'auto'
		});
	}

	loadSection(slug) {
		return ApiService.get(`/help/load/${slug}`).then(data => {
			if (!data || typeof data.content !== 'string') {
				throw new Error('No content returned from API');
			}

			return data;
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

	showLoader(contentNode) {
		contentNode.innerHTML = `
			<div class="help-load-state" role="status" aria-live="polite">
				<span class="help-load-state__spinner" aria-hidden="true"></span>
				<p>Loading content...</p>
			</div>
		`;
	}

	showInlineError(contentNode) {
		contentNode.innerHTML = `<p class="help-load-error">${this.errorText}</p>`;
	}
}


document.addEventListener('DOMContentLoaded', () => {
	new HelpPage();
});
