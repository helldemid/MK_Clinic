<?php

namespace App\Service;

use App\Repository\EmailVerificationRepository;
use App\Entity\EmailVerificationRequest;
use App\Entity\User;

class EmailVerificationService
{
	const VERIFICATION_MAX_TIME = 3 * 3600;
	private EmailVerificationRepository $repository;

	public function __construct(EmailVerificationRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * Remove existing verification requests for the given email
	 *
	 * @param string $email
	 */
	public function remove(string $email): void
	{
		$this->repository->remove($email);
	}

	/**
	 * Create a new verification request and returns its code
	 *
	 * @param string $email
	 * @param integer $user_id
	 * @param integer $action
	 * @return string 32 chars code
	 */
	public function create(string $email, int $user_id, int $action) {
		$this->remove($email);

		$code = md5(uniqid($email.rand(), true));

		$entityManager = $this->repository->getEntityManager();
		$verificationRequest = new EmailVerificationRequest();
		$verificationRequest->setEmail($email);
		$verificationRequest->setUser($entityManager->getRepository(User::class)->find($user_id));
		$verificationRequest->setAction($action);
		$verificationRequest->setCode($code);
		$verificationRequest->setUpdatedAt(new \DateTimeImmutable());

		$entityManager->persist($verificationRequest);
		$entityManager->flush();

		return $code;
	}

	/**
	 * Confirm the email verification code
	 *
	 * @param string $code
	 * @return array
	 */
	public function confirm(string $code): array {
		$now = new \DateTimeImmutable();
		$maxTimeAgo = $now->modify('-' . self::VERIFICATION_MAX_TIME . ' seconds');

		$verificationRequest = $this->repository->createQueryBuilder('v')
			->andWhere('v.code = :code')
			->andWhere('v.updatedAt >= :maxTimeAgo')
			->setParameter('code', $code)
			->setParameter('maxTimeAgo', $maxTimeAgo)
			->getQuery()
			->getOneOrNullResult();

		if (!$verificationRequest) {
			return [];
		}

		return ['action' => $verificationRequest->getAction(), 'email' => $verificationRequest->getEmail(), 'user_id' => $verificationRequest->getUser()->getId()];
	}

	/**
	 * Undocumented function
	 *
	 * @param integer $userId
	 * @param integer $confirmType
	 * @return string
	 */
	public function getToken(int $userId, int $confirmType): string {
		$now = new \DateTimeImmutable();
		$maxTimeAgo = $now->modify('-' . self::VERIFICATION_MAX_TIME . ' seconds');

		$verificationRequest = $this->repository->createQueryBuilder('v')
			->andWhere('v.user = :userId')
			->andWhere('v.action = :confirmType')
			->andWhere('v.updatedAt >= :maxTimeAgo')
			->setParameter('userId', $userId)
			->setParameter('confirmType', $confirmType)
			->setParameter('maxTimeAgo', $maxTimeAgo)
			->getQuery()
			->getOneOrNullResult();

		if (!$verificationRequest) {
			return '';
		}

		return $verificationRequest->getCode();
	}

}