<?php
// src/Twig/TreatmentExtension.php
namespace App\Twig;

use App\Service\SliderHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;

class SliderExtension extends AbstractExtension
{
	public function __construct(private SliderHelper $sliderHelper, private Environment $twig)
	{
	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('render_slider', [$this, 'renderSlider'], ['is_safe' => ['html']]),
		];
	}

	public function renderSlider(): string
	{
		$slides = [];

		$slides = $this->sliderHelper->getPopularSlidesData();

		return $this->twig->render('components/slider.html.twig', [
			'slides' => $slides,
		]);
	}
}
