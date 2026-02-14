<?php

namespace App\Command;

use App\Entity\PriceCell;
use App\Entity\PriceColumn;
use App\Entity\PriceRow;
use App\Entity\PriceSection;
use App\Repository\PriceSectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsCommand(
	name: 'app:import-price-list',
	description: 'Imports price list sections, columns, rows and cells from the PRICE_SECTIONS constant.',
)]
class ImportPriceListCommand extends Command
{
	private const PRICE_SECTIONS = [
		[
			'id' => 'lasemd-ultra',
			'title' => 'LaseMD Ultra Price Guide',
			'navLabel' => 'LaseMD Ultra',
			'description' => 'Fractional laser resurfacing options tailored to your goals.',
			'note' => 'Course of 3: 10% off | Course of 5: 20% off',
			'columns' => ['Treatment indication', 'Single session', 'Course of 3', 'Course of 5'],
			'rows' => [
				['Full face', 450, 1215, 1800],
				['Full face and neck', 550, 1485, 2200],
				['Baby face (VA/VC)', 500, 1350, 2000],
				['Time correction (VA)', 500, 1350, 2000],
				['Mela bright (TA)', 550, 1485, 2200],
				['Keralase (Kerafactor serum £114 per ampoule)', 550, 1485, 2200],
				['Ultra kiss', 300, 810, 1200],
				['Ultra eyes', 300, 810, 1200],
				['Bruising', 75, null, null],
				['LaseMD Combo (tailored to specific needs)', 650, 1755, 2600],
			],
		],
		[
			'id' => 'clarity-hair-removal',
			'title' => 'Clarity II - Hair Removal',
			'navLabel' => 'Hair Removal',
			'description' => 'Laser hair removal packages for smooth results.',
			'note' => 'Course of 3: 10% off | Course of 6: 20% off',
			'columns' => ['Treatment', 'Single session', 'Course of 3', 'Course of 6'],
			'rows' => [
				['Upper lip', 40, 108, 192],
				['Chin', 40, 108, 192],
				['Lip and Chin', 60, 162, 288],
				['Lower face', 50, 135, 240],
				['Full face', 70, 189, 336],
				['Back of neck', 55, 149, 264],
				['Half back or chest', 150, 405, 720],
				['Full back', 100, 270, 480],
				['Full chest and stomach', 200, 540, 960],
				['Bikini line', 90, 243, 432],
				['Hollywood', 140, 410, 672],
				['Underarms', 55, 149, 264],
				['Half legs', 125, 338, 600],
				['Full legs', 170, 459, 816],
				['Toes or fingers', 30, 81, 144],
				['Half arm', 60, 162, 288],
				['Full arms', 80, 216, 384],
				['Full body', 270, 729, 1296],
			],
		],
		[
			'id' => 'clarity-vascular',
			'title' => 'Clarity II - Vascular',
			'navLabel' => 'Vascular',
			'description' => 'Targets visible vessels and redness.',
			'note' => 'Course of 3: 10% off | Course of 6: 20% off',
			'columns' => ['Treatment', 'Single session', 'Course of 3', 'Course of 6'],
			'rows' => [
				['Large area (45-60 mins)', 450, 1215, 2160],
				['Small area (15-30 mins)', 200, 540, 960],
				['Facial flushing', 250, 675, 1200],
				['Angiomas', 80, 216, 384],
				['Venous lakes', 120, 324, 576],
			],
		],
		[
			'id' => 'clarity-skin-rejuvenation',
			'title' => 'Clarity II - Skin Rejuvenation',
			'navLabel' => 'Skin Rejuvenation',
			'description' => 'Enhance skin tone, texture, and appearance.',
			'note' => 'Course of 3: 10% off | Course of 6: 20% off',
			'columns' => ['Treatment', 'Single session', 'Course of 3', 'Course of 6'],
			'rows' => [
				['Clear', 250, 675, 1200],
				['Smooth', 250, 675, 1200],
				['Clear and Smooth combo', 350, 945, 1680],
				['Advanced skin rejuvenation', 350, 945, 1680],
				['Acne treatment', 200, 540, 960],
				['Blackhead treatment', 180, 486, 864],
			],
		],
		[
			'id' => 'clarity-pigmentation',
			'title' => 'Clarity II - Pigmentation',
			'navLabel' => 'Pigmentation',
			'description' => 'Treat uneven tone and pigmentation.',
			'note' => 'Course of 3: 10% off | Course of 6: 20% off',
			'columns' => ['Treatment', 'Single session', 'Course of 3', 'Course of 6'],
			'rows' => [
				['Freckle toning', 250, 675, 1200],
				['Spot treating', 80, 216, 384],
				['Seborrheic keratosis', 120, 324, 576],
			],
		],
		[
			'id' => 'anti-wrinkle',
			'title' => 'Anti-Wrinkle Injections',
			'navLabel' => 'Anti-Wrinkle',
			'description' => 'Targeted injections for smoother expression lines.',
			'note' => '',
			'columns' => ['Treatment', '1 area', '2 area', '3 area', 'Full face'],
			'rows' => [
				['Anti-Wrinkle', 170, 180, 190, 500],
				['Downturned smile', 170, null, null, null],
				['Bunny lines', 170, null, null, null],
				['Jaw slimming or teeth grinding', 200, null, null, null],
				['Nefertiti neck lift', 260, null, null, null],
				['Pebble chin', 170, null, null, null],
				['Excessive sweating', 350, null, null, null],
				["Men's Anti-Wrinkle", 180, 200, 230, null],
			],
		],
		[
			'id' => 'akradex',
			'title' => 'Akradex Treatments',
			'navLabel' => 'Akradex Treatments',
			'description' => 'Skin renewal and booster protocols.',
			'note' => '',
			'columns' => ['Treatment', '1 session', '3 sessions', '4 sessions'],
			'rows' => [
				['Mesotherapy', 150, 405, 510],
				['Skin boosters', 170, 459, 578],
				['Polynucleotides', 190, 500, null],
			],
		],
		[
			'id' => 'plla-collagen',
			'title' => 'PLLA Collagen Stimulator / Bioremodulator',
			'navLabel' => 'PLLA Collagen',
			'description' => 'Stimulate collagen for long-term support.',
			'note' => '',
			'columns' => ['Treatment', '1 vial', '2 vials', '4 vials'],
			'rows' => [
				['PLLA Collagen stimulator / Bioremodulator', 350, 600, 1100],
			],
		],
		[
			'id' => 'peels',
			'title' => 'Peels',
			'navLabel' => 'Peels',
			'description' => 'Professional exfoliation for refreshed skin.',
			'note' => '',
			'columns' => ['Treatment', '1 session', '3 sessions', '5 sessions'],
			'rows' => [
				['BioRepeel Standard', 125, 350, 600],
			],
		],
		[
			'id' => 'hydrodiamond',
			'title' => 'HydroDiamond Facial Treatment',
			'navLabel' => 'HydroDiamond',
			'description' => 'Hydradermabrasion and glow-focused facials.',
			'note' => '',
			'columns' => ['Treatment', '1 session', '6 sessions', '12 sessions'],
			'rows' => [
				['Deep Cleansing and Skin Reset', 95, 480, 900],
				['Acne and Post-Acne Repair', 120, 600, 1100],
				['Anti-Age Lift and Firm', 140, 820, 1300],
				['Ultimate Hydration and Glow (Premium)', 150, 760, 1440],
			],
		],
		[
			'id' => 'lpg-infinity',
			'title' => 'LPG Infinity Non-Invasive Mechanical Massage',
			'navLabel' => 'LPG Infinity',
			'description' => 'Mechanical massage for body contour and tone.',
			'note' => '',
			'columns' => ['Treatment', '1 session', '12 sessions'],
			'rows' => [
				['Body and pouch', 120, 1200],
				['Face', 80, 800],
				['Body and Face plus pouch', 160, 1600],
			],
		],
		[
			'id' => 'idenel-microneedling',
			'title' => 'Idenel Microneedling Treatment',
			'navLabel' => 'Idenel Microneedling',
			'description' => 'Microneedling to improve texture and firmness.',
			'note' => '',
			'columns' => ['Treatment', '1 session', '5 sessions'],
			'rows' => [
				["The 'Liquid microneedling' treatment", 200, 800],
			],
		],
		[
			'id' => 'sunbed-red-light',
			'title' => 'Sunbed and Red Light Therapy',
			'navLabel' => 'Sunbed and Red Light',
			'description' => 'Sunbed and red light session bundles.',
			'note' => '',
			'columns' => ['Treatment', '1 minute', '5 sessions', '10 sessions'],
			'rows' => [
				['Sunbed and Red Light Therapy', 1, 5, 10],
			],
		],
	];
	public function __construct(
		private readonly EntityManagerInterface $entityManager,
		private readonly PriceSectionRepository $sectionRepository,
		private readonly SluggerInterface $slugger,
	) {
		parent::__construct();
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);
		$sections = self::PRICE_SECTIONS;

