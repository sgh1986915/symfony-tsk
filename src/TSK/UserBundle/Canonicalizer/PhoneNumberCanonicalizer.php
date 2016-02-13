<?php
namespace TSK\UserBundle\Canonicalizer;

use FOS\UserBundle\Util\CanonicalizerInterface;

class PhoneNumberCanonicalizer implements CanonicalizerInterface
{
    public function canonicalize($phone)
    {
        return $phone;
    }
}
