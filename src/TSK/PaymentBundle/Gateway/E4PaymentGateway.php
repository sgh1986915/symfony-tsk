<?php
namespace TSK\PaymentBundle\Gateway;
use VinceG\FirstDataApi\FirstData;

class E4PaymentGateway implements PaymentGatewayInterface
{
    private $firstDataService;
    public function __construct($firstDataService)
    {
        $this->firstDataService = $firstDataService;
        return $this;
    }

    public function setAmount($amount)
    {
        $this->firstDataService->setAmount($amount);
        return $this;
    }

    public function setCreditCardNumber($number)
    {
        $this->firstDataService->setCreditCardNumber($number);
        return $this;
    }

    public function setCreditCardType($type)
    {
        $this->firstDataService->setCreditCardType($type);
        return $this;
    }

    public function setCreditCardVerification($cvv)
    {
        $this->firstDataService->setCreditCardVerification($cvv);
        return $this;
    }

    public function setCardHoldersName($name)
    {
        $this->firstDataService->setCreditCardName($name);
        return $this;
    }

    public function setReferenceNumber($number)
    {
        $this->firstDataService->setReferenceNumber($number);
        return $this;
    }

    public function setCreditCardZipcode($zipcode)
    {
        $this->firstDataService->setCreditCardZipCode($zipcode);
        return $this;
    }

    public function setCreditCardAddress($address)
    {
        $this->firstDataService->setCreditCardAddress($address);
        return $this;
    }

    public function setCreditCardExpiration($mmdd)
    {
        if ($mmdd instanceof \DateTime) {
            $result = $mmdd->format('my');
            $mmdd = $result;
        }
        $this->firstDataService->setCreditCardExpiration($mmdd);
        return $this;
    }


    public function setAuthorizationNumber($number)
    {
        $this->firstDataService->setAuthNumber($number);
        return $this;
    }


    public function setToken($token)
    {
        $this->firstDataService->setTransArmorToken($token);
        return $this;
    }

    public function preAuth($amount)
    {
        $this->firstDataService->setTransactionType(FirstData::TRAN_PREAUTH);
        $this->firstDataService->setAmount($amount);
        return $this->process();
    }

    public function purchase($amount)
    {
        $this->firstDataService->setTransactionType(FirstData::TRAN_PURCHASE);
        $this->firstDataService->setAmount($amount);
        return $this->process();
    }

    public function preAuthOnly()
    {
        $this->setAmount(0);
        $this->firstDataService->setTransactionType(FirstData::TRAN_PREAUTHONLY);
        $this->firstDataService->setAmount($amount);
        return $this->process();
    }

    public function void($txn)
    {
        $this->firstDataService->setTransactionType(FirstData::TRAN_VOID);
    }

    public function refund($amount)
    {
        $this->setAmount($amount);
        $this->firstDataService->setTransactionType(FirstData::TRAN_REFUND);
    }
    
    private function process()
    {
        $result = $this->firstDataService->process();
        if ($this->firstDataService->isError()) {
            throw new \Exception($this->firstDataService->getErrorMessage());
        }
        return $result;
    }
}
