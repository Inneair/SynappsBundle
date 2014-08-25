<?php

namespace Inneair\SynappsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Validation constraint definition to check a value is not in a set of predefined values.
 *
 * @Annotation
 */
class NotIn extends Constraint
{
    /**
     * Array of reserved values.
     * @var array
     */
    public $reservedValues = array();
    /**
     * Tells the check the value using a case-insensitive comparison.
     * @var boolean
     */
    public $ignoreCase = false;
    /**
     * Message the value is one of the reserved values.
     * @var string
     */
    public $message = 'This value should not be identical to one in this set ({{ reserved_values }}).';

    /**
     * {@inheritDoc}
     */
    public function validatedBy()
    {
        // Returning the constraint 'alias' is mandatory so as Symfony can inject dependencies.
        return 'notinvalidator';
    }
}
