<?php

namespace App\Controller\Admin;

use App\Entity\HelpSection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class HelpSectionCrudController extends AbstractCrudController
{
	public static function getEntityFqcn(): string
	{
		return HelpSection::class;
	}

	public function configureCrud(Crud $crud): Crud
	{
		return $crud
			->setEntityLabelInSingular('Help Section')
			->setEntityLabelInPlural('Help Sections')
			->showEntityActionsInlined()
			->setDefaultSort(['position' => 'ASC', 'id' => 'ASC']);
	}

	public function configureFields(string $pageName): iterable
	{
		$slug = TextField::new('slug');
		$title = TextField::new('title');
		$position = IntegerField::new('position');
		$content = TextareaField::new('content')
			->setFormType(CKEditorType::class)
			->setFormTypeOptions([
				'config_name' => 'help_section',
				'attr' => ['rows' => 60],
			])
			->hideOnIndex();

		if (Crud::PAGE_INDEX === $pageName) {
			return [
				IdField::new('id'),
				$slug,
				$title,
				$position,
			];
		}

		return [
			$slug,
			$title,
			$position,
			$content,
		];
	}
}
