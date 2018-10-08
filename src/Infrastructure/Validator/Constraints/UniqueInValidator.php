<?php

namespace App\Infrastructure\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueInValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param mixed    $value
     * @param UniqueIn $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $entityRepository = $this->em->getRepository($constraint->entityClass);

        $searchResults = $entityRepository->findBy([
            $constraint->field => $value,
        ]);

        if ($constraint->ignoreId && $constraint->ignoreId === $searchResults->getId()) {
            return;
        }

        if (count($searchResults) > 0) {
            $this->context->buildViolation($constraint->message)
                          ->addViolation();
        }
    }
}
