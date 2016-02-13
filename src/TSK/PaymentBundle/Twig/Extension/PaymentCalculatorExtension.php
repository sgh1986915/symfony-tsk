<?php
namespace TSK\PaymentBundle\Twig\Extension;

class PaymentCalculatorExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'get_payment_calculator_data' => new \Twig_Function_Method($this, 'getPaymentCalculatorData')
        );
    }

    public function getPaymentCalculatorData($data, $key)
    {
        $obj = json_decode($data);
        if (!empty($obj->$key)) {
            return $obj->$key;
        }
    }

    public function getName()
    {
        return 'payment_calculator';
    }
}
