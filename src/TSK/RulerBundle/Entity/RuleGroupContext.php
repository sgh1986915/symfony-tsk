<?php
namespace TSK\RulerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use TSK\RulerBundle\Model\RuleGroupContext as BaseRuleGroupContext;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RuleGroupContext
 * 
 * @ORM\Entity
 * @ORM\Table(name="tsk_rule_group_context")
 * 
 */
class RuleGroupContext extends BaseRuleGroupContext
{
    /**
     * @var integer
     *
     * @ORM\Column(name="rule_group_context_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var text
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    protected $name;

    /**
     * @var text
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;


    // public function getId()
    // {
    //     return $this->id;
    // }

    // /**
    //  * Get description.
    //  *
    //  * @return description.
    //  */
    // public function getDescription()
    // {
    //     return $this->description;
    // }
 
    // /**
    //  * Set description.
    //  *
    //  * @param description the value to set.
    //  */
    // public function setDescription($description)
    // {
    //     $this->description = $description;
    //     return $this;
    // }
 
    /**
     * Get name.
     *
     * @return name.
     */
    public function getName()
    {
        return $this->name;
    }
 
    /**
     * Set name.
     *
     * @param name the value to set.
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function __toString()
    {
        return (string) $this->name;
    }
}
