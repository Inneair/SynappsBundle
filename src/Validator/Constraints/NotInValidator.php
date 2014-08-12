<?php

namespace Inneair\SynappsBundle\Validator\Constraints;

use Inneair\Synapps\Util\StringUtils;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validator for class properties, where the value must not be one of the reserved values in the constraint definition.
 */
class NotInValidator extends ConstraintValidator
{
    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {
        // Ensures the property name is not a reserved name.
        foreach ($constraint->reservedValues as $reservedValue) {
            if (StringUtils::equals($reservedValue, $value, $constraint->ignoreCase)) {
                $this->context->addViolation(
                    $constraint->message,
                    array(
                        '{{ reserved_values }}'
                            => implode(StringUtils::ARRAY_VALUES_SEPARATOR, $constraint->reservedValues)
                    )
                );
            }
        }
    }
}
