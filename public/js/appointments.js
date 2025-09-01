const appointmentForm = document.getElementById('appointment-form');
if (appointmentForm) {
	appointmentForm.addEventListener('submit', async (e) => {
		e.preventDefault();
		const form = e.target;
		const data = new FormData(form);
		const errorsContainer = document.getElementById('booking-form-errors');
		errorsContainer.innerHTML = '';
		const formType = form.dataset.bookType || 'appointment'; // default to 'appointment' if not specified

		try {
			handleApiResponse(
				ApiService.post(form.action, Object.fromEntries(data)),
				`Your ${formType} request has been submitted successfully.`, false
			).then(result => {
				if (result.success) {
					window.location.href = `/${formType}/success`;
				} else if (result.errors) {
					for (const field in result.errors) {
						const div = document.createElement('div');
						div.textContent = `${field}: ${result.errors[field]}`;
						errorsContainer.appendChild(div);
						errorsContainer.style.display = 'block';
					}
				} else {
					errorsContainer.textContent = result.message || 'Something went wrong';
					errorsContainer.style.display = 'block';
				}
			});
		} catch (err) {
			console.error(err);
			AlertService.error('Server error. Please try again later.');
		}
	});
}
