<?php
namespace TSK\UserBundle\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * PostalCode 
 * 
 * @Annotation
 * @version $id$
 * @copyright 2012 Obverse Dev
 * @author Malaney J. Hill <malaney@gmail.com> 
 * @license PHP Version 3.01 {@link http://www.php.net/license/3_01.txt}
 */
class PostalCode extends Constraint
{
     public $message = 'The postal code must be in 5 or 5-4 digits. Your input was "{{ value }}".';
}
