import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
	static targets = [];

	async filter(event) {
		const container = document.getElementById('treatments_container');
		const categorySelect = document.querySelector('.filters select[data-action*="filter"]:not(#activity)');
		const activitySelect = document.getElementById('activity');

		// Категория
		const categoryId = categorySelect ? categorySelect.value : '';
		// Активность (по умолчанию 1 — только активные)
		let activity = 1;
		if (activitySelect) {
			activity = activitySelect.value;
		}

		// Если нет селектора активности (не editor), всегда только активные
		if (!activitySelect) {
			activity = 1;
		}

		const formData = new FormData();
		formData.append('category_id', categoryId);
		formData.append('activity', activity);

		const response = await fetch('/treatments/filter', {
			method: 'POST',
			body: formData,
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			}
		});

		const data = await response.json();

		// Плавное исчезновение
		container.classList.add('fade-out');

		container.addEventListener('transitionend', () => {
			container.innerHTML = data.html;
			container.classList.remove('fade-out');
			container.classList.add('fade-in', 'show');
			container.addEventListener('transitionend', () => {
				container.classList.remove('fade-in', 'show');
			}, { once: true });
		}, { once: true });
	}
}