<?php

namespace Inneair\SynappsBundle\Test\Aop;

use CG\Proxy\MethodInvocation;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Annotations\Reader;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Exception;
use Inneair\Synapps\Exception\DataNotFoundException;
use Inneair\SynappsBundle\Annotation\Transactional;
use Inneair\SynappsBundle\Aop\TransactionalInterceptor;
use Inneair\SynappsBundle\Entity\DefaultEntityRepository;
use Inneair\SynappsBundle\Test\AbstractTest;
use Inneair\SynappsBundle\Test\Aop\Fixture\InterceptedClass;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class containing test suite for the {@link TransactionalInterceptor} class.
 */
class TransactionalInterceptorTest extends AbstractTest
{
    /**
     * Mocked entity manager.
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManager;
    /**
     * Mocked entity manager registry.
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerRegistry;
    /**
     * Mocked DB connection.
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $connection;
    /**
     * Mocked logger.
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $logger;
    /**
     * Mocked annotation reader.
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $reader;
    /**
     * Mocked repository.
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;
    /**
     * Transactional interceptor.
     * @var TransactionalInterceptor
     */
    private $transactionalInterceptor;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->connection = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();

        $this->repository = $this->getMockBuilder(DefaultEntityRepository::class)->disableOriginalConstructor()
            ->getMock();

        $this->entityManager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $this->entityManager->expects($this->any())->method('getConnection')->willReturn($this->connection);
        $this->entityManager->expects($this->any())->method('getRepository')->willReturn($this->repository);

        $this->entityManagerRegistry = $this->getMock(RegistryInterface::class);
        $this->entityManagerRegistry->expects(static::any())->method('getManager')->willReturn($this->entityManager);

        $this->logger = $this->getMock(LoggerInterface::class);

        $this->reader = $this->getMock(Reader::class);

