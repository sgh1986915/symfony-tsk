<?php

namespace TSK\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use TSK\PaymentBundle\Entity\Charge;
use TSK\PaymentBundle\Form\ChargeType;

/**
 * Charge controller.
 *
 * @Route("/charge")
 */
class ChargeController extends Controller
{
    /**
     * Lists all Charge entities.
     *
     * @Route("/", name="charge")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('TSKPaymentBundle:Charge')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Charge entity.
     *
     * @Route("/{id}/show", name="charge_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TSKPaymentBundle:Charge')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Charge entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Charge entity.
     *
     * @Route("/new", name="charge_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Charge();
        $form   = $this->createForm(new ChargeType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Charge entity.
     *
     * @Route("/create", name="charge_create")
     * @Method("POST")
     * @Template("TSKPaymentBundle:Charge:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Charge();
        $form = $this->createForm(new ChargeType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('charge_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Charge entity.
     *
     * @Route("/{id}/edit", name="charge_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TSKPaymentBundle:Charge')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Charge entity.');
        }

        $editForm = $this->createForm(new ChargeType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Charge entity.
     *
     * @Route("/{id}/update", name="charge_update")
     * @Method("POST")
     * @Template("TSKPaymentBundle:Charge:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TSKPaymentBundle:Charge')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Charge entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ChargeType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('charge_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Charge entity.
     *
     * @Route("/{id}/delete", name="charge_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('TSKPaymentBundle:Charge')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Charge entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('charge'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
