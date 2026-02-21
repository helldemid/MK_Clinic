<?php

namespace App\Controller\Admin;

use App\Entity\PriceCell;
use App\Entity\PriceColumn;
use App\Entity\PriceRow;
use App\Entity\PriceSection;
use App\Form\Type\PriceGridType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PriceSectionCrudController extends AbstractCrudController
{
	private const POSITION_OFFSET = 1000000;

	public static function getEntityFqcn(): string
	{
		return PriceSection::class;
	}

	public function configureCrud(Crud $crud): Crud
	{
		return $crud
			->setEntityLabelInSingular('Price Section')
			->setEntityLabelInPlural('Price Sections')
			->showEntityActionsInlined()
			->setDefaultSort(['position' => 'ASC']);
	}

	public function configureFilters(Filters $filters): Filters
	{
		return $filters
			->add(TextFilter::new('title'))
			->add(TextFilter::new('navLabel'))
			->add(TextFilter::new('slug'))
			->add(NumericFilter::new('position'));
	}

	public function configureAssets(Assets $assets): Assets
	{
		return $assets
			->addJsFile(Asset::new('adminPanel/position-collection-sort.js')->onlyOnForms())
			->addJsFile(Asset::new('adminPanel/price-grid-editor.js')->onlyOnForms())
			->addCssFile(Asset::new('adminPanel/price-grid-editor.css')->onlyOnForms());
	}

	public function configureFields(string $pageName): iterable
	{
		$title = TextField::new('title');
		$navLabel = TextField::new('navLabel', 'Navigation label');
		$slug = SlugField::new('slug')->setTargetFieldName('title');
		$description = TextareaField::new('description')->setRequired(false);
		$note = TextareaField::new('note')->setRequired(false);
		$position = IntegerField::new('position');
		$rowsCount = IntegerField::new('rowsCount', 'Rows')
			->onlyOnIndex()
			->formatValue(static fn ($value, PriceSection $section): int => $section->getRows()->count());
		$columnsCount = IntegerField::new('columnsCount', 'Columns')
			->onlyOnIndex()
			->formatValue(static fn ($value, PriceSection $section): int => $section->getColumns()->count());
		$columns = CollectionField::new('columns')
			->setLabel('Columns')
			->setEntryIsComplex()
			->setEntryToStringMethod(static function (?PriceColumn $column): string {
				if ($column === null) {
					return '';
				}

				return sprintf('%d. %s', $column->getPosition(), $column->getLabel());
			})
			->allowAdd()
			->allowDelete()
			->renderExpanded()
			->setFormTypeOption('by_reference', false)
			->setFormTypeOption('row_attr.data-position-sortable', 'true')
			->useEntryCrudForm(
				PriceColumnCrudController::class,
				PriceColumnCrudController::PAGE_EMBEDDED_NEW,
				PriceColumnCrudController::PAGE_EMBEDDED_EDIT,
			);
		$rows = CollectionField::new('rows')
			->setLabel('Rows')
			->setEntryIsComplex()
			->setEntryToStringMethod(static function (?PriceRow $row): string {
				if ($row === null) {
					return '';
				}

				return sprintf('%d. %s', $row->getPosition(), $row->getTitle());
			})
			->allowAdd()
			->allowDelete()
			->renderExpanded()
			->setFormTypeOption('by_reference', false)
			->setFormTypeOption('row_attr.data-position-sortable', 'true')
			->useEntryCrudForm(
				PriceRowCrudController::class,
				PriceRowCrudController::PAGE_EMBEDDED_NEW,
				PriceRowCrudController::PAGE_EMBEDDED_EDIT,
			);
		$priceGrid = Field::new('priceGrid', 'Price matrix')
			->setFormType(PriceGridType::class)
			->setHelp('Use this table for inline prices. Empty cells are allowed.')
			->onlyOnForms();

		if (Crud::PAGE_INDEX === $pageName) {
			return [
				IdField::new('id'),
				$title,
				$navLabel,
				$slug,
				$position,
				$rowsCount,
				$columnsCount,
			];
		}

			return [
				FormField::addTab('General', 'fa-solid fa-gear'),
				$title,
				$navLabel,
				$slug,
				$description,
				$note,
				$position,
				FormField::addTab('Price Table', 'fa-solid fa-table'),
				$priceGrid,
				FormField::addTab('Columns', 'fa-solid fa-table-columns'),
				$columns,
				FormField::addTab('Rows', 'fa-solid fa-grip-lines'),
				$rows,
			];
	}

	public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
	{
		if (!$entityInstance instanceof PriceSection) {
			parent::persistEntity($entityManager, $entityInstance);
			return;
		}

		$this->persistSectionWithStableChildPositions($entityManager, $entityInstance);
	}

	public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
	{
		if (!$entityInstance instanceof PriceSection) {
			parent::updateEntity($entityManager, $entityInstance);
			return;
		}

		$this->persistSectionWithStableChildPositions($entityManager, $entityInstance);
	}

	private function persistSectionWithStableChildPositions(EntityManagerInterface $entityManager, PriceSection $section): void
	{
		$this->synchronizeGridPayload($section);

		$orderedColumns = $this->getOrderedColumns($section);
		$orderedRows = $this->getOrderedRows($section);

		$entityManager->persist($section);

		foreach ($orderedColumns as $index => $column) {
			$column->setPosition(self::POSITION_OFFSET + $index);
			$entityManager->persist($column);
		}
		foreach ($orderedRows as $index => $row) {
			$row->setPosition(self::POSITION_OFFSET + $index);
			$entityManager->persist($row);
		}
		$entityManager->flush();

		foreach ($orderedColumns as $index => $column) {
			$column->setPosition($index);
			$entityManager->persist($column);
		}
		foreach ($orderedRows as $index => $row) {
			$row->setPosition($index);
			$entityManager->persist($row);
		}
		$entityManager->flush();
	}

	private function synchronizeGridPayload(PriceSection $section): void
	{
		$grid = $section->getPriceGrid();
		if (!is_array($grid)) {
			return;
		}

		$submittedColumns = array_values(array_filter(
			is_array($grid['columns'] ?? null) ? $grid['columns'] : [],
			static fn ($column): bool => is_array($column),
		));
		$submittedRows = array_values(array_filter(
			is_array($grid['rows'] ?? null) ? $grid['rows'] : [],
			static fn ($row): bool => is_array($row),
		));

		/** @var array<int, PriceColumn> $existingColumnsById */
		$existingColumnsById = [];
		foreach ($section->getColumns() as $column) {
			$columnId = $column->getId();
			if ($columnId !== null) {
				$existingColumnsById[$columnId] = $column;
			}
		}

		/** @var array<int, PriceRow> $existingRowsById */
		$existingRowsById = [];
		foreach ($section->getRows() as $row) {
			$rowId = $row->getId();
			if ($rowId !== null) {
				$existingRowsById[$rowId] = $row;
			}
		}

		$resolvedColumns = [];
		$keptColumnIds = [];
		$columnKeyToEntity = [];

		foreach ($submittedColumns as $index => $columnPayload) {
			$columnId = isset($columnPayload['id']) && is_numeric((string) $columnPayload['id']) ? (int) $columnPayload['id'] : null;
			$column = ($columnId !== null && isset($existingColumnsById[$columnId])) ? $existingColumnsById[$columnId] : new PriceColumn();

			$label = trim((string) ($columnPayload['label'] ?? ''));
			$column
				->setLabel($label !== '' ? $label : sprintf('Column %d', $index + 1))
				->setPosition($index);

			$section->addColumn($column);

			if ($columnId !== null) {
				$keptColumnIds[] = $columnId;
			}

			$key = (string) ($columnPayload['key'] ?? '');
			if ($key === '') {
				$key = sprintf('col_auto_%d', $index);
			}

			$resolvedColumns[] = $column;
			$columnKeyToEntity[$key] = $column;
		}

		foreach ($section->getColumns()->toArray() as $column) {
			$columnId = $column->getId();
			if ($columnId !== null && !in_array($columnId, $keptColumnIds, true)) {
				foreach ($section->getRows() as $row) {
					foreach ($row->getCells()->toArray() as $cell) {
						if ($cell->getColumn() === $column) {
							$row->removeCell($cell);
						}
					}
				}
				$section->removeColumn($column);
			}
		}

		$keptRowIds = [];
		foreach ($submittedRows as $rowIndex => $rowPayload) {
			$rowId = isset($rowPayload['id']) && is_numeric((string) $rowPayload['id']) ? (int) $rowPayload['id'] : null;
			$row = ($rowId !== null && isset($existingRowsById[$rowId])) ? $existingRowsById[$rowId] : new PriceRow();

			$rowTitle = trim((string) ($rowPayload['title'] ?? ''));
			$row
				->setTitle($rowTitle !== '' ? $rowTitle : sprintf('Row %d', $rowIndex + 1))
				->setPosition($rowIndex);

			$section->addRow($row);

			if ($rowId !== null) {
				$keptRowIds[] = $rowId;
			}

			/** @var array<int, PriceCell> $cellsByColumnId */
			$cellsByColumnId = [];
			foreach ($row->getCells() as $cell) {
				$columnId = $cell->getColumn()?->getId();
				if ($columnId !== null) {
					$cellsByColumnId[$columnId] = $cell;
				}
			}

			$submittedCells = is_array($rowPayload['cells'] ?? null) ? $rowPayload['cells'] : [];

			foreach ($resolvedColumns as $columnIndex => $column) {
				if ($columnIndex === 0) {
					$existingCell = $column->getId() !== null ? ($cellsByColumnId[$column->getId()] ?? null) : $this->findUnsavedCellForColumn($row, $column);
					if ($existingCell !== null) {
						$row->removeCell($existingCell);
					}
					continue;
				}

				$columnKey = (string) ($submittedColumns[$columnIndex]['key'] ?? sprintf('col_auto_%d', $columnIndex));
				$rawCell = $submittedCells[$columnKey] ?? null;
				[$normalizedValue, $normalizedPromoValue] = $this->normalizeSubmittedCell($rawCell);

				$existingCell = $column->getId() !== null ? ($cellsByColumnId[$column->getId()] ?? null) : $this->findUnsavedCellForColumn($row, $column);
				if ($normalizedValue === null && $normalizedPromoValue === null) {
					if ($existingCell !== null) {
						$row->removeCell($existingCell);
					}
					continue;
				}

				$cell = $existingCell ?? new PriceCell();
				$cell
					->setColumn($column)
					->setValue($normalizedValue)
					->setPromoValue($normalizedPromoValue);
				$row->addCell($cell);
			}

			$activeColumns = array_values(array_filter(
				$columnKeyToEntity,
				static fn (PriceColumn $column): bool => $column->getPosition() > 0,
			));
			foreach ($row->getCells()->toArray() as $cell) {
				if (!in_array($cell->getColumn(), $activeColumns, true)) {
					$row->removeCell($cell);
				}
			}
		}

		foreach ($section->getRows()->toArray() as $row) {
			$rowId = $row->getId();
			if ($rowId !== null && !in_array($rowId, $keptRowIds, true)) {
				$section->removeRow($row);
			}
		}
	}

	private function normalizePriceValue(mixed $rawValue): ?string
	{
		if ($rawValue === null) {
			return null;
		}

		$rawString = trim(str_replace(',', '.', (string) $rawValue));
		if ($rawString === '' || !is_numeric($rawString)) {
			return null;
		}

		return number_format((float) $rawString, 2, '.', '');
	}

	/**
	 * @return array{0: ?string, 1: ?string}
	 */
	private function normalizeSubmittedCell(mixed $rawCell): array
	{
		if (is_array($rawCell)) {
			return [
				$this->normalizePriceValue($rawCell['value'] ?? null),
				$this->normalizePriceValue($rawCell['promoValue'] ?? null),
			];
		}

		return [
			$this->normalizePriceValue($rawCell),
			null,
		];
	}

	private function findUnsavedCellForColumn(PriceRow $row, PriceColumn $column): ?PriceCell
	{
		foreach ($row->getCells() as $cell) {
			if ($cell->getColumn() === $column) {
				return $cell;
			}
		}

		return null;
	}

	/**
	 * @return array<int, PriceColumn>
	 */
	private function getOrderedColumns(PriceSection $section): array
	{
		$columns = $section->getColumns()->toArray();
		usort($columns, static function (PriceColumn $left, PriceColumn $right): int {
			$byPosition = $left->getPosition() <=> $right->getPosition();
			if ($byPosition !== 0) {
				return $byPosition;
			}

			return ($left->getId() ?? 0) <=> ($right->getId() ?? 0);
		});

		return $columns;
	}

	/**
	 * @return array<int, PriceRow>
	 */
	private function getOrderedRows(PriceSection $section): array
	{
		$rows = $section->getRows()->toArray();
		usort($rows, static function (PriceRow $left, PriceRow $right): int {
			$byPosition = $left->getPosition() <=> $right->getPosition();
			if ($byPosition !== 0) {
				return $byPosition;
			}

			return ($left->getId() ?? 0) <=> ($right->getId() ?? 0);
		});

		return $rows;
	}
}
