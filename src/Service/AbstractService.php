<?php

namespace Inneair\SynappsBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Inneair\TransactionBundle\Annotation\TransactionalAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Abstract base implementation of an Inneair service.
 */
abstract class AbstractService implements TransactionalAwareInterface
{
    /**
     * Event dispatcher.
     * @var EventDispatcherInterface
     */
    protected $dispatcher;
    /**
     * Doctrine's entity manager registry.
     * @var RegistryInterface
     */
    protected $entityManagerRegistry;
    /**
     * Logger
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Sets the event dispatcher.
     *
     * NOTE: this method is automatically invoked by the framework, and should never be called manually.
     *
     * @param EventDispatcherInterface $dispatcher Event dispatcher.
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Sets the entity manager registry.
     *
     * NOTE: this method is automatically invoked by the framework, and should never be called manually.
     *
     * @param RegistryInterface $entityManagerRegistry Registry.
     */
    public function setEntityManagerRegistry(RegistryInterface $entityManagerRegistry)
    {
        $this->entityManagerRegistry = $entityManagerRegistry;
    }

    /**
     * Sets the logging service.
     *
     * NOTE: this method is automatically invoked by the framework, and should never be called manually.
     *
     * @param LoggerInterface $logger Logging service.
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Initializes the service.
     *
     * NOTE: this method is automatically invoked by the framework, and should never be called manually.
     * Actually, this method does nothing but logging the service is ready. It may be overridden by concrete services,
     * to perform additional initializations other than raw instantiations (but calling this parent method is always
     * mandatory to ensure forward compatibility).
     */
    public function init()
    {
        $this->logger->debug('Service \'' . static::class . '\' is up.');
    }

    /**
     * Gets the Doctrine's entity manager.
     *
     * @return EntityManagerInterface|ObjectManager A Doctrine's entity manager.
     */
    protected function getEntityManager()
    {
        return $this->entityManagerRegistry->getManager();
    }
}
