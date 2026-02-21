<?php

namespace App\Repository;

use App\Entity\HelpFaq;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HelpFaq>
 *
 * @method HelpFaq|null find($id, $lockMode = null, $lockVersion = null)
 * @method HelpFaq|null findOneBy(array $criteria, array $orderBy = null)
 * @method HelpFaq[]    findAll()
 * @method HelpFaq[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HelpFaqRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, HelpFaq::class);
	}
}
