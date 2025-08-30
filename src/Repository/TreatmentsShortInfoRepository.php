<?php

namespace App\Repository;

use App\Entity\TreatmentsShortInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TreatmentsShortInfo>
 *
 * @method TreatmentsShortInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method TreatmentsShortInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method TreatmentsShortInfo[]    findAll()
 * @method TreatmentsShortInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TreatmentsShortInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TreatmentsShortInfo::class);
    }

//    /**
//     * @return TreatmentsShortInfo[] Returns an array of TreatmentsShortInfo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TreatmentsShortInfo
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
