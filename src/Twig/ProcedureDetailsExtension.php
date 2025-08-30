<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;

class ProcedureDetailsExtension extends AbstractExtension
{
	public function __construct(private Environment $twig)
	{
	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('render_procedure_details', [$this, 'renderProcedureDetails'], ['is_safe' => ['html']]),
		];
	}

	/**
	 * Converts int variable to time tex that can be displayed
	 *
	 * @param integer $time
	 * @param string $type
	 * @return string
	 */
	private function generateTimeText(int $time, string $type): string {
		return $time > 0 ? $time.' '.($time > 1 ? $type.'s' : $type) : '';
	}

	public function renderProcedureDetails(array $treatmentData): string
	{

		$procedureTimeTextHour = $this->generateTimeText((int) $treatmentData['hours'] ?? 0, 'hour');
		$procedureTimeTextMinute = $this->generateTimeText((int) $treatmentData['minutes'] ?? 0, 'minute');

		$price = (int) $treatmentData['price'] ?? 0;
		$priceType = $treatmentData['priceType'] ?? '';
		$isFixedPrice = (bool) ($treatmentData['isFixed'] ?? true);

		$recover_from = (int) $treatmentData['recover_from'] ?? 0;
		$recover_to = (int) $treatmentData['recover_to'] ?? 0;
		$recoverDesc = '';
		if (0 === $recover_from && 0 === $recover_to) {
			$recoverDesc = $treatmentData['period'] ?? 'Minimal';
		} else if (0 === $recover_from) {
			$recoverDesc = '~' . $recover_to . ' ' . $treatmentData['period'] ?? 'Minimal';
		} else if (0 === $recover_to) {
			$recoverDesc = 'From ' . $recover_from . ' ' . $treatmentData['period'] ?? 'days';
		} else {
			$recoverDesc = 'From ' . $recover_from . ' to ' . $recover_to . ' ' . $treatmentData['period'] ?? 'days';
		}

		$discomfortLevel = $treatmentData['discomfortLevel'] ?? 0;
		$points = [];

		$points[] = [
			'name' => 'Price',
			'description' => $isFixedPrice ? "£$price" : "From £$price per $priceType",
			'icon' => 'price.svg'
		];
		$points[] = [
			'name' => 'Time of procedure',
			'description' => empty($procedureTimeTextHour) ? $procedureTimeTextMinute : "$procedureTimeTextHour $procedureTimeTextMinute",
			'icon' => 'time.svg'
		];
		$points[] = [
			'name' => 'Discomfort level',
			'description' => "$discomfortLevel out of 5",
			'icon' => 'discomfort.svg'
		];
		$points[] = [
			'name' => 'Recover',
			'description' => $recoverDesc,
			'icon' => 'recover.svg'
		];

		return $this->twig->render('components/procedureDetails.html.twig', [
			'points' => $points,
		]);
	}
}
