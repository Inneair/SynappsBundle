<?php

namespace Inneair\SynappsBundle\Controller;

use Exception;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Inneair\Synapps\Exception\UniqueConstraintException;
use Inneair\SynappsBundle\Exception\ValidationException;
use Inneair\SynappsBundle\Http\ErrorsContent;
use Inneair\SynappsBundle\Http\ErrorResponseContent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * Abstract web controller providing additional services over the FOS REST controller, and the Symfony controller.
 */
abstract class AbstractRestController extends FOSRestController
{
    /**
     * Domain name for translations.
     * @var string
     */
    const CONTROLLER_DOMAIN = 'controllers';

    /**
     * Logging service.
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Sets the logging service.
     *
     * NOTE: this method is automatically invoked by the framework, and should never be called manually.
     *
     * @param LoggerInterface $logger Logging service.
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Initializes the controller.
     *
     * NOTE: this method is automatically invoked by the framework, and should never be called manually.
     * Actually, this method does nothing but logging the controller is ready. It may be overridden by concrete
     * controllers, to perform additional initializations other than raw instantiations (but calling this parent method
     * is always mandatory to ensure forward compatibility).
     */
    public function init()
    {
        $this->logger->debug('Controller \'' . static::class . '\' is up.');
    }

    /**
     * Creates and returns a FormInterface instance from the given type.
     *
     * @param string|FormInterface $type The built type of the form (defaults to 'form').
     * @param mixed $data The initial data for the form (defaults to <code>null</code>).
     * @param array $options Options for the form (defaults to an empty array).
     * @param string $name Form name (defaults to <code>null</code>).
     * @return FormInterface A form instance.
     * @throws InvalidOptionsException If any given option is not applicable to the given type.
     */
    public function createNamedForm($type = 'form', $data = null, array $options = [], $name = null)
    {
        return $this->container->get('form.factory')->createNamed($name, $type, $data, $options);
    }

    /**
     * Creates and returns a FormBuilderInterface instance from the given type.
     *
     * @param string|FormInterface $type The built type of the form (defaults to 'form').
     * @param mixed $data The initial data for the form (defaults to <code>null</code>).
     * @param array $options Options for the form (defaults to an empty array).
     * @param string $name Form name (defaults to <code>null</code>).
     * @return FormBuilderInterface A form builder instance.
     * @throws InvalidOptionsException If any given option is not applicable to the given type
     */
    public function createNamedBuilder($type = 'form', $data = null, array $options = [], $name = null)
    {
        return $this->container->get('form.factory')->createNamedBuilder($name, $type, $data, $options);
    }

    /**
     * Sends a 400 HTTP status code for a data not found exception.
     *
     * @param mixed $data The data to be enclosed in the response (defaults to <code>null</code>).
     * @return View The view mapped to a 400 HTTP status code.
     */
    protected function createHttpBadRequestView($data = null)
    {
        return $this->view($data, Response::HTTP_BAD_REQUEST);
    }

    /**
     * Sends a 409 HTTP status code due to a conflict.
     *
     * @param mixed $data The data to be enclosed in the response (defaults to <code>null</code>).
     * @return View The view mapped to a 409 HTTP status code.
     */
    protected function createHttpConflictView($data = null)
    {
        return $this->view($data, Response::HTTP_CONFLICT);
    }

    /**
     * Sends a 404 HTTP status code for a data not found exception.
     *
     * @param mixed $data The data to be enclosed in the response (defaults to <code>null</code>).
     * @return View The view mapped to a 404 HTTP status code.
     */
    protected function createHttpNotFoundView($data = null)
    {
        return $this->view($data, Response::HTTP_NOT_FOUND);
    }

    /**
     * Converts unique constraint exception on a property into a 409 HTTP status code.
     *
     * @param UniqueConstraintException $exception The exception that caused a unique constraint violation.
     * @return View The view mapped to a 409 HTTP status code.
     */
    protected function uniqueViolationToHttpConflictView(UniqueConstraintException $exception)
    {
        $errors = new ErrorsContent();
        $errors->mergeFieldErrors($exception->getProperty(), ['controller.general.uniquevalueerror']);
        $content = new ErrorResponseContent($errors);
        return $this->view($content, Response::HTTP_CONFLICT);
    }

    /**
     * Converts exceptions into a 400 HTTP status code.
     *
     * If the exception is a {@link ValidationException}, a special behaviour allows to keep field-related violations
     * intact.
     *
     * @param Exception $exception An exception.
     * @return View The view mapped to a 400 HTTP status code.
     */
    protected function exceptionToHttpBadRequestView(Exception $exception)
    {
        $errors = new ErrorsContent();
        if ($exception instanceof ValidationException) {
            $errors->addGlobalErrors($exception->getGlobalErrors());
            $allFieldErrors = $exception->getFieldErrors();
            foreach ($allFieldErrors as $fieldName => $fieldErrors) {
                $errors->mergeFieldErrors($fieldName, $fieldErrors);
            }
        } else {
            $errors->addGlobalErrors([$exception->getMessage()]);
        }

        $content = new ErrorResponseContent($errors);
        return $this->createHttpBadRequestView($content);
    }
}
