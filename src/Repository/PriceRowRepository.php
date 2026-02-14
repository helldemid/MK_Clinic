<?php

namespace App\Repository;

use App\Entity\PriceRow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PriceRow>
 *
 * @method PriceRow|null find($id, $lockMode = null, $lockVersion = null)
 * @method PriceRow|null findOneBy(array $criteria, array $orderBy = null)
 * @method PriceRow[]    findAll()
 * @method PriceRow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PriceRowRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, PriceRow::class);
	}
}
