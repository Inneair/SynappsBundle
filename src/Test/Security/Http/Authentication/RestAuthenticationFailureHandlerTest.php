<?php

namespace Inneair\SynappsBundle\Test\Security\Http\Authentication;

use Inneair\SynappsBundle\Security\Http\Authentication\RestAuthenticationFailureHandler;
use Inneair\SynappsBundle\Test\AbstractTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * Class containing test suite for the {@link RestAuthenticationFailureHandler} class.
 */
class RestAuthenticationFailureHandlerTest extends AbstractTest
{
    /**
     * Authentication failure handler.
     * @var RestAuthenticationFailureHandler
     */
    private $failureHandler;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->failureHandler = new RestAuthenticationFailureHandler(
            $this->getMock(HttpKernelInterface::class),
            $this->getMock(HttpUtils::class),
            array()
        );
    }

    /**
     * Check a HTTP response with status code 401 is returned in case of an authentication exception.
     */
    public function testAuthenticationFailure()
    {
        $session = $this->getMock(SessionInterface::class);
        $request = new Request();
        $request->setSession($session);

        $response = $this->failureHandler->onAuthenticationFailure($request, new AuthenticationException());
        $this->assertNotNull($response);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
