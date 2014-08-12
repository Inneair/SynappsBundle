<?php

namespace Inneair\SynappsBundle\Validator\Constraints;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validator for HTTP request parameters.
 */
class HttpRequestParametersValidator extends ConstraintValidator
{
    /**
     * HTTP request begin validated.
     * @var Request
     */
    private $request;

    /**
     * Creates a validator for HTTP request parameters.
     *
     * @param RequestStack $requestStack The HTTP requests stack containing the request to be validated.
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {
        $requestParameters = $this->request->request->all();
        $missingParameters = array_diff($constraint->requiredParameters, array_keys($requestParameters));
        if (!empty($missingParameters)) {
            $this->context->addViolation(
                $constraint->requiredParametersMessage,
                array('{{ parameters }}' => implode(', ', $missingParameters))
            );
        }
    }
}
