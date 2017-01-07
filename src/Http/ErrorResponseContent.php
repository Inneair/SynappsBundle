<?php

namespace Inneair\SynappsBundle\Http;

/**
 * A base class for all HTTP response contents containing errors, and that can be used for serialization/deserialization
 * purposes.
 */
class ErrorResponseContent
{
    /**
     * Errors.
     * @var ErrorsContent
     */
    public $errors;
    /**
     * Custom data sent in the response.
     * @var mixed
     */
    public $data;

    /**
     * Creates an HTTP response containing the given errors, and data.
     *
     * @param ErrorsContent $errors Errors (defaults to <code>null</code>).
     * @param mixed $data Data (defaults to <code>null</code>).
     */
    public function __construct(ErrorsContent $errors = null, $data = null)
    {
        $this->errors = $errors;
        $this->data = $data;
    }
}
