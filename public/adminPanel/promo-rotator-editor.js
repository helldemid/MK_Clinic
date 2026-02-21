(() => {
	const selector = '.js-promo-rotator';

	const parseJson = (value) => {
		if (!value || typeof value !== 'string') {
			return [];
		}

		try {
			const decoded = JSON.parse(value);
			return Array.isArray(decoded) ? decoded : [];
		} catch (error) {
			return [];
		}
	};

	const normalizeItems = (items) => items
		.filter((item) => item && typeof item === 'object')
		.map((item) => ({
			text: typeof item.text === 'string' ? item.text : '',
			url: typeof item.url === 'string' ? item.url : '',
		}));

	const createButton = (label, className, onClick) => {
		const button = document.createElement('button');
		button.type = 'button';
		button.className = className;
		button.textContent = label;
		button.addEventListener('click', onClick);

		return button;
	};

	const mount = (container) => {
		const input = container.querySelector('input[type="hidden"], input[type="text"], textarea');
		const editor = container.querySelector('.js-promo-rotator-editor');
		if (!input || !editor) {
			return;
		}

		if (input instanceof HTMLInputElement) {
			input.type = 'hidden';
		} else {
			input.setAttribute('hidden', 'hidden');
		}

		let items = normalizeItems(parseJson(input.value));

		const save = () => {
			input.value = JSON.stringify(items);
		};

		const rerender = () => {
			editor.innerHTML = '';

			const toolbar = document.createElement('div');
			toolbar.className = 'promo-rotator-toolbar';
			toolbar.append(
				createButton('+ Message', 'btn btn-secondary btn-sm', () => {
					items.push({ text: '', url: '' });
					save();
					rerender();
				})
			);
			editor.appendChild(toolbar);

			const list = document.createElement('div');
			list.className = 'promo-rotator-list';

			items.forEach((item, index) => {
				const row = document.createElement('div');
				row.className = 'promo-rotator-row';

				const textInput = document.createElement('input');
				textInput.type = 'text';
				textInput.className = 'form-control form-control-sm';
				textInput.placeholder = 'Promo text';
				textInput.value = item.text;
				textInput.addEventListener('input', () => {
					item.text = textInput.value;
					save();
				});

				const urlInput = document.createElement('input');
				urlInput.type = 'text';
				urlInput.className = 'form-control form-control-sm';
				urlInput.placeholder = 'https://... or /help/slug';
				urlInput.value = item.url;
				urlInput.addEventListener('input', () => {
					item.url = urlInput.value;
					save();
				});

				const actions = document.createElement('div');
				actions.className = 'promo-rotator-actions';
				actions.append(
					createButton('↑', 'btn btn-light btn-sm', () => {
						if (index === 0) {
							return;
						}
						[items[index - 1], items[index]] = [items[index], items[index - 1]];
						save();
						rerender();
					}),
					createButton('↓', 'btn btn-light btn-sm', () => {
						if (index >= items.length - 1) {
							return;
						}
						[items[index + 1], items[index]] = [items[index], items[index + 1]];
						save();
						rerender();
					}),
					createButton('×', 'btn btn-danger btn-sm', () => {
						items.splice(index, 1);
						save();
						rerender();
					})
				);

				row.append(textInput, urlInput, actions);
				list.appendChild(row);
			});

			editor.appendChild(list);
		};

		save();
		rerender();
	};

	const init = () => {
		document.querySelectorAll(selector).forEach((container) => {
			if (container.dataset.promoRotatorReady === '1') {
				return;
			}

			container.dataset.promoRotatorReady = '1';
			mount(container);
		});
	};

	window.addEventListener('DOMContentLoaded', init);
})();
