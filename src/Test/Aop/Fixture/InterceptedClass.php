<?php

namespace Inneair\SynappsBundle\Test\Aop\Fixture;

use Closure;
use Inneair\SynappsBundle\Aop\TransactionalInterceptor;
use ReflectionClass;

/**
 * A class intercepted by the {@link TransactionalInterceptor}. 
 */
class InterceptedClass
{
    /**
     * A method returing a value.
     *
     * @return null Null value.
     */
    public function aMethod()
    {
        return null;
    }

    /**
     * A method throwing an exception.
     *
     * @param string $exceptionClass Exception class to be thrown.
     * @throws Exception The thrown exception.
     */
    public function bMethodThrowException($exceptionClass)
    {
        $reflectedException = new ReflectionClass($exceptionClass);
        throw $reflectedException->newInstance();
    }

    /**
     * A method returning the result of another method (to check nested transactions).
     *
     * @param Closure $nestedCallback Nested [@link TransactionalInterceptor::intercept} call.
     * @param array $parameters Parameters for the nested call.
     * @return null Null value.
     */
    public function cMethod(Closure $nestedCallback, array $parameters = null)
    {
        return call_user_func_array($nestedCallback, $parameters);
    }
}
