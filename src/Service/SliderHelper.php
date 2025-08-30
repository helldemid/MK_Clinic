<?php

// src/Service/SliderHelper.php
namespace App\Service;

use App\Repository\PopularTreatmentsRepository;

class SliderHelper
{
	private PopularTreatmentsRepository $treatmentRepo;

	public function __construct(PopularTreatmentsRepository $treatmentRepository) {
		$this->treatmentRepo = $treatmentRepository;
	}

	public function getPopularSlidesData(): array {
		return $this->treatmentRepo->getPopularTreatmentsData();
	}
}
