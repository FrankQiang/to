<?php

namespace Blog\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Blog\BlogBundle\Entity\Diary;
use Blog\BlogBundle\Form\DiaryType;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Blog\BlogBundle\Admin\Markdown;

/**
 * Diary controller.
 *
 */
class DiaryController extends Controller
{

    /**
     * Lists all Diary entities.
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
            'SELECT d FROM BlogBlogBundle:Diary d WHERE d.user_id = '.$user_id.' ORDER BY  d.click  DESC'
    );
        $entities = $query->getResult();

        //$entities = $em->getRepository('BlogBlogBundle:Diary')->findBy(array('user_id' => $user_id));

        return $this->render('BlogBlogBundle:Diary:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Diary entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Diary();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('diary_show', array('token' => $entity->getToken())));
        }

        return $this->render('BlogBlogBundle:Diary:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Diary entity.
     *
     * @param Diary $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Diary $entity)
    {
        $form = $this->createForm(new DiaryType(), $entity, array(
            'action' => $this->generateUrl('diary_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit',array('attr' => array('class' => 'btn btn-info'),'label' => '确认'));

        return $form;
    }

    /**
     * Displays a form to create a new Diary entity.
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

        $entity = new Diary();

        $entity->setUserId($user_id);

        $entity->setToken($token);
        $entity->setClick(0);
        $form   = $this->createCreateForm($entity);

        return $this->render('BlogBlogBundle:Diary:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Diary entity.
     *
     */
    public function showAction($token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogBlogBundle:Diary')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Diary entity.');
        }

        $deleteForm = $this->createDeleteForm($token);

        $entity->setBlog(Markdown::defaultTransform($entity->getBlog()));

        return $this->render('BlogBlogBundle:Diary:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Diary entity.
     *
     */
    public function editAction($token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogBlogBundle:Diary')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Diary entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($token);

        return $this->render('BlogBlogBundle:Diary:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Diary entity.
    *
    * @param Diary $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Diary $entity)
    {
        $form = $this->createForm(new DiaryType(), $entity, array(
            'action' => $this->generateUrl('diary_update', array('token' => $entity->getToken())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit',array('attr' => array('class' => 'btn btn-info'),'label' => '确认'));

        return $form;
    }
    /**
     * Edits an existing Diary entity.
     *
     */
    public function updateAction(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogBlogBundle:Diary')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Diary entity.');
        }

        $deleteForm = $this->createDeleteForm($token);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('diary'));
        }

        return $this->render('BlogBlogBundle:Diary:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Diary entity.
     *
     */
    public function deleteAction(Request $request, $token)
    {
        $form = $this->createDeleteForm($token);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BlogBlogBundle:Diary')->findOneByToken($token);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Diary entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('diary'));
    }

    /**
     * Creates a form to delete a Diary entity by token.
     *
     * @param mixed $token The entity token
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($token)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('diary_delete', array('token' => $token)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('attr' => array('class' => 'btn btn-info'),'label' => '删除'))
            ->getForm()
        ;
    }
}
