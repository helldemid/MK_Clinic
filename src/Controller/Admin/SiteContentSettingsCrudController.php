<?php

namespace App\Controller\Admin;

use App\Entity\SiteContentSettings;
use App\Field\PromoRotatorField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\HttpKernel\KernelInterface;

class SiteContentSettingsCrudController extends AbstractCrudController
{
	private string $siteMediaUploadDir;
	private string $currentEnv;

	public function __construct(KernelInterface $kernel)
	{
		$basePath = $kernel->getProjectDir();

		$this->currentEnv = $kernel->getEnvironment();
		$this->siteMediaUploadDir = $this->currentEnv === 'prod'
			? '/home2/mkaesthe/public_html/media/site'
			: $basePath . '/public/media/site';
	}

	public static function getEntityFqcn(): string
	{
		return SiteContentSettings::class;
	}

	public function configureCrud(Crud $crud): Crud
	{
		return $crud
			->setEntityLabelInSingular('Site Content Settings')
			->setEntityLabelInPlural('Site Content Settings');
	}

	public function configureAssets(Assets $assets): Assets
	{
		return $assets
			->addJsFile(Asset::new('adminPanel/promo-rotator-editor.js')->onlyOnForms())
			->addCssFile(Asset::new('adminPanel/promo-rotator-editor.css')->onlyOnForms())
			->addJsFile(Asset::new('adminPanel/site-content-settings-editor.js')->onlyOnForms())
			->addCssFile(Asset::new('adminPanel/site-content-settings-editor.css')->onlyOnForms());
	}

	public function configureActions(Actions $actions): Actions
	{
		return $actions
			->disable(Action::NEW , Action::DELETE, Action::BATCH_DELETE);
	}

	public function configureFields(string $pageName): iterable
	{
		$this->ensureUploadDirectoryExists();
		$uploadDirectory = $this->currentEnv === 'prod'
		    ? '../public_html/media/site'
		    : 'public/media/site';

		$promoItems = PromoRotatorField::new('promoItems', 'Promo messages')
			->setHelp('Set rotating promo messages and links for the top bar.');
		$heroDesktopImage = ImageField::new('heroDesktopImage', 'Hero image (desktop)')
			->setBasePath('/media/site')
			->setUploadDir($uploadDirectory)
			->setUploadedFileNamePattern('hero-desktop-[timestamp].[extension]')
			->setFormTypeOption('attr.data-download-prefix', '/media/site/')
			->setHelp('Upload desktop image. Current image is shown below with download link.');
		$heroMobileImage = ImageField::new('heroMobileImage', 'Hero image (mobile)')
			->setBasePath('/media/site')
			->setUploadDir($uploadDirectory)
			->setUploadedFileNamePattern('hero-mobile-[timestamp].[extension]')
			->setFormTypeOption('attr.data-download-prefix', '/media/site/')
			->setHelp('Upload mobile image. Current image is shown below with download link.');

		if (Crud::PAGE_INDEX === $pageName) {
			return [
				IdField::new('id'),
				$heroDesktopImage,
				$heroMobileImage,
			];
		}

		return [
			$promoItems,
			$heroDesktopImage,
			$heroMobileImage,
		];
	}

	private function ensureUploadDirectoryExists(): void
	{
		if (is_dir($this->siteMediaUploadDir)) {
			return;
		}

		@mkdir($this->siteMediaUploadDir, 0775, true);
	}
}
