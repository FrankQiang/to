<?php

namespace Blog\NewsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Blog\NewsBundle\Entity\News;
use Blog\NewsBundle\Form\NewsType;

/**
 * News controller.
 *
 */
class NewsController extends Controller
{

    /**
     * Lists all News entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BlogNewsBundle:News')->getYesterdayNews();

        $yesterday = date('Y-m-d',time()-(3600*24));

        return $this->render('BlogNewsBundle:News:index.html.twig', array(
            'entities' => $entities,
            'yesterday' => $yesterday,
        ));
    }

    public function dateAction($date)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BlogNewsBundle:News')->getNews($date);

         $yesterday = date('Y-m-d',strtotime($date)-(3600*24));

         $after = date('Y-m-d',strtotime($date)+(3600*24));

         $today = date('Y-m-d',time());

         $noAfter = 1;

         if (strtotime($date)>=strtotime($today)) {
             $noAfter = 0;
         }

        return $this->render('BlogNewsBundle:News:date.html.twig', array(
            'entities' => $entities,
            'yesterday' => $yesterday,
            'after' => $after,
            'noAfter' => $noAfter,
        ));
    }


    /**
     * Creates a new News entity.
     *
     */
    public function createAction()
    {
        
        $yesterday = date('Ymd',time()-(3600*24));
        $mode = 0777;
        $headdir =  __DIR__.'/../../../../web/uploads/news/'.$yesterday.'/2.txt';

        if (!file_exists($headdir)) {
            return $this->redirect($this->generateUrl('news_new'));
        }

        $f = fopen ($headdir, "r");
        while (! feof ($f)) {
            $line= fgets ($f);
            if (!($line===FALSE)) {
                $temp = split("\t", $line);
                $entity = new News();
                $entity->setUrl($temp[0]);
                $entity->setTitle($temp[1]);

                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
            }
        }

        return $this->redirect($this->generateUrl('news'));
    }

    /**
     * Displays a form to create a new News entity.
     *
     */
    public function newAction()
    {

        return $this->render('BlogNewsBundle:News:new.html.twig');
    }

    public function pageAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $news = $em->getRepository('BlogNewsBundle:News')->getFourNews($page);

        $userNews = array();

        foreach ($news as  $new) {
            $id = $new -> getId();
            $userNew = $em->getRepository('BlogNewsBundle:UserNews')->findOneBy(array('news_id' => $id));
            if ($userNew) {
                    $userNews[$id] = $userNew->getStatus();                
            }else{
                    $userNews[$id] = 2;
            }
            
        }

        if ($news) {
            $page++;
        }else{
            $page--;
        }
        
        return $this->render('BlogNewsBundle:News:page.html.twig',array(
            'news' => $news,
            'page' => $page,
            'userNews' => $userNews,
            ));
    }


}
