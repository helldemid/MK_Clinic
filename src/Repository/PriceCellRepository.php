<?php

namespace App\Repository;

use App\Entity\PriceCell;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PriceCell>
 *
 * @method PriceCell|null find($id, $lockMode = null, $lockVersion = null)
 * @method PriceCell|null findOneBy(array $criteria, array $orderBy = null)
 * @method PriceCell[]    findAll()
 * @method PriceCell[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PriceCellRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, PriceCell::class);
	}
}
