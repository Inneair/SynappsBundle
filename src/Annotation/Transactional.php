<?php

namespace Inneair\SynappsBundle\Annotation;

use Inneair\Synapps\Exception\DataNotFoundException;

/**
 * This class handles properties of the @Transactional annotation.
 * This annotation can be applied on classes or public methods. When applied on classes, all public methods inherit this
 * setting. By default, if the annotation exists with no explicit policy, the REQUIRED policy is set.
 *
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
class Transactional
{
    /**
     * A transactional context is not required for this method execution.
     * No transactional context will be created, and the method will be executed in the outer transactional context, if
     * any.
     * @var int
     */
    const NOT_REQUIRED = 0;
    /**
     * A transactional context is required for this method execution.
     * - If there is no transactional context in the call stack, this will lead to the creation of a new transaction.
     * - If there is a transactional context in the call stack, the method will be executed in this outer transactional
     * context.
     * @var int
     */
    const REQUIRED = 1;
    /**
     * A new transactional context is required for this method execution.
     * Note that this behaviour is not strictly supported by default, and requires use of save points is enabled at
     * connection-level (see connection options).
     * - If there is no transactional context in the call stack, this will lead to the creation of a new transaction.
     * - If there is a transactional context in the call stack, this will 'only' lead to the incrementation of the
     * nesting level, and optionally to the creation of a nested transaction (with a save point), if enabled in the
     * connection configuration.
     * @var int
     */
    const REQUIRES_NEW = 2;

    /**
     * Transaction policy.
     * @var int
     */
    public $policy = self::REQUIRED;
    /**
     * An array of exceptions that will not lead to a transaction rollback, if thrown during the method execution.
     * @var array
     */
    public $noRollbackExceptions = array(DataNotFoundException::class);
}