		$stats = [
			'sections_created' => 0,
			'sections_updated' => 0,
			'columns_created' => 0,
			'columns_updated' => 0,
			'columns_deleted' => 0,
			'rows_created' => 0,
			'rows_updated' => 0,
			'rows_deleted' => 0,
			'cells_created' => 0,
			'cells_updated' => 0,
			'cells_deleted' => 0,
		];

		$io->title('Price List Import');
		$io->writeln(sprintf('Loaded %d sections from source constant.', count($sections)));

		try {
			foreach ($sections as $sectionPosition => $sectionData) {
				$this->importSection($sectionData, $sectionPosition, $stats);
			}

			$this->entityManager->flush();
		} catch (\Throwable $exception) {
			$io->error(sprintf('Import failed: %s', $exception->getMessage()));

			return Command::FAILURE;
		}

		$io->table(
			['Metric', 'Count'],
			[
				['Sections created', (string) $stats['sections_created']],
				['Sections updated', (string) $stats['sections_updated']],
				['Columns created', (string) $stats['columns_created']],
				['Columns updated', (string) $stats['columns_updated']],
				['Columns deleted', (string) $stats['columns_deleted']],
				['Rows created', (string) $stats['rows_created']],
				['Rows updated', (string) $stats['rows_updated']],
				['Rows deleted', (string) $stats['rows_deleted']],
				['Cells created', (string) $stats['cells_created']],
				['Cells updated', (string) $stats['cells_updated']],
				['Cells deleted', (string) $stats['cells_deleted']],
			]
		);

