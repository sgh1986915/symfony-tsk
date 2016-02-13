<?php
use Symfony\Component\Form\DataTransformerInterface;

class EntityToStringTransformer extends DataTransformerInterface
{

    public function transform($elem)
    {
        if (!$elem) {
            $elem = array();
        }

        return implode(', ', $elem);
    }
    
    public function reverseTransform($elem)
    {
        if (!$elem) {
            $elem = '';
        }

        return array_filter(array_map('trim', explode(',', $elem)));
    }
}

