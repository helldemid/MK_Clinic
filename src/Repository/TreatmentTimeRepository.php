<?php

namespace App\Repository;

use App\Entity\TreatmentTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TreatmentTime>
 *
 * @method TreatmentTime|null find($id, $lockMode = null, $lockVersion = null)
 * @method TreatmentTime|null findOneBy(array $criteria, array $orderBy = null)
 * @method TreatmentTime[]    findAll()
 * @method TreatmentTime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TreatmentTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TreatmentTime::class);
    }

//    /**
//     * @return TreatmentTime[] Returns an array of TreatmentTime objects
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

//    public function findOneBySomeField($value): ?TreatmentTime
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
