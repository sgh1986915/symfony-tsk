<?php
namespace TSK\UserBundle\Twig\Extension;

class FormattingExtension extends \Twig_Extension
{
    private $baseDir;

    public function __construct($baseDir='')
    {
        $this->baseDir = $baseDir;
    }

    public function getFunctions()
    {
        return array(
            'format_money' => new \Twig_Function_Method($this, 'formatMoney')
        );
    }

    public function formatMoney($amount, $prefix='$')
    {
        if ($amount == floor($amount)) {
            $formatStr = '%s%5.0f';
        } else {
            $formatStr = '%s%5.2f';
        }
        return sprintf($formatStr, $prefix, $amount);
    }

    public function getName()
    {
        return 'formatting';
    }
}
