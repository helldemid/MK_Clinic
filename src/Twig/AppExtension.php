<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Entity\SiteContentSettings;
use App\Repository\HelpSectionRepository;
use App\Repository\CategoriesRepository;
use App\Repository\SiteContentSettingsRepository;
use App\Repository\TreatmentsRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

use App\Service\PabauService;

use Twig\TwigFunction;
use Twig\Environment;
class AppExtension extends AbstractExtension implements GlobalsInterface
{
	private const DEFAULT_PROMO_ITEMS = [
		['text' => '50% OFF all laser device treatments', 'url' => '#'],
		['text' => 'REFER A FRIEND AND RECEIVE 500 CREDIT POINTS BOTH', 'url' => 'https://mkaestheticclinic.com/help/rewards-programme'],
		['text' => 'Earn rewards with every visit', 'url' => 'https://mkaestheticclinic.com/help/rewards-programme'],
		['text' => 'Refer a friend and enjoy shared benefits', 'url' => 'https://mkaestheticclinic.com/help/rewards-programme'],
		['text' => 'Celebrate your birthday with us', 'url' => 'https://mkaestheticclinic.com/help/rewards-programme'],
		['text' => 'Join our team', 'url' => 'https://mkaestheticclinic.com/help/career'],
	];

	private const DEFAULT_HERO_DESKTOP = 'media/welcome_3.webp';
	private const DEFAULT_HERO_MOBILE = 'media/welcome_mobile_1.webp';
	private const DEFAULT_PRICE_LIST_HERO_DESKTOP = 'media/welcome_3.webp';
	private const DEFAULT_PRICE_LIST_HERO_MOBILE = 'media/welcome_mobile_1.webp';
	private const DEFAULT_OUR_ETHOS_IMAGE = 'media/our_ethos.png';
	private const DEFAULT_OUR_STORY_IMAGE = 'media/about_us.png';
	private const DEFAULT_CONSULTATION_IMAGE = 'media/welcome_3.webp';
	private const DEFAULT_BOOKING_IMAGE = 'media/book_pic_1_1.png';

	private $catRepo;
	private $tRepo;
	private $pabauService;
	private SiteContentSettingsRepository $siteContentSettingsRepository;
	private HelpSectionRepository $helpSectionRepository;
	private SluggerInterface $slugger;
	private UrlGeneratorInterface $urlGenerator;

