<?php

namespace TSK\RankBundle\Form\Model;

class RankValue
{
    protected $value;

 
    /**
     * Get value.
     *
     * @return value.
     */
    public function getValue()
    {
        return $this->value;
    }
 
    /**
     * Set value.
     *
     * @param value the value to set.
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}
