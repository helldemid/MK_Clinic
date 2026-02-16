<?php

namespace App\Controller\Admin;

use App\Entity\PriceCell;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class PriceCellCrudController extends AbstractCrudController
{
	public const PAGE_EMBEDDED_NEW = 'embedded_new';
	public const PAGE_EMBEDDED_EDIT = 'embedded_edit';

	public static function getEntityFqcn(): string
	{
		return PriceCell::class;
	}

	public function configureCrud(Crud $crud): Crud
	{
		return $crud
			->setEntityLabelInSingular('Price Cell')
			->setEntityLabelInPlural('Price Cells')
			->showEntityActionsInlined()
			->setDefaultSort(['id' => 'ASC']);
	}

	public function configureFields(string $pageName): iterable
	{
		$row = AssociationField::new('row')
			->setCrudController(PriceRowCrudController::class)
			->autocomplete();
		$column = AssociationField::new('column')
			->setCrudController(PriceColumnCrudController::class)
			->autocomplete();
		$value = NumberField::new('value')
			->setNumDecimals(2)
			->setStoredAsString(true)
			->setRequired(false);
		$promoValue = NumberField::new('promoValue', 'Promo price')
			->setNumDecimals(2)
			->setStoredAsString(true)
			->setRequired(false);

		if (\in_array($pageName, [self::PAGE_EMBEDDED_NEW, self::PAGE_EMBEDDED_EDIT], true)) {
			return [
				$column,
				$value,
				$promoValue,
			];
		}

		if (Crud::PAGE_INDEX === $pageName) {
			return [
				IdField::new('id'),
				$row,
				$column,
				$value,
				$promoValue,
			];
		}

		return [
			$row,
			$column,
			$value,
			$promoValue,
		];
	}
}
