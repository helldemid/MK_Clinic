<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Repository\CategoriesRepository;
use App\Repository\TreatmentsRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

use App\Service\PabauService;

use Twig\TwigFunction;
use Twig\Environment;
class AppExtension extends AbstractExtension implements GlobalsInterface
{
	private $catRepo;
	private $tRepo;
	private $pabauService;

	public function __construct(CategoriesRepository $catRepo, TreatmentsRepository $tRepo, PabauService $pabauService, private Environment $twig)
	{
		$this->catRepo = $catRepo;
		$this->tRepo = $tRepo;
		$this->pabauService = $pabauService;
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

		return [
			'treatmentsCategories' => $categoriesToRespond,
			'dataForMenu' => $dataForMenu,
			'pabauBaseUrl' => 'https://partner.pabau.com/online-bookings/mkaestheticclinic',
			'consultationCategoryMasterId' => $consultationCategoryMasterId
		];
	}
}
