<?php

namespace TSK\RulerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TSK\RulerBundle\Model\Fact as BaseFact;

/**
 * Fact
 *
 * @ORM\Table(name="tsk_fact")
 * @ORM\Entity
 */
class Fact extends BaseFact
{
    /**
     * @var integer
     *
     * @ORM\Column(name="fact_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\UserBundle\Entity\Organization", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_org_id", referencedColumnName="org_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\UserBundle\Entity\Organization")
     */
    protected $organization;

    /**
     * @var string
     *
     * @ORM\Column(name="fact_type", type="string", length=100)
     */
    protected $factType;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="method", type="string", length=100)
     */
    protected $method;

    public function getLabel()
    {
        return (string) $this->getName() . ' ' . '(' . $this->getFactType() . ')';
    }
}
