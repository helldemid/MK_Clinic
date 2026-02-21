(() => {
	const findFieldRoot = (fileInput) => fileInput.closest('.ea-fileupload');

	const createOrFindBlock = (root, className) => {
		let block = root.querySelector(`.${className}`);
		if (block) {
			return block;
		}

		block = document.createElement('div');
		block.className = className;
		root.appendChild(block);

		return block;
	};

	const normalizeLabelText = (text) => {
		const value = (text || '').trim();
		if (value === '' || !value.includes('.')) {
			return '';
		}

		return value;
	};

	const mountFilePreview = (fileInput) => {
		const root = findFieldRoot(fileInput);
		if (!root) {
			return;
		}

		const label = root.querySelector('.custom-file-label');
		const downloadPrefix = fileInput.dataset.downloadPrefix || '/';
		const currentBlock = createOrFindBlock(root, 'site-content-current-preview');
		const nextBlock = createOrFindBlock(root, 'site-content-next-preview');

		const renderCurrent = () => {
			const currentFileName = normalizeLabelText(label ? label.textContent : '');
			if (!currentFileName || fileInput.files.length > 0) {
				currentBlock.innerHTML = '';
				return;
			}

			const url = `${downloadPrefix}${currentFileName}`.replace(/([^:]\/)\/+/g, '$1');
			currentBlock.innerHTML = `
				<div class="site-content-preview-title">Current image</div>
				<div class="site-content-preview-wrap">
					<a href="${url}" download target="_blank" rel="noopener">Download current</a>
					<img src="${url}" alt="Current image preview" />
				</div>
			`;
		};

		const renderNext = () => {
			if (!fileInput.files || fileInput.files.length === 0) {
				nextBlock.innerHTML = '';
				renderCurrent();
				return;
			}

			const file = fileInput.files[0];
			const fileUrl = URL.createObjectURL(file);
			nextBlock.innerHTML = `
				<div class="site-content-preview-title">New image preview</div>
				<div class="site-content-preview-wrap">
					<div>${file.name}</div>
					<img src="${fileUrl}" alt="New image preview" />
				</div>
			`;
		};

		fileInput.addEventListener('change', renderNext);
		renderCurrent();
	};

	const init = () => {
		document.querySelectorAll('input[type="file"][name$="[heroDesktopImage][file]"], input[type="file"][name$="[heroMobileImage][file]"]').forEach((input) => {
			if (input.dataset.siteContentPreviewReady === '1') {
				return;
			}

			input.dataset.siteContentPreviewReady = '1';
			mountFilePreview(input);
		});
	};

	window.addEventListener('DOMContentLoaded', init);
})();
