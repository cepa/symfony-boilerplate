<?php

namespace Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class EntityNotExists extends Constraint
{
    const DOES_EXIST_ERROR = '142505a0-d2ac-4a77-a612-8497dd47217c';

    public $entityClass = null;
    public $field = null;
    public $message = 'Value {{ value }} already exists.';

    public function getRequiredOptions()
    {
        return ['entityClass', 'field'];
    }
}
