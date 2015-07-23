<?php

namespace Blog\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BlogNewsBundle:Default:index.html.twig', array('name' => $name));
    }
}
