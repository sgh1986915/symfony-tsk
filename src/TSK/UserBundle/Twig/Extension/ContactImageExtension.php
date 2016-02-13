<?php
namespace TSK\UserBundle\Twig\Extension;

class ContactImageExtension extends \Twig_Extension
{
    private $baseDir;

    public function __construct($baseDir='')
    {
        $this->baseDir = $baseDir;
    }

    public function getFilters()
    {
        return array(
            'get_web_path' => new \Twig_Filter_Method($this, 'getWebPath')
        );
    }

    public function getWebPath($path)
    {
        if ($path) {
            $path = str_replace($this->baseDir . '/../web/', '/', $path);
        }
        return $path;
    }

    public function getName()
    {
        return 'contact_image';
    }
}
