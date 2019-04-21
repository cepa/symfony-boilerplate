<?php

namespace Core\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EntityNotExistsValidator extends ConstraintValidator
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof EntityNotExists) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\EntityNotExists');
        }

        $repo = $this->em->getRepository($constraint->entityClass);
        $entity = $repo->findOneBy([$constraint->field => $value]);

        if (!$entity instanceof $constraint->entityClass) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->atPath($value)
            ->setParameter('{{ value }}', $value)
            ->setInvalidValue($value)
            ->setCode(EntityNotExists::DOES_EXIST_ERROR)
            ->addViolation();
    }
}
