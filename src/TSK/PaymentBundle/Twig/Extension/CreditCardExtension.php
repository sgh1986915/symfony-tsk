<?php
namespace TSK\PaymentBundle\Twig\Extension;

class CreditCardExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'mask_credit_card' => new \Twig_Filter_Method($this, 'maskCreditCard')
        );
    }

    public function maskCreditCard($str, $char='*', $len=4)
    {
        $result = sprintf('%s%s', str_repeat($char, (strlen($str) - $len)), substr($str, $len * -1, $len));
        return $result;
    }

    public function getName()
    {
        return 'tsk_payment_credit_card_twig_extension';
    }
}
