<?php

namespace Inneair\SynappsBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Inneair\SynappsBundle\Http\ErrorsContent;
use Inneair\SynappsBundle\Http\ErrorResponseContent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Abstract web controller providing additional services over the FOS REST controller, and the Symfony controller.
 */
abstract class AbstractController extends FOSRestController
{
    /**
     * Domain name for translations.
     * @var string
     */
    const CONTROLLER_DOMAIN = 'controllers';

    /**
     * Translator service.
     * @var TranslatorInterface
     */
    protected $translator;
    /**
     * Logging service.
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Sets the translator service.
     *
     * NOTE: this method is automatically invoked by the framework, and should never be called manually.
     *
     * @param TranslatorInterface $translator Translator service.
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

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
     * controllers, to perform additional initializations other than raw instanciations (but calling this parent method
     * is always mandatory to ensure forward compatibility).
     */
    public function init()
    {
        $this->logger->debug('Controller \'' . static::class . '\' is up.');
    }

    /**
     * Handles a request and validates the underlying form (forces it if the request contains no data).
     *
     * @param Request $request HTTP request.
     * @param FormInterface $form Underlying form.
     * @return bool <code>true</code> if the form is valid, <code>false</code> otherwise.
     */
    protected function validateRequest(Request $request, FormInterface $form)
    {
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            $this->logger->warning(
                'Missing form parameters, form submission is forced for validation: ['
                . $request->getMethod() . ' ' . $request->getUri() . ']');
            $form->submit(null);
        }

        return $form->isValid();
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
    public function createNamedForm($type = 'form', $data = null, Array $options = array(), $name = null)
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
    public function createNamedBuilder($type = 'form', $data = null, Array $options = array(), $name = null)
    {
        return $this->container->get('form.factory')->createNamedBuilder($name, $type, $data, $options);
    }

    /**
     * Creates a view useful to send a HTTP 400 status code to the client, due to bad request format.
     *
     * @param FormInterface $form Form containing format errors.
     * @param mixed $data The data to be enclosed in the response (defaults to <code>null</code>).
     * @return View The view mapped to a HTTP 400 status code.
     */
    protected function createBadRequestView(FormInterface $form, $data = null)
    {
        $content = new ErrorResponseContent($this->convertFormErrorsToErrorsContent($form), $data);
        return $this->view($content, Response::HTTP_BAD_REQUEST);
    }

    /**
     * Extracts all errors from a form and its children, recursively, and converts them into an object that can be
     * serialized in HTTP responses.
     *
     * @param FormInterface $form The root form.
     * @return ErrorsContent An instance containing errors that can be serialized in the HTTP response.
     */
    protected function convertFormErrorsToErrorsContent(FormInterface $form)
    {
        $errors = new ErrorsContent();
        $formErrors = $form->getErrors();
        foreach ($formErrors as $formError) {
            if ($formError instanceof FormError) {
                if (empty($form->getName())) {
                    $errors->global[] = $formError->getMessage();
                } else {
                    $errors->fields[$form->getName()][] = $formError->getMessage();
                }
            }
        }

        $children = $form->all();
        foreach ($children as $child) {
            // Merge errors from child with this form errors.
            $errors->merge($this->convertFormErrorsToErrorsContent($child));
        }

        return $errors;
    }
}
