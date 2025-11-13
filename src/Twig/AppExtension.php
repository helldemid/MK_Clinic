<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Repository\CategoriesRepository;
use App\Repository\TreatmentsRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
	private $catRepo;
	private $tRepo;

	public function __construct(CategoriesRepository $catRepo, TreatmentsRepository $tRepo)
	{
		$this->catRepo = $catRepo;
		$this->tRepo = $tRepo;
	}

	public function getGlobals(): array
	{
		$categories = $this->catRepo->getCategoriesSortedByNameLength();
		$dataForMenu = [];
		$categoriesToRespond = [];
		foreach($categories as $category) {
			$treatments = $this->tRepo->getTreatmentsDataForMenu($category['id']);
			if($treatments && !empty($treatments)) {
				$dataForMenu[$category['name']] = $treatments;
				$categoriesToRespond[] = $category;
			}
		}
		return [
			'treatmentsCategories' => $categoriesToRespond,
			'dataForMenu' => $dataForMenu
		];
	}
}
