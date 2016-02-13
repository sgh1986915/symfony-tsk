<?php
namespace TSK\UserBundle\Canonicalizer;

use FOS\UserBundle\Util\CanonicalizerInterface;

class PostalCodeCanonicalizer implements CanonicalizerInterface
{
    public function canonicalize($postalCode)
    {
        $postalCode = preg_replace('/([^d])/', '', $postalCode);
        return $postalCode;
    }
}
