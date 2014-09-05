<?php

namespace Inneair\SynappsBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\View\View;
use FOS\UserBundle\Model\UserInterface;
use Inneair\SynappsBundle\Controller\AbstractController;
use Inneair\SynappsBundle\Entity\Account;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Web interface for accounts management.
 * @Route(service="inneair_synapps.accountcontroller")
 */
class AccountController extends AbstractController
{
    /**
     * Creates a controller for accounts management with HTTP.
     */
    public function __construct()
    {
    }

    /**
     * Action called to get a content type.
     *
     * @return View|Account An error view, or the account found.
     * @RestView
     */
    public function getAction()
    {
        $account = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($account) || !$account instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $account;
    }
}
