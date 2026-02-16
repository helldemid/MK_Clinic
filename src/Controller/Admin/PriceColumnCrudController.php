<?php

namespace App\Controller\Admin;

use App\Entity\PriceColumn;
use App\Entity\PriceSection;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PriceColumnCrudController extends AbstractCrudController
{
	private const POSITION_OFFSET = 1000000;

	public const PAGE_EMBEDDED_NEW = 'embedded_new';
	public const PAGE_EMBEDDED_EDIT = 'embedded_edit';

	public static function getEntityFqcn(): string
	{
		return PriceColumn::class;
	}

	public function configureCrud(Crud $crud): Crud
	{
		return $crud
			->setEntityLabelInSingular('Price Column')
			->setEntityLabelInPlural('Price Columns')
			->showEntityActionsInlined()
			->setDefaultSort(['position' => 'ASC']);
	}

	public function configureFields(string $pageName): iterable
	{
		$section = AssociationField::new('section')
			->setCrudController(PriceSectionCrudController::class)
			->autocomplete();
		$label = TextField::new('label');
		$position = IntegerField::new('position');

		if (\in_array($pageName, [self::PAGE_EMBEDDED_NEW, self::PAGE_EMBEDDED_EDIT], true)) {
			return [
				$label,
				$position,
			];
		}

		if (Crud::PAGE_INDEX === $pageName) {
			return [
				IdField::new('id'),
				$section,
				$label,
				$position,
			];
		}

		return [
			$section,
			$label,
			$position,
		];
	}

	public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
	{
		if (!$entityInstance instanceof PriceColumn) {
			parent::persistEntity($entityManager, $entityInstance);
			return;
		}

		$section = $entityInstance->getSection();
		if ($section === null) {
			parent::persistEntity($entityManager, $entityInstance);
			return;
		}

		$orderedColumns = $this->buildOrderedColumns($entityManager, $section, $entityInstance, $entityInstance->getPosition());
		$entityManager->persist($entityInstance);
		$this->applyOrderedPositions($entityManager, $orderedColumns);
	}

	public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
	{
		if (!$entityInstance instanceof PriceColumn) {
			parent::updateEntity($entityManager, $entityInstance);
			return;
		}

		$section = $entityInstance->getSection();
		if ($section === null) {
			parent::updateEntity($entityManager, $entityInstance);
			return;
		}

		$originalSection = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance)['section'] ?? null;

		$orderedColumns = $this->buildOrderedColumns($entityManager, $section, $entityInstance, $entityInstance->getPosition());
		$this->applyOrderedPositions($entityManager, $orderedColumns);

		if ($originalSection instanceof PriceSection && $originalSection !== $section) {
			$this->compactSectionColumns($entityManager, $originalSection);
		}
	}

	public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
	{
		if (!$entityInstance instanceof PriceColumn) {
			parent::deleteEntity($entityManager, $entityInstance);
			return;
		}

		$section = $entityInstance->getSection();
		parent::deleteEntity($entityManager, $entityInstance);

		if ($section instanceof PriceSection) {
			$this->compactSectionColumns($entityManager, $section);
		}
	}

	/**
	 * @return array<int, PriceColumn>
	 */
	private function buildOrderedColumns(EntityManagerInterface $entityManager, PriceSection $section, PriceColumn $movedColumn, int $targetPosition): array
	{
		/** @var array<int, PriceColumn> $columns */
		$columns = $entityManager->getRepository(PriceColumn::class)->findBy(
			['section' => $section],
			['position' => 'ASC', 'id' => 'ASC'],
		);

		$columns = array_values(array_filter(
			$columns,
			static fn (PriceColumn $column): bool => $column->getId() !== $movedColumn->getId(),
		));

		$clampedPosition = max(0, min($targetPosition, count($columns)));
		array_splice($columns, $clampedPosition, 0, [$movedColumn]);

		return $columns;
	}

	private function compactSectionColumns(EntityManagerInterface $entityManager, PriceSection $section): void
	{
		/** @var array<int, PriceColumn> $columns */
		$columns = $entityManager->getRepository(PriceColumn::class)->findBy(
			['section' => $section],
			['position' => 'ASC', 'id' => 'ASC'],
		);

		if ($columns === []) {
			return;
		}

		$this->applyOrderedPositions($entityManager, $columns);
	}

	/**
	 * @param array<int, PriceColumn> $columns
	 */
	private function applyOrderedPositions(EntityManagerInterface $entityManager, array $columns): void
	{
		foreach ($columns as $index => $column) {
			$column->setPosition(self::POSITION_OFFSET + $index);
			$entityManager->persist($column);
		}
		$entityManager->flush();

		foreach ($columns as $index => $column) {
			$column->setPosition($index);
			$entityManager->persist($column);
		}
		$entityManager->flush();
	}
}
