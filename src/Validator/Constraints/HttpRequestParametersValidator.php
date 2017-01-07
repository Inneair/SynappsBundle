<?php

namespace Inneair\SynappsBundle\Validator\Constraints;

use Inneair\Synapps\Util\StringUtils;
use RuntimeException;
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
     *
     * @throws RuntimeException If the given constraint is not a {@link HttpRequestParameters} instance.
     */
    public function validate($value, Constraint $constraint)
    {
        if ($constraint instanceof HttpRequestParameters) {
            $requestParameters = $this->request->request->all();
            $missingParameters = array_diff($constraint->requiredParameters, array_keys($requestParameters));
            if (!empty($missingParameters)) {
                $this->context->addViolation(
                    $constraint->requiredParametersMessage,
                    ['{{ parameters }}' => implode(StringUtils::ARRAY_VALUES_SEPARATOR, $missingParameters)]
                );
            }
        } else {
            throw new RuntimeException(
                'Invalid constraint: ' . HttpRequestParameters::class . ' instance expected, ' . get_class($constraint)
                . ' provided'
            );
        }
    }
}
