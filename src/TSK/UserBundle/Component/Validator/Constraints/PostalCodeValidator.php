<?php
namespace TSK\UserBundle\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PostalCodeValidator extends ConstraintValidator
{
    private function isValidUSPostalCode($zipcode)
    {
        return preg_match('/^\d{5}([\-]?\d{4})?$/', $zipcode);
    }

    public function isValid($value, Constraint $constraint)
    {
        if ($value) {
            if (!$this->isValidUSPostalCode($value)) {
                $this->setMessage($constraint->message, array('{{ value }}' => $value));
                return false;
            }
        }
        return true;
    }
}
