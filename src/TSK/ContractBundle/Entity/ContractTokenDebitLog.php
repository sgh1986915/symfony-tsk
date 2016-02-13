<?php

namespace TSK\ContractBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * ContractTokenDebitLog
 *
 * @ORM\Table(name="tsk_contract_token_debit_log")
 * @ORM\Entity
 */
class ContractTokenDebitLog
{
    /**
     * @var integer
     *
     * @ORM\Column(name="contract_token_debit_log_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\ContractBundle\Entity\Contract")
     * @ORM\JoinColumn(name="fk_contract_id", referencedColumnName="contract_id", nullable=false)
     * @Assert\Type(type="TSK\ContractBundle\Entity\Contract")
     */
    private $contract;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\ScheduleBundle\Entity\ScheduleAttendance")
     * @ORM\JoinColumn(name="fk_schedule_attendance_id", referencedColumnName="schedule_attendance_id", onDelete="CASCADE")
     * @Assert\Type(type="TSK\ScheduleBundle\Entity\ScheduleAttendance")
     */
    private $attendance;

    /**
     * @var integer
     *
     * @ORM\Column(name="value", type="integer")
     */
    private $value;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="debitDate", type="datetime")
     */
    private $debitDate;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set value
     *
     * @param integer $value
     * @return ContractTokenDebitLog
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set debitDate
     *
     * @param \DateTime $debitDate
     * @return ContractTokenDebitLog
     */
    public function setDebitDate($debitDate)
    {
        $this->debitDate = $debitDate;

        return $this;
    }

    /**
     * Get debitDate
     *
     * @return \DateTime 
     */
    public function getDebitDate()
    {
        return $this->debitDate;
    }

 
    /**
     * Get attendance.
     *
     * @return attendance.
     */
    public function getAttendance()
    {
        return $this->attendance;
    }
 
    /**
     * Set attendance.
     *
     * @param attendance the value to set.
     */
    public function setAttendance($attendance)
    {
        $this->attendance = $attendance;
        return $this;
    }

}
