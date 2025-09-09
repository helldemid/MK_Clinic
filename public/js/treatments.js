// Скрыть/показать услугу
document.addEventListener('click', function (e) {
	const toggleBtn = e.target.closest('.treatment-hide, .inactive-overlay');
	if (!toggleBtn) return;

	const card = toggleBtn.closest('.treatment_card');
	const treatmentId = card.dataset.id;

	if (!treatmentId) return;

	handleApiResponse(
		ApiService.put(`/treatment/${treatmentId}/toggle`),
		showModalOnResult = false
	).then(resp => {
		if (resp.success) {
			card.style.display = 'none';
			AlertService.success('Treatment ' + (resp.isActive ? 'activated!' : 'hidden!'));
		} else {
			AlertService.error('Action failed: ' + (resp.error || 'Unknown error'));
		}
	});
});



// Пометить/снять популярность
document.addEventListener('click', function (e) {
	if (e.target.closest('.treatment-popular')) {
		const btn = e.target.closest('.treatment-popular');
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
			} else {
				AlertService.error('Action failed: ' + (resp.error || 'Unknown error'));
			}
		});
	}
});


const container = document.getElementById('questions-container');
const addButton = document.getElementById('add-question');
let index = container.querySelectorAll('.question-block').length; // учёт существующих

addButton.addEventListener('click', () => {
	const prototype = container.dataset.prototype.replace(/__name__/g, index);
	const div = document.createElement('div');
	div.classList.add('question-block');
	div.innerHTML = prototype + '<button type="button" class="remove-question">Remove</button>';
	container.appendChild(div);
	index++;
});

container.addEventListener('click', (e) => {
	if (e.target.classList.contains('remove-question')) {
		e.target.closest('.question-block').remove();
	}
});


document.addEventListener('DOMContentLoaded', () => {
    const fileInputs = document.querySelectorAll('.file-upload .file-input');

    fileInputs.forEach(input => {
        input.addEventListener('change', () => {
            // Найти ближайший .file-upload и внутри него .file-name
            const fileUpload = input.closest('.file-upload');
            if (!fileUpload) return;
            const fileNameSpan = fileUpload.querySelector('.file-name');
            if (fileNameSpan) {
                fileNameSpan.textContent = input.files.length ? input.files[0].name : 'No file chosen';
            }
        });
    });
});

// В treatments.js
document.addEventListener('DOMContentLoaded', function () {
	const isFixed = document.getElementById('treatment_price_isFixed');
	const priceTypeRow = document.getElementById('price-type-row');
	if (isFixed && priceTypeRow) {
		isFixed.addEventListener('change', function () {
			priceTypeRow.style.display = this.checked ? 'none' : '';
		});
		// Инициализация при загрузке
		priceTypeRow.style.display = isFixed.checked ? 'none' : '';
	}
});



