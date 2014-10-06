<?php

namespace Inneair\SynappsBundle\Test\Security\Http\Firewall;

use Exception;
use Inneair\SynappsBundle\Security\Http\Firewall\ExceptionListener;
use Inneair\SynappsBundle\Test\AbstractTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class containing test suite for the {@link ExceptionListener} class.
 */
class ExceptionListenerTest extends AbstractTest
{
    /**
     * Exception listener.
     * @var ExceptionListener
     */
    private $exceptionListener;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
    
        $this->exceptionListener = new ExceptionListener(
            $this->getMock(SecurityContextInterface::class),
            $this->getMock(AuthenticationTrustResolverInterface::class),
            $this->getMock(HttpUtils::class),
            null
        );
    }

    /**
     * Check a HTTP response with status code 401 is returned in case of an authentication exception.
     */
    public function testListenAuthenticationException()
    {
        $event = new GetResponseForExceptionEvent(
            $this->getMock(HttpKernelInterface::class),
            new Request(),
            HttpKernelInterface::MASTER_REQUEST,
            new AuthenticationException()
        );
        $this->exceptionListener->onKernelException($event);
        $this->assertNotNull($event->getResponse());
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $event->getResponse()->getStatusCode());
    }

    /**
     * Check a HTTP response with status code 401 is returned in case of an authentication exception.
     */
    public function testListenUnknownException()
    {
        $event = new GetResponseForExceptionEvent(
            $this->getMock(HttpKernelInterface::class),
            new Request(),
            HttpKernelInterface::MASTER_REQUEST,
            new Exception()
        );
        $this->exceptionListener->onKernelException($event);
        $this->assertNull($event->getResponse());
    }
}
