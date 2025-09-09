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
	 * @param int $activity 1 - active only, 0 - inactive only, -1 - all
	 * @return array
	 */
	public function getTreatmentsDataForCards(int $categoryId, int $activity): array
	{
		$qb = $this->createQueryBuilder('t')
			->select('t.id, t.name, t.imageName, t.isActive, tsi.title, tsi.description, (CASE WHEN pt.id IS NOT NULL THEN 1 ELSE 0 END) AS isPopular')
			->join('App\Entity\TreatmentsShortInfo', 'tsi', 'WITH', 'tsi.treatment = t.id')
			->leftJoin('App\Entity\PopularTreatments', 'pt', 'WITH', 'pt.treatment = t.id');

		if ($categoryId !== 0) {
			$qb->where('t.category = :categoryId')
				->setParameter('categoryId', $categoryId);
		}
		if ($activity !== -1) {
			$qb->andWhere('t.isActive = :activity')
				->setParameter('activity', $activity === 1);
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
			->leftJoin('App\Entity\TreatmentPrice', 'tp', 'WITH', 'tp.id = t.price')
			->addSelect('tp.isFixed, tp.price, tp.priceType')
			// join с временем через поле treatment_id в таблице TreatmentTime
			->leftJoin('App\Entity\TreatmentTime', 'tt', 'WITH', 'tt.id = t.time')
			->addSelect('tt.hours, tt.minutes')
			// join с восстановлением, если есть
			->leftJoin('App\Entity\TreatmentRecover', 'tr', 'WITH', 'tr.id = t.recover')
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
