<?php
namespace TSK\RulerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TSK\RulerBundle\Model\Rule as BaseRule;

/**
 * Rule 
 * 
 * @ORM\Table(name="tsk_rule")
 * @ORM\Entity
 * 
 */
class Rule extends BaseRule
{
    /**
     * @var integer
     *
     * @ORM\Column(name="rule_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="RuleGroup", inversedBy="rules", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_rule_group_id", referencedColumnName="rule_group_id")
    */
    protected $group;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Fact", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_fact_id", referencedColumnName="fact_id")
     * @Assert\Type(type="TSK\RulerBundle\Entity\Fact")
     */
    protected $fact;

    /**
     * @var string
     *
     * @ORM\Column(name="comparator", type="string", length=50)
     */
    protected $comparator;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    protected $value;

    public function __toString()
    {
        if ($this->getFact()) {
            return $this->getFact()->getName() . ' ' . $this->getComparator() . ' ' . $this->getValue();
        }
        return '<rule>';
    }
}
