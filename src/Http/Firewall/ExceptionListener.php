<?php

namespace Inneair\OrigamiBundle\Http\Firewall;

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
 *
 * @author Innéair
 */
class ExceptionListener extends BaseExceptionListener
{
    /**
     * Logger.
     * @var LoggerInterface
     */
    private $logger;

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
     * @param GetResponseForExceptionEvent $event An GetResponseForExceptionEvent instance
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $this->logger->err($exception->getTraceAsString());
        do {
            if ($exception instanceof AuthenticationException) {
                return $this->handleAuthenticationException($event, $exception);
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
        GetResponseForExceptionEvent $event, AuthenticationException $exception
    )
    {
        if ($this->logger !== null) {
            $this->logger->debug('Authentication required: ' . $exception->getMessage());
        }

        try {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            $event->setResponse($response);
        } catch (Exception $e) {
            $event->setException($e);
        }
    }
}
