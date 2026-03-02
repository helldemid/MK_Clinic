document.addEventListener('click', function (e) {
	const toggleBtn = e.target.closest('.treatment-hide, .inactive-overlay');
	if (!toggleBtn) {
		return;
	}

	const card = toggleBtn.closest('.treatment_card');
	const treatmentId = card.dataset.id;
	if (!treatmentId) {
		return;
	}

	handleApiResponse(
		ApiService.put(`/treatment/${treatmentId}/toggle`),
		showModalOnResult = false
	).then(resp => {
		if (resp.success) {
			card.style.display = 'none';
			AlertService.success('Treatment ' + (resp.isActive ? 'activated!' : 'hidden!'));
			return;
		}

		AlertService.error('Action failed: ' + (resp.error || 'Unknown error'));
	});
});

document.addEventListener('click', function (e) {
	const btn = e.target.closest('.treatment-popular');
	if (!btn) {
		return;
	}

	const treatmentId = btn.dataset.id;
	handleApiResponse(
		ApiService.put(`/treatment/${treatmentId}/popular`),
		showModalOnResult = false
	).then(resp => {
		if (resp.success) {
			if (resp.isPopular) {
				btn.classList.add('active');
				AlertService.success('Treatment marked as popular!');
			} else {
				btn.classList.remove('active');
				AlertService.success('Treatment unmarked as popular!');
			}
			return;
		}

		AlertService.error('Action failed: ' + (resp.error || 'Unknown error'));
	});
});

