<?php

namespace Inneair\SynappsBundle\Test\IO;

use Inneair\SynappsBundle\Exception\ValidationException;
use Inneair\SynappsBundle\Test\AbstractTest;

/**
 * Class containing test suite for the {@link ValidationException} exception.
 */
class ValidationExceptionTest extends AbstractTest
{
    /**
     * An error message.
     * @var string
     */
    const ERROR_MESSAGE = 'error';
    /**
     * A field name.
     * @var string
     */
    const FIELD_NAME = 'fieldName';

    /**
     * Throws an exception with a global error message.
     */
    public function testGlobalError()
    {
        $hasException = false;
        try {
            $exception = new ValidationException();
            $exception->addGlobalError(self::ERROR_MESSAGE);
            throw $exception;
        } catch (ValidationException $e) {
            $hasException = true;
            $globalErrors = $e->getGlobalErrors();
            $this->assertNotNull($globalErrors);
            $this->assertCount(1, $globalErrors);
            $this->assertEquals(self::ERROR_MESSAGE, $globalErrors[0]);
        }
        $this->assertException($hasException);
    }

    /**
     * Throws an exception with an error message indexed with a field name.
     */
    public function testFieldError()
    {
        $hasException = false;
        try {
            $exception = new ValidationException();
            $exception->addFieldError(self::FIELD_NAME, self::ERROR_MESSAGE);
            throw $exception;
        } catch (ValidationException $e) {
            $hasException = true;
            $allFieldErrors = $e->getFieldErrors();
            $this->assertNotNull($allFieldErrors);
            $this->assertCount(1, $allFieldErrors);
            $this->assertArrayHasKey(self::FIELD_NAME, $allFieldErrors);
            $this->assertCount(1, $allFieldErrors[self::FIELD_NAME]);
            $this->assertTrue(in_array(self::ERROR_MESSAGE, $allFieldErrors[self::FIELD_NAME]));
        }
        $this->assertException($hasException);
    }
}
