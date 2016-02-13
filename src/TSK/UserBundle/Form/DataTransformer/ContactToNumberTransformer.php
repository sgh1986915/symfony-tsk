<?php
namespace TSK\UserBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
// use Acme\TaskBundle\Entity\contact;

class ContactToNumberTransformer implements DataTransformerInterface
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
     * Transforms an object (contact) to a string (number).
     *
     * @param  contact|null $contact
     * @return string
     */
    public function transform($contact)
    {
        if (empty($contact)) {
            return null;
        }

        return $contact->getId();
    }

    /**
     * Transforms a string (number) to an object (contact).
     *
     * @param  string $number
     *
     * @return contact|null
     *
     * @throws TransformationFailedException if object (contact) is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        $contact = $this->om
            ->getRepository('TSKUserBundle:contact')
            ->find($number)
        ;

        if (null === $contact) {
            throw new TransformationFailedException(sprintf(
                'An contact with number "%s" does not exist!',
                $number
            ));
        }

        return $contact;
    }
}
