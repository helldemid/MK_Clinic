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
	}
};