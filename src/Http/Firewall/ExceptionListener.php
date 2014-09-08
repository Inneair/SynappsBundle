<?php

namespace Inneair\SynappsBundle\Http\Firewall;

use Exception;
use Inneair\Synapps\Util\StringUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\Firewall\ExceptionListener as BaseExceptionListener;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * This exception listener catches authentication exceptions, and sends a 401 HTTP code in response.
 */
class ExceptionListener extends BaseExceptionListener
{
    /**
     * Logger.
     * @var LoggerInterface
     */
    private $logger;

    /**
     * {@inheritDoc}
     */
    public function __construct(
        SecurityContextInterface $context,
        AuthenticationTrustResolverInterface $trustResolver,
        HttpUtils $httpUtils,
        $providerKey,
        AuthenticationEntryPointInterface $authenticationEntryPoint = null,
        $errorPage = null,
        AccessDeniedHandlerInterface $accessDeniedHandler = null,
        LoggerInterface $logger = null
    )
    {
        parent::__construct(
            $context,
            $trustResolver,
            $httpUtils,
            $providerKey,
            $authenticationEntryPoint,
            $errorPage,
            $accessDeniedHandler,
            $logger
        );
        $this->logger = $logger;
    }

    /**
     * Handles security related exceptions.
     *
     * This implementation returns 401 HTTP status code for authentication exceptions. For other exceptions, management
     * is entrusted to the base kernel implementation.
     *
     * @param GetResponseForExceptionEvent $event An GetResponseForExceptionEvent instance
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        do {
            if ($exception instanceof AuthenticationException) {
                $this->handleAuthenticationException($event, $exception);
                return;
            }
            $exception = $exception->getPrevious();
        } while ($exception !== null);

        return parent::onKernelException($event);
    }

    /**
     * Handles authentication exceptions by sending a HTTP 401 status code.
     *
     * @param GetResponseForExceptionEvent $event Event.
     * @param AuthenticationException $exception Pending authentication exception.
     */
    protected function handleAuthenticationException(
        GetResponseForExceptionEvent $event,
        AuthenticationException $exception
    )
    {
        if ($this->logger !== null) {
            $this->logger->debug('Authentication required: ' . $exception->getMessage());
        }

        try {
            $event->setResponse(new Response(StringUtils::EMPTY_STR, Response::HTTP_UNAUTHORIZED));
        } catch (Exception $e) {
            $event->setException($e);
        }
    }
}
