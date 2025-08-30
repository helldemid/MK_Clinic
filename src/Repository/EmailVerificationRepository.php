<?php

namespace App\Repository;

use App\Entity\EmailVerificationRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailVerificationRequest>
 */
class EmailVerificationRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, EmailVerificationRequest::class);
	}

	public function remove(string $email, int $limit = 1)
	{
		$entityManager = $this->getEntityManager();
		$qb = $this->createQueryBuilder('e')
			->where('e.email = :email')
			->setParameter('email', $email)
			->setMaxResults($limit);

		$results = $qb->getQuery()->getResult();

		foreach ($results as $existingRequest) {
			$entityManager->remove($existingRequest);
		}

		if (count($results) > 0) {
			$entityManager->flush();
		}
	}
}