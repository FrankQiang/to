<?php

namespace Blog\NewsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Blog\NewsBundle\Entity\UserNews;
use Blog\NewsBundle\Form\UserNewsType;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * UserNews controller.
 *
 */
class UserNewsController extends Controller
{

    /**
     * Lists all UserNews entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BlogNewsBundle:UserNews')->findAll();

        return $this->render('BlogNewsBundle:UserNews:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new UserNews entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new UserNews();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_news_status_show', array('id' => $entity->getId())));
        }

        return $this->render('BlogNewsBundle:UserNews:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a UserNews entity.
     *
     * @param UserNews $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(UserNews $entity)
    {
        $form = $this->createForm(new UserNewsType(), $entity, array(
            'action' => $this->generateUrl('user_news_status_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new UserNews entity.
     *
     */
    public function newAction()
    {
        $entity = new UserNews();
        $form   = $this->createCreateForm($entity);

        return $this->render('BlogNewsBundle:UserNews:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a UserNews entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogNewsBundle:UserNews')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserNews entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BlogNewsBundle:UserNews:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing UserNews entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogNewsBundle:UserNews')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserNews entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BlogNewsBundle:UserNews:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a UserNews entity.
    *
    * @param UserNews $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(UserNews $entity)
    {
        $form = $this->createForm(new UserNewsType(), $entity, array(
            'action' => $this->generateUrl('user_news_status_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing UserNews entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogNewsBundle:UserNews')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserNews entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('user_news_status_edit', array('id' => $id)));
        }

        return $this->render('BlogNewsBundle:UserNews:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a UserNews entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BlogNewsBundle:UserNews')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find UserNews entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('user_news_status'));
    }

    /**
     * Creates a form to delete a UserNews entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_news_status_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function likeAction($id)
    {
    
        $em = $this->getDoctrine()->getManager();

        $userNews = $em->getRepository('BlogNewsBundle:UserNews')->findOneBy(array('news_id' => $id));

        if ($userNews) {
            $userNews->setStatus(1);
            $em->flush();
        }else{
            $user = $this->getUser();

            if (!is_object($user) || !$user instanceof UserInterface) {
                throw new AccessDeniedException('This user does not have access to this section.');
            }
            
            $user_id=$user->getId();
        
            $entity = new UserNews();
            $entity->setUserId($user_id);
            $entity->setNewsId($id);
            $entity->setStatus(1);
            $em->persist($entity);
            $em->flush();
        }

        $response = new Response(200);

        return $response;

    }

    public function dislikeAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $userNews = $em->getRepository('BlogNewsBundle:UserNews')->findOneBy(array('news_id' => $id));

        if ($userNews) {
            $userNews->setStatus(0);
            $em->flush();
        }else{
            $user = $this->getUser();

            if (!is_object($user) || !$user instanceof UserInterface) {
                throw new AccessDeniedException('This user does not have access to this section.');
            }
            
            $user_id=$user->getId();
        
            $entity = new UserNews();
            $entity->setUserId($user_id);
            $entity->setNewsId($id);
            $entity->setStatus(0);
            $em->persist($entity);
            $em->flush();
        }

        $response = new Response(200);

        return $response;

    }

}
