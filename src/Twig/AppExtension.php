<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Repository\CategoriesRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
	private $catRepo;

	public function __construct(CategoriesRepository $catRepo)
	{
		$this->catRepo = $catRepo;
	}

	public function getGlobals(): array
	{
		return [
			'treatmentsCategories' => $this->catRepo->findAll()
		];
	}
}
