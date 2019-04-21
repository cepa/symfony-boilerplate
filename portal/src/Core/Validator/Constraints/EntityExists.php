<?php

namespace Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class EntityExists extends Constraint
{
    const DOES_NOT_EXIST_ERROR = 'be5d2cc9-6965-4ecf-953a-061cc347dca2';

    public $entityClass = null;
    public $field = null;
    public $message = 'Value {{ value }} does not exist.';

    public function getRequiredOptions()
    {
        return ['entityClass', 'field'];
    }
}
