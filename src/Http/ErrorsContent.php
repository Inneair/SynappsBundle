<?php

namespace Inneair\SynappsBundle\Http;

/**
 * A class representing errors, that can be used in HTTP responses related to failures, and for
 * serialization/deserialization purposes.
 */
class ErrorsContent
{
    /**
     * An array of global error messages.
     * @var string[]
     */
    private $globalErrors;
    /**
     * An array of fields errors, whose keys are fields names, and values are arrays of error messages.
     * @var array
     */
    private $fieldsErrors;

    /**
     * Creates a content for errors.
     *
     * @param string[] $globalErrors Array of global error messages.
     * @param array $fieldsErrors Array of fields errors, whose keys are fields names, and values are arrays of error
     * messages.
     */
    public function __construct(array $globalErrors = [], array $fieldsErrors = [])
    {
        $this->globalErrors = $globalErrors;
        $this->fieldsErrors = $fieldsErrors;
    }

    /**
     * Adds global errors to this content.
     *
     * The method removes duplicated messages.
     *
     * @param string[] $globalErrors Array of global error messages.
     */
    public function addGlobalErrors(array $globalErrors)
    {
        $this->globalErrors = array_unique(array_merge($this->globalErrors, $globalErrors));
    }

    /**
     * Gets errors for a given fields.
     *
     * @param string $fieldName Field name.
     * @return string[] An array of error messages, or <code>null</code> if there are no errors for this field.
     */
    public function getFieldErrors($fieldName)
    {
        $fieldErrors = null;
        if (isset($this->fieldsErrors[$fieldName])) {
            $fieldErrors = $this->fieldsErrors[$fieldName];
        }
        return $fieldErrors;
    }

    /**
     * Gets errors for all fields.
     *
     * @return array An array of fields errors, whose keys are fields names, and values are arrays of error messages.
     */
    public function getFieldsErrors()
    {
        return $this->fieldsErrors;
    }

    /**
     * Gets the global errors.
     *
     * @return string[] An array of global error messages.
     */
    public function getGlobalErrors()
    {
        return $this->globalErrors;
    }

    /**
     * Merges errors from another instance into this instance.
     *
     * @param ErrorsContent $errors The errors to be merged into this instance.
     */
    public function merge(ErrorsContent $errors)
    {
        if ($errors->globalErrors !== null) {
            // Merge global errors.
            foreach ($errors->globalErrors as $errorMessage) {
                if (!in_array($errorMessage, $this->globalErrors)) {
                    $this->globalErrors[] = $errorMessage;
                }
            }
        }

        if ($errors->fieldsErrors !== null) {
            // Merge fields errors.
            foreach ($errors->fieldsErrors as $fieldName => $fieldErrors) {
                $this->mergeFieldErrors($fieldName, $fieldErrors);
            }
        }
    }

    /**
     * Merge errors related to a given field from another instance into this instance.
     *
     * @param string $fieldName Field name.
     * @param string[] $fieldErrors An array of error messages for this field.
     */
    public function mergeFieldErrors($fieldName, array $fieldErrors)
    {
        foreach ($fieldErrors as $fieldErrorMessage) {
            if (!isset($this->fieldsErrors[$fieldName])
                || !in_array($fieldErrorMessage, $this->fieldsErrors[$fieldName])) {
                $this->fieldsErrors[$fieldName][] = $fieldErrorMessage;
            }
        }
    }
}
