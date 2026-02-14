<?php

namespace App\Controller;

use App\Entity\PriceSection;
use App\Repository\PriceSectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PriceListController extends AbstractController
{
	public function __construct(
		private readonly PriceSectionRepository $priceSectionRepository,
	) {
	}

	#[Route('/price-list', name: 'price_list')]
	public function index(): Response
	{
		$priceSections = $this->buildPriceSectionsFromDatabase();
		return $this->render('price_list/index.html.twig', [
			'priceSections' => $priceSections,
		]);
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	private function buildPriceSectionsFromDatabase(): array
	{
		$sections = $this->priceSectionRepository->findBy([], ['position' => 'ASC']);
		$normalizedSections = [];

		foreach ($sections as $section) {
			$columnsByPosition = [];
			foreach ($section->getColumns() as $column) {
				$columnsByPosition[$column->getPosition()] = $column->getLabel();
			}
			ksort($columnsByPosition);

			$normalizedRows = [];
			foreach ($section->getRows() as $row) {
				$cellsByColumnPosition = [];
				foreach ($row->getCells() as $cell) {
					$column = $cell->getColumn();
					if ($column === null) {
						continue;
					}

					$cellsByColumnPosition[$column->getPosition()] = $this->normalizeCellValue($cell->getValue());
				}

				$normalizedRow = [$row->getTitle()];
				foreach ($columnsByPosition as $columnPosition => $unusedLabel) {
					if ($columnPosition === 0) {
						continue;
					}

					$normalizedRow[] = $cellsByColumnPosition[$columnPosition] ?? null;
				}

				$normalizedRows[] = $normalizedRow;
			}

			$normalizedSections[] = $this->normalizeSection($section, array_values($columnsByPosition), $normalizedRows);
		}

		return $normalizedSections;
	}

	/**
	 * @param array<int, string> $columns
	 * @param array<int, array<int, mixed>> $rows
	 * @return array<string, mixed>
	 */
	private function normalizeSection(PriceSection $section, array $columns, array $rows): array
	{
		return [
			'id' => $section->getSlug(),
			'title' => $section->getTitle(),
			'navLabel' => $section->getNavLabel(),
			'description' => $section->getDescription(),
			'note' => $section->getNote(),
			'columns' => $columns,
			'rows' => $rows,
		];
	}

	private function normalizeCellValue(?string $value): int|string|null
	{
		if ($value === null) {
			return null;
		}

		if (!is_numeric($value)) {
			return $value;
		}

		$trimmed = rtrim(rtrim($value, '0'), '.');
		if ($trimmed === '') {
			return 0;
		}

		return str_contains($trimmed, '.') ? $trimmed : (int) $trimmed;
	}
}