<?php

namespace App\Controller\Admin;

use App\Entity\HelpSection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
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

	public function configureAssets(Assets $assets): Assets
	{
		return $assets
			->addJsFile(Asset::new('adminPanel/position-collection-sort.js')->onlyOnForms())
			->addJsFile(Asset::new('adminPanel/help-section-editor.js')->onlyOnForms());
	}

	public function configureFields(string $pageName): iterable
	{
		$slug = TextField::new('slug');
		$title = TextField::new('title');
		$position = IntegerField::new('position');
		$faqSection = BooleanField::new('faqSection', 'Q&A section')
			->setHelp('When enabled, section content is managed via Help FAQs.');
		$content = TextareaField::new('content')
			->setFormType(CKEditorType::class)
			->setFormTypeOptions([
				'config_name' => 'help_section',
				'attr' => ['rows' => 60],
			])
			->hideOnIndex();
		$faqsCount = IntegerField::new('faqsCount', 'FAQs')
			->onlyOnIndex()
			->formatValue(static fn ($value, HelpSection $section): int => $section->getFaqs()->count());

		if (Crud::PAGE_INDEX === $pageName) {
			return [
				IdField::new('id'),
				$slug,
				$title,
				$position,
				$faqSection,
				$faqsCount,
			];
		}

		return [
			FormField::addTab('General', 'fa-solid fa-file-lines'),
			$slug,
			$title,
			$position,
			$faqSection,
			$content,
		];
	}
}
