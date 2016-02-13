<?php
namespace TSK\RulerBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use TSK\RulerBundle\Model\Reward as BaseReward;
use TSK\RulerBundle\Model\RuleGroup as BaseRuleGroup;

/**
 * Reward
 *
 * @ORM\Entity
 * @ORM\Table(name="tsk_reward")
 */
class Reward extends BaseReward
{
    /**
     * @var integer
     *
     * @ORM\Column(name="reward_id", type="integer")
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="method", type="string", length=100, nullable=false)
     */
    protected $method;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_data", type="string", nullable=true)
     */
    protected $metaData;

    /**
     * Get group.
     *
     * @return group.
     */
    public function getGroup()
    {
        return $this->group;
    }
 
    /**
     * Set group.
     *
     * @param group the value to set.
     */
    public function setGroup(BaseRuleGroup $group)
    {
        $this->group = $group;
        return $this;
    }

    public function __toString()
    {
        return (string) $this->getMethod() . ' ' . $this->getMetaData();
    }
 
    /**
     * Get metaData.
     *
     * @return metaData.
     */
    public function getMetaData()
    {
        return $this->metaData;
    }
 
    /**
     * Set metaData.
     *
     * @param metaData the value to set.
     */
    public function setMetaData($metaData)
    {
        $this->metaData = $metaData;
    }
}
