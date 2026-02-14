<?php

namespace App\Repository;

use App\Entity\PriceColumn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PriceColumn>
 *
 * @method PriceColumn|null find($id, $lockMode = null, $lockVersion = null)
 * @method PriceColumn|null findOneBy(array $criteria, array $orderBy = null)
 * @method PriceColumn[]    findAll()
 * @method PriceColumn[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PriceColumnRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, PriceColumn::class);
	}
}
