<?php

namespace Blog\ManageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{

    public function indexAction()
    {
    	$em = $this->getDoctrine()->getManager();

    	$entities = $em->getRepository('BlogUserBundle:User')->findAll();

        return $this->render('BlogManageBundle:User:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    public function searchAction()
    {
      $em = $this->getDoctrine()->getManager();
      $query = $this->getRequest()->get('query');

        if(!$query) {
            return $this->redirect($this->generateUrl('admin_user'));
        }

      $entity = $em->getRepository('BlogUserBundle:User')->findOneByUsername($query);

        return $this->render('BlogManageBundle:User:search.html.twig', array(
            'entity' => $entity,
        ));
    }

    public function journalAction($user_id)
    {
    	$em = $this->getDoctrine()->getManager();

    	$entities = $em->getRepository('BlogBlogBundle:Diary')->findBy(array('user_id' => $user_id));

    	$user = $em->getRepository('BlogUserBundle:User')->find($user_id);

        return $this->render('BlogManageBundle:User:journal.html.twig', array(
            'entities' => $entities,
            'user'	=> $user,
        ));
    }

    public function blogAction($user_id)
    {
    	$em = $this->getDoctrine()->getManager();

    	$entities = $em->getRepository('BlogBlogBundle:Blog')->findBy(array('user_id' => $user_id));

    	$user = $em->getRepository('BlogUserBundle:User')->find($user_id);

        return $this->render('BlogManageBundle:User:blog.html.twig', array(
            'entities' => $entities,
            'user'	=> $user,
        ));
    }

}
