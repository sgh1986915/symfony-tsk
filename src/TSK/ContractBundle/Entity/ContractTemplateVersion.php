<?php

namespace TSK\ContractBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;

/**
 * ContractTemplateVersion
 *
 * @ORM\Table(name="tsk_contract_template_version")
 * @ORM\Entity
 */
class ContractTemplateVersion extends AbstractLogEntry
{
    /**
     * @var datetime $activeDate
     *
     * @ORM\Column(name="active_date", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    protected $activeDate;

    public function __toString()
    {
        return sprintf('%d | %s', $this->getVersion(), $this->getLoggedAt()->format('Y-m-d H:i:s'));
    }

    public function setActiveDate($activeDate=null)
    {
        if (is_null($activeDate)) {
            $activeDate = new \DateTime();
        }
        $this->activeDate = $activeDate;
        return $this;
    }

    public function getActiveDate()
    {
        return $this->activeDate;
    }

}
