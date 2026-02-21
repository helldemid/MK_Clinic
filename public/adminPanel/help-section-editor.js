(() => {
	const sectionFormSelector = 'form.ea-edit-form, form.ea-new-form';
	const faqSectionSelector = 'input[type="checkbox"][name$="[faqSection]"]';
	const contentSelector = '[name$="[content]"]';

	const mount = (form) => {
		const faqSectionInput = form.querySelector(faqSectionSelector);
		const contentInput = form.querySelector(contentSelector);
		if (!faqSectionInput || !contentInput) {
			return;
		}

		const contentRow = contentInput.closest('.field-textarea') || contentInput.closest('.form-group') || contentInput.parentElement;
		if (!contentRow) {
			return;
		}

		const sync = () => {
			const isFaqSection = faqSectionInput.checked;
			contentInput.disabled = isFaqSection;
			contentRow.style.display = isFaqSection ? 'none' : '';
		};

		faqSectionInput.addEventListener('change', sync);
		sync();
	};

	const init = () => {
		document.querySelectorAll(sectionFormSelector).forEach((form) => {
			if (form.dataset.helpSectionEditorReady === '1') {
				return;
			}

			form.dataset.helpSectionEditorReady = '1';
			mount(form);
		});
	};

	window.addEventListener('DOMContentLoaded', init);
})();
