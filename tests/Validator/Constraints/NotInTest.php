<?php

namespace Inneair\SynappsBundle\Validator\Constraints;

use Inneair\SynappsBundle\Test\AbstractTest;
use RuntimeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContext;
use Inneair\Synapps\Util\StringUtils;

/**
 * Class containing test suite for the {@link NotInValidator} validator.
 */
class NotInTest extends AbstractTest
{
    /**
     * Value in lower case.
     * @var string
     */
    const VALUE_LOWER = 'value';

    /**
     * Validator.
     * @var NotInValidator
     */
    private $validator;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->validator = new NotInValidator();

        parent::setUp();
    }

    /**
     * Validates a value is not reserved.
     */
    public function testValidateReservedValueWithoutIgnoreCase()
    {
        $constraint = new NotIn();
        $constraint->ignoreCase = false;
        $constraint->reservedValues[] = mb_strtoupper(self::VALUE_LOWER);

        $context = $this->getMockBuilder(ExecutionContext::class)-> disableOriginalConstructor()->getMock();
        $context->expects(static::never())->method('addViolation');

        $this->validator->initialize($context);
        $this->validator->validate(self::VALUE_LOWER, $constraint);
    }

    /**
     * Validates a value is not reserved.
     */
    public function testValidateReservedValueWithIgnoreCase()
    {
        $constraint = new NotIn();
        $constraint->ignoreCase = true;
        $constraint->reservedValues[] = mb_strtoupper(self::VALUE_LOWER);

        $context = $this->getMockBuilder(ExecutionContext::class)-> disableOriginalConstructor()->getMock();
        $context->expects(static::once())->method('addViolation')->with(
            $constraint->message,
            ['{{ reserved_values }}' => implode(StringUtils::ARRAY_VALUES_SEPARATOR, $constraint->reservedValues)]
        );

        $this->validator->initialize($context);
        $this->validator->validate(self::VALUE_LOWER, $constraint);
    }

    /**
     * Validates with an unknown constraint.
     */
    public function testValidateWithUnknownConstraint()
    {
        $hasException = false;
        try {
            $this->validator->validate(null, $this->getMockForAbstractClass(Constraint::class));
        } catch (RuntimeException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);
    }
}
