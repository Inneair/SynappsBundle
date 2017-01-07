<?php

namespace Inneair\SynappsBundle\Test\Controller;

use Inneair\SynappsBundle\Test\AbstractTest;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Base class for unit tests of controllers.
 */
abstract class AbstractUnitTest extends AbstractTest
{
    /**
     * Mocked service container.
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $container;
    /**
     * Mocked form factory.
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $formFactory;
    /**
     * Mocked logger.
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $logger;
    /**
     * Mocked router.
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $router;
    /**
     * Mocked translator.
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $translator;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->expects(static::any())->method('get')->willReturnCallback(array($this, 'getComponent'));

        parent::setUp();
    }

    /**
     * Callback used by the dependency container, to load required components.
     *
     * @param string $id Component ID.
     * @return object The component, or <code>null</code> if the ID is unknown.
     */
    public function getComponent($id)
    {
        switch ($id) {
            case 'form.factory':
                $component = $this->formFactory;
                break;
            case 'logger':
                $component = $this->logger;
                break;
            case 'router':
                $component = $this->router;
                break;
            case 'translator':
                $component = $this->translator;
                break;
            default:
                $component = null;
                break;
        }

        return $component;
    }

    /**
     * Gets the mocked service container.
     *
     * @return PHPUnit_Framework_MockObject_MockObject The mocked service container.
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Gets a controller instance.
     *
     * @param string $className Full class name of the controller.
     * @param array $controllerArgs Optional arguments for the controller constructor (defaults to <code>null</code>).
     * @return AbstractController The controller instance.
     * @throws ReflectionException If the controller cannot be instanciated.
     */
    protected function getController($className, array $controllerArgs = null)
    {
        $class = new ReflectionClass($className);
        if ($controllerArgs === null) {
            $controller = $class->newInstance();
        } else {
            $controller = $class->newInstanceArgs($controllerArgs);
        }
        $controller->setContainer($this->getContainer());
        $controller->setTranslator($this->getTranslator());
        $controller->setLogger($this->getLogger());
        $controller->init();
        return $controller;
    }

    /**
     * Gets the mocked form factory.
     *
     * @return PHPUnit_Framework_MockObject_MockObject Mocked form factory.
     */
    protected function getFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * Gets the mocked logger.
     *
     * @return PHPUnit_Framework_MockObject_MockObject The mocked logger.
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    /**
     * Gets the mocked router.
     *
     * @return PHPUnit_Framework_MockObject_MockObject Mocked router.
     */
    protected function getRouter()
    {
        return $this->router;
    }

    /**
     * Gets the mocked translation service.
     *
     * @return PHPUnit_Framework_MockObject_MockObject The mocked translation service.
     */
    protected function getTranslator()
    {
        return $this->translator;
    }
}
