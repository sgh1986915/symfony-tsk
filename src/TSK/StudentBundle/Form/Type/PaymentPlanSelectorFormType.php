<?php
namespace TSK\StudentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PaymentPlanSelectorFormType extends AbstractType
{
    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'tsk_payment_plan_selector_form_type';
    }
}
