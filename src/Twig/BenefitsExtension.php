<?php
// src/Twig/TreatmentExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;

class BenefitsExtension extends AbstractExtension
{
	public function __construct(private Environment $twig)
	{
	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('render_benefits', [$this, 'renderBenefits'], ['is_safe' => ['html']]),
		];
	}

	public function renderBenefits(): string
	{
		$benefitsInfo = [];
		$benefitsInfo[] = [
			'title' => 'Qualified personnel',
			'description' => '10+ years of average experience of specialists who not only receive patients, but also conduct training.',
			'icon' => 'service.svg'
		];
		$benefitsInfo[] = [
			'title' => 'Service and quality',
			'description' => 'Natural coffee, sweets, relaxing background music and high-quality services await you in the studio.',
			'icon' => 'quality.svg'
		];
		$benefitsInfo[] = [
			'title' => 'Convenient location',
			'description' => 'Clinic is located in the city center, there is a large parking lot nearby.',
			'icon' => 'location.svg'
		];
		$benefitsInfo[] = [
			'title' => 'Up to date knowledge',
			'description' => 'Our specialists undergo new training at least once every six months to stay up to date with all modern techniques.',
			'icon' => 'lips.svg'
		];

		return $this->twig->render('components/benefits.html.twig', [
			'benefits' => $benefitsInfo,
		]);
	}
}
