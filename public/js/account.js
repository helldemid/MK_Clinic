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

	if (document.getElementById('users-table')) {
		$('#users-table').DataTable({
			order: [[0, 'desc']]
		});
	}
	if (document.getElementById('categories-table')) {
		$('#categories-table').DataTable({
			order: [[0, 'desc']]
		});
	}
	if (document.getElementById('requests-table')) {
		$('#requests-table').DataTable({
			order: [[0, 'desc']]
		});
	}
	if (document.getElementById('appointments-table')) {
		$('#appointments-table').DataTable({
			order: [[0, 'desc']]
		});
	}

	document.querySelectorAll('.colored-select').forEach(select => {
		select.addEventListener('change', () => {
			select.className = 'colored-select ' + select.value;
		});
	});


	// Смена роли
	document.querySelectorAll('.user-role-select').forEach(select => {
		select.addEventListener('change', function () {
			const userId = this.dataset.userId || null;
			if (!userId) {
				AlertService.error('Something went wrong. Call Demyd');
				return;
			}
			const newRole = this.value;

			fetch(`/account/user/${userId}/change-role`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-Requested-With': 'XMLHttpRequest',
				},
				body: JSON.stringify({ role: newRole })
			})
				.then(r => r.json())
				.then(data => {
					if (!data.success) {
						AlertService.error(data.message || 'Failed to change role');
						console.error(data.error);
					} else {
						AlertService.success('Role changed');
					}
				});
		});
	});

	// Смена активности
	document.querySelectorAll('.user-active-checkbox').forEach(checkbox => {
		checkbox.addEventListener('change', function () {
			const userId = this.dataset.userId || null;
			if (!userId) {
				AlertService.error('Something went wrong. Call Demyd');
				return;
			}
			const isActive = this.checked;

			fetch(`/account/user/${userId}/toggle-active`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-Requested-With': 'XMLHttpRequest',
				},
				body: JSON.stringify({ isActive })
			})
				.then(r => r.json())
				.then(data => {
					if (!data.success) {
						AlertService.error(data.message || 'Failed to change status');
						console.error(data.error);
					} else {
						AlertService.success(data.isActive ? 'User activated' : 'User deactivated');
					}
				});
		});
	});


	// Delete category
	// ================== DELETE CATEGORY ==================
	document.querySelectorAll("#categories-table .delete-button").forEach(button => {
		button.addEventListener("click", async (e) => {
			const categoryId = e.target.parentElement.dataset.categoryId || null;
			const row = e.target.closest("tr");

			if (!categoryId) {
				AlertService.error("Something went wrong. Call Demyd");
				return;
			}

			const result = await AlertService.confirm("Treatments for this category will be deleted");

			if (result.isConfirmed) {
				handleApiResponse(
					ApiService.delete(`/treatments/category/${categoryId}/delete`),
					"The category has been deleted."
				).then(data => {
					if (data.success) row.remove();
				});
			}
		});
	});


	// ================== EDIT CATEGORY ==================
	document.querySelectorAll("#categories-table .category-name").forEach(input => {
		input.addEventListener("change", () => {
			const categoryId = input.dataset.categoryId;
			const newName = input.value.trim();

			if (newName.length === 0) {
				AlertService.error("Name cannot be empty");
				return;
			}

			handleApiResponse(
				ApiService.post(`/treatments/category/${categoryId}/edit`, { name: newName }),
				"Category updated successfully"
			).then(data => {
				if (!data.success) {
					// если ошибка — вернем старое имя (для UX)
					input.value = input.dataset.oldName || input.value;
				} else {
					// сохраним актуальное имя для будущего отката
					input.dataset.oldName = newName;
				}
			});
		});
	});


	// ================== CREATE CATEGORY ==================
	document.getElementById("create-category-btn").addEventListener("click", () => {
		AlertService.htmlFromTemplate("#category-form-template", "Create Category")
			.then(result => {
				if (result.isConfirmed && result.value) {
					handleApiResponse(
						ApiService.post("/treatments/category/create", result.value),
						"Category created successfully!"
					).then(data => {
						if (data.success) {
							const table = document.getElementById('categories-table').querySelector('tbody');
							// Создай строку
							const tr = document.createElement('tr');
							tr.innerHTML = `
								<td>
									${data.category.name}
								</td>
								<td>
									Will be available after reload
								</td>
							`;
							table.appendChild(tr);
						} else {
							const alert = document.querySelector('#swal-category-form .alert');
							if (alert) {
								alert.textContent = data.message || "Error creating category";
								alert.style.display = '';
							}
						}
					});
				}
			});
	});


	// change appointment request status
	document.querySelectorAll('.request-status-select').forEach(select => {
		select.addEventListener('change', function () {
			const requestId = this.dataset.requestId || null;
			if (!requestId) {
				AlertService.error('Something went wrong. Call Demyd');
				return;
			}
			const newStatus = this.value;

			handleApiResponse(
				ApiService.post(`/appointment/request/${requestId}/change-status`, { status: newStatus }),
				"Status changed", false
			).then(() => {
				// additional handling for 'confirmed' status
				if ('confirmed' !== newStatus) {
					AlertService.success('Status changed');
					return;
				} else {
					AlertService.confirm('Do you want to create an appointment for this request now?', 'Request confirmed', { confirm: 'Yes', cancel: 'No' })
						.then(result => {
							if (result.isConfirmed) {
								window.open(`/appointment/new?request_id=${requestId}`, '_blank');
							} else {
								AlertService.success('Status changed');
							}
						});
				}
			});
		});
	});

	function confirmAndSendNotification(appointmentId, fields) {
		AlertService.confirm('Send notification email to patient?', 'Notify user', { confirm: 'Yes', cancel: 'No' })
			.then(result => {
				if (result.isConfirmed) {
					// Send the email notification
					handleApiResponse(
						ApiService.post(`/appointment/${appointmentId}/send-change-notification`, fields),
						"Notification sent", false
					).then((response) => {
						if (response.success) {
							AlertService.success('Appointment updated and user notified');
						} else {
							AlertService.error('Something went wrong. User not notified.');
						}
					});
				} else {
					AlertService.success('Appointment updated');
				}
			});
	}

	document.querySelectorAll('.appointment-status-select').forEach(select => {
		select.addEventListener('change', function () {
			const appointmentId = this.dataset.appointmentId || null;
			if (!appointmentId) {
				AlertService.error('Something went wrong. Call Demyd');
				return;
			}
			const newStatus = this.value;

			handleApiResponse(
				ApiService.post(`/appointment/${appointmentId}/edit-appointment`, { field: 'status', value: newStatus }),
				"Status changed", false
			).then(() => {
				confirmAndSendNotification(appointmentId, {'status' : newStatus });
			});
		});
	});

});