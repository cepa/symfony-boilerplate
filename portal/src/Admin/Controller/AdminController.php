<?php

namespace Admin\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends \EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController
{
    use AdminTrait;

    /**
     * @Route("/", name="easyadmin")
     * @Route("/", name="admin")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        return parent::indexAction($request);
    }

    /**
     * Override for soft delete.
     * @param object $entity
     */
    protected function removeEntity($entity)
    {
        if (method_exists($entity, 'setIsDeleted')) {
            $entity->setIsDeleted(true);
        }
        $this->updateEntity($entity);
    }
}