	public function __construct(
		CategoriesRepository $catRepo,
		TreatmentsRepository $tRepo,
		PabauService $pabauService,
		SiteContentSettingsRepository $siteContentSettingsRepository,
		HelpSectionRepository $helpSectionRepository,
		SluggerInterface $slugger,
		UrlGeneratorInterface $urlGenerator,
		private Environment $twig
	)
	{
		$this->catRepo = $catRepo;
		$this->tRepo = $tRepo;
		$this->pabauService = $pabauService;
		$this->siteContentSettingsRepository = $siteContentSettingsRepository;
		$this->helpSectionRepository = $helpSectionRepository;
		$this->slugger = $slugger;
		$this->urlGenerator = $urlGenerator;
	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('get_pabau_treatment_url', [$this, 'getPabauTreatmentUrl'], ['is_safe' => ['html']]),
			new TwigFunction('treatment_slug', [$this, 'buildTreatmentSlug']),
		];
	}

	public function buildTreatmentSlug(string $name): string
	{
		$slug = $this->slugger->slug($name)->lower()->toString();

		return $slug !== '' ? str_replace('-', '_', $slug) : 'treatment';
	}

	public function getPabauTreatmentUrl(int $superCategoryId = 0, string $treatmentName = ''): string {
		$url = 'https://partner.pabau.com/online-bookings/mkaestheticclinic';

		if ($superCategoryId <= 0) return $url;

		$url .= '?groupCategory=' . $superCategoryId;

		if (empty($treatmentName)) return $url;

		$subcategoryId = $this->pabauService->findCategoryIdByName($treatmentName);

		if ($subcategoryId === null) return $url;

		$url .= '&category=' . $subcategoryId;

		return $url;
	}

	public function getGlobals(): array
	{
		$categories = $this->catRepo->getCategoriesSortedByNameLength();
		$dataForMenu = [];
		$categoriesToRespond = [];
		$consultationCategoryMasterId = 16436;
		foreach ($categories as $category) {
			$treatments = $this->tRepo->getTreatmentsDataForMenu($category['id']);
			if ($treatments && !empty($treatments) && $category['is_shown']) {
				$dataForMenu[$category['name']] = $treatments;
			}
			$categoriesToRespond[] = $category;
			if ($category['name'] === 'Consultation') $consultationCategoryMasterId = $category['pabauMasterCategoryId'];
		}

		uasort($dataForMenu, function ($a, $b) {
			return count($b) <=> count($a);
		});

		$settings = null;
		$promoItems = self::DEFAULT_PROMO_ITEMS;
		$heroDesktopImage = null;
		$heroMobileImage = null;
		$helpTitleBySlug = [];

		try {
			$settings = $this->siteContentSettingsRepository->findSingleton();
			$loadedPromoItems = $settings?->getPromoItems() ?? [];
			if ($loadedPromoItems !== []) {
				$promoItems = $loadedPromoItems;
			}
			$heroDesktopImage = $settings?->getHeroDesktopImage();
			$heroMobileImage = $settings?->getHeroMobileImage();
		} catch (\Throwable) {
			// Allow admin/frontend to render with defaults if DB schema is temporarily out of sync.
		}

		try {
			foreach ($this->helpSectionRepository->findAllOrdered() as $section) {
				$helpTitleBySlug[$section->getSlug()] = $section->getTitle();
			}
		} catch (\Throwable) {
			$helpTitleBySlug = [];
		}

		$footerCustomerCare = $this->resolveFooterLinks(
			$settings?->getFooterCustomerCareLinks() ?? SiteContentSettings::DEFAULT_FOOTER_CUSTOMER_CARE_LINKS,
			$helpTitleBySlug,
		);
		$footerCompanyLegal = $this->resolveFooterLinks(
			$settings?->getFooterCompanyLegalLinks() ?? SiteContentSettings::DEFAULT_FOOTER_COMPANY_LEGAL_LINKS,
			$helpTitleBySlug,
		);

		return [
			'treatmentsCategories' => $categoriesToRespond,
			'dataForMenu' => $dataForMenu,
			'pabauBaseUrl' => 'https://partner.pabau.com/online-bookings/mkaestheticclinic',
			'consultationCategoryMasterId' => $consultationCategoryMasterId,
			'sitePromoItems' => $promoItems,
			'siteHeroDesktopImage' => $this->resolveSiteImage($heroDesktopImage, self::DEFAULT_HERO_DESKTOP),
			'siteHeroMobileImage' => $this->resolveSiteImage($heroMobileImage, self::DEFAULT_HERO_MOBILE),
			'siteHeroHeadlineLine1' => $settings?->getHeroHeadlineLine1() ?? SiteContentSettings::DEFAULT_HERO_HEADLINE_LINE_1,
			'siteHeroHeadlineLine2' => $settings?->getHeroHeadlineLine2() ?? SiteContentSettings::DEFAULT_HERO_HEADLINE_LINE_2,
			'siteHeroSubheadline' => $settings?->getHeroSubheadline() ?? SiteContentSettings::DEFAULT_HERO_SUBHEADLINE,
			'sitePriceListHeroDesktopImage' => $this->resolveSiteImage($settings?->getPriceListHeroDesktopImage(), self::DEFAULT_PRICE_LIST_HERO_DESKTOP),
			'sitePriceListHeroMobileImage' => $this->resolveSiteImage($settings?->getPriceListHeroMobileImage(), self::DEFAULT_PRICE_LIST_HERO_MOBILE),
			'sitePriceListHeroEyebrow' => $settings?->getPriceListHeroEyebrow() ?? SiteContentSettings::DEFAULT_PRICE_LIST_HERO_EYEBROW,
			'sitePriceListHeroTitle' => $settings?->getPriceListHeroTitle() ?? SiteContentSettings::DEFAULT_PRICE_LIST_HERO_TITLE,
			'sitePriceListHeroLead' => $settings?->getPriceListHeroLead() ?? SiteContentSettings::DEFAULT_PRICE_LIST_HERO_LEAD,
			'sitePriceListHeroHighlight1' => $settings?->getPriceListHeroHighlight1() ?? SiteContentSettings::DEFAULT_PRICE_LIST_HERO_HIGHLIGHT_1,
			'sitePriceListHeroHighlight2' => $settings?->getPriceListHeroHighlight2() ?? SiteContentSettings::DEFAULT_PRICE_LIST_HERO_HIGHLIGHT_2,
			'sitePriceListHeroHighlight3' => $settings?->getPriceListHeroHighlight3() ?? SiteContentSettings::DEFAULT_PRICE_LIST_HERO_HIGHLIGHT_3,
			'siteContactDetailBlocks' => $settings?->getContactDetailBlocks() ?? SiteContentSettings::DEFAULT_CONTACT_DETAIL_BLOCKS,
			'siteContactIconBlocks' => $settings?->getContactIconBlocks() ?? SiteContentSettings::DEFAULT_CONTACT_ICON_BLOCKS,
			'siteOurEthosTitle' => $settings?->getOurEthosTitle() ?? SiteContentSettings::DEFAULT_OUR_ETHOS_TITLE,
			'siteOurEthosBody' => $settings?->getOurEthosBody() ?? SiteContentSettings::DEFAULT_OUR_ETHOS_BODY,
			'siteOurEthosImage' => $this->resolveSiteImage($settings?->getOurEthosImage(), self::DEFAULT_OUR_ETHOS_IMAGE),
			'siteOurStoryTitle' => $settings?->getOurStoryTitle() ?? SiteContentSettings::DEFAULT_OUR_STORY_TITLE,
			'siteOurStoryBody' => $settings?->getOurStoryBody() ?? SiteContentSettings::DEFAULT_OUR_STORY_BODY,
			'siteOurStoryImage' => $this->resolveSiteImage($settings?->getOurStoryImage(), self::DEFAULT_OUR_STORY_IMAGE),
			'siteConsultationEyebrow' => $settings?->getConsultationEyebrow() ?? SiteContentSettings::DEFAULT_CONSULTATION_EYEBROW,
			'siteConsultationTitle' => $settings?->getConsultationTitle() ?? SiteContentSettings::DEFAULT_CONSULTATION_TITLE,
			'siteConsultationBody' => $settings?->getConsultationBody() ?? SiteContentSettings::DEFAULT_CONSULTATION_BODY,
			'siteConsultationImage' => $this->resolveSiteImage($settings?->getConsultationImage(), self::DEFAULT_CONSULTATION_IMAGE),
			'siteConsultationButtonLabel' => $settings?->getConsultationButtonLabel() ?? SiteContentSettings::DEFAULT_CONSULTATION_BUTTON_LABEL,
			'siteBookingEyebrow' => $settings?->getBookingEyebrow() ?? SiteContentSettings::DEFAULT_BOOKING_EYEBROW,
			'siteBookingTitle' => $settings?->getBookingTitle() ?? SiteContentSettings::DEFAULT_BOOKING_TITLE,
			'siteBookingBody' => $settings?->getBookingBody() ?? SiteContentSettings::DEFAULT_BOOKING_BODY,
			'siteBookingImage' => $this->resolveSiteImage($settings?->getBookingImage(), self::DEFAULT_BOOKING_IMAGE),
			'siteBookingButtonLabel' => $settings?->getBookingButtonLabel() ?? SiteContentSettings::DEFAULT_BOOKING_BUTTON_LABEL,
			'siteFooterDescription' => $settings?->getFooterDescription() ?? SiteContentSettings::DEFAULT_FOOTER_DESCRIPTION,
			'siteFooterSocialLinks' => $settings?->getFooterSocialLinks() ?? SiteContentSettings::DEFAULT_FOOTER_SOCIAL_LINKS,
			'siteFooterCustomerCareLinks' => $footerCustomerCare,
			'siteFooterCompanyLegalLinks' => $footerCompanyLegal,
		];
	}

	private function resolveSiteImage(?string $storedImage, string $fallbackPath): string
	{
		$image = trim((string) $storedImage);

		return $image !== '' ? 'media/site/'.$image : $fallbackPath;
	}

	/**
	 * @param array<int, array{sourceKey: string, label?: string, position?: int}> $items
	 * @param array<string, string> $helpTitleBySlug
	 * @return array<int, array{label: string, url: string}>
	 */
	private function resolveFooterLinks(array $items, array $helpTitleBySlug): array
	{
		$resolved = [];

		foreach ($items as $item) {
			if (!is_array($item)) {
				continue;
			}

			$sourceKey = trim((string) ($item['sourceKey'] ?? ''));
			$customLabel = trim((string) ($item['label'] ?? ''));
			if ($sourceKey === '') {
				continue;
			}

			if (str_starts_with($sourceKey, 'help:')) {
				$slug = substr($sourceKey, 5);
				if ($slug === false || $slug === '') {
					continue;
				}

				$resolved[] = [
					'label' => $customLabel !== '' ? $customLabel : ($helpTitleBySlug[$slug] ?? ucfirst(str_replace('-', ' ', $slug))),
					'url' => $this->urlGenerator->generate('help', ['slug' => $slug]),
				];
				continue;
			}

			if ($sourceKey === 'route:contacts') {
				$resolved[] = [
					'label' => $customLabel !== '' ? $customLabel : 'Contact Us',
					'url' => $this->urlGenerator->generate('contacts'),
				];
			}
		}

		return $resolved;
	}
}