        $this->transactionalInterceptor = new TransactionalInterceptor(
            $this->entityManagerRegistry,
            $this->reader,
            $this->logger
        );
    }

    /**
     * Invokes the interceptor for an annotated method (transaction required), in an unannotated class. The method will
     * invoke a nested method (new transaction required), that should lead to opening two nested transactions.
     */
    public function testNestedNewRequiredTransactionForAnnotatedMethodInUnannotatedClass()
    {
        $class = new ReflectionClass(InterceptedClass::class);
        $instance = $class->newInstance();

        $this->reader->expects(static::never())->method('getClassAnnotation');

        // Method 'cMethod' requires a transaction.
        $annotationRequired = new Transactional();
        $annotationRequired->policy = Transactional::REQUIRED;
        // Nested method 'aMethod' requires a new transaction whatever the transactional context.
        $annotationRequiresNew = new Transactional();
        $annotationRequiresNew->policy = Transactional::REQUIRES_NEW;

        $this->reader->expects(static::exactly(2))->method('getMethodAnnotation')->willReturnOnConsecutiveCalls(
            $annotationRequired,
            $annotationRequiresNew
        );

        $this->entityManager->expects(static::exactly(2))->method('beginTransaction');
        $this->entityManager->expects(static::exactly(2))->method('commit');
        $this->entityManager->expects(static::never())->method('rollback');

        $nestedTransaction = function($class, $instance) {
            $this->transactionalInterceptor->intercept(new MethodInvocation(
                $class->getMethod('aMethod'),
                $instance,
                array(),
                array()
            ));
        };

        $this->assertNull($this->transactionalInterceptor->intercept(new MethodInvocation(
            $class->getMethod('cMethod'),
            $instance,
            array($nestedTransaction, array($class, $instance)),
            array()
        )));
    }

    /**
     * Invokes the interceptor for an annotated method (transaction required), in an unannotated class. The method will
     * invoke a nested method (new transaction required), that should lead to opening two nested transactions.
     */
    public function testNestedNewRequiredTransactionForUnannotatedMethodInAnnotatedClass()
    {
        $class = new ReflectionClass(InterceptedClass::class);
        $instance = $class->newInstance();

        $this->reader->expects(static::exactly(2))->method('getMethodAnnotation')->willReturn(null);

        // Method 'cMethod' requires a transaction.
        $annotationRequired = new Transactional();
        $annotationRequired->policy = Transactional::REQUIRED;

        // Nested method 'aMethod' requires a new transaction whatever the transactional context.
        $annotationRequiresNew = new Transactional();
        $annotationRequiresNew->policy = Transactional::REQUIRES_NEW;

        $this->reader->expects(static::exactly(2))->method('getClassAnnotation')->willReturnOnConsecutiveCalls(
            $annotationRequired,
            $annotationRequiresNew
        );

        $this->entityManager->expects(static::exactly(2))->method('beginTransaction');
        $this->entityManager->expects(static::exactly(2))->method('commit');
        $this->entityManager->expects(static::never())->method('rollback');

        $nestedTransaction = function($class, $instance) {
            $this->transactionalInterceptor->intercept(new MethodInvocation(
                $class->getMethod('aMethod'),
                $instance,
                array(),
                array()
            ));
        };

        $this->assertNull($this->transactionalInterceptor->intercept(new MethodInvocation(
            $class->getMethod('cMethod'),
            $instance,
            array($nestedTransaction, array($class, $instance)),
            array()
        )));
    }

    /**
     * Invokes the interceptor for an annotated method (transaction not required), in an unannotated class.
     */
    public function testNotRequiredTransactionForAnnotatedMethodInUnannotatedClass()
    {
        $class = new ReflectionClass(InterceptedClass::class);
        $instance = $class->newInstance();

        $annotation = new Transactional();
        $annotation->policy = Transactional::NOT_REQUIRED;
        $this->reader->expects(static::once())->method('getMethodAnnotation')->willReturn($annotation);
        $this->reader->expects(static::never())->method('getClassAnnotation');
        $this->entityManager->expects(static::never())->method('beginTransaction');
        $this->entityManager->expects(static::never())->method('commit');
        $this->entityManager->expects(static::never())->method('rollback');

        $this->assertNull($this->transactionalInterceptor->intercept(new MethodInvocation(
            $class->getMethod('aMethod'),
            $instance,
            array(),
            array()
        )));
    }

    /**
     * Invokes the interceptor for an annotated method (transaction not required), in an unannotated class.
     */
    public function testNotRequiredTransactionForUnannotatedMethodInAnnotatedClass()
    {
        $class = new ReflectionClass(InterceptedClass::class);
        $instance = $class->newInstance();

        $annotation = new Transactional();
        $annotation->policy = Transactional::NOT_REQUIRED;
        $this->reader->expects(static::once())->method('getMethodAnnotation')->willReturn(null);
        $this->reader->expects(static::once())->method('getClassAnnotation')->willReturn($annotation);
        $this->entityManager->expects(static::never())->method('beginTransaction');
        $this->entityManager->expects(static::never())->method('commit');
        $this->entityManager->expects(static::never())->method('rollback');

        $this->assertNull($this->transactionalInterceptor->intercept(new MethodInvocation(
            $class->getMethod('aMethod'),
            $instance,
            array(),
            array()
        )));
    }

    /**
     * Invokes the interceptor for an annotated method (transaction required), in an unannotated class.
     */
    public function testRequiredTransactionForAnnotatedMethodInUnannotatedClass()
    {
        $class = new ReflectionClass(InterceptedClass::class);
        $instance = $class->newInstance();

        $annotation = new Transactional();
        $annotation->policy = Transactional::REQUIRED;
        $this->reader->expects(static::once())->method('getMethodAnnotation')->willReturn($annotation);
        $this->reader->expects(static::never())->method('getClassAnnotation');
        $this->entityManager->expects(static::once())->method('beginTransaction');
        $this->entityManager->expects(static::once())->method('commit');
        $this->entityManager->expects(static::never())->method('rollback');

        $this->assertNull($this->transactionalInterceptor->intercept(new MethodInvocation(
            $class->getMethod('aMethod'),
            $instance,
            array(),
            array()
        )));
    }

    /**
     * Invokes the interceptor for an annotated method (transaction required), in an unannotated class. The method
     * throws an exception that is ignored by the interceptor, and the transaction is committed.
     */
    public function testRequiredTransactionForAnnotatedMethodInUnannotatedClassWithIgnoredException()
    {
        $class = new ReflectionClass(InterceptedClass::class);
        $instance = $class->newInstance();

        $annotation = new Transactional();
        $annotation->policy = Transactional::REQUIRED;
        $this->reader->expects(static::once())->method('getMethodAnnotation')->willReturn($annotation);
        $this->reader->expects(static::never())->method('getClassAnnotation');
        $this->entityManager->expects(static::once())->method('beginTransaction');
        $this->entityManager->expects(static::once())->method('commit');
        $this->entityManager->expects(static::never())->method('rollback');

        $hasException = false;
        try {
            $this->transactionalInterceptor->intercept(new MethodInvocation(
                $class->getMethod('bMethodThrowException'),
                $instance,
                array(DataNotFoundException::class),
                array()
            ));
        } catch (DataNotFoundException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);
    }

    /**
     * Invokes the interceptor for an annotated method (transaction required), in an unannotated class. The method
     * throws an exception that rollbacks the transaction.
     */
    public function testRequiredTransactionForAnnotatedMethodInUnannotatedClassWithException()
    {
        $class = new ReflectionClass(InterceptedClass::class);
        $instance = $class->newInstance();

        $annotation = new Transactional();
        $annotation->policy = Transactional::REQUIRED;
        $this->reader->expects(static::once())->method('getMethodAnnotation')->willReturn($annotation);
        $this->reader->expects(static::never())->method('getClassAnnotation');
        $this->entityManager->expects(static::once())->method('beginTransaction');
        $this->entityManager->expects(static::never())->method('commit');
        $this->entityManager->expects(static::once())->method('rollback');

        $hasException = false;
        try {
            $this->transactionalInterceptor->intercept(new MethodInvocation(
                $class->getMethod('bMethodThrowException'),
                $instance,
                array(Exception::class),
                array()
            ));
        } catch (Exception $e) {
            $hasException = true;
        }
        $this->assertException($hasException);
    }

    /**
     * Invokes the interceptor for an unannotated method, in an annotated class (transaction required).
     */
    public function testRequiredTransactionForUnannotatedMethodInAnnotatedClass()
    {
        $class = new ReflectionClass(InterceptedClass::class);
        $instance = $class->newInstance();

        $annotation = new Transactional();
        $annotation->policy = Transactional::REQUIRED;
        $this->reader->expects(static::once())->method('getMethodAnnotation')->willReturn(null);
        $this->reader->expects(static::once())->method('getClassAnnotation')->willReturn($annotation);
        $this->entityManager->expects(static::once())->method('beginTransaction');
        $this->entityManager->expects(static::once())->method('commit');
        $this->entityManager->expects(static::never())->method('rollback');

        $this->assertNull($this->transactionalInterceptor->intercept(new MethodInvocation(
            $class->getMethod('aMethod'),
            $instance,
            array(),
            array()
        )));
    }

    /**
     * Invokes the interceptor for an annotated method (transaction required), in an unannotated class. The method
     * throws an exception that is ignored by the interceptor, and the transaction is committed.
     */
    public function testRequiredTransactionForUnannotatedMethodInAnnotatedClassWithIgnoredException()
    {
        $class = new ReflectionClass(InterceptedClass::class);
        $instance = $class->newInstance();

        $annotation = new Transactional();
        $annotation->policy = Transactional::REQUIRED;
        $this->reader->expects(static::once())->method('getMethodAnnotation')->willReturn(null);
        $this->reader->expects(static::once())->method('getClassAnnotation')->willReturn($annotation);
        $this->entityManager->expects(static::once())->method('beginTransaction');
        $this->entityManager->expects(static::once())->method('commit');
        $this->entityManager->expects(static::never())->method('rollback');

        $hasException = false;
        try {
            $this->transactionalInterceptor->intercept(new MethodInvocation(
                $class->getMethod('bMethodThrowException'),
                $instance,
                array(DataNotFoundException::class),
                array()
            ));
        } catch (DataNotFoundException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);
    }

    /**
     * Invokes the interceptor for an annotated method (transaction required), in an unannotated class. The method
     * throws an exception that rollbacks the transaction.
     */
    public function testRequiredTransactionForUnannotatedMethodInAnnotatedClassWithException()
    {
        $class = new ReflectionClass(InterceptedClass::class);
        $instance = $class->newInstance();

        $annotation = new Transactional();
        $annotation->policy = Transactional::REQUIRED;
        $this->reader->expects(static::once())->method('getMethodAnnotation')->willReturn(null);
        $this->reader->expects(static::once())->method('getClassAnnotation')->willReturn($annotation);
        $this->entityManager->expects(static::once())->method('beginTransaction');
        $this->entityManager->expects(static::never())->method('commit');
        $this->entityManager->expects(static::once())->method('rollback');

        $hasException = false;
        try {
            $this->transactionalInterceptor->intercept(new MethodInvocation(
                $class->getMethod('bMethodThrowException'),
                $instance,
                array(Exception::class),
                array()
            ));
        } catch (Exception $e) {
            $hasException = true;
        }
        $this->assertException($hasException);
    }

    /**
     * Invokes the interceptor for a method in a class, both unannotated.
     */
    public function testUnannotatedMethodInUnannotatedClass()
    {
        $class = new ReflectionClass(InterceptedClass::class);
        $instance = $class->newInstance();

        $this->reader->expects(static::once())->method('getMethodAnnotation')->willReturn(null);
        $this->reader->expects(static::once())->method('getClassAnnotation')->willReturn(null);
        $this->logger->expects(static::once())->method('warning');
        $this->entityManager->expects(static::never())->method('beginTransaction');
        $this->entityManager->expects(static::never())->method('commit');
        $this->entityManager->expects(static::never())->method('rollback');

        $this->assertNull($this->transactionalInterceptor->intercept(new MethodInvocation(
            $class->getMethod('aMethod'),
            $instance,
            array(),
            array()
        )));
    }
}
