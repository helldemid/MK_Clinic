document.addEventListener('DOMContentLoaded', () => {

	$(function () {
		$('.autocomplete-user').select2({
			placeholder: 'Find user...',
			minimumInputLength: 2,
			width: '100%',
			ajax: {
				url: '/user/autocomplete',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return { q: params.term };
				},
				processResults: function (data) {
					return {
						results: data.items
					};
				},
				cache: true
			}
		});
	});

	const userRadio = document.querySelector('input[value="user"]');
	const guestRadio = document.querySelector('input[value="guest"]');

	const guestFields = document.querySelectorAll('.guest-fields .form-control');
	const userField = document.querySelector('.user-field');

	function toggleFields() {
		if (userRadio.checked) {
			userField.style.display = '';
			guestFields.forEach(f => f.closest('.full').style.display = 'none');
		} else {
			userField.style.display = 'none';
			guestFields.forEach(f => f.closest('.full').style.display = '');
		}
	}

	userRadio.addEventListener('change', toggleFields);
	guestRadio.addEventListener('change', toggleFields);

	toggleFields(); // инициализация
});