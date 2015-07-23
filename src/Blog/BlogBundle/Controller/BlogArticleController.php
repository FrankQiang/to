<?php

namespace Blog\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Blog\BlogBundle\Entity\BlogArticle;
use Blog\BlogBundle\Form\BlogArticleType;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Blog\BlogBundle\Admin\Markdown;

/**
 * BlogArticle controller.
 *
 */
class BlogArticleController extends Controller
{

    /**
     * Lists all BlogArticle entities.
     *
     */
    public function indexAction($token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogBlogBundle:Blog')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BlogArticle entity.');
        }

        $entity->setBlog(Markdown::defaultTransform($entity->getBlog()));

        $id  =  $entity->getId();

        $articles =  $em->getRepository('BlogBlogBundle:BlogArticle')->findBy(array('article_id' => $id));

        return $this->render('BlogBlogBundle:BlogArticle:index.html.twig', array(
            'entity' => $entity,
            'articles' => $articles,
        ));
    }
    /**
     * Creates a new BlogArticle entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new BlogArticle();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('blog_article_show', array('token' => $entity->getToken())));
        }

        return $this->render('BlogBlogBundle:BlogArticle:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a BlogArticle entity.
     *
     * @param BlogArticle $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BlogArticle $entity)
    {
        $form = $this->createForm(new BlogArticleType(), $entity, array(
            'action' => $this->generateUrl('blog_article_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit',array('attr' => array('class' => 'btn btn-info'),'label' => '确认'));

        return $form;
    }

    /**
     * Displays a form to create a new BlogArticle entity.
     *
     */
    public function newAction($id)
    {
        $user = $this->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        $user_id=$user->getId();
        
        $tokenGenerator = $this->get('fos_user.util.token_generator');
        $token = $tokenGenerator->generateToken();

        $entity = new BlogArticle();

        $entity->setUserId($user_id);
        $entity->setToken($token);
        $entity->setArticleId($id);
        $form   = $this->createCreateForm($entity);

        return $this->render('BlogBlogBundle:BlogArticle:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a BlogArticle entity.
     *
     */
    public function showAction($token)
    {
        $em = $this->getDoctrine()->getManager();

        $temp = $em->getRepository('BlogBlogBundle:BlogArticle')->findOneByToken($token);

        if (!$temp) {
            throw $this->createNotFoundException('Unable to find BlogArticle entity.');
        }

        $id = $temp->getArticleId();

        $entity = $em->getRepository('BlogBlogBundle:Blog')->find($id);

        $articles = $em->getRepository('BlogBlogBundle:BlogArticle')->findBy(array('article_id' => $id));

        $temp->setBlog(Markdown::defaultTransform($temp->getBlog()));
        $deleteForm = $this->createDeleteForm($token);

        return $this->render('BlogBlogBundle:BlogArticle:show.html.twig', array(
            'entity'      => $entity,
            'articles' => $articles,
            'temp' => $temp,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing BlogArticle entity.
     *
     */
    public function editAction($token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogBlogBundle:BlogArticle')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BlogArticle entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($token);

        return $this->render('BlogBlogBundle:BlogArticle:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a BlogArticle entity.
    *
    * @param BlogArticle $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(BlogArticle $entity)
    {
        $form = $this->createForm(new BlogArticleType(), $entity, array(
            'action' => $this->generateUrl('blog_article_update', array('token' => $entity->getToken())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('attr' => array('class' => 'btn btn-info'),'label' => '确认'));

        return $form;
    }
    /**
     * Edits an existing BlogArticle entity.
     *
     */
    public function updateAction(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogBlogBundle:BlogArticle')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BlogArticle entity.');
        }

        $deleteForm = $this->createDeleteForm($token);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('blog_article_show', array('token' => $entity->getToken())));
        }

        return $this->render('BlogBlogBundle:BlogArticle:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a BlogArticle entity.
     *
     */
    public function deleteAction(Request $request, $token)
    {
        $form = $this->createDeleteForm($token);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BlogBlogBundle:BlogArticle')->findOneByToken($token);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BlogArticle entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('blog'));
    }

    /**
     * Creates a form to delete a BlogArticle entity by token.
     *
     * @param mixed $token The entity token
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($token)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('blog_article_delete', array('token' => $token)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('attr' => array('class' => 'btn btn-info'),'label' => '删除'))
            ->getForm()
        ;
    }
}
