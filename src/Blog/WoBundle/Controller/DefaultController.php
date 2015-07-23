<?php

namespace Blog\WoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $user = $this->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        $user_id=$user->getId();
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BlogManageBundle:IndexCategory')->findBy(array('user_id' => $user_id));
        $webpages = array();
        foreach ($entities as $entity) {      
            $category_id = $entity->getId();
            $entityWebpages = $em->getRepository('BlogManageBundle:IndexWebPage')->findBy(array('category_id' => $category_id));
            $webpages[$category_id] = $entityWebpages;
        }

        $news = $em->getRepository('BlogNewsBundle:News')->getFourNews(0);

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


        return $this->render('BlogWoBundle:Default:index.html.twig', array(
            'entities' => $entities,
            'webpages' => $webpages,
            'news' => $news,
            'userNews' => $userNews,
            'page' => 1,
        ));
    }

    public function aboutAction()
    {
        return $this->render('BlogWoBundle:Default:about.html.twig');
    }

}