<?php
namespace TSK\RulerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use TSK\RulerBundle\Model\RuleContext as BaseRuleContext;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RuleGroupContext
 * 
 * @ORM\Entity
 * @ORM\Table(name="tsk_rule_context", uniqueConstraints={@ORM\UniqueConstraint(name="rule_context_name_uniq", columns={"name"})})
 * 
 */
class RuleContext extends BaseRuleContext
{
    /**
     * @var integer
     *
     * @ORM\Column(name="rule_context_id", type="integer")
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
 
    /**
     * Get description.
     *
     * @return description.
     */
    public function getDescription()
    {
        return $this->description;
    }
 
    /**
     * Set description.
     *
     * @param description the value to set.
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
}
