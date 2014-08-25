<?php

namespace Inneair\SynappsBundle\Test;

use Inneair\SynappsBundle\Util\SerializerUtils;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Base class for functional web tests.
 */
abstract class AbstractWebTest extends WebTestCase
{
    /**
     * Environment name.
     * @var string
     */
    private static $environment;

    /**
     * Initializes Symfony kernel.
     */
    public function setUp()
    {
        self::$environment = (isset($_ENV['SYMFONY_ENVIRONMENT'])) ? $_ENV['SYMFONY_ENVIRONMENT'] : 'test';

        static::$kernel = static::createKernel();
        static::$kernel->boot();
    }

    /**
     * {@inheritDoc}
     */
    protected static function createKernel(array $options = array())
    {
        // Force using the environment we specify with a system variable.
        if (!isset($options['environment'])) {
            $options['environment'] = self::$environment;
        }
        return parent::createKernel($options);
    }

    /**
     * Logs a message (requires Symfony kernel is initialized).
     *
     * @param string $message A message.
     * @param Client $client An HTTP client (defaults to <code>null</code>).
     */
    protected static function debug($message, Client $client = null)
    {
        static::get('logger', $client)->debug($message);
    }

    /**
     * Gets a service from its ID (requires Symfony kernel is initialized).
     *
     * @param string $serviceId Service ID.
     * @param Client $client An HTTP client (defaults to <code>null</code>).
     * @return Object A service instance, or <code>null</code> if no Service
     * has the given ID.
     */
    protected static function get($serviceId, Client $client = null)
    {
        if ($client === null) {
            return static::$kernel->getContainer()->get($serviceId);
        } else {
            return $client->getContainer()->get($serviceId);
        }
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string $route The name of the route
     * @param Client $client An HTTP client (defaults to <code>null</code>).
     * @param mixed $parameters An array of parameters
     * @param boolean|string $referenceType The type of reference (one of the
     * constants in UrlGeneratorInterface)
     * @return string The generated URL
     * @see UrlGeneratorInterface
     */
    protected static function generateUrl(
        $route,
        $parameters = array(),
        Client $client = null,
        $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ) {
        return static::get('router', $client)->generate($route, $parameters, $referenceType);
    }

    /**
     * Asserts the given HTTP response has the expected HTTP status code, and tries to deserialize its content.
     *
     * @param Response $response HTTP response.
     * @param int $statusCode HTTP status code.
     * @param string $contentClassName Qualified name of the class that will be instanciated and populated with
     * deserialized content (defaults to <code>null</code>, which means no deserialization is attempted).
     * @param string $contentFormat Content format (must be one of the FORMAT_* constants, defaults to JSON).
     * @return mixed The response content, optionally deserialized in the given class, if provided.
     * @see SerializerUtils
     */
    protected function assertHttpResponse(
        Response $response,
        $statusCode,
        $contentClassName = null,
        $contentFormat = SerializerUtils::FORMAT_JSON
    ) {
        $this->assertEquals($statusCode, $response->getStatusCode());
        $content = $response->getContent();

        if ($contentClassName !== null) {
            $serializer = static::get('inneair_synapps.serializer');
            $content = $serializer->deserialize($content, $contentClassName, $contentFormat);
        }

        return $content;
    }
}
