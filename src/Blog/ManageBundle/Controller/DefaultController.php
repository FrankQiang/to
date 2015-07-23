<?php

namespace Blog\ManageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BlogManageBundle:Default:index.html.twig', array('name' => $name));
    }
}
