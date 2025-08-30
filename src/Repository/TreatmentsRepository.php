<?php

namespace App\Repository;

use App\Entity\Treatments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Treatments>
 *
 * @method Treatments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Treatments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Treatments[]    findAll()
 * @method Treatments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TreatmentsRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Treatments::class);
	}

	/**
	 * Return data for treatments cards by given category_id
	 * @param int $categoryId
	 * @return array
	 */
	public function getTreatmentsDataForCards(int $categoryId): array
	{
		$qb = $this->createQueryBuilder('t')
			->select('t.id, t.name, t.imageName, tsi.title, tsi.description')
			->join('App\Entity\TreatmentsShortInfo', 'tsi', 'WITH', 'tsi.treatment = t.id');

		if ($categoryId !== 0) {
			$qb->where('t.category = :categoryId')
				->setParameter('categoryId', $categoryId);
		}

		return $qb->getQuery()->getResult();
	}

	/**
	 * Return data for treatment page
	 * @param int $treatment_id
	 */
	public function getFullTreatmentData(int $treatment_id): ?array
	{
		$treatmentData = $this->createQueryBuilder('t')
			->select('t.id, t.name, t.imageName, t.discomfortLevel, t.fullDescription')
			->leftJoin('App\Entity\Categories', 'c', 'WITH', 'c.id = t.category')
			->addSelect('c.name as categoryName')
			// join с ценами через поле treatment_id в таблице TreatmentPrice
			->leftJoin('App\Entity\TreatmentPrice', 'tp', 'WITH', 'tp.id = t.id')
			->addSelect('tp.isFixed, tp.price, tp.priceType')
			// join с временем через поле treatment_id в таблице TreatmentTime
			->leftJoin('App\Entity\TreatmentTime', 'tt', 'WITH', 'tt.id = t.id')
			->addSelect('tt.hours, tt.minutes')
			// join с восстановлением, если есть
			->leftJoin('App\Entity\TreatmentRecover', 'tr', 'WITH', 'tr.id = t.id')
			->addSelect('tr.min as recover_from, tr.max as recover_to, tr.period')
			->where('t.id = :id')
			->setParameter('id', $treatment_id)
			->getQuery()
			->getResult();

		return $treatmentData[0] ?? null;
	}


	//    /**
//     * @return Treatments[] Returns an array of Treatments objects
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

	//    public function findOneBySomeField($value): ?Treatments
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
