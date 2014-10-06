<?php

namespace Inneair\SynappsBundle\Test\Security\Http\Authentication;

use Inneair\SynappsBundle\Security\Http\Authentication\RestAuthenticationSuccessHandler;
use Inneair\SynappsBundle\Test\AbstractTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * Class containing test suite for the {@link RestAuthenticationSuccessHandler} class.
 */
class RestAuthenticationSuccessHandlerTest extends AbstractTest
{
    /**
     * Authentication success handler.
     * @var RestAuthenticationSuccessHandler
     */
    private $successHandler;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->successHandler = new RestAuthenticationSuccessHandler($this->getMock(HttpUtils::class), array());
    }

    /**
     * Check a HTTP response with status code 204 is returned in case of an authentication successful.
     */
    public function testAuthenticationSuccess()
    {
        $response = $this->successHandler->onAuthenticationSuccess(new Request(), $this->getMock(TokenInterface::class));
        $this->assertNotNull($response);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
