<?php

namespace Inneair\SynappsBundle\Validator\Constraints;

use Inneair\Synapps\Http\Method;
use Inneair\SynappsBundle\Test\AbstractTest;
use Inneair\SynappsBundle\Validator\Constraints\HttpRequestParametersValidator;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * Class containing test suite for the {@link HttpRequestParametersValidator} validator.
 */
class HttpRequestParametersValidatorTest extends AbstractTest
{
    /**
     * Parameter name.
     * @var string
     */
    const PARAMETER = 'parameter';
    /**
     * An URI.
     * @var string
     */
    const URI = '';

    /**
     * Request stack.
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $requestStack;
    /**
     * Validator.
     * @var HttpRequestParametersValidator
     */
    private $validator;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $request = Request::create(self::URI);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->requestStack->expects(static::any())->method('getCurrentRequest')->willReturn($request);

        $this->validator = new HttpRequestParametersValidator($this->requestStack);

        parent::setUp();
    }

    /**
     * Validates a query with no required parameter.
     */
    public function testValidate()
    {
        $context = $this->getMockBuilder(ExecutionContext::class)-> disableOriginalConstructor()->getMock();
        $context->expects(static::never())->method('addViolation');

        $this->validator->initialize($context);
        $this->validator->validate(null, new HttpRequestParameters());
    }

    /**
     * Validates a query with a missing required parameter.
     */
    public function testValidateMissingRequiredParameter()
    {
        $constraint = new HttpRequestParameters();
        $constraint->requiredParameters[] = self::PARAMETER;

        $context = $this->getMockBuilder(ExecutionContext::class)-> disableOriginalConstructor()->getMock();
        $context->expects(static::once())->method('addViolation')->with(
            $constraint->requiredParametersMessage,
            array('{{ parameters }}' => self::PARAMETER)
        );

        $this->validator->initialize($context);
        $this->validator->validate(null, $constraint);
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
