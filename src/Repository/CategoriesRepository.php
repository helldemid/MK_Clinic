<?php

namespace App\Repository;

use App\Entity\Categories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categories>
 *
 * @method Categories|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categories|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categories[]    findAll()
 * @method Categories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoriesRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Categories::class);
	}

	public function getCategoriesSortedByNameLength(): ?array
	{
		return $this->createQueryBuilder('c')
			->orderBy('LENGTH(c.name)', 'DESC')
			->getQuery()
			->getArrayResult();
	}

	public function getActiveCategories(): array
	{
		return $this->createQueryBuilder('c')
			->select('c')
			->where('c.is_shown = :status')
			->setParameter('status', true)
			->getQuery()
			->getResult();
	}

	/**
	 * Проверяет, активна ли категория по ее ID.
	 */
	public function isActiveCategory(int $id): bool
	{
		try {
			$state = $this->createQueryBuilder('c')
				->select('c.is_shown')
				->where('c.id = :id')
				->setParameter('id', $id)
				->getQuery()
				->getSingleScalarResult();

			return (bool) $state;

		} catch (\Doctrine\ORM\NoResultException $e) {
			return false;
		}
	}

}
