<?php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class UniqueEmailValidator extends ConstraintValidator
{
	public function __construct(private EntityManagerInterface $em)
	{
	}

	public function validate($value, Constraint $constraint)
	{
		if (!$value) {
			return;
		}

		$exists = $this->em->getRepository(User::class)->findOneBy(['email' => $value]);

		if ($exists) {
			$this->context->buildViolation($constraint->message)
				->addViolation();
		}
	}
}
