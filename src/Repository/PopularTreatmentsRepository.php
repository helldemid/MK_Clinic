<?php

namespace App\Repository;

use App\Entity\PopularTreatments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PopularTreatments>
 *
 * @method PopularTreatments|null find($id, $lockMode = null, $lockVersion = null)
 * @method PopularTreatments|null findOneBy(array $criteria, array $orderBy = null)
 * @method PopularTreatments[]    findAll()
 * @method PopularTreatments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PopularTreatmentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PopularTreatments::class);
    }

    /**
     * Return data to create popular treatments layout
     * @return array
     */
    public function getPopularTreatmentsData(): array {
        $treatments = $this->createQueryBuilder('pt')
            ->select('t.id, t.name, t.imageName, tsi.title, tsi.description')
            ->join('pt.treatment', 't')
            ->join('App\Entity\TreatmentsShortInfo', 'tsi', 'WITH', 'tsi.treatment = t.id')
            ->getQuery()
            ->getResult();

        return $treatments;
    }

//    /**
//     * @return PopularTreatments[] Returns an array of PopularTreatments objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PopularTreatments
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
