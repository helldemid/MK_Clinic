(() => {
	const collectionSelector = '[data-ea-collection-field="true"][data-position-sortable="true"]';
	const itemSelector = '.ea-form-collection-items .field-collection-item';

	const updatePositionInputs = (collection) => {
		let index = 0;
		collection.querySelectorAll(itemSelector).forEach((item) => {
			const positionInput = item.querySelector('input[name$="[position]"]');
			if (!positionInput) {
				return;
			}

			positionInput.value = String(index);
			index += 1;
		});
	};

	const addDragHandle = (item) => {
		const button = item.querySelector('.accordion-button');
		if (!button || button.querySelector('.js-position-drag-handle')) {
			return;
		}

		const handle = document.createElement('span');
		handle.className = 'js-position-drag-handle';
		handle.textContent = '[drag]';
		handle.style.fontSize = '0.75rem';
		handle.style.marginRight = '0.5rem';
		handle.style.color = '#6c757d';
		button.prepend(handle);
	};

	const enableSorting = (collection) => {
		if (collection.dataset.positionSortingReady !== '1') {
			collection.dataset.positionSortingReady = '1';
			collection.addEventListener('dragover', (event) => {
				event.preventDefault();
				const draggedItem = collection._draggedItem;
				if (!draggedItem) {
					return;
				}

				const currentItem = event.target instanceof Element ? event.target.closest('.field-collection-item') : null;
				if (!currentItem || currentItem === draggedItem) {
					return;
				}

				const rect = currentItem.getBoundingClientRect();
				const shouldInsertAfter = event.clientY >= rect.top + rect.height / 2;
				const parent = currentItem.parentNode;

				if (!parent) {
					return;
				}

				parent.insertBefore(draggedItem, shouldInsertAfter ? currentItem.nextSibling : currentItem);
			});
		}

		collection.querySelectorAll(itemSelector).forEach((item) => {
			if (item.dataset.positionDragReady === '1') {
				return;
			}

			item.dataset.positionDragReady = '1';
			item.draggable = true;
			item.style.cursor = 'move';
			addDragHandle(item);

			item.addEventListener('dragstart', (event) => {
				collection._draggedItem = item;
				item.classList.add('ea-position-dragging');
				if (event.dataTransfer) {
					event.dataTransfer.effectAllowed = 'move';
					event.dataTransfer.setData('text/plain', item.dataset.positionDragReady);
				}
			});

			item.addEventListener('dragend', () => {
				item.classList.remove('ea-position-dragging');
				collection._draggedItem = null;
				updatePositionInputs(collection);
			});
		});

		updatePositionInputs(collection);
	};

	const init = () => {
		document.querySelectorAll(collectionSelector).forEach((collection) => {
			enableSorting(collection);
		});
	};

	window.addEventListener('DOMContentLoaded', init);
	document.addEventListener('ea.collection.item-added', () => {
		setTimeout(init, 0);
	});
	document.addEventListener('ea.collection.item-removed', () => {
		setTimeout(init, 0);
	});
})();
