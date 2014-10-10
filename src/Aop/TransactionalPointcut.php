<?php

namespace Inneair\SynappsBundle\Aop;

use Doctrine\Common\Annotations\Reader;
use Inneair\SynappsBundle\Annotation\Transactional;
use Inneair\SynappsBundle\Annotation\TransactionalAwareInterface;
use JMS\AopBundle\Aop\PointcutInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionMethod;

/**
 * This class defines a pointcut specification for transaction management.
 */
class TransactionalPointcut implements PointcutInterface
{
    /**
     * Annotation reader in PHP class.
     * @var Reader
     */
    private $reader;
    /**
     * Logger.
     * @var LoggerInterface
     */
    private $logger;
    /**
     * Whether target classes must also implement the {@link TransactionalAwareInterface} interface.
     * @var bool
     */
    private $strictModeEnabled;

    /**
     * Creates a transactional pointcut.
     *
     * @param Reader $reader An annotations reader.
     * @param LoggerInterface $logger Logger.
     * @param bool $strictModeEnabled Whether target classes must implement also the TransactionalAwareInterface
     * interface (defaults to <code>false</code>).
     * @see TransactionalAwareInterface
     */
    public function __construct(Reader $reader, LoggerInterface $logger, $strictModeEnabled = false)
    {
        $this->reader = $reader;
        $this->logger = $logger;
        $this->strictModeEnabled = $strictModeEnabled;
    }

    /**
     * The @Transactional annotation is enabled for all instances (if strict mode is disabled), or instances of the
     * {@link TransactionalAwareInterface} interface.
     *
     * {@inheritDoc}
     */
    public function matchesClass(ReflectionClass $class)
    {
        return ($this->strictModeEnabled ? $class->implementsInterface(TransactionalAwareInterface::class) : true);
    }

    /**
     * The @Transactional annotation is enabled for public methods of services.
     *
     * {@inheritDoc}
     */
    public function matchesMethod(ReflectionMethod $method)
    {
        $transactionalEnabled = false;
        if ($method->isPublic()) {
            // Gets method-level annotation.
            $annotation = $this->reader->getMethodAnnotation($method, Transactional::class);
            $transactionalEnabled = ($annotation !== null);
            if (!$transactionalEnabled) {
                // If there is no method-level annotation,
                // gets class-level annotation.
                $annotation = $this->reader->getClassAnnotation($method->getDeclaringClass(), Transactional::class);
                $transactionalEnabled = ($annotation !== null);
            }

            if ($transactionalEnabled) {
                $this->logger->debug(
                    'TX mode for \'' . $method->getDeclaringClass()->getName() . '::'
                        . $method->getName() . '\': ' . (int) $annotation->policy
                );
            }
        }

        return $transactionalEnabled;
    }
}
