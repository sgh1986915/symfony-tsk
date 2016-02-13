<?php

namespace TSK\UserBundle\Form\Model;

class PermBit
{
    private $bit;

    public function __construct($bit=NULL)
    {
        $this->setBit($bit);
    }
 /**
  * Get bits.
  *
  * @return bits.
  */
 function getBit()
 {
     return $this->bit;
 }
 
 /**
  * Set bits.
  *
  * @param bits the value to set.
  */
 function setBit($bit)
 {
     $this->bit = $bit;
 }
}