		$io->success('Price list import completed.');

		return Command::SUCCESS;
	}

	/**
	 * @param array<string, mixed> $sectionData
	 * @param array<string, int> $stats
	 */
	private function importSection(array $sectionData, int $sectionPosition, array &$stats): void
	{
		$title = (string) ($sectionData['title'] ?? '');
		$slug = $this->slugger->slug($title)->lower()->toString();

		$section = $this->sectionRepository->findOneBySlug($slug);
		if ($section === null) {
			$section = new PriceSection();
			$stats['sections_created']++;
		} else {
			$stats['sections_updated']++;
		}

		$section
			->setTitle($title)
			->setNavLabel((string) ($sectionData['navLabel'] ?? ''))
			->setSlug($slug)
			->setDescription($this->normalizeNullableText($sectionData['description'] ?? null))
			->setNote($this->normalizeNullableText($sectionData['note'] ?? null))
			->setPosition($sectionPosition);

		$this->entityManager->persist($section);

		$activeColumns = $this->syncColumns($section, $sectionData, $stats);
		$this->syncRows($section, $sectionData, $activeColumns, $stats);
	}

	/**
	 * @param array<string, mixed> $sectionData
	 * @param array<string, int> $stats
	 * @return array<int, PriceColumn>
	 */
	private function syncColumns(PriceSection $section, array $sectionData, array &$stats): array
	{
		$existingColumnsByPosition = [];
		foreach ($section->getColumns() as $column) {
			$existingColumnsByPosition[$column->getPosition()] = $column;
		}

		$activeColumnsByPosition = [];
		$columnLabels = (array) ($sectionData['columns'] ?? []);

		foreach ($columnLabels as $position => $label) {
			$column = $existingColumnsByPosition[$position] ?? null;
			if ($column === null) {
				$column = (new PriceColumn())->setSection($section);
				$this->entityManager->persist($column);
				$stats['columns_created']++;
			} else {
				$stats['columns_updated']++;
			}

			$column
				->setLabel((string) $label)
				->setPosition($position);

			$activeColumnsByPosition[$position] = $column;
		}

		foreach ($existingColumnsByPosition as $position => $column) {
			if (!isset($activeColumnsByPosition[$position])) {
				$this->entityManager->remove($column);
				$stats['columns_deleted']++;
			}
		}

		return $activeColumnsByPosition;
	}

	/**
	 * @param array<string, mixed> $sectionData
	 * @param array<int, PriceColumn> $activeColumnsByPosition
	 * @param array<string, int> $stats
	 */
	private function syncRows(PriceSection $section, array $sectionData, array $activeColumnsByPosition, array &$stats): void
	{
		$existingRowsByPosition = [];
		foreach ($section->getRows() as $row) {
			$existingRowsByPosition[$row->getPosition()] = $row;
		}

		$activeRowsByPosition = [];
		$rowDataList = (array) ($sectionData['rows'] ?? []);

		foreach ($rowDataList as $position => $rowData) {
			$rowData = (array) $rowData;
			$row = $existingRowsByPosition[$position] ?? null;

			if ($row === null) {
				$row = (new PriceRow())->setSection($section);
				$this->entityManager->persist($row);
				$stats['rows_created']++;
			} else {
				$stats['rows_updated']++;
			}

			$row
				->setTitle((string) ($rowData[0] ?? ''))
				->setPosition($position);

			$this->syncCells($row, $rowData, $activeColumnsByPosition, $stats);
			$activeRowsByPosition[$position] = $row;
		}

		foreach ($existingRowsByPosition as $position => $row) {
			if (!isset($activeRowsByPosition[$position])) {
				$this->entityManager->remove($row);
				$stats['rows_deleted']++;
			}
		}
	}

	/**
	 * @param array<int, mixed> $rowData
	 * @param array<int, PriceColumn> $activeColumnsByPosition
	 * @param array<string, int> $stats
	 */
	private function syncCells(PriceRow $row, array $rowData, array $activeColumnsByPosition, array &$stats): void
	{
		$existingCellsByColumnPosition = [];
		foreach ($row->getCells() as $cell) {
			$column = $cell->getColumn();
			if ($column !== null) {
				$existingCellsByColumnPosition[$column->getPosition()] = $cell;
			}
		}

		$activeCellsByColumnPosition = [];
		ksort($activeColumnsByPosition);

		foreach ($activeColumnsByPosition as $columnPosition => $column) {
			if ($columnPosition === 0) {
				continue;
			}

			$cell = $existingCellsByColumnPosition[$columnPosition] ?? null;
			if ($cell === null) {
				$cell = (new PriceCell())->setRow($row);
				$this->entityManager->persist($cell);
				$stats['cells_created']++;
			} else {
				$stats['cells_updated']++;
			}

			$rawValue = $rowData[$columnPosition] ?? null;
			$cell
				->setColumn($column)
				->setValue($rawValue === null ? null : (string) $rawValue);

			$activeCellsByColumnPosition[$columnPosition] = $cell;
		}

		foreach ($existingCellsByColumnPosition as $columnPosition => $cell) {
			if (!isset($activeCellsByColumnPosition[$columnPosition])) {
				$this->entityManager->remove($cell);
				$stats['cells_deleted']++;
			}
		}
	}

	private function normalizeNullableText(mixed $value): ?string
	{
		if ($value === null) {
			return null;
		}

		$normalized = trim((string) $value);

		return $normalized === '' ? null : $normalized;
	}
}
