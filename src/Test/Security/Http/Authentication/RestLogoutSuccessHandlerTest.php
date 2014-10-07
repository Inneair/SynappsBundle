<?php

namespace Inneair\SynappsBundle\Test\Security\Http\Authentication;

use Inneair\SynappsBundle\Security\Http\Authentication\RestLogoutSuccessHandler;
use Inneair\SynappsBundle\Test\AbstractTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * Class containing test suite for the {@link RestLogoutSuccessHandler} class.
 */
class RestLogoutSuccessHandlerTest extends AbstractTest
{
    /**
     * Logout success handler.
     * @var RestLogoutSuccessHandler
     */
    private $successHandler;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->successHandler = new RestLogoutSuccessHandler($this->getMock(HttpUtils::class));
    }

    /**
     * Check a HTTP response with status code 204 is returned in case of successful logout.
     */
    public function testLogoutSuccess()
    {
        $response = $this->successHandler->onLogoutSuccess(new Request());
        $this->assertNotNull($response);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->assertEmpty($response->getContent());
    }
}
