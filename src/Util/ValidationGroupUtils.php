<?php

namespace Inneair\SynappsBundle\Util;

/**
 * Class containing predefined validation groups, that can be used in validation constraints definition.
 */
final class ValidationGroupUtils
{
    /**
     * Group name for constraints to be used for ID properties.
     * @var string
     */
    const GROUP_ID = 'Id';
    /**
     * Group name for other constraints (no need to be set in the constraint definition).
     * @var string
     */
    const GROUP_OTHER_FIELDS = 'Default';

    /**
     * Group set dedicated to create operations.
     * @var string[]
     */
    private static $createGroupSet = array(self::GROUP_OTHER_FIELDS);
    /**
     * Group set dedicated to delete operations.
     * @var string[]
     */
    private static $deleteGroupSet = array(self::GROUP_ID);
    /**
     * Group set dedicated to other operations than create or delete.
     * @var string[]
     */
    private static $defaultGroupSet = array(self::GROUP_ID, self::GROUP_OTHER_FIELDS);

    /**
     * Empty private constructor to prevent erroneous instanciations.
     */
    private function __construct()
    {
    }

    /**
     * Gets the group set dedicated to validate data for create operations.
     *
     * @return string[] Array of group names.
     */
    public static function getCreateGroupSet()
    {
        return self::$createGroupSet;
    }

    /**
     * Gets the group set dedicated to validate data for delete operations.
     *
     * @return string[] Array of group names.
     */
    public static function getDeleteGroupSet()
    {
        return self::$deleteGroupSet;
    }

    /**
     * Gets the group set dedicated to validate data for other operations than create or delete.
     *
     * @return string[] Array of group names.
     */
    public static function getDefaultGroupSet()
    {
        return self::$defaultGroupSet;
    }
}
