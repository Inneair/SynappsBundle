<?php

namespace Inneair\SynappsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Validation constraint definition for HTTP request parameters.
 *
 * @Annotation
 */
class HttpRequestParameters extends Constraint
{
    /**
     * Array of required parameters' name.
     * @var array
     */
    public $requiredParameters = array();
    /**
     * Message when at least one required parameter is missing in the request.
     * @var string
     */
    public $requiredParametersMessage = 'Missing parameters';

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * {@inheritDoc}
     */
    public function validatedBy()
    {
        // Returning the constraint 'alias' is mandatory so as Symfony can inject dependencies.
        return 'httprequestparametersvalidator';
    }
}
