<?php

namespace Inneair\SynappsBundle\Http;

use Inneair\Synapps\Util\StringUtils;

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
    public $global;
    /**
     * An array of fields errors, whose keys are fields names, and values are
     * arrays of error messages.
     * @var array
     */
    public $fields;

    /**
     * Merges errors from another instance into this instance.
     *
     * @param ErrorsContent $errors The errors to be merged into this instance.
     */
    public function merge(ErrorsContent $errors)
    {
        if ($errors->global !== null) {
            // Merge global errors.
            foreach ($errors->global as $errorMessage) {
                if (!in_array($errorMessage, $this->global)) {
                    $this->global[] = $errorMessage;
                }
            }
        }

        if ($errors->fields !== null) {
            // Merge fields errors.
            foreach ($errors->fields as $fieldName => $fieldErrors) {
                if (isset($this->fields[$fieldName])) {
                    $this->mergeFieldErrors($fieldName, $fieldErrors);
                } else {
                    $this->fields[$fieldName] = $errors->fields[$fieldName];
                }
            }
        }
    }

    /**
     * Merge errors related to a given field from another instance into this instance.
     *
     * @param string $fieldName Field name.
     * @param string[] $fieldErrors An array of error messages for this field.
     */
    protected function mergeFieldErrors($fieldName, array $fieldErrors)
    {
        foreach ($fieldErrors as $fieldErrorMessage) {
            if (!in_array($fieldErrorMessage, $this->fields[$fieldName])) {
                $this->fields[$fieldName][] = $fieldErrorMessage;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return 'ErrorsContent {global='
            . StringUtils::defaultString($this->global) . ', fields='
            . StringUtils::defaultString($this->fields) . '}';
    }
}
