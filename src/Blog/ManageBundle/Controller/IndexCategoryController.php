<?php

namespace Blog\ManageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Blog\ManageBundle\Entity\IndexCategory;
use Blog\ManageBundle\Form\IndexCategoryType;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * IndexCategory controller.
 *
 */
class IndexCategoryController extends Controller
{

    /**
     * Lists all IndexCategory entities.
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

        $entities = $em->getRepository('BlogManageBundle:IndexCategory')->findBy(array('user_id' => $user_id));

        return $this->render('BlogManageBundle:IndexCategory:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new IndexCategory entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new IndexCategory();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('index_category_show', array('token' => $entity->getToken())));
        }

        return $this->render('BlogManageBundle:IndexCategory:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a IndexCategory entity.
     *
     * @param IndexCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(IndexCategory $entity)
    {
        $form = $this->createForm(new IndexCategoryType(), $entity, array(
            'action' => $this->generateUrl('index_category_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit',array('attr' => array('class' => 'btn btn-info'),'label' => '提交'));

        return $form;
    }

    /**
     * Displays a form to create a new IndexCategory entity.
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

        $entity = new IndexCategory();

        $entity->setUserId($user_id);

        $entity->setToken($token);

        $form   = $this->createCreateForm($entity);

        return $this->render('BlogManageBundle:IndexCategory:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a IndexCategory entity.
     *
     */
    public function showAction($token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogManageBundle:IndexCategory')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find IndexCategory entity.');
        }

        $id = $entity->getId();

        $webpages = $em->getRepository('BlogManageBundle:IndexWebPage')->findBy(array('category_id' => $id));

        $deleteForm = $this->createDeleteForm($token);

        return $this->render('BlogManageBundle:IndexCategory:show.html.twig', array(
            'entity'      => $entity,
            'webpages' => $webpages,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing IndexCategory entity.
     *
     */
    public function editAction($token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogManageBundle:IndexCategory')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find IndexCategory entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($token);

        return $this->render('BlogManageBundle:IndexCategory:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a IndexCategory entity.
    *
    * @param IndexCategory $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(IndexCategory $entity)
    {
        $form = $this->createForm(new IndexCategoryType(), $entity, array(
            'action' => $this->generateUrl('index_category_update', array('token' => $entity->getToken())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('attr' => array('class' => 'btn btn-info'),'label' => '确认'));

        return $form;
    }
    /**
     * Edits an existing IndexCategory entity.
     *
     */
    public function updateAction(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogManageBundle:IndexCategory')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find IndexCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($token);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('index_category_show', array('token' => $token)));
        }

        return $this->render('BlogManageBundle:IndexCategory:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a IndexCategory entity.
     *
     */
    public function deleteAction(Request $request, $token)
    {
        $form = $this->createDeleteForm($token);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BlogManageBundle:IndexCategory')->findOneByToken($token);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find IndexCategory entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('index_category'));
    }

    /**
     * Creates a form to delete a IndexCategory entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($token)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('index_category_delete', array('token' => $token)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('attr' => array('class' => 'btn btn-info'),'label' => '删除该类'))
            ->getForm()
        ;
    }
}
