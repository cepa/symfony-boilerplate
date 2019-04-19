<?php

namespace Portal\Controller;

use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="portal_index")
     */
    public function index()
    {
        return $this->render('portal/index.html.twig');
    }
}
