<?php

namespace App\Repository;

use App\Entity\TreatmentRecover;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TreatmentRecover>
 *
 * @method TreatmentRecover|null find($id, $lockMode = null, $lockVersion = null)
 * @method TreatmentRecover|null findOneBy(array $criteria, array $orderBy = null)
 * @method TreatmentRecover[]    findAll()
 * @method TreatmentRecover[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TreatmentRecoverRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TreatmentRecover::class);
    }

//    /**
//     * @return TreatmentRecover[] Returns an array of TreatmentRecover objects
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

//    public function findOneBySomeField($value): ?TreatmentRecover
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
