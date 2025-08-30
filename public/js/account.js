document.addEventListener('DOMContentLoaded', function () {
	const menuLinks = document.querySelectorAll('.side-menu > li');
	const blocks = document.querySelectorAll('.account-content > .account-block');

	menuLinks.forEach(link => {
		link.addEventListener('click', function (e) {
			e.preventDefault();
			if (this.classList.contains('active')) return;

			const targetId = this.getAttribute('data-target');

			// hide all blocks
			blocks.forEach(block => {
				block.style.display = 'none';
			});

			// remove active class from all menu items
			menuLinks.forEach(l => l.classList.remove('active'));

			// Show the target block and highlight the menu
			const targetBlock = document.getElementById(targetId);
			if (targetBlock) {
				targetBlock.style.display = '';
			}
			this.classList.add('active');
		});
	});

	/**
	 * Show the edit form for a specific field.
	 * @param {string} editBtnId
	 * @param {string} containerId
	 * @param {string} formId
	 */
	function showForm(editBtnId, containerId, formId) {
		const btn = document.getElementById(editBtnId);
		const container = document.getElementById(containerId);
		const form = document.getElementById(formId);

		if (!btn || !container || !form) return;

		btn.addEventListener("click", () => {
			if (form.classList.contains("hidden")) {
				form.classList.remove("hidden");
				const firstInput = form.querySelector("input");
				if (firstInput) firstInput.focus();
			} else {
				form.classList.add("hidden");
			}
		});
	}

	function setFieldError(form, fieldName, message) {
		const errorEl = form.querySelector(`.field-error[data-for="${CSS.escape(fieldName)}"]`);
		const input = form.querySelector(`[name="${CSS.escape(fieldName)}"]`);
		if (errorEl) errorEl.textContent = message || "";
		if (input) input.setAttribute("aria-invalid", message ? "true" : "false");
	}

	function clearErrors(form) {
		form.querySelectorAll(".field-error").forEach(el => el.textContent = "");
		form.querySelectorAll("input,select,textarea").forEach(el => el.setAttribute("aria-invalid", "false"));
	}

	function trimInputs(form) {
		form.querySelectorAll("input[type='text'], input[type='tel'], input[type='email']").forEach(i => i.value = i.value.trim());
	}

	async function ajaxSubmit({ formId, containerId, textId, formatText }) {
		const form = document.getElementById(formId);
		const container = document.getElementById(containerId);
		const textEl = document.getElementById(textId);
		if (!form) return;

		form.addEventListener("submit", async (e) => {
			e.preventDefault();
			clearErrors(form);
			trimInputs(form);

			// HTML5 клиентская проверка
			if (!form.checkValidity()) {
				form.reportValidity();
				Array.from(form.elements).forEach(el => {
					if (el instanceof HTMLInputElement && !el.checkValidity()) {
						el.setAttribute("aria-invalid", "true");
					}
				});
				return;
			}

			const submitBtn = form.querySelector('button[type="submit"]');
			if (submitBtn) submitBtn.disabled = true;

			try {
				const resp = await fetch(form.action, {
					method: "POST",
					body: new FormData(form),
					headers: {
						"X-Requested-With": "XMLHttpRequest",
						"Accept": "application/json"
					}
				});

				const data = await resp.json().catch(() => ({}));

				if (!resp.ok || data.success === false) {
					const errors = (data && data.errors) || {};
					Object.keys(errors).forEach(name => setFieldError(form, name, errors[name]));
					if (!Object.keys(errors).length) {
						AlertService.error(data.message || "Saving data error");
					}
					return;
				}

				// Обновляем текст
				if (textEl && typeof formatText === "function") {
					textEl.textContent = formatText(data);
				}

				if (container) container.style.display = "inline";
				form.classList.add("hidden");

				AlertService.success("Data successfully saved");
			} catch (err) {
				AlertService.error("Network error. Please try again.");
			} finally {
				if (submitBtn) submitBtn.disabled = false;
			}
		});
	}

	// Инициализация кнопок редактирования
	showForm("edit-fullname-btn", "fullname-container", "fullname-form");
	showForm("edit-phone-btn", "phone-container", "phone-form");

	// Инициализация AJAX
	ajaxSubmit({
		formId: "fullname-form",
		containerId: "fullname-container",
		textId: "fullname-text",
		formatText: (data) => `${data.firstName} ${data.lastName}`.trim()
	});

	ajaxSubmit({
		formId: "phone-form",
		containerId: "phone-container",
		textId: "phone-text",
		formatText: (data) => data.phone
	});
});