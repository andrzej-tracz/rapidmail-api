<?php

namespace App\Infrastructure\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Doctrine\ORM\Mapping\Annotation;

/**
 * Class UniqueIn.
 *
 * @Annotation
 */
class UniqueIn extends Constraint
{
    /**
     * @var string
     */
    public $message = 'This value is already used.';

    /**
     * @var string
     */
    public $entityClass;

    /**
     * @var string
     */
    public $field;

    /**
     * @var int
     */
    public $ignoreId;

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
        return get_class($this).'Validator';
    }
}
