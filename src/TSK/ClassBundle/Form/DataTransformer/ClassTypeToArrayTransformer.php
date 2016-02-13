<?php
namespace TSK\ClassBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use TSK\ClassBundle\Entity\ClassTypeCredit;
use TSK\ClassBundle\Entity\ClassType;
use TSK\ClassBundle\Entity\Classes;

class ClassTypeToArrayTransformer implements DataTransformerInterface
{
    private $fields = array('fk_class_type_id', 'fk_class_id', 'value');

    public function __construct()
    {
        exit;
    }

    public function transform($classTypeCredit)
    {
        exit;
        if (null == $classTypeCredit) {
            return array_intersect_key(array(
                'fk_class_type_id' => '',
                'fk_class_id' => '',
                'value' => ''
            ), array_flip($this->fields));
        }

        if (!$classTypeCredit instanceof \TSK\ClassBundle\Entity\ClassTypeCredit) {
            throw new UnexpectedTypeException($ClassTypeCredit, '\TSK\ClassBundle\Entity\ClassTypeCredit');
        }
        $result = array_intersect(array(
            'fk_class_type_id' => $classTypeCredit->getClasssType()->getId(),
            'fk_class_id' => $classTypeCredit->getClasss()->getId(),
            'value' => $classTypeCredit->getValue()
        ), array_flip($this->fields));
    }

    public function reverseTransform($value)
    {
        exit;
        if (null === $value) {
            return null;
        }
        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        if ('' === implode('', $value)) {
            return null;
        }

        $emptyFields = array();
        foreach ($this->fields as $field) {
            if (!isset($value[$field])) {
                $emptyFields[] = $field;
            }
        }

        if (count($emptyFields) > 0) {
            throw new TransformationFailedException(
                sprintf('The fields "%s" should not be empty', implode('", "', $emptyFields))
            );
        }

        if (isset($value['fk_class_type_id']) && !ctype_digit($value['fk_class_type_id']) && !is_int($value['fk_class_type_id'])) {
            throw new TransformationFailedException('ClassTypeID is invalid');
        }

        if (isset($value['fk_class_id']) && !ctype_digit($value['fk_class_id']) && !is_int($value['fk_class_id'])) {
            throw new TransformationFailedException('ClassID is invalid');
        }

        if (isset($value['value']) && !ctype_digit($value['value']) && !is_int($value['value'])) {
            throw new TransformationFailedException('Value is invalid');
        }

        $classTypeCredit = new ClassTypeCredit();
        $classTypeCredit->setClassType();
        $classTypeCredit->setClass();
        $classTypeCredit->setValue();

    }
}
