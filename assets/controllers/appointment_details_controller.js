import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
	static values = { url: String }

	connect() {
		// Find button save
		const saveBtn = this.element.querySelector('.create_button');
		if (saveBtn) {
			saveBtn.addEventListener('click', () => this.save());
		}
	}

	show(event) {
		event.preventDefault();
		const appointmentId = event.currentTarget.dataset.appointmentId;
		handleApiResponse(ApiService.get(`/admin/appointment/${appointmentId}/details`))
			.then(result => {
				AlertService.customModal(result.html, 'appointment-details-modal');
			})
			.catch(error => {
				AlertService.error("Error loading appointment details: " + error.message);
			});
	}

	save(event) {
		event.preventDefault();

		if (this.saving) return; // защита от двойного клика
		this.saving = true;
		// Очистить старые ошибки
		const modal = document.querySelector('.appointment-modal');
		if (!modal) {
			console.error("Modal not found");
			return;
		}
		modal.querySelectorAll('.form-error').forEach(e => e.remove());
		modal.querySelectorAll('.error').forEach(e => e.classList.remove('error'));

		const data = {
			status: modal.querySelector('#appointment-status').value,
			treatment: modal.querySelector('#appointment-treatment').value,
			doctor: modal.querySelector('#doctor').value,
			appointmentDate: modal.querySelector('#appointment-date').value,
			payment_status: modal.querySelector('#payment-status').value,
			payment_method: modal.querySelector('#payment-method').value,
			payment_amount: modal.querySelector('#payment-amount').value,
			patient_name: modal.querySelector('#patient_name').value,
			patient_phone: modal.querySelector('#patient_phone').value,
			patient_email: modal.querySelector('#patient_email').value,
		};

		let hasError = false;

		// Пример валидации
		if (!data.appointmentDate) {
			this.showFieldError('#appointment-date', 'Date and time is required');
			hasError = true;
		}
		if (!data.treatment) {
			this.showFieldError('#appointment-treatment', 'Treatment is required');
			hasError = true;
		}
		if (!data.patient_name) {
			this.showFieldError('#patient_name', 'Name is required');
			hasError = true;
		}
		if (!data.patient_phone) {
			this.showFieldError('#patient_phone', 'Phone is required');
			hasError = true;
		}
		if (data.patient_email && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(data.patient_email)) {
			this.showFieldError('#patient_email', 'Invalid email');
			hasError = true;
		}

		if (hasError) return;

		const appointmentId = modal.dataset.appointmentId;

		handleApiResponse(ApiService.post(`/admin/appointment/${appointmentId}/update`, data), 'Saved', false)
			.then(response => {
				if (response.success) {
					if (response.changedImportantFields && Object.keys(response.changedImportantFields).length > 0) {
						this.sendNotificationAfterUpdate(appointmentId, response.changedImportantFields);
					} else {
						AlertService.success('Appointment was updated!');
					}
				} else {
					if (response.errors.length > 0) {
						Object.entries(response.errors).forEach(([field, message]) => {
							this.showFieldError(`#${field}`, message);
						});
					} else {
						AlertService.error();
					}
				}
			})

		this.saving = false;
	}

	showFieldError(selector, message) {
		const input = document.querySelector('.appointment-modal').querySelector(selector);
		if (input) {
			input.classList.add('error');
			const errorDiv = document.createElement('div');
			errorDiv.className = 'form-error';
			errorDiv.innerText = message;
			input.parentNode.insertBefore(errorDiv, input);
		}
	}

	sendNotificationAfterUpdate(appointmentId, fields) {
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
}