(() => {
	const editorConfig = {
		allowedContent: true,
		extraAllowedContent: '*(*);*{*};*[*]',
		entities: false,
		basicEntities: false,
		format_tags: 'p;h2;h3;h4',
		removePlugins: 'elementspath',
		toolbar: [
			['Source', '-', 'Format', 'Styles'],
			['Bold', 'Italic', 'Blockquote', 'HorizontalRule'],
			['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'],
			['Link', 'Unlink'],
			['Undo', 'Redo']
		],
		stylesSet: [
			{ name: 'Lead text', element: 'p', attributes: { class: 'help-lead' } },
			{ name: 'Muted text', element: 'p', attributes: { class: 'help-muted' } },
			{ name: 'Highlighted box', element: 'blockquote', attributes: { class: 'help-highlight' } }
		]
	};

	const normalizeUrl = (value) => value.replace(/([^:]\/)\/+/g, '$1');

	const initAnswerEditors = (root = document) => {
		if (typeof window.CKEDITOR === 'undefined') {
			return;
		}

		root.querySelectorAll('textarea.js-treatment-answer-editor').forEach((textarea) => {
			if (!textarea.id || window.CKEDITOR.instances[textarea.id]) {
				return;
			}

			window.CKEDITOR.replace(textarea.id, editorConfig);
		});
	};

	const destroyAnswerEditors = (root) => {
		if (typeof window.CKEDITOR === 'undefined') {
			return;
		}

		root.querySelectorAll('textarea.js-treatment-answer-editor').forEach((textarea) => {
			if (!textarea.id || !window.CKEDITOR.instances[textarea.id]) {
				return;
			}

			window.CKEDITOR.instances[textarea.id].destroy(true);
		});
	};

	const updateEditorsBeforeSubmit = (form) => {
		if (typeof window.CKEDITOR === 'undefined') {
			return;
		}

		form.addEventListener('submit', () => {
			Object.values(window.CKEDITOR.instances).forEach((instance) => {
				instance.updateElement();
			});
		});
	};

	const mountQuestionCollection = () => {
		const container = document.getElementById('questions-container');
		const addButton = document.getElementById('add-question');
		if (!container || !addButton) {
			return;
		}

		let index = container.querySelectorAll('.question-block').length;

		addButton.addEventListener('click', () => {
			const prototype = container.dataset.prototype.replace(/__name__/g, index);
			const block = document.createElement('div');
			block.classList.add('question-block');
			block.innerHTML = prototype + '<button type="button" class="remove-question">Remove</button>';
			container.appendChild(block);
			initAnswerEditors(block);
			index++;
		});

		container.addEventListener('click', (e) => {
			if (!e.target.classList.contains('remove-question')) {
				return;
			}

			const block = e.target.closest('.question-block');
			if (!block) {
				return;
			}

			destroyAnswerEditors(block);
			block.remove();
		});
	};

	const mountFilePreview = (input) => {
		if (input.dataset.treatmentPreviewReady === '1') {
			return;
		}

		input.dataset.treatmentPreviewReady = '1';
		const upload = input.closest('.file-upload');
		if (!upload) {
			return;
		}

		const fileNameSpan = upload.querySelector('.file-name');
		const currentPreview = upload.querySelector('.site-content-current-preview');
		const nextPreview = upload.querySelector('.site-content-next-preview');
		const currentFile = (input.dataset.currentFile || '').trim();
		const downloadPrefix = normalizeUrl((input.dataset.downloadPrefix || '/').trim() + '/');

		const renderCurrent = () => {
			if (!currentPreview) {
				return;
			}

			if (!currentFile || (input.files && input.files.length > 0)) {
				currentPreview.innerHTML = '';
				currentPreview.hidden = true;
				return;
			}

			const url = normalizeUrl(downloadPrefix + currentFile);
			currentPreview.innerHTML = `
				<div class="site-content-preview-title">Current image</div>
				<div class="site-content-preview-wrap">
					<a href="${url}" download target="_blank" rel="noopener">Download current</a>
					<img src="${url}" alt="Current image preview" />
				</div>
			`;
			currentPreview.hidden = false;
		};

		const renderNext = () => {
			if (fileNameSpan) {
				fileNameSpan.textContent = input.files && input.files.length > 0 ? input.files[0].name : (currentFile || 'No file chosen');
			}

			if (!nextPreview) {
				renderCurrent();
				return;
			}

			if (!input.files || input.files.length === 0) {
				nextPreview.innerHTML = '';
				nextPreview.hidden = true;
				renderCurrent();
				return;
			}

			const file = input.files[0];
			const fileUrl = URL.createObjectURL(file);
			nextPreview.innerHTML = `
				<div class="site-content-preview-title">New image preview</div>
				<div class="site-content-preview-wrap">
					<div>${file.name}</div>
					<img src="${fileUrl}" alt="New image preview" />
				</div>
			`;
			nextPreview.hidden = false;
			if (currentPreview) {
				currentPreview.innerHTML = '';
				currentPreview.hidden = true;
			}
		};

		input.addEventListener('change', renderNext);
		renderCurrent();
	};

	const mountPriceToggle = () => {
		const isFixed = document.getElementById('treatment_price_isFixed');
		const priceTypeRow = document.getElementById('price-type-row');
		if (!isFixed || !priceTypeRow) {
			return;
		}

		const applyVisibility = () => {
			priceTypeRow.style.display = isFixed.checked ? 'none' : '';
		};

		isFixed.addEventListener('change', applyVisibility);
		applyVisibility();
	};

	const mountTabs = () => {
		const root = document.querySelector('[data-treatment-tabs]');
		if (!root) {
			return;
		}

		const storageKey = root.dataset.storageKey || 'treatment-form-tab';
		const tabs = Array.from(root.querySelectorAll('[data-tab-target]'));
		const panels = Array.from(document.querySelectorAll('[data-treatment-tab-panel]'));
		if (tabs.length === 0 || panels.length === 0) {
			return;
		}

		const panelMap = new Map(panels.map((panel) => [panel.dataset.treatmentTabPanel, panel]));
		const getFirstErrorTab = () => {
			const errorPanel = panels.find((panel) => panel.querySelector('.form-error, .alert.alert-danger, .error, [aria-invalid="true"]'));
			return errorPanel ? errorPanel.dataset.treatmentTabPanel : null;
		};

		const activateTab = (target, shouldPersist = true) => {
			if (!panelMap.has(target)) {
				return;
			}

			tabs.forEach((tab) => {
				const isActive = tab.dataset.tabTarget === target;
				tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
			});

			panels.forEach((panel) => {
				panel.style.display = panel.dataset.treatmentTabPanel === target ? '' : 'none';
			});

			if (shouldPersist) {
				window.localStorage.setItem(storageKey, target);
			}
		};

		tabs.forEach((tab) => {
			tab.addEventListener('click', () => {
				activateTab(tab.dataset.tabTarget);
			});
		});

		const storedTab = window.localStorage.getItem(storageKey);
		const initialTab = getFirstErrorTab() || (storedTab && panelMap.has(storedTab) ? storedTab : tabs[0].dataset.tabTarget);
		activateTab(initialTab, false);
	};

	const boot = () => {
		document.querySelectorAll('input[type="file"][data-treatment-file-preview="1"]').forEach(mountFilePreview);
		initAnswerEditors();
		mountQuestionCollection();
		mountPriceToggle();
		mountTabs();

		const form = document.querySelector('.treatment-creation-form form');
		if (form) {
			updateEditorsBeforeSubmit(form);
		}
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
