<?php

namespace Inneair\SynappsBundle\Exception;

use Exception;
use RuntimeException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Exception thrown when an object is not valid. This class can be used in business services when needed. Controllers
 * are encourage to catch this exception, and to throw a 400 HTTP status code.
 */
class ValidationException extends RuntimeException
{
    /**
     * The list of violations.
     * @var ConstraintViolationListInterface
     */
    private $violations;

    /**
     * Creates an exception based on the given parameters.
     *
     * @param ConstraintViolationListInterface List of constraint violations (defaults to <code>null</code>).
     * @param string $message Message (defaults to <code>null</code>).
     * @param string $code Custom error code (defaults to <code>null</code>).
     * @param Exception $previous Parent exception (defaults to <code>null</code>).
     */
    public function __construct(
        ConstraintViolationListInterface $violations = null,
        $message = null,
        $code = null,
        Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->violations = $violations;
    }

    /**
     * Gets the list of violations.
     *
     * @return ConstraintViolationListInterface List of constraint violations.
     */
    public function getViolations()
    {
        return $this->violations;
    }
}
