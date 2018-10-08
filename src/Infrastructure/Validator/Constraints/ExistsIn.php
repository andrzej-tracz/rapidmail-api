<?php

namespace App\Infrastructure\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Doctrine\ORM\Mapping\Annotation;

/**
 * Class UniqueIn.
 *
 * @Annotation
 */
class ExistsIn extends Constraint
{
    /**
     * @var string
     */
    public $message = 'This value is invalid.';

    /**
     * @var string
     */
    public $entityClass;

    /**
     * @var string
     */
    public $field;

    public function getRequiredOptions()
    {
        return ['entityClass', 'field'];
    }

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy()
    {
        return ExistsInValidator::class;
    }
}
