<?php

namespace App\Repository;

use App\Entity\SiteContentSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SiteContentSettings>
 */
class SiteContentSettingsRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, SiteContentSettings::class);
	}

	public function findSingleton(): ?SiteContentSettings
	{
		return $this->findOneBy([]);
	}
}
