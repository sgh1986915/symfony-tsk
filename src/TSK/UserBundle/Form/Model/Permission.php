<?php
namespace TSK\UserBundle\Form\Model;

class Permission
{
    private $className;
    private $classType;
    private $fieldName;
    private $bits;

    public function __construct($className, $classType, $fieldName = null, array $bits=array())
    {
        $this->setClassName($className);
        $this->setClassType($classType);
        $this->setFieldName($fieldName);
        $this->setBits($bits);
    }
 
    /**
     * Get resource.
     *
     * @return resource.
     */
    public function getResource()
    {
        return $this->resource;
    }
 
    /**
     * Set resource.
     *
     * @param resource the value to set.
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }
 
    /**
     * Get bits.
     *
     * @return bits.
     */
    public function getBits()
    {
        return $this->bits;
    }
 
    /**
     * Set bits.
     *
     * @param bits the value to set.
     */
    public function setBits($bits)
    {
        $this->bits = $bits;
        return $this;
    }
/*

    public function addBit(PermBit $bit)
    {
        $this->bits[] = $bit;
    }

    public function removeBit(PermBit $bit)
    {
        $this->bits->removeElement($bit);
    }
*/
 
    /**
     * Get className.
     *
     * @return className.
     */
    public function getClassName()
    {
        return $this->className;
    }
 
    /**
     * Set className.
     *
     * @param className the value to set.
     */
    public function setClassName($className)
    {
        $this->className = $className;
        return $this;
    }
 
    /**
     * Get classType.
     *
     * @return classType.
     */
    public function getClassType()
    {
        return $this->classType;
    }
 
    /**
     * Set classType.
     *
     * @param classType the value to set.
     */
    public function setClassType($classType)
    {
        $this->classType = $classType;
        return $this;
    }
 
    /**
     * Get fieldName.
     *
     * @return fieldName.
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }
 
    /**
     * Set fieldName.
     *
     * @param fieldName the value to set.
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
        return $this;
    }
}
