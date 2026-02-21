(() => {
	const selector = '.js-price-grid';

	const parseJson = (value) => {
		if (!value || typeof value !== 'string') {
			return { columns: [], rows: [] };
		}

		try {
			const decoded = JSON.parse(value);
			if (!decoded || typeof decoded !== 'object') {
				return { columns: [], rows: [] };
			}

			return {
				columns: Array.isArray(decoded.columns) ? decoded.columns : [],
				rows: Array.isArray(decoded.rows) ? decoded.rows : [],
			};
		} catch (error) {
			return { columns: [], rows: [] };
		}
	};

	const normalizeCellData = (rawCell) => {
		if (rawCell && typeof rawCell === 'object' && !Array.isArray(rawCell)) {
			const value = rawCell.value === null || rawCell.value === undefined ? '' : String(rawCell.value);
			const promoValue = rawCell.promoValue === null || rawCell.promoValue === undefined ? '' : String(rawCell.promoValue);

			return { value, promoValue };
		}

		const value = rawCell === null || rawCell === undefined ? '' : String(rawCell);

		return { value, promoValue: '' };
	};

	const normalizeState = (state) => {
		const columns = state.columns.map((column, index) => ({
			key: column.key || `new_col_${index}_${Date.now()}`,
			id: column.id || null,
			label: typeof column.label === 'string' ? column.label : '',
			position: index,
		}));

		const rows = state.rows.map((row, rowIndex) => {
			const cells = {};
			columns.forEach((column) => {
				const raw = row && row.cells ? row.cells[column.key] : null;
				cells[column.key] = normalizeCellData(raw);
			});

			return {
				key: row.key || `new_row_${rowIndex}_${Date.now()}`,
				id: row.id || null,
				title: typeof row.title === 'string' ? row.title : '',
				position: rowIndex,
				cells,
			};
		});

		return { columns, rows };
	};

	const stateToPayload = (state) => ({
		columns: state.columns.map((column, index) => ({
			key: column.key,
			id: column.id,
			label: column.label,
			position: index,
		})),
		rows: state.rows.map((row, rowIndex) => ({
			key: row.key,
			id: row.id,
			title: row.title,
			position: rowIndex,
			cells: state.columns.reduce((acc, column) => {
				const cell = row.cells[column.key] || { value: '', promoValue: '' };
				const value = (typeof cell.value === 'string' ? cell.value : String(cell.value ?? '')).trim();
				const promoValue = (typeof cell.promoValue === 'string' ? cell.promoValue : String(cell.promoValue ?? '')).trim();

				if (value === '' && promoValue === '') {
					acc[column.key] = null;
				} else {
					acc[column.key] = {
						value: value === '' ? null : value,
						promoValue: promoValue === '' ? null : promoValue,
					};
				}

				return acc;
			}, {}),
		})),
	});

	const createButton = (label, className, onClick) => {
		const button = document.createElement('button');
		button.type = 'button';
		button.className = className;
		button.textContent = label;
		button.addEventListener('click', onClick);

		return button;
	};

	const mount = (container) => {
		const hiddenInput = container.querySelector('input[type="hidden"], input[type="text"], textarea');
		const editorRoot = container.querySelector('.js-price-grid-editor');
		if (!hiddenInput || !editorRoot) {
			return;
		}

		if (hiddenInput instanceof HTMLInputElement) {
			hiddenInput.type = 'hidden';
		} else {
			hiddenInput.setAttribute('hidden', 'hidden');
		}
		hiddenInput.classList.add('price-grid-source-input');

		let state = normalizeState(parseJson(hiddenInput.value));

		const saveState = () => {
			hiddenInput.value = JSON.stringify(stateToPayload(state));
		};

		const rerender = () => {
			editorRoot.innerHTML = '';

			const toolbar = document.createElement('div');
			toolbar.className = 'price-grid-toolbar';
			toolbar.append(
				createButton('+ Column', 'btn btn-secondary btn-sm', () => {
					const key = `new_col_${Date.now()}_${state.columns.length}`;
					state.columns.push({
						key,
						id: null,
						label: state.columns.length === 0 ? 'Treatment' : '',
						position: state.columns.length,
					});
					state.rows = state.rows.map((row) => {
						row.cells[key] = { value: '', promoValue: '' };
						return row;
					});
					saveState();
					rerender();
				}),
				createButton('+ Row', 'btn btn-secondary btn-sm', () => {
					const rowCells = {};
					state.columns.forEach((column) => {
						rowCells[column.key] = { value: '', promoValue: '' };
					});
					state.rows.push({
						key: `new_row_${Date.now()}_${state.rows.length}`,
						id: null,
						title: '',
						position: state.rows.length,
						cells: rowCells,
					});
					saveState();
					rerender();
				}),
			);
			editorRoot.append(toolbar);

			const tableWrap = document.createElement('div');
			tableWrap.className = 'price-grid-wrap';
			const table = document.createElement('table');
			table.className = 'price-grid-table';

			const thead = document.createElement('thead');
			const headRow = document.createElement('tr');

			const rowHead = document.createElement('th');
			rowHead.className = 'price-grid-row-head';
			const titleColumn = state.columns[0] ?? null;
			if (titleColumn) {
				const titleColumnInput = document.createElement('input');
				titleColumnInput.type = 'text';
				titleColumnInput.className = 'form-control form-control-sm';
				titleColumnInput.placeholder = 'First column';
				titleColumnInput.value = titleColumn.label;
				titleColumnInput.addEventListener('input', () => {
					titleColumn.label = titleColumnInput.value;
					saveState();
				});

				const titleColumnActions = document.createElement('div');
				titleColumnActions.className = 'price-grid-col-actions';
				const moveLeftButton = createButton('←', 'btn btn-light btn-sm', () => {
					return;
				});
				moveLeftButton.disabled = true;
				titleColumnActions.append(
					moveLeftButton,
					createButton('→', 'btn btn-light btn-sm', () => {
						if (state.columns.length <= 1) {
							return;
						}
						[state.columns[1], state.columns[0]] = [state.columns[0], state.columns[1]];
						saveState();
						rerender();
					}),
					createButton('×', 'btn btn-danger btn-sm', () => {
						const removed = state.columns.splice(0, 1)[0];
						state.rows = state.rows.map((row) => {
							delete row.cells[removed.key];
							return row;
						});
						saveState();
						rerender();
					}),
				);

				rowHead.append(titleColumnInput, titleColumnActions);
			} else {
				rowHead.textContent = 'First column';
			}
			headRow.appendChild(rowHead);

			state.columns.slice(1).forEach((column, relativeIndex) => {
				const columnIndex = relativeIndex + 1;
				const th = document.createElement('th');
				th.className = 'price-grid-col-head';

				const labelInput = document.createElement('input');
				labelInput.type = 'text';
				labelInput.className = 'form-control form-control-sm';
				labelInput.placeholder = `Column ${columnIndex + 1}`;
				labelInput.value = column.label;
				labelInput.addEventListener('input', () => {
					column.label = labelInput.value;
					saveState();
				});

				const actions = document.createElement('div');
				actions.className = 'price-grid-col-actions';
				actions.append(
					createButton('←', 'btn btn-light btn-sm', () => {
						if (columnIndex === 0) {
							return;
						}
						[state.columns[columnIndex - 1], state.columns[columnIndex]] = [state.columns[columnIndex], state.columns[columnIndex - 1]];
						saveState();
						rerender();
					}),
					createButton('→', 'btn btn-light btn-sm', () => {
						if (columnIndex >= state.columns.length - 1) {
							return;
						}
						[state.columns[columnIndex + 1], state.columns[columnIndex]] = [state.columns[columnIndex], state.columns[columnIndex + 1]];
						saveState();
						rerender();
					}),
					createButton('×', 'btn btn-danger btn-sm', () => {
						const removed = state.columns.splice(columnIndex, 1)[0];
						state.rows = state.rows.map((row) => {
							delete row.cells[removed.key];
							return row;
						});
						saveState();
						rerender();
					}),
				);

				th.append(labelInput, actions);
				headRow.appendChild(th);
			});

			thead.appendChild(headRow);
			table.appendChild(thead);

			const tbody = document.createElement('tbody');
			state.rows.forEach((row, rowIndex) => {
				const tr = document.createElement('tr');

				const titleCell = document.createElement('th');
				titleCell.className = 'price-grid-title-cell';
				const titleInput = document.createElement('input');
				titleInput.type = 'text';
				titleInput.className = 'form-control form-control-sm';
				titleInput.placeholder = `Row ${rowIndex + 1}`;
				titleInput.value = row.title;
				titleInput.addEventListener('input', () => {
					row.title = titleInput.value;
					saveState();
				});

				const rowActions = document.createElement('div');
				rowActions.className = 'price-grid-row-actions';
				rowActions.append(
					createButton('↑', 'btn btn-light btn-sm', () => {
						if (rowIndex === 0) {
							return;
						}
						[state.rows[rowIndex - 1], state.rows[rowIndex]] = [state.rows[rowIndex], state.rows[rowIndex - 1]];
						saveState();
						rerender();
					}),
					createButton('↓', 'btn btn-light btn-sm', () => {
						if (rowIndex >= state.rows.length - 1) {
							return;
						}
						[state.rows[rowIndex + 1], state.rows[rowIndex]] = [state.rows[rowIndex], state.rows[rowIndex + 1]];
						saveState();
						rerender();
					}),
					createButton('×', 'btn btn-danger btn-sm', () => {
						state.rows.splice(rowIndex, 1);
						saveState();
						rerender();
					}),
				);

				titleCell.append(titleInput, rowActions);
				tr.appendChild(titleCell);

				state.columns.slice(1).forEach((column) => {
					const td = document.createElement('td');
					td.className = 'price-grid-value-cell';
					const cellState = row.cells[column.key] ?? normalizeCellData(null);
					row.cells[column.key] = cellState;
					const inputGroup = document.createElement('div');
					inputGroup.className = 'price-grid-value-inputs';

					const regularWrap = document.createElement('div');
					regularWrap.className = 'price-grid-currency-wrap';
					const valueInput = document.createElement('input');
					valueInput.type = 'number';
					valueInput.step = '0.01';
					valueInput.inputMode = 'decimal';
					valueInput.className = 'form-control form-control-sm';
					valueInput.placeholder = 'Regular';
					valueInput.value = cellState.value;
					valueInput.addEventListener('input', () => {
						row.cells[column.key].value = valueInput.value;
						saveState();
					});
					regularWrap.appendChild(valueInput);

					const promoWrap = document.createElement('div');
					promoWrap.className = 'price-grid-currency-wrap is-promo';
					const promoInput = document.createElement('input');
					promoInput.type = 'number';
					promoInput.step = '0.01';
					promoInput.inputMode = 'decimal';
					promoInput.className = 'form-control form-control-sm';
					promoInput.placeholder = 'Promo';
					promoInput.value = cellState.promoValue;
					promoInput.addEventListener('input', () => {
						row.cells[column.key].promoValue = promoInput.value;
						saveState();
					});
					promoWrap.appendChild(promoInput);

					inputGroup.append(regularWrap, promoWrap);
					td.appendChild(inputGroup);
					tr.appendChild(td);
				});

				tbody.appendChild(tr);
			});
			table.appendChild(tbody);

			tableWrap.appendChild(table);
			editorRoot.appendChild(tableWrap);
		};

		saveState();
		rerender();
	};

	const init = () => {
		document.querySelectorAll(selector).forEach((container) => {
			if (container.dataset.priceGridReady === '1') {
				return;
			}

			container.dataset.priceGridReady = '1';
			mount(container);
		});
	};

	window.addEventListener('DOMContentLoaded', init);
})();
