<?php

namespace Inneair\SynappsBundle\Security\Http\Authentication;

use Inneair\Synapps\Util\StringUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;

/**
 * Authentication handler that sends a HTTP 204 status code, in case of authentication failure.
 */
class RestLogoutSuccessHandler extends DefaultLogoutSuccessHandler
{
    /**
     * {@inheritDoc}
     */
    public function onLogoutSuccess(Request $request)
    {
        return new Response(StringUtils::EMPTY_STR, Response::HTTP_NO_CONTENT);
    }
}
