<?php

namespace Inneair\SynappsBundle\Exception;

use Exception;
use RuntimeException;

/**
 * Exception thrown when an object is not valid. This class can be used in business services when needed. Controllers
 * are encourage to catch this exception, and to throw a 400 HTTP status code.
 */
class ValidationException extends RuntimeException
{
    /**
     * Global error messages.
     * @var string[]
     */
    private $globalErrors;
    /**
     * Indexed error messages by field.
     * @var string[]
     */
    private $fieldErrors;

    /**
     * Creates an exception based on the given parameters.
     *
     * @param array $globalErrors Global error messages (defaults to an empty array).
     * @param array $fieldErrors Error messages indexed by field names (defaults to an empty array).
     * @param string $message Message (defaults to <code>null</code>).
     * @param string $code Custom error code (defaults to <code>null</code>).
     * @param Exception $previous Parent exception (defaults to <code>null</code>).
     */
    public function __construct(
        array $globalErrors = [],
        array $fieldErrors = [],
        $message = null,
        $code = null,
        Exception $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
        $this->globalErrors = $globalErrors;
        $this->fieldErrors = $fieldErrors;
    }

    /**
     * Adds an error message indexed by a field name.
     *
     * @param string $fieldName Field name.
     * @param string $errorMessage Error message.
     */
    public function addFieldError($fieldName, $errorMessage)
    {
        $this->fieldErrors[$fieldName][] = $errorMessage;
    }

    /**
     * Adds a global error message.
     *
     * @param string $errorMessage Error message.
     */
    public function addGlobalError($errorMessage)
    {
        $this->globalErrors[] = $errorMessage;
    }

    /**
     * Gets the list of error messages indexed by a field names.
     *
     * @return string[] Error messages indexed by field names.
     */
    public function getFieldErrors()
    {
        return $this->fieldErrors;
    }

    /**
     * Gets the list of global errors.
     *
     * @return string[] Global error messages.
     */
    public function getGlobalErrors()
    {
        return $this->globalErrors;
    }
}
