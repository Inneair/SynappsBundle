<?php

namespace Inneair\SynappsBundle\Aop;

use CG\Proxy\MethodInvocation;
use CG\Proxy\MethodInterceptorInterface;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Exception;
use Inneair\SynappsBundle\Annotation\Transactional;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * AOP advice for transaction management in services.
 */
class TransactionalInterceptor implements MethodInterceptorInterface
{
    /**
     * Doctrine's entity manager registry.
     * @var RegistryInterface
     */
    private $entityManagerRegistry;
    /**
     * Annotations reader.
     * @var Reader
     */
    private $reader;
    /**
     * Logger.
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Creates a method interceptor managing the @Transactional annotation.
     *
     * @param RegistryInterface $entityManagerRegistry Doctrine's entity manager registry.
     * @param Reader $reader Doctrine Annotation reader.
     * @param LoggerInterface $logger Logger.
     */
    public function __construct(
        RegistryInterface $entityManagerRegistry,
        Reader $reader,
        LoggerInterface $logger
    ) {
        $this->entityManagerRegistry = $entityManagerRegistry;
        $this->reader = $reader;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     *
     * @param MethodInvocation $method Current method invocation.
     */
    public function intercept(MethodInvocation $method)
    {
        // Gets the @Transactional annotation, if existing.
        $annotation = $this->getTransactionalAnnotation($method);

        // The transactional policy is determined by the annotation, if found.
        // If missing, default behaviour is to do nothing (no transaction started).
        $policy = ($annotation === null) ? Transactional::NOT_REQUIRED : $annotation->policy;
        if ($policy === Transactional::NOT_REQUIRED) {
            // No transaction context is required, so we just invoke the target method.
            if ($annotation === null) {
                // No annotation found: there is probably a bug in the pointcut class, because the interceptor should
                // not have been invoked.
                $this->logger->warn('Transactional interceptor was invoked, but no annotation was found for method \''
                    . $method->reflection->getDeclaringClass()->getName()
                    . '::' . $method->reflection->getName() . '\''
                );
            }

            return $result = $method->proceed();
        }

        // Gets the entity manager.
        $invokingService = $method->reflection->getDeclaringClass();
        /** @var EntityManager $entityManager */
        $entityManager = $this->entityManagerRegistry->getManager();

        // Determine if a transaction must be started.
        $transactionRequired = $this->isTransactionRequired(
            $annotation->policy,
            $entityManager->getConnection()->isTransactionActive()
        );
        if ($transactionRequired) {
            // Starts a transaction.
            $this->beginTransaction($entityManager);
        }
        try {
            // Invokes the method.
            $this->logger->debug($invokingService->getName() . '::' . $method->reflection->getName());
            $result = $method->proceed();

            if ($transactionRequired) {
                // Commits the transaction.
                $this->commit($entityManager);
            }

            return $result;
        } catch (Exception $e) {
            // Manage special exceptions (commit or rollback strategy).
            if ($transactionRequired) {
                $noRollbackExceptions = ($annotation === null) ? null : $annotation->noRollbackExceptions;
                if (!empty($noRollbackExceptions) && in_array(get_class($e), $noRollbackExceptions)) {
                    // Commits the transaction.
                    $this->logger->debug('No rollback for exception ' . get_class($e));
                    $this->commit($entityManager);
                } else {
                    // Rollbacks the transaction.
                    $this->logger->debug('Exception ' . get_class($e) . ' causes rollback');
                    $this->rollback($entityManager);
                }
            }

            throw $e;
        }
    }

    /**
     * Starts a transaction on the underlying database connection of a Doctrine's entity manager.
     *
     * @param EntityManager $entityManager Entity manager
     */
    protected function beginTransaction(EntityManager $entityManager)
    {
        $this->logger->debug(static::class . '::beginTransaction');
        $entityManager->beginTransaction();
    }

    /**
     * Commits the pending transaction on the underlying database connection of a Doctrine's entity manager.
     *
     * @param EntityManager $entityManager Entity manager
     */
    protected function commit(EntityManager $entityManager)
    {
        $this->logger->debug(static::class . '::commit');
        $entityManager->flush();
        $entityManager->commit();
    }

    /**
     * Closes the entity manager, and rollbacks the pending transaction on the underlying database connection of a
     * Doctrine's entity manager with the given name. This method also resets the manager, so as it can be recreated for
     * a new transaction, when needed.
     *
     * @param EntityManager $entityManager Entity manager
     */
    protected function rollback(EntityManager $entityManager)
    {
        $this->logger->debug(static::class . '::rollback');
        $entityManager->rollback();

        // Close the manager if there is no transaction started.
        if (!$entityManager->getConnection()->isTransactionActive()) {
            $entityManager->close();
            $this->entityManagerRegistry->resetManager();
        }
    }

    /**
     * Gets the @Transactional annotation, if any, looking at method level as a priority, then at class level.
     *
     * @param MethodInvocation $method Current method invocation.
     * @return Transactional The transaction annotation, or <code>null</code> if not found.
     */
    protected function getTransactionalAnnotation(MethodInvocation $method)
    {
        $annotation = $this->reader->getMethodAnnotation($method->reflection, Transactional::class);
        if ($annotation === null) {
            // If there is no method-level annotation, gets class-level annotation.
            $annotation = $this->reader->getClassAnnotation(
                $method->reflection->getDeclaringClass(),
                Transactional::class
            );
        }
        return $annotation;
    }

    /**
     * Tells whether a transaction must be started, depending on the configured policy and the current TX status.
     *
     * @param int $policy One of the policy defined in the Transactional annotation.
     * @param bool $isTransactionActive Whether a transaction is already active when invoking a method.
     * @return bool <code>true</code> if a new transaction is required, <code>false</code> otherwise.
     */
    protected function isTransactionRequired($policy, $isTransactionActive)
    {
        return ($policy === Transactional::REQUIRES_NEW)
            || (($policy === Transactional::REQUIRED) && !$isTransactionActive);
    }
}
