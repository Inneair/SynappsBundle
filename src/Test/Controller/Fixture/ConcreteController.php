<?php

namespace Inneair\SynappsBundle\Test\Controller\Fixture;

use Inneair\SynappsBundle\Controller\AbstractController;
use Inneair\SynappsBundle\Exception\ValidationException;
use Inneair\Synapps\Exception\UniqueConstraintException;

/**
 * A test implementation of base controller that allows to test protected/private utilities.
 */
class ConcreteController extends AbstractController
{

    /**
     * A method that gives access to the {@link AbstractController#createHttpBadRequestView} method.
     *
     * @param mixed $data The data to be enclosed in the response (defaults to <code>null</code>).
     * @return View The view mapped to a 400 HTTP status code.
     */
    public function createHttpBadRequestViewInternal($data = null)
    {
        return $this->createHttpBadRequestView($data);
    }
    /**
     * A method that gives access to the {@link AbstractController#createHttpConflictView} method.
     *
     * @param mixed $data The data to be enclosed in the response (defaults to <code>null</code>).
     * @return View The view mapped to a 409 HTTP status code.
     */
    public function createHttpConflictViewInternal($data = null)
    {
        return $this->createHttpConflictView($data);
    }

    /**
     * A method that gives access to the {@link AbstractController#createHttpNotFoundView} method.
     *
     * @param mixed $data The data to be enclosed in the response (defaults to <code>null</code>).
     * @return View The view mapped to a 404 HTTP status code.
     */
    public function createHttpNotFoundViewInternal($data = null)
    {
        return $this->createHttpNotFoundView($data);
    }

    /**
     * A method that gives access to the {@link AbstractController#uniqueViolationToHttpConflictView} method.
     *
     * @param UniqueConstraintException $exception The exception that caused a unique constraint violation.
     * @return View The view mapped to a 409 HTTP status code.
     */
    public function uniqueViolationToHttpConflictViewInternal(UniqueConstraintException $exception)
    {
        return $this->uniqueViolationToHttpConflictView($exception);
    }

    /**
     * A method that gives access to the {@link AbstractController#validationExceptionToHttpBadRequestView} method.
     *
     * @param ValidationException $exception Validation exception.
     * @return View The view mapped to a 400 HTTP status code.
     */
    public function validationExceptionToHttpBadRequestViewInternal(ValidationException $exception)
    {
        return $this->validationExceptionToHttpBadRequestView($exception);
    }
}
