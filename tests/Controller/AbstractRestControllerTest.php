<?php

namespace Inneair\SynappsBundle\Test\Controller;

use Exception;
use Inneair\Synapps\Exception\UniqueConstraintException;
use Inneair\SynappsBundle\Exception\ValidationException;
use Inneair\SynappsBundle\Http\ErrorResponseContent;
use Inneair\SynappsBundle\Test\Controller\Fixture\ConcreteRestController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class containing tests for the abstract controller.
 */
class AbstractRestControllerTest extends AbstractUnitTest
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
     * A concrete controller.
     * @var ConcreteRestController
     */
    private $controller;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->controller = $this->getController(ConcreteRestController::class);
    }

    /**
     * Creates a HTTP response with status code 400.
     */
    public function testCreateHttpBadRequestView()
    {
        $data = [];
        $view = $this->controller->createHttpBadRequestViewInternal($data);

        $this->assertNotNull($view);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $view->getStatusCode());
        $this->assertEquals($data, $view->getData());
    }

    /**
     * Creates a HTTP response with status code 409.
     */
    public function testCreateHttpConflictViewInternal()
    {
        $data = [];
        $view = $this->controller->createHttpConflictViewInternal($data);

        $this->assertNotNull($view);
        $this->assertEquals(Response::HTTP_CONFLICT, $view->getStatusCode());
        $this->assertEquals($data, $view->getData());
    }

    /**
     * Creates a HTTP response with status code 404.
     */
    public function testCreateHttpNotFoundViewInternal()
    {
        $data = [];
        $view = $this->controller->createHttpNotFoundViewInternal($data);

        $this->assertNotNull($view);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $view->getStatusCode());
        $this->assertEquals($data, $view->getData());
    }

    /**
     * Converts a validation exception into a HTTP response with status code 400.
     */
    public function testUniqueViolationToHttpConflictView()
    {
        $view = $this->controller->uniqueViolationToHttpConflictViewInternal(new UniqueConstraintException(
            self::FIELD_NAME
        ));

        $this->assertNotNull($view);
        $this->assertEquals(Response::HTTP_CONFLICT, $view->getStatusCode());
        $data = $view->getData();
        $this->assertTrue($data instanceof ErrorResponseContent);
        $this->assertNotNull($data->errors);

        $globalErrors = $data->errors->getGlobalErrors();
        $this->assertTrue(is_array($globalErrors));
        $this->assertCount(0, $globalErrors);

        $fieldsErrors = $data->errors->getFieldsErrors();
        $this->assertTrue(is_array($fieldsErrors));
        $this->assertCount(1, $fieldsErrors);
        $this->assertArrayHasKey(self::FIELD_NAME, $fieldsErrors);
        $this->assertTrue(is_array($fieldsErrors[self::FIELD_NAME]));
        $this->assertCount(1, $fieldsErrors[self::FIELD_NAME]);
    }

    /**
     * Converts a non-validation exception into a HTTP response with status code 400.
     */
    public function testExceptionToHttpBadRequestView()
    {
        $exception = new Exception(self::ERROR_MESSAGE);
        $view = $this->controller->exceptionToHttpBadRequestViewInternal($exception);

        $this->assertNotNull($view);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $view->getStatusCode());
        $data = $view->getData();
        $this->assertTrue($data instanceof ErrorResponseContent);
        $this->assertNotNull($data->errors);

        $globalErrors = $data->errors->getGlobalErrors();
        $this->assertTrue(is_array($globalErrors));
        $this->assertCount(1, $globalErrors);
        $this->assertEquals($exception->getMessage(), $globalErrors[0]);

        $fieldsErrors = $data->errors->getFieldsErrors();
        $this->assertTrue(is_array($fieldsErrors));
        $this->assertCount(0, $fieldsErrors);
    }

    /**
     * Converts a validation exception into a HTTP response with status code 400.
     */
    public function testValidationExceptionToHttpBadRequestView()
    {
        $exception = new ValidationException(
            [self::ERROR_MESSAGE],
            [self::FIELD_NAME => [self::ERROR_MESSAGE]]
        );
        $view = $this->controller->exceptionToHttpBadRequestViewInternal($exception);

        $this->assertNotNull($view);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $view->getStatusCode());
        $data = $view->getData();
        $this->assertTrue($data instanceof ErrorResponseContent);
        $this->assertNotNull($data->errors);

        $globalErrors = $data->errors->getGlobalErrors();
        $this->assertTrue(is_array($globalErrors));
        $this->assertCount(1, $globalErrors);
        $this->assertEquals(self::ERROR_MESSAGE, $globalErrors[0]);

        $fieldsErrors = $data->errors->getFieldsErrors();
        $this->assertTrue(is_array($fieldsErrors));
        $this->assertCount(1, $fieldsErrors);
        $this->assertArrayHasKey(self::FIELD_NAME, $fieldsErrors);
        $this->assertTrue(is_array($fieldsErrors[self::FIELD_NAME]));
        $this->assertCount(1, $fieldsErrors[self::FIELD_NAME]);
        $this->assertEquals(self::ERROR_MESSAGE, $fieldsErrors[self::FIELD_NAME][0]);
    }
}
