<?php

namespace App\Repository;

use App\Entity\HelpSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HelpSection>
 *
 * @method HelpSection|null find($id, $lockMode = null, $lockVersion = null)
 * @method HelpSection|null findOneBy(array $criteria, array $orderBy = null)
 * @method HelpSection[]    findAll()
 * @method HelpSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HelpSectionRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, HelpSection::class);
	}

	/**
	 * Получить все разделы, отсортированные для меню.
	 * Обычно используется для левой боковой панели.
	 *
	 * @return HelpSection[]
	 */
	public function findAllOrdered(): array
	{
		return $this->createQueryBuilder('h')
			->select('h.title, h.slug, h.position')
			->orderBy('h.position', 'ASC')
			->getQuery()
			->getResult();
	}

	/**
	 * Найти раздел по slug
	 */
	public function findBySlug(string $slug): ?HelpSection
	{
		return $this->findOneBy(['slug' => $slug]);
	}

	/**
	 * Получить первый раздел (для загрузки по умолчанию)
	 */
	public function findFirst(): ?HelpSection
	{
		return $this->createQueryBuilder('h')
			->orderBy('h.position', 'ASC')
			->setMaxResults(1)
			->getQuery()
			->getOneOrNullResult();
	}
}
