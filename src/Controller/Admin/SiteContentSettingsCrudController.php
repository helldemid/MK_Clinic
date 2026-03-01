<?php

namespace App\Controller\Admin;

use App\Entity\SiteContentSettings;
use App\Form\Type\ContactDetailBlockType;
use App\Form\Type\ContactIconBlockType;
use App\Field\PromoRotatorField;
use App\Form\Type\FooterNavigationLinkItemType;
use App\Form\Type\FooterSocialLinkItemType;
use App\Repository\HelpSectionRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;

class SiteContentSettingsCrudController extends AbstractCrudController
{
	public const SECTION_PROMO = 'promo';
	public const SECTION_HERO = 'hero';
	public const SECTION_PRICE_LIST_HERO = 'price_list_hero';
	public const SECTION_CONTACTS = 'contacts';
	public const SECTION_OUR_ETHOS = 'our_ethos';
	public const SECTION_OUR_STORY = 'our_story';
	public const SECTION_CONSULTATION = 'consultation';
	public const SECTION_BOOKING = 'booking';
	public const SECTION_FOOTER = 'footer';

	private const FOOTER_ICON_CHOICES = [
		'Instagram' => 'fa-brands fa-instagram',
		'Facebook' => 'fa-brands fa-facebook-f',
		'WhatsApp' => 'fa-brands fa-whatsapp',
		'TikTok' => 'fa-brands fa-tiktok',
		'YouTube' => 'fa-brands fa-youtube',
		'LinkedIn' => 'fa-brands fa-linkedin-in',
		'X (Twitter)' => 'fa-brands fa-x-twitter',
		'Telegram' => 'fa-brands fa-telegram',
		'Pinterest' => 'fa-brands fa-pinterest-p',
	];

	private const CONTACT_ICON_CHOICES = [
		'Phone' => 'res1.svg',
		'Email' => 'email.svg',
		'Location' => 'location_grey.svg',
		'Instagram' => 'instagram.svg',
		'Facebook' => 'facebook.svg',
		'WhatsApp' => 'whatsapp.svg',
		'TikTok' => 'tiktok-alt.svg',
		'LinkedIn' => 'linkedin.svg',
		'X (Twitter)' => 'x-twitter.svg',
		'YouTube' => 'youtube-icon.svg',
		'Telegram' => 'telegram.svg',
		'Pinterest' => 'pinterest.svg',
		'Time' => 'time.svg',
		'Service' => 'service.svg',
		'Quality' => 'quality.svg',
	];

	private string $siteMediaUploadDir;
	private string $currentEnv;

	public function __construct(
		KernelInterface $kernel,
		private readonly RequestStack $requestStack,
		private readonly HelpSectionRepository $helpSectionRepository,
	)
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
		$sectionLabel = $this->getSectionLabel($this->getActiveSection());

