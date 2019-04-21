<?php

namespace Core\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EntityExistsValidator extends ConstraintValidator
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof EntityExists) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\EntityExists');
        }

        $repo = $this->em->getRepository($constraint->entityClass);
        $entity = $repo->findOneBy([$constraint->field => $value]);

        if ($entity instanceof $constraint->entityClass) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->atPath($value)
            ->setParameter('{{ value }}', $value)
            ->setInvalidValue($value)
            ->setCode(EntityExists::DOES_NOT_EXIST_ERROR)
            ->addViolation();
    }
}
