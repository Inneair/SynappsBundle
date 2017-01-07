<?php

namespace Inneair\SynappsBundle\Test\Controller\Fixture;

use Exception;
use FOS\RestBundle\View\View;
use Inneair\Synapps\Exception\UniqueConstraintException;
use Inneair\SynappsBundle\Controller\AbstractRestController;
use Inneair\SynappsBundle\Exception\ValidationException;

/**
 * A test implementation of a REST controller that allows to test protected/private utilities.
 */
class ConcreteRestController extends AbstractRestController
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
     * A method that gives access to the {@link AbstractController#exceptionToHttpBadRequestView} method.
     *
     * @param ValidationException $exception Validation exception.
     * @return View The view mapped to a 400 HTTP status code.
     */
    public function exceptionToHttpBadRequestViewInternal(Exception $exception)
    {
        return $this->exceptionToHttpBadRequestView($exception);
    }
}
