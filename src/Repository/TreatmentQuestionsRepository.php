<?php

namespace App\Repository;

use App\Entity\TreatmentQuestions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TreatmentQuestions>
 *
 * @method TreatmentQuestions|null find($id, $lockMode = null, $lockVersion = null)
 * @method TreatmentQuestions|null findOneBy(array $criteria, array $orderBy = null)
 * @method TreatmentQuestions[]    findAll()
 * @method TreatmentQuestions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TreatmentQuestionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TreatmentQuestions::class);
    }

    public function getAdditionalInformation(int $itemId) {
		$qb = $this->createQueryBuilder('tq')
            ->select('tq.question, tq.answer')
            ->where('tq.treatment = :itemId')
            ->setParameter('itemId', $itemId);

        return $qb->getQuery()->getResult();
	}

//    /**
//     * @return TreatmentQuestions[] Returns an array of TreatmentQuestions objects
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

//    public function findOneBySomeField($value): ?TreatmentQuestions
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
