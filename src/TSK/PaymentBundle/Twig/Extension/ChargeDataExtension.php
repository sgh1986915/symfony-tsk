<?php
namespace TSK\PaymentBundle\Twig\Extension;

class ChargeDataExtension extends \Twig_Extension
{
    private $em;
    public function __construct($em)
    {
        $this->em = $em;
    }

    public function getFunctions()
    {
        return array(
            'get_charge_data' => new \Twig_Function_Method($this, 'getChargeData')
        );
    }

    public function getChargeData($id, $property)
    {
        $charge = $this->em->getRepository('TSKPaymentBundle:Charge')->find($id);
        return $charge->{$property}();
    }

    public function getName()
    {
        return 'tsk_payment_get_charge_data_twig_extension';
    }
}
