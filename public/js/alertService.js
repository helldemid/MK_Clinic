const AlertService = {
	success(text = 'Operation completed successfully!', title = 'Success') {
		Swal.fire({
			icon: 'success',
			title: title,
			text: text,
			confirmButtonColor: '#222',
			customClass: { popup: 'my-popup-class' }
		});
	},

	error(text = 'Something went wrong!', title = 'Oops...') {
		Swal.fire({
			icon: 'error',
			title: title,
			text: text,
			confirmButtonColor: '#222',
			customClass: { popup: 'my-popup-class' }
		});
	},
	confirm(text = 'This action cannot be canceled', title = 'Are you sure?') {
		return Swal.fire({
			icon: 'warning',
			title: title,
			text: text,
			showCancelButton: true,
			confirmButtonColor: '#222',
			customClass: { popup: 'my-popup-class' },
			confirmButtonText: "Confirm",
			cancelButtonText: "Cancel"
		});
	},
	htmlFromTemplate(selector, title = 'Form', showButtons = true) {
		const template = document.querySelector(selector);
		if (!template) {
			console.error("Template not found:", selector);
			return;
		}

		return Swal.fire({
			title: title,
			html: template.innerHTML,
			showCancelButton: showButtons,
			confirmButtonColor: '#222',
			focusConfirm: false,
			customClass: { popup: 'my-popup-class' },
			preConfirm: () => {
				const form = Swal.getPopup().querySelector("form");
				if (form) {
					const formData = new FormData(form);
					return Object.fromEntries(formData.entries());
				}
			}
		});
	}
};