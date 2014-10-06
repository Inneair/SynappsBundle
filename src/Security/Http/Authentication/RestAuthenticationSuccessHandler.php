<?php

namespace Inneair\OrigamiBundle\Security\Http\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * Class to proxy authentication success handlers
 */
class RestAuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    /**
     * {@inheritdoc}
     */
    public function __construct(HttpUtils $httpUtils, array $options)
    {
        error_log('RestAuthenticationSuccessHandler constructor');
        parent::__construct($httpUtils, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        error_log('RestAuthenticationSuccessHandler onAuthenticationSuccess ' . $token);
        $response = new Response();
        $response->setStatusCode(204);
        return $response;
    }
}
