<?php

namespace Inneair\SynappsBundle\Serializer;

use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\VisitorInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;

/**
 * Object constructor for new (or existing) objects during deserialization, using reflection and default constructor to
 * create instances of classes.
 */
class DefaultObjectConstructor implements ObjectConstructorInterface
{
    /**
     * Fallback object constructors, in case of there is no default constructor in the class.
     * @var ObjectConstructorInterface
     */
    private $fallbackConstructor;
    /**
     * Logger
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor.
     *
     * @param ObjectConstructorInterface $fallbackConstructor Fallback object constructor (defaults to
     * <code>null</code>).
     * @param LoggerInterface $logger A logger
     */
    public function __construct(ObjectConstructorInterface $fallbackConstructor, LoggerInterface $logger)
    {
        $this->fallbackConstructor = $fallbackConstructor;
        $this->logger = $logger;
    }

    /**
     * This class tries to create a new instance using the default empty constructor, if any. Otherwise, it fallbacks to
     * the configured object constructor.
     *
     * {@inheritDoc}
     *
     * @param VisitorInterface $visitor A visitor.
     * @param ClassMetadata $metadata Class metadata.
     * @param mixed $data The visited data.
     * @param array $type The type of object to be created.
     * @param DeserializationContext $context Deserialization context.
     */
    public function construct(
        VisitorInterface $visitor,
        ClassMetadata $metadata,
        $data,
        array $type,
        DeserializationContext $context
    ) {
        try {
            $class = new ReflectionClass($metadata->name);
            $object = $class->newInstance();
        } catch (ReflectionException $e) {
            // Exception thrown if the class is not instanciable, or has no
            // default constructor (with no args).
            $this->logger->warn('Class is not instanciable (class or empty constructor not found): ' . $e);

            // Try to get the object from the fallback constructor.
            $object = $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        return $object;
    }
}
