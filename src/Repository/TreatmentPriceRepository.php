<?php

namespace App\Repository;

use App\Entity\TreatmentPrice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TreatmentPrice>
 *
 * @method TreatmentPrice|null find($id, $lockMode = null, $lockVersion = null)
 * @method TreatmentPrice|null findOneBy(array $criteria, array $orderBy = null)
 * @method TreatmentPrice[]    findAll()
 * @method TreatmentPrice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TreatmentPriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TreatmentPrice::class);
    }

//    /**
//     * @return TreatmentPrice[] Returns an array of TreatmentPrice objects
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

//    public function findOneBySomeField($value): ?TreatmentPrice
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
