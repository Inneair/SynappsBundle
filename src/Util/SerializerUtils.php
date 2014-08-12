<?php

namespace Inneair\SynappsBundle\Util;

/**
 * Class containing helper functions for serialization purposes.
 */
final class SerializerUtils
{
    /**
     * The JSON format.
     * @var string
     */
    const FORMAT_JSON = 'json';
    /**
     * The XML format.
     * @var string
     */
    const FORMAT_XML = 'xml';
    /**
     * The YAML format.
     * @var string
     */
    const FORMAT_YAML = 'yaml';

    /**
     * Empty private constructor to prevent erroneous instanciations.
     */
    private function __construct()
    {
    }
}
