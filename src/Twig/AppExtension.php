<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Repository\CategoriesRepository;
use App\Repository\SiteContentSettingsRepository;
use App\Repository\TreatmentsRepository;
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

	private $catRepo;
	private $tRepo;
	private $pabauService;
	private SiteContentSettingsRepository $siteContentSettingsRepository;

	public function __construct(
		CategoriesRepository $catRepo,
		TreatmentsRepository $tRepo,
		PabauService $pabauService,
		SiteContentSettingsRepository $siteContentSettingsRepository,
		private Environment $twig
	)
	{
		$this->catRepo = $catRepo;
		$this->tRepo = $tRepo;
		$this->pabauService = $pabauService;
		$this->siteContentSettingsRepository = $siteContentSettingsRepository;
	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('get_pabau_treatment_url', [$this, 'getPabauTreatmentUrl'], ['is_safe' => ['html']]),
		];
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

		$settings = $this->siteContentSettingsRepository->findSingleton();
		$promoItems = $settings?->getPromoItems() ?? [];
		if ($promoItems === []) {
			$promoItems = self::DEFAULT_PROMO_ITEMS;
		}
		$heroDesktopImage = $settings?->getHeroDesktopImage();
		$heroMobileImage = $settings?->getHeroMobileImage();

		return [
			'treatmentsCategories' => $categoriesToRespond,
			'dataForMenu' => $dataForMenu,
			'pabauBaseUrl' => 'https://partner.pabau.com/online-bookings/mkaestheticclinic',
			'consultationCategoryMasterId' => $consultationCategoryMasterId,
			'sitePromoItems' => $promoItems,
			'siteHeroDesktopImage' => $heroDesktopImage !== null && $heroDesktopImage !== '' ? 'media/site/'.$heroDesktopImage : self::DEFAULT_HERO_DESKTOP,
			'siteHeroMobileImage' => $heroMobileImage !== null && $heroMobileImage !== '' ? 'media/site/'.$heroMobileImage : self::DEFAULT_HERO_MOBILE,
		];
	}
}
