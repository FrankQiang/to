<?php

namespace Blog\WoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Search controller.
 *
 */
class SearchController extends Controller
{

	public function blogAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $this->getRequest()->get('query');

        if(!$query) {
            return $this->redirect($this->generateUrl('blog_wo_homepage'));
        }

        $entities = $em->getRepository('BlogBlogBundle:Blog')->getForLuceneQuery($query);

        for ($i=0; $i < count($entities); $i++) { 
            $entities[$i]->setBlog(mb_strcut(preg_replace('/\*||\#||\>||\`||\[\S*\)/i','',$entities[$i]->getBlog()),0,260,'utf-8').'...');
        }
        
        return $this->render('BlogWoBundle:Search:search_blog.html.twig', array('entities' => $entities));
    }

    public function journalAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $this->getRequest()->get('query');

        if(!$query) {
            return $this->redirect($this->generateUrl('blog_wo_homepage'));
        }

        $entities = $em->getRepository('BlogBlogBundle:Diary')->getForLuceneQuery($query);

        for ($i=0; $i < count($entities); $i++) { 
            $entities[$i]->setBlog(mb_strcut(preg_replace('/\*||\#||\>||\`||\[\S*\)/i','',$entities[$i]->getBlog()),0,260,'utf-8').'...');
        }
        
        return $this->render('BlogWoBundle:Search:search_journal.html.twig', array('entities' => $entities));
    }


}