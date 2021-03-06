<?php

namespace Inneair\SynappsBundle\Test\Util;

use Inneair\SynappsBundle\Test\AbstractTest;
use Inneair\SynappsBundle\Http\ErrorsContent;

/**
 * Class containing the test suite for content used to report errors.
 */
class ErrorContentTest extends AbstractTest
{
    /**
     * A field name.
     * @var string
     */
    const FIELD_NAME_1 = 'field1';
    /**
     * A field name.
     * @var string
     */
    const FIELD_NAME_2 = 'field2';
    /**
     * An error message.
     * @var string
     */
    const ERROR_MESSAGE_1 = 'error1';
    /**
     * An error message.
     * @var string
     */
    const ERROR_MESSAGE_2 = 'error2';

    /**
     * Add global errors into a content with a new error and a duplicated error.
     */
    public function testAddGlobalErrors()
    {
        $content = new ErrorsContent([self::ERROR_MESSAGE_1]);
        $content->addGlobalErrors([self::ERROR_MESSAGE_1, self::ERROR_MESSAGE_2]);
        $this->assertCount(2, $content->getGlobalErrors());
        $globalErrors = $content->getGlobalErrors();
        $this->assertTrue(is_array($globalErrors));
        $this->assertCount(2, $globalErrors);
        $this->assertContains(self::ERROR_MESSAGE_1, $globalErrors);
        $this->assertContains(self::ERROR_MESSAGE_2, $globalErrors);
    }

    /**
     * Merge errors from two contents with duplicated global errors and field errors.
     */
    public function testMerge()
    {
        $content1 = new ErrorsContent(
            [self::ERROR_MESSAGE_1],
            [
                self::FIELD_NAME_1 => [self::ERROR_MESSAGE_1],
                self::FIELD_NAME_2 => [self::ERROR_MESSAGE_2]
            ]
        );
        $content2 = new ErrorsContent(
            [self::ERROR_MESSAGE_1, self::ERROR_MESSAGE_2],
            [
                self::FIELD_NAME_2 => [self::ERROR_MESSAGE_1, self::ERROR_MESSAGE_2]
            ]
        );

        $content1->merge($content2);
    
        $globalErrors = $content1->getGlobalErrors();
        $this->assertCount(2, $globalErrors);
        $this->assertContains(self::ERROR_MESSAGE_1, $globalErrors);
        $this->assertContains(self::ERROR_MESSAGE_2, $globalErrors);
    
        $fieldsErrors = $content1->getFieldsErrors();
        $this->assertCount(2, $fieldsErrors);
        $this->assertArrayHasKey(self::FIELD_NAME_1, $fieldsErrors);
        $this->assertTrue(is_array($fieldsErrors[self::FIELD_NAME_1]));
        $this->assertArrayHasKey(self::FIELD_NAME_2, $fieldsErrors);
        $this->assertTrue(is_array($fieldsErrors[self::FIELD_NAME_2]));
    
        $fieldErrors = $content1->getFieldErrors(self::FIELD_NAME_1);
        $this->assertCount(1, $fieldErrors);
        $this->assertContains(self::ERROR_MESSAGE_1, $fieldErrors);
    
        $fieldErrors = $content1->getFieldErrors(self::FIELD_NAME_2);
        $this->assertCount(2, $fieldErrors);
        $this->assertContains(self::ERROR_MESSAGE_1, $fieldErrors);
        $this->assertContains(self::ERROR_MESSAGE_2, $fieldErrors);
    }

    /**
     * Merge field errors with duplicated error messages.
     */
    public function testMergeFieldErrors()
    {
        $content = new ErrorsContent();
        $this->assertTrue(is_array($content->getGlobalErrors()));
        $this->assertCount(0, $content->getGlobalErrors());
        $this->assertTrue(is_array($content->getFieldsErrors()));
        $this->assertCount(0, $content->getFieldsErrors());
        $this->assertNull($content->getFieldErrors(self::FIELD_NAME_1));

        $content->mergeFieldErrors(self::FIELD_NAME_1, [self::ERROR_MESSAGE_1]);
        $this->assertCount(1, $content->getFieldsErrors());
        $fieldErrors = $content->getFieldErrors(self::FIELD_NAME_1);
        $this->assertTrue(is_array($fieldErrors));
        $this->assertCount(1, $fieldErrors);
        $this->assertContains(self::ERROR_MESSAGE_1, $fieldErrors);
    }
}
