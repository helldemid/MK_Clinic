import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
	static targets = [];

	async filter(event) {
		const categoryId = event.target.value;
		const container = document.getElementById('treatments_container');

		const formData = new FormData();
		formData.append('category_id', categoryId);

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
			// Подмена контента
			container.innerHTML = data.html;

			// Анимация появления
			container.classList.remove('fade-out');
			container.classList.add('fade-in', 'show');

			// Ждём окончания появления и чистим классы
			container.addEventListener('transitionend', () => {
				container.classList.remove('fade-in', 'show');
			}, { once: true });
		}, { once: true });
	}
}


