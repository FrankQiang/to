<?php

namespace Blog\ManageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Blog\ManageBundle\Entity\IndexWebPage;
use Blog\ManageBundle\Form\IndexWebPageType;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * IndexWebPage controller.
 *
 */
class IndexWebPageController extends Controller
{

    /**
     * Lists all IndexWebPage entities.
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

        $entities = $em->getRepository('BlogManageBundle:IndexWebPage')->findBy(array('user_id' => $user_id));

        return $this->render('BlogManageBundle:IndexWebPage:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a BlogIndexWebPage entity.
     *
     */
    public function showAction($token)
    {
        $em = $this->getDoctrine()->getManager();

        $temp = $em->getRepository('BlogManageBundle:IndexWebPage')->findOneByToken($token);

        if (!$temp) {
            throw $this->createNotFoundException('Unable to find BlogIndexWebPage entity.');
        }

        $id = $temp->getCategoryId();

        $entity = $em->getRepository('BlogManageBundle:IndexCategory')->find($id);

        $webpages = $em->getRepository('BlogManageBundle:IndexWebPage')->findBy(array('category_id' => $id));

        $deleteForm = $this->createDeleteForm($token);

        return $this->render('BlogManageBundle:IndexWebPage:show.html.twig', array(
            'entity'      => $entity,
            'webpages' => $webpages,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a new IndexWebPage entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new IndexWebPage();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('index_webpage_show', array('token' => $entity->getToken())));
        }

        return $this->render('BlogManageBundle:IndexWebPage:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a IndexWebPage entity.
     *
     * @param IndexWebPage $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(IndexWebPage $entity)
    {
        $form = $this->createForm(new IndexWebPageType(), $entity, array(
            'action' => $this->generateUrl('index_webpage_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit',array('attr' => array('class' => 'btn btn-info'),'label' => '提交'));

        return $form;
    }

    /**
     * Displays a form to create a new IndexWebPage entity.
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

        $entity = new IndexWebPage();

        $entity->setUserId($user_id);

        $entity->setToken($token);

        $entity->setCategoryId($id);

        $form   = $this->createCreateForm($entity);

        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('BlogManageBundle:IndexCategory')->find($id);

        return $this->render('BlogManageBundle:IndexWebPage:new.html.twig', array(
            'entity' => $entity,
            'token' => $category->getToken(),
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing BlogIndexWebPage entity.
     *
     */
    public function editAction($token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogManageBundle:IndexWebPage')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BlogIndexWebPage entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($token);

        return $this->render('BlogManageBundle:IndexWebPage:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a BlogIndexWebPage entity.
    *
    * @param BlogIndexWebPage $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(IndexWebPage $entity)
    {
        $form = $this->createForm(new IndexWebPageType(), $entity, array(
            'action' => $this->generateUrl('index_webpage_update', array('token' => $entity->getToken())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('attr' => array('class' => 'btn btn-info'),'label' => '确认'));

        return $form;
    }
    /**
     * Edits an existing BlogIndexWebPage entity.
     *
     */
    public function updateAction(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogManageBundle:IndexWebPage')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BlogIndexWebPage entity.');
        }

        $deleteForm = $this->createDeleteForm($token);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('index_webpage_show', array('token' => $token)));
        }

        return $this->render('BlogManageBundle:IndexWebPage:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }



    /**
     * Deletes a IndexWebPage entity.
     *
     */
    public function deleteAction(Request $request, $token)
    {
        $form = $this->createDeleteForm($token);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BlogManageBundle:IndexWebPage')->findOneByToken($token);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find IndexWebPage entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('index_category'));
    }

    /**
     * Creates a form to delete a IndexWebPage entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($token)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('index_webpage_delete', array('token' => $token)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('attr' => array('class' => 'btn btn-info'),'label' => '删除'))
            ->getForm()
        ;
    }
}
