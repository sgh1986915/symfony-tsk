<?php

namespace TSK\ProgramBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;

class ProgramController extends Controller
{
    /**
     * return the Response object associated to the edit action
     *
     *
     * @param mixed $id
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return Response
     */
    public function editAction($id = null)
    {
        // the key used to lookup the template
        $templateKey = 'edit';

        $id = $this->get('request')->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        // PAYMENT PLAN DELETION CODE 
        //
        // We keep an array of the original payment plans so that we 
        // know which ones need to be deleted, based on which of the original
        // are no longer present in the post.  See below
        //
        $originalPaymentPlans = array();
        foreach ($object->getPaymentPlans() as $pp) {
            $originalPaymentPlans[$pp->getId()] = $pp;
        }

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('EDIT', $object)) {
            throw new AccessDeniedException();
        }

        $this->admin->setSubject($object);

        /** @var $form \Symfony\Component\Form\Form */
        $form = $this->admin->getForm();
        $form->setData($object);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            $em = $this->getDoctrine()->getManager();
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                // 
                // Payment Plan Deletion Code
                // This iterates through eacho of the original payment plans and tests if 
                // the plan still exists in the submitted data.  If not, the plan is deleted.
                // 
                if (count($originalPaymentPlans)) {
                    foreach ($originalPaymentPlans as $paymentPlanID => $old_pp) {
                        if (!$object->getPaymentPlans()->contains($old_pp)) {
                            $em->remove($old_pp);
                            $em->flush();
                        }
                    }
                }

                $this->admin->update($object);
                $this->get('session')->setFlash('sonata_flash_success', 'flash_edit_success');

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array(
                        'result'    => 'ok',
                        'objectId'  => $this->admin->getNormalizedIdentifier($object)
                    ));
                }

                // redirect to edit mode
                return $this->redirectTo($object);
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    $this->get('session')->setFlash('sonata_flash_error', 'flash_edit_error');
                }
            } elseif ($this->isPreviewRequested()) {
                // enable the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
            }
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'edit',
            'form'   => $view,
            'object' => $object,
        ));
    }
}
