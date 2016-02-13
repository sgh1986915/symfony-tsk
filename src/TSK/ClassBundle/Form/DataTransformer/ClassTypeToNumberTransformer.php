<?php
namespace TSK\ClassBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use TSK\ClassBundle\Entity\ClassType;

class ClassTypeToNumberTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($classType)
    {
        print 'in there?';
        var_dump($classType); 
        if (null === $classType) {
            return "";
        }

        return $classType->getId();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $number
     * @return Issue|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($number)
    {
        print 'in here?';
        if (!$number) {
            return null;
        }

        $classType = $this->om
            ->getRepository('TSKClassBundle:ClassType')
            ->findOneBy(array('id' => $number))
        ;

        if (null === $classType) {
            throw new TransformationFailedException(sprintf(
                'An classType with number "%s" does not exist!',
                $number
            ));
        }

        return $classType;
    }
}
