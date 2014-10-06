<?php

namespace Inneair\SynappsBundle\Security\Http\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;

/**
 * Authentication handler that sends a HTTP 401 status code, in case of authentication failure.
 */
class RestAuthenticationFailureHandler extends DefaultAuthenticationFailureHandler
{
    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $request->getSession()->set(SecurityContextInterface::AUTHENTICATION_ERROR, $exception);

        $response = new Response();
        $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
        return $response;
    }
}
