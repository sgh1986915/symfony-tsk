<?php

namespace TSK\StudentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use TSK\RankBundle\Entity\Rank;
use TSK\RankBundle\Entity\RankType;

/**
 * StudentRank
 *
 * @ORM\Table(name="tsk_student_rank")
 * @ORM\Entity(repositoryClass="TSK\StudentBundle\Entity\StudentRankRepository")
 */
class StudentRank
{
    /**
     * @var integer
     *
     * @ORM\Column(name="student_rank_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\StudentBundle\Entity\Student", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_student_id", referencedColumnName="student_id", nullable=true)
     * @Assert\Type(type="TSK\StudentBundle\Entity\Student")
     */
    private $student;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\RankBundle\Entity\Rank", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_rank_id", referencedColumnName="rank_id", nullable=true)
     * @Assert\Type(type="TSK\RankBundle\Entity\Rank")
     */
    private $rank;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\RankBundle\Entity\RankType", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_rank_type_id", referencedColumnName="rank_type_id", nullable=true)
     * @Assert\Type(type="TSK\RankBundle\Entity\RankType")
     */
    private $rankType;

    /**
     * @var string
     *
     * @Gedmo\Blameable(on="create", field={"rank"})
     * @ORM\Column(name="awarded_by", type="string", length=50, nullable=true)
     */
    private $awardedBy;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create", field={"rank"})
     * @ORM\Column(name="awarded_date", type="datetime", nullable=true)
     */
    private $awardedDate;


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
     * Set awardedBy
     *
     * @param string $awardedBy
     * @return StudentRank
     */
    public function setAwardedBy($awardedBy)
    {
        $this->awardedBy = $awardedBy;

        return $this;
    }

    /**
     * Get awardedBy
     *
     * @return string 
     */
    public function getAwardedBy()
    {
        return $this->awardedBy;
    }

    /**
     * Set awardedDate
     *
     * @param \DateTime $awardedDate
     * @return StudentRank
     */
    public function setAwardedDate($awardedDate)
    {
        $this->awardedDate = $awardedDate;

        return $this;
    }

    /**
     * Get awardedDate
     *
     * @return \DateTime 
     */
    public function getAwardedDate()
    {
        return $this->awardedDate;
    }
 
    /**
     * Get student.
     *
     * @return student.
     */
    public function getStudent()
    {
        return $this->student;
    }
 
    /**
     * Set student.
     *
     * @param student the value to set.
     */
    public function setStudent(Student $student)
    {
        $this->student = $student;
        return $this;
    }
 
    /**
     * Get rank.
     *
     * @return rank.
     */
    public function getRank()
    {
        return $this->rank;
    }
 
    /**
     * Set rank.
     *
     * @param rank the value to set.
     */
    public function setRank(Rank $rank)
    {
        $this->rank = $rank;
        return $this;
    }
 
    /**
     * Get rankType.
     *
     * @return rankType.
     */
    public function getRankType()
    {
        return $this->rankType;
    }
 
    /**
     * Set rankType.
     *
     * @param rankType the value to set.
     */
    public function setRankType(RankType $rankType)
    {
        $this->rankType = $rankType;
        return $this;
    }
}
