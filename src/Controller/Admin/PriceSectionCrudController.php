<?php

namespace App\Controller\Admin;

use App\Entity\PriceColumn;
use App\Entity\PriceRow;
use App\Entity\PriceSection;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
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

	public function configureAssets(Assets $assets): Assets
	{
		return $assets
			->addJsFile(Asset::new('admin/position-collection-sort.js')->onlyOnForms());
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
