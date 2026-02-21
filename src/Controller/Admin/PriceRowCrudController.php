<?php

namespace App\Controller\Admin;

use App\Entity\PriceCell;
use App\Entity\PriceRow;
use App\Entity\PriceSection;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PriceRowCrudController extends AbstractCrudController
{
	private const POSITION_OFFSET = 1000000;

	public const PAGE_EMBEDDED_NEW = 'embedded_new';
	public const PAGE_EMBEDDED_EDIT = 'embedded_edit';

	public static function getEntityFqcn(): string
	{
		return PriceRow::class;
	}

	public function configureCrud(Crud $crud): Crud
	{
		return $crud
			->setEntityLabelInSingular('Price Row')
			->setEntityLabelInPlural('Price Rows')
			->showEntityActionsInlined()
			->setDefaultSort(['position' => 'ASC']);
	}

	public function configureFilters(Filters $filters): Filters
	{
		return $filters
			->add(EntityFilter::new('section'))
			->add(TextFilter::new('title'))
			->add(NumericFilter::new('position'));
	}

	public function configureFields(string $pageName): iterable
	{
		$section = AssociationField::new('section')
			->setCrudController(PriceSectionCrudController::class)
			->autocomplete();
		$title = TextField::new('title');
		$position = IntegerField::new('position');
		$cellsCount = IntegerField::new('cellsCount', 'Cells')
			->onlyOnIndex()
			->formatValue(static fn ($value, PriceRow $row): int => $row->getCells()->count());
		$cells = CollectionField::new('cells')
			->setLabel('Price Cells')
			->setEntryIsComplex()
			->setEntryToStringMethod(static fn (?PriceCell $cell): string => (string) ($cell?->getValue() ?? ''))
			->allowAdd()
			->allowDelete()
			->renderExpanded()
			->setFormTypeOption('by_reference', false)
			->useEntryCrudForm(
				PriceCellCrudController::class,
				PriceCellCrudController::PAGE_EMBEDDED_NEW,
				PriceCellCrudController::PAGE_EMBEDDED_EDIT,
			);

		if (\in_array($pageName, [self::PAGE_EMBEDDED_NEW, self::PAGE_EMBEDDED_EDIT], true)) {
			return [
				$title,
				$position,
			];
		}

		if (Crud::PAGE_INDEX === $pageName) {
			return [
				IdField::new('id'),
				$section,
				$title,
				$position,
				$cellsCount,
			];
		}

		return [
			$section,
			$title,
			$position,
			$cells,
		];
	}

	public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
	{
		if (!$entityInstance instanceof PriceRow) {
			parent::persistEntity($entityManager, $entityInstance);
			return;
		}

		$section = $entityInstance->getSection();
		if ($section === null) {
			parent::persistEntity($entityManager, $entityInstance);
			return;
		}

		$orderedRows = $this->buildOrderedRows($entityManager, $section, $entityInstance, $entityInstance->getPosition());
		$entityManager->persist($entityInstance);
		$this->applyOrderedPositions($entityManager, $orderedRows);
	}

	public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
	{
		if (!$entityInstance instanceof PriceRow) {
			parent::updateEntity($entityManager, $entityInstance);
			return;
		}

		$section = $entityInstance->getSection();
		if ($section === null) {
			parent::updateEntity($entityManager, $entityInstance);
			return;
		}

		$originalSection = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance)['section'] ?? null;

		$orderedRows = $this->buildOrderedRows($entityManager, $section, $entityInstance, $entityInstance->getPosition());
		$this->applyOrderedPositions($entityManager, $orderedRows);

		if ($originalSection instanceof PriceSection && $originalSection !== $section) {
			$this->compactSectionRows($entityManager, $originalSection);
		}
	}

	public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
	{
		if (!$entityInstance instanceof PriceRow) {
			parent::deleteEntity($entityManager, $entityInstance);
			return;
		}

		$section = $entityInstance->getSection();
		parent::deleteEntity($entityManager, $entityInstance);

		if ($section instanceof PriceSection) {
			$this->compactSectionRows($entityManager, $section);
		}
	}

	/**
	 * @return array<int, PriceRow>
	 */
	private function buildOrderedRows(EntityManagerInterface $entityManager, PriceSection $section, PriceRow $movedRow, int $targetPosition): array
	{
		/** @var array<int, PriceRow> $rows */
		$rows = $entityManager->getRepository(PriceRow::class)->findBy(
			['section' => $section],
			['position' => 'ASC', 'id' => 'ASC'],
		);

		$rows = array_values(array_filter(
			$rows,
			static fn (PriceRow $row): bool => $row->getId() !== $movedRow->getId(),
		));

		$clampedPosition = max(0, min($targetPosition, count($rows)));
		array_splice($rows, $clampedPosition, 0, [$movedRow]);

		return $rows;
	}

	private function compactSectionRows(EntityManagerInterface $entityManager, PriceSection $section): void
	{
		/** @var array<int, PriceRow> $rows */
		$rows = $entityManager->getRepository(PriceRow::class)->findBy(
			['section' => $section],
			['position' => 'ASC', 'id' => 'ASC'],
		);

		if ($rows === []) {
			return;
		}

		$this->applyOrderedPositions($entityManager, $rows);
	}

	/**
	 * @param array<int, PriceRow> $rows
	 */
	private function applyOrderedPositions(EntityManagerInterface $entityManager, array $rows): void
	{
		foreach ($rows as $index => $row) {
			$row->setPosition(self::POSITION_OFFSET + $index);
			$entityManager->persist($row);
		}
		$entityManager->flush();

		foreach ($rows as $index => $row) {
			$row->setPosition($index);
			$entityManager->persist($row);
		}
		$entityManager->flush();
	}
}