		return $crud
			->setEntityLabelInSingular($sectionLabel)
			->setEntityLabelInPlural($sectionLabel);
	}

	public function configureAssets(Assets $assets): Assets
	{
		return $assets
			->addJsFile(Asset::new('adminPanel/promo-rotator-editor.js')->onlyOnForms())
			->addCssFile(Asset::new('adminPanel/promo-rotator-editor.css')->onlyOnForms())
			->addJsFile(Asset::new('adminPanel/site-content-settings-editor.js')->onlyOnForms())
			->addCssFile(Asset::new('adminPanel/site-content-settings-editor.css')->onlyOnForms())
			->addJsFile(Asset::new('adminPanel/position-collection-sort.js')->onlyOnForms());
	}

	public function configureActions(Actions $actions): Actions
	{
		return $actions
			->disable(Action::NEW, Action::DELETE, Action::BATCH_DELETE);
	}

	public function configureFields(string $pageName): iterable
	{
		$this->ensureUploadDirectoryExists();

		if (Crud::PAGE_INDEX === $pageName) {
			return [
				IdField::new('id'),
			];
		}

		$section = $this->getActiveSection();
		$uploadDirectory = $this->currentEnv === 'prod'
			? '../public_html/media/site'
			: 'public/media/site';

		if ($section === self::SECTION_PROMO) {
			return [
				PromoRotatorField::new('promoItems', 'Promo messages')
					->setHelp('Set rotating promo messages and links for the top bar.'),
			];
		}

		if ($section === self::SECTION_HERO) {
			return $this->buildHeroFields(
				'heroHeadlineLine1',
				'heroHeadlineLine2',
				'heroSubheadline',
				'heroDesktopImage',
				'heroMobileImage',
				'hero',
				$uploadDirectory,
			);
		}

		if ($section === self::SECTION_PRICE_LIST_HERO) {
			$fields = $this->buildHeroFields(
				'priceListHeroEyebrow',
				'priceListHeroTitle',
				'priceListHeroLead',
				'priceListHeroDesktopImage',
				'priceListHeroMobileImage',
				'price-list-hero',
				$uploadDirectory,
				'Eyebrow',
				'Title',
				'Lead text',
			);

			$fields[] = TextField::new('priceListHeroHighlight1', 'Highlight 1')->setRequired(true);
			$fields[] = TextField::new('priceListHeroHighlight2', 'Highlight 2')->setRequired(true);
			$fields[] = TextField::new('priceListHeroHighlight3', 'Highlight 3')->setRequired(true);

			return $fields;
		}

		if ($section === self::SECTION_CONTACTS) {
			return [
				CollectionField::new('contactDetailBlocks', 'Contact blocks with text')
					->setHelp('Blocks with title, optional extra text, and contact rows with icon + text.')
					->setEntryIsComplex()
					->setEntryToStringMethod(static function ($item): string {
						if (!is_array($item)) {
							return '';
						}

						return trim((string) ($item['title'] ?? ''));
					})
					->allowAdd()
					->allowDelete()
					->renderExpanded()
					->setFormTypeOption('by_reference', false)
					->setFormTypeOption('entry_type', ContactDetailBlockType::class)
					->setFormTypeOption('entry_options.icon_choices', self::CONTACT_ICON_CHOICES)
					->setFormTypeOption('row_attr.data-position-sortable', 'true'),
				CollectionField::new('contactIconBlocks', 'Icon-only blocks')
					->setHelp('Blocks for social links or other icon-only contact groups.')
					->setEntryIsComplex()
					->setEntryToStringMethod(static function ($item): string {
						if (!is_array($item)) {
							return '';
						}

						return trim((string) ($item['title'] ?? ''));
					})
					->allowAdd()
					->allowDelete()
					->renderExpanded()
					->setFormTypeOption('by_reference', false)
					->setFormTypeOption('entry_type', ContactIconBlockType::class)
					->setFormTypeOption('entry_options.icon_choices', self::CONTACT_ICON_CHOICES)
					->setFormTypeOption('row_attr.data-position-sortable', 'true'),
			];
		}

		if ($section === self::SECTION_OUR_ETHOS) {
			return [
				TextField::new('ourEthosTitle', 'Section title')->setRequired(true),
				TextareaField::new('ourEthosBody', 'Body text')
					->setRequired(true)
					->setFormTypeOption('attr.rows', 12),
				$this->buildImageField('ourEthosImage', 'Section image', 'our-ethos-[timestamp].[extension]', $uploadDirectory),
			];
		}

		if ($section === self::SECTION_OUR_STORY) {
			return [
				TextField::new('ourStoryTitle', 'Section title')->setRequired(true),
				TextareaField::new('ourStoryBody', 'Body text')
					->setRequired(true)
					->setFormTypeOption('attr.rows', 12),
				$this->buildImageField('ourStoryImage', 'Section image', 'our-story-[timestamp].[extension]', $uploadDirectory),
			];
		}

		if ($section === self::SECTION_CONSULTATION) {
			return [
				TextField::new('consultationEyebrow', 'Eyebrow text')->setRequired(true),
				TextField::new('consultationTitle', 'Block title')->setRequired(true),
				TextareaField::new('consultationBody', 'Body text')
					->setRequired(true)
					->setFormTypeOption('attr.rows', 6),
				TextField::new('consultationButtonLabel', 'Button label')->setRequired(true),
				$this->buildImageField('consultationImage', 'Block image', 'consultation-[timestamp].[extension]', $uploadDirectory),
			];
		}

		if ($section === self::SECTION_BOOKING) {
			return [
				TextField::new('bookingEyebrow', 'Eyebrow text')->setRequired(true),
				TextField::new('bookingTitle', 'Block title')->setRequired(true),
				TextareaField::new('bookingBody', 'Body text')
					->setRequired(true)
					->setFormTypeOption('attr.rows', 6),
				TextField::new('bookingButtonLabel', 'Button label')->setRequired(true),
				$this->buildImageField('bookingImage', 'Block image', 'booking-[timestamp].[extension]', $uploadDirectory),
			];
		}

		$linkChoices = $this->buildFooterLinkChoices();

		return [
			TextareaField::new('footerDescription', 'Footer description')
				->setRequired(true)
				->setFormTypeOption('attr.rows', 5),
			CollectionField::new('footerSocialLinks', 'Social links')
				->setHelp('Add, remove and reorder social links. URL must be valid.')
				->setEntryIsComplex()
				->setEntryToStringMethod(static function ($item): string {
					if (!is_array($item)) {
						return '';
					}

					$label = trim((string) ($item['label'] ?? ''));
					$url = trim((string) ($item['url'] ?? ''));

					return $label !== '' ? $label : $url;
				})
				->allowAdd()
				->allowDelete()
				->renderExpanded()
				->setFormTypeOption('by_reference', false)
				->setFormTypeOption('entry_type', FooterSocialLinkItemType::class)
				->setFormTypeOption('entry_options.icon_choices', self::FOOTER_ICON_CHOICES)
				->setFormTypeOption('row_attr.data-position-sortable', 'true'),
			CollectionField::new('footerCustomerCareLinks', 'Customer Care links')
				->setHelp('Choose links from Help entries or Contact Us page and set display order.')
				->setEntryIsComplex()
				->setEntryToStringMethod(static function ($item): string {
					if (!is_array($item)) {
						return '';
					}

					return trim((string) ($item['label'] ?? '')) ?: (string) ($item['sourceKey'] ?? '');
				})
				->allowAdd()
				->allowDelete()
				->renderExpanded()
				->setFormTypeOption('by_reference', false)
				->setFormTypeOption('entry_type', FooterNavigationLinkItemType::class)
				->setFormTypeOption('entry_options.link_choices', $linkChoices)
				->setFormTypeOption('row_attr.data-position-sortable', 'true'),
			CollectionField::new('footerCompanyLegalLinks', 'Company & Legal links')
				->setHelp('Choose links from Help entries or Contact Us page and set display order.')
				->setEntryIsComplex()
				->setEntryToStringMethod(static function ($item): string {
					if (!is_array($item)) {
						return '';
					}

					return trim((string) ($item['label'] ?? '')) ?: (string) ($item['sourceKey'] ?? '');
				})
				->allowAdd()
				->allowDelete()
				->renderExpanded()
				->setFormTypeOption('by_reference', false)
				->setFormTypeOption('entry_type', FooterNavigationLinkItemType::class)
				->setFormTypeOption('entry_options.link_choices', $linkChoices)
				->setFormTypeOption('row_attr.data-position-sortable', 'true'),
		];
	}

	/**
	 * @return array<string, string>
	 */
	private function buildFooterLinkChoices(): array
	{
		$choices = [
			'Contact Us (standalone page)' => 'route:contacts',
		];

		foreach ($this->helpSectionRepository->findAllOrdered() as $section) {
			$choices['Help: '.$section->getTitle()] = 'help:'.$section->getSlug();
		}

		return $choices;
	}

	private function buildImageField(string $property, string $label, string $filePattern, string $uploadDirectory): ImageField
	{
		return ImageField::new($property, $label)
			->setBasePath('/media/site')
			->setUploadDir($uploadDirectory)
			->setUploadedFileNamePattern($filePattern)
			->setFormTypeOption('attr.data-download-prefix', '/media/site/')
			->setFormTypeOption('attr.data-site-content-preview', '1');
	}

	private function ensureUploadDirectoryExists(): void
	{
		if (is_dir($this->siteMediaUploadDir)) {
			return;
		}

		@mkdir($this->siteMediaUploadDir, 0775, true);
	}

	private function getActiveSection(): string
	{
		$raw = strtolower((string) ($this->requestStack->getCurrentRequest()?->query->get('section', self::SECTION_PROMO)));
		$allowed = [
			self::SECTION_PROMO,
			self::SECTION_HERO,
			self::SECTION_PRICE_LIST_HERO,
			self::SECTION_CONTACTS,
			self::SECTION_OUR_ETHOS,
			self::SECTION_OUR_STORY,
			self::SECTION_CONSULTATION,
			self::SECTION_BOOKING,
			self::SECTION_FOOTER,
		];

		return in_array($raw, $allowed, true) ? $raw : self::SECTION_PROMO;
	}

	private function getSectionLabel(string $section): string
	{
		return match ($section) {
			self::SECTION_HERO => 'Homepage Hero',
			self::SECTION_PRICE_LIST_HERO => 'Price List Hero',
			self::SECTION_CONTACTS => 'Contact Us',
			self::SECTION_OUR_ETHOS => 'Homepage - Our Ethos',
			self::SECTION_OUR_STORY => 'Homepage - Our Story',
			self::SECTION_CONSULTATION => 'Consultation Block',
			self::SECTION_BOOKING => 'Booking Block',
			self::SECTION_FOOTER => 'Footer Settings',
			default => 'Promo Bar',
		};
	}

	/**
	 * @return array<int, TextField|TextareaField|ImageField>
	 */
	private function buildHeroFields(
		string $eyebrowProperty,
		string $titleProperty,
		string $leadProperty,
		string $desktopImageProperty,
		string $mobileImageProperty,
		string $filePrefix,
		string $uploadDirectory = 'public/media/site',
		string $eyebrowLabel = 'Headline line 1',
		string $titleLabel = 'Headline line 2',
		string $leadLabel = 'Subheadline',
	): array {
		return [
			TextField::new($eyebrowProperty, $eyebrowLabel)
				->setRequired(true),
			TextField::new($titleProperty, $titleLabel)
				->setRequired(true),
			TextareaField::new($leadProperty, $leadLabel)
				->setRequired(true)
				->setFormTypeOption('attr.rows', 4),
			$this->buildImageField($desktopImageProperty, 'Hero image (desktop)', $filePrefix.'-desktop-[timestamp].[extension]', $uploadDirectory),
			$this->buildImageField($mobileImageProperty, 'Hero image (mobile)', $filePrefix.'-mobile-[timestamp].[extension]', $uploadDirectory),
		];
	}
}
