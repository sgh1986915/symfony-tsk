<?php
namespace TSK\UserBundle\Model;

interface ContactInterface
{
    public function getPhone();
    public function getPhoneCanonical();
    public function getMobile();
    public function getMobileCanonical();
    public function getPostalCode();
    public function getPostalCodeCanonical();

    public function setPhone($phone);
    public function setPhoneCanonical($phoneCanonical);
    public function setMobile($mobile);
    public function setMobileCanonical($mobileCanonical);
    public function setPostalCode($postalCode);
    public function setPostalCodeCanonical($postalCodeCanonical);
}
