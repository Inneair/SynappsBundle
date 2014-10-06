<?php

namespace Inneair\SynappsBundle\Security\Http\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

/**
 * Authentication handler that sends a HTTP 204 status code, in case of authentication failure.
 */
class RestAuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $response = new Response();
        $response->setStatusCode(Response::HTTP_NO_CONTENT);
        return $response;
    }
}
