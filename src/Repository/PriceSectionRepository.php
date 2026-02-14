<?php

namespace App\Repository;

use App\Entity\PriceSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PriceSection>
 *
 * @method PriceSection|null find($id, $lockMode = null, $lockVersion = null)
 * @method PriceSection|null findOneBy(array $criteria, array $orderBy = null)
 * @method PriceSection[]    findAll()
 * @method PriceSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PriceSectionRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, PriceSection::class);
	}

	public function findOneBySlug(string $slug): ?PriceSection
	{
		return $this->findOneBy(['slug' => $slug]);
	}
}
