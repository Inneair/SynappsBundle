<?php

namespace Inneair\OrigamiBundle\Security\Http\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * Class to proxy authentication success/failure handlers
 */
class RestAuthenticationFailureHandler extends DefaultAuthenticationFailureHandler
{
    /**
     * Constructor.
     */
    public function __construct(HttpKernelInterface $httpKernel, HttpUtils $httpUtils, array $options, LoggerInterface $logger = null)
    {
        error_log('RestAuthenticationFailureHandler constructor');
        parent::__construct($httpKernel, $httpUtils, $options, $logger);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        error_log('RestAuthenticationFailureHandler onAuthenticationFailure ' . $exception);
        $request->getSession()->set(SecurityContextInterface::AUTHENTICATION_ERROR, $exception);

        $response = new Response();
        $response->setStatusCode(204);
        return $response;
    }
}
