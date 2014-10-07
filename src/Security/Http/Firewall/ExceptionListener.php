<?php

namespace Inneair\SynappsBundle\Security\Http\Firewall;

use Inneair\Synapps\Util\StringUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ExceptionListener as BaseExceptionListener;

/**
 * This exception listener catches authentication exceptions, and sends a 401 HTTP code in response.
 */
class ExceptionListener extends BaseExceptionListener
{
    /**
     * Handles security related exceptions.
     *
     * This implementation sets a new HTTP response in the event, with a status code 401 in case of authentication
     * exceptions. For other exceptions, management is entrusted to the base kernel implementation.
     *
     * @param GetResponseForExceptionEvent $event An GetResponseForExceptionEvent instance
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        do {
            if ($exception instanceof AuthenticationException) {
                $event->setResponse(new Response(StringUtils::EMPTY_STR, Response::HTTP_UNAUTHORIZED));
                return;
            }
            $exception = $exception->getPrevious();
        } while ($exception !== null);

        return parent::onKernelException($event);
    }
}
