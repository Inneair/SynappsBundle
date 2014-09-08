<?php

namespace Inneair\SynappsBundle\Test\Security;

use Inneair\SynappsBundle\Test\AbstractTest;

/**
 * Test case to check authentication inside secured entry points.
 */
class AuthenticatedRequestTest extends AbstractTest
{
    /**
     * Checks an unauthenticated request leads to a 401 HTTP error.
     */
    public function testUnauthenticatedRequest()
    {
    }

    /**
     * Checks an authenticated request leads to a controller call.
     */
    public function testAuthenticatedRequest()
    {
    }
}
