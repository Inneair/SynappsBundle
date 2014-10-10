<?php

namespace Inneair\SynappsBundle\Test\Aop;

use Doctrine\Common\Annotations\Reader;
use Inneair\SynappsBundle\Aop\TransactionalPointcut;
use Inneair\SynappsBundle\Test\AbstractTest;
use Inneair\SynappsBundle\Test\Aop\Fixture\NonTransactionalAwareClass;
use Inneair\SynappsBundle\Test\Aop\Fixture\TransactionalAwareClass;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use Inneair\SynappsBundle\Annotation\Transactional;

/**
 * Class containing test suite for the {@link TransactionalPointcut} class.
 */
class TransactionalPointcutTest extends AbstractTest
{
    /**
     * Mocked logger.
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $logger;
    /**
     * Transactional pointcut.
     * @var TransactionalPointcut
     */
    private $transactionalPointcut;
    /**
     * Mocked annotation reader.
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $reader;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->logger = $this->getMock(LoggerInterface::class);

        $this->reader = $this->getMock(Reader::class);

        $this->transactionalPointcut = new TransactionalPointcut(
            $this->reader,
            $this->logger,
            true
        );
    }

    /**
     * Tests the pointcut deals with non transactional aware class, when strict mode is not enabled.
     */
    public function testMatchesNonTransactionalAwareClassWithoutStrictMode()
    {
        $transactionalPointcut = new TransactionalPointcut(
            $this->reader,
            $this->logger,
            false
        );
        $this->assertTrue($transactionalPointcut->matchesClass(new ReflectionClass(
            NonTransactionalAwareClass::class
        )));
    }

    /**
     * Tests the pointcut does not deal with non transactional aware class, when strict mode is enabled.
     */
    public function testMatchesNonTransactionalAwareClassWithStrictMode()
    {
        $this->assertFalse($this->transactionalPointcut->matchesClass(new ReflectionClass(
            NonTransactionalAwareClass::class
        )));
    }

    /**
     * Tests the pointcut deals with transactional aware class, when strict mode is enabled.
     */
    public function testMatchesTransactionalAwareClassWithStrictMode()
    {
        $this->assertTrue($this->transactionalPointcut->matchesClass(new ReflectionClass(
            TransactionalAwareClass::class
        )));
    }

    /**
     * Tests the pointcut does not deal with non-public methods.
     */
    public function testMatchesNonPublicMethod()
    {
        $class = new ReflectionClass(TransactionalAwareClass::class);
        $this->assertFalse($this->transactionalPointcut->matchesMethod($class->getMethod('nonPublicMethod')));
    }

    /**
     * Tests the pointcut does not deal with non-public methods.
     */
    public function testMatchesAnnotatedPublicMethod()
    {
        $this->reader->expects(static::once())->method('getMethodAnnotation')->willReturn(new Transactional());
        $this->reader->expects(static::never())->method('getClassAnnotation');

        $class = new ReflectionClass(TransactionalAwareClass::class);
        $this->assertTrue($this->transactionalPointcut->matchesMethod($class->getMethod('publicMethod')));
    }

    /**
     * Tests the pointcut does not deal with non-public methods.
     */
    public function testMatchesUnannotatedPublicMethodInAnnotatedClass()
    {
        $this->reader->expects(static::once())->method('getMethodAnnotation')->willReturn(null);
        $this->reader->expects(static::once())->method('getClassAnnotation')->willReturn(new Transactional());

        $class = new ReflectionClass(TransactionalAwareClass::class);
        $this->assertTrue($this->transactionalPointcut->matchesMethod($class->getMethod('publicMethod')));
    }

    /**
     * Tests the pointcut does not deal with non-public methods.
     */
    public function testMatchesUnannotatedPublicMethodInUnannotatedClass()
    {
        $this->reader->expects(static::once())->method('getMethodAnnotation')->willReturn(null);
        $this->reader->expects(static::once())->method('getClassAnnotation')->willReturn(null);

        $class = new ReflectionClass(TransactionalAwareClass::class);
        $this->assertFalse($this->transactionalPointcut->matchesMethod($class->getMethod('publicMethod')));
    }
}
