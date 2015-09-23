<?php

namespace Blog\WoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Blog\BlogBundle\Admin\Markdown;

/**
 * Community controller.
 *
 */
class CommunityController extends Controller
{
	/**
     * Lists all Community entities.
     *
     */
    public function blogAction()
    {
        $em = $this->getDoctrine()->getManager();

        // $entities = $em->getRepository('BlogBlogBundle:Blog')->findAll();
        $query = $em->createQuery(
            'SELECT b FROM BlogBlogBundle:Blog b ORDER BY  b.click  DESC'
    );
        $entities = $query->getResult();

        for ($i=0; $i < count($entities); $i++) { 
            $entities[$i]->setBlog(mb_strcut(preg_replace('/\*||\#||\>||\`||\[\S*\)/i','',$entities[$i]->getBlog()),0,260,'utf-8').'...');
        }

        return $this->render('BlogWoBundle:Community:blog.html.twig', array(
            'entities' => $entities,
        ));

    }

    public function blogShowAction($id)
    {
      $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogBlogBundle:Blog')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Blog entity.');
        }

        $articles =  $em->getRepository('BlogBlogBundle:BlogArticle')->findBy(array('article_id' => $id));

        $click = $entity->getClick();
        
        $entity->setClick(($click+1));
        
        $em->flush();

        $id = $entity->getUserId();

        $weibo = $em->getRepository('BlogUserBundle:User')->find($id)->getWeiboId();

         $entity->setBlog(Markdown::defaultTransform($entity->getBlog()));

        return $this->render('BlogWoBundle:Community:blog_show.html.twig', array(
            'entity'      => $entity,
            'articles'  =>  $articles,
            'weibo' => $weibo,
        ));

    }

    public function blogArticleShowAction($id)
    {
      $em = $this->getDoctrine()->getManager();

      $temp = $em->getRepository('BlogBlogBundle:BlogArticle')->find($id);
  

        if (!$temp) {
            throw $this->createNotFoundException('Unable to find Blog entity.');
        }

        $id = $temp->getArticleId();

        $entity = $em->getRepository('BlogBlogBundle:Blog')->find($id);

        $articles =  $em->getRepository('BlogBlogBundle:BlogArticle')->findBy(array('article_id' => $id));

        $user_id = $entity->getUserId();

        $weibo = $em->getRepository('BlogUserBundle:User')->find($user_id)->getWeiboId();

         $temp->setBlog(Markdown::defaultTransform($temp->getBlog()));

        return $this->render('BlogWoBundle:Community:blog_article_show.html.twig', array(
            'entity'      => $entity,
            'articles'  =>  $articles,
            'temp'  =>  $temp,
            'weibo' => $weibo,
        ));

    }

    public function journalAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BlogBlogBundle:Diary')->findAll();

        for ($i=0; $i < count($entities); $i++) { 
            $entities[$i]->setBlog(mb_strcut(preg_replace('/\*||\#||\>||\`||\[\S*\)/i','',$entities[$i]->getBlog()),0,260,'utf-8').'...');
        }

        return $this->render('BlogWoBundle:Community:journal.html.twig', array(
            'entities' => $entities,
        ));

    }

    public function journalShowAction($id)
    {
      $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogBlogBundle:Diary')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Diary entity.');
        }

        $click = $entity->getClick();
        
        $entity->setClick(($click+1));
        
        $em->flush();

        $id = $entity->getUserId();

        $weibo = $em->getRepository('BlogUserBundle:User')->find($id)->getWeiboId();


         $entity->setBlog(Markdown::defaultTransform($entity->getBlog()));

        return $this->render('BlogWoBundle:Community:journal_show.html.twig', array(
            'entity'      => $entity,
            'weibo' => $weibo,
        ));

    }



}

?>