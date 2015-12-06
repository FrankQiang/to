<?php

namespace Blog\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Blog\BlogBundle\Entity\Blog;
use Blog\BlogBundle\Form\BlogType;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Blog\BlogBundle\Admin\Markdown;

/**
 * Blog controller.
 *
 */
class BlogController extends Controller
{

    /**
     * Lists all Blog entities.
     *
     */
    public function indexAction()
    {

        $user = $this->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        $user_id=$user->getId();
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT b FROM BlogBlogBundle:Blog b WHERE b.user_id ='.$user_id.' ORDER BY  b.click  DESC'
    );
        $entities = $query->getResult();

        //$entities = $em->getRepository('BlogBlogBundle:Blog')->findBy(array('user_id' => $user_id));

        return $this->render('BlogBlogBundle:Blog:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Blog entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Blog();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('blog_article', array('token' => $entity->getToken())));
        }

        return $this->render('BlogBlogBundle:Blog:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Blog entity.
     *
     * @param Blog $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Blog $entity)
    {
        $form = $this->createForm(new BlogType(), $entity, array(
            'action' => $this->generateUrl('blog_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit',array('attr' => array('class' => 'btn btn-info'),'label' => '提交'));

        return $form;
    }

    /**
     * Displays a form to create a new Blog entity.
     *
     */
    public function newAction()
    {
        $user = $this->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        $user_id=$user->getId();
        
        $tokenGenerator = $this->get('fos_user.util.token_generator');
        $token = $tokenGenerator->generateToken();

        $entity = new Blog();
        $entity->setUserId($user_id);

        $entity->setToken($token);

        $entity->setClick(0);
        $form   = $this->createCreateForm($entity);

        return $this->render('BlogBlogBundle:Blog:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Blog entity.
     *
     */
    public function showAction($token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogBlogBundle:Blog')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Blog entity.');
        }

        $deleteForm = $this->createDeleteForm($token);

        $entity->setBlog(Markdown::defaultTransform($entity->getBlog()));

        return $this->render('BlogBlogBundle:Blog:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Blog entity.
     *
     */
    public function editAction($token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogBlogBundle:Blog')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Blog entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($token);

        return $this->render('BlogBlogBundle:Blog:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Blog entity.
    *
    * @param Blog $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Blog $entity)
    {
        $form = $this->createForm(new BlogType(), $entity, array(
            'action' => $this->generateUrl('blog_update', array('token' => $entity->getToken())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('attr' => array('class' => 'btn btn-info'),'label' => '确认'));

        return $form;
    }
    /**
     * Edits an existing Blog entity.
     *
     */
    public function updateAction(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogBlogBundle:Blog')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Blog entity.');
        }

        $deleteForm = $this->createDeleteForm($token);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('blog_article', array('token' => $token)));
        }

        return $this->render('BlogBlogBundle:Blog:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Blog entity.
     *
     */
    public function deleteAction(Request $request, $token)
    {
        $form = $this->createDeleteForm($token);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BlogBlogBundle:Blog')->findOneByToken($token);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Blog entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('blog'));
    }

    /**
     * Creates a form to delete a Blog entity by token.
     *
     * @param mixed $token The entity token
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($token)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('blog_delete', array('token' => $token)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('attr' => array('class' => 'btn btn-info'),'label' => '删除'))
            ->getForm()
        ;
    }
}
