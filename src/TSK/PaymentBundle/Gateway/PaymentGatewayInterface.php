<?php
namespace TSK\PaymentBundle\Gateway;

interface PaymentGatewayInterface
{
    public function setAmount($amount);
    public function setCardHoldersName($name);
    public function setCreditCardNumber($number);
    public function setCreditCardAddress($address);
    public function setCreditCardExpiration($date);
    public function setCreditCardVerification($cvv);
    public function setCreditCardType($type);
    public function setCreditCardZipcode($zipcode);
    public function setReferenceNumber($number);
    public function setAuthorizationNumber($number);
    public function setToken($token);
    public function preAuth($amount);
    public function purchase($amount);
    public function preAuthOnly();
    public function refund($txn);
    public function void($txn);
}
