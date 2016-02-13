<?php
namespace TSK\ScheduleBundle\Form\Model;

use TSK\ScheduleBundle\Entity\Roster;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

class Attendance
{
    /**
     * roster
     * 
     * @var mixed
     * @access private
     */
    private $rosters;

    private $statuses;

    private $notes;

    /**
     * @var \DateTime
     */
    private $attDate;

    public function __construct()
    {
        $this->rosters = new ArrayCollection();
        $this->statuses = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }
 
    /**
     * Get rosters.
     *
     * @return rosters.
     */
    public function getRosters()
    {
        return $this->rosters;
    }
 
    /**
     * Set rosters.
     *
     * @param rosters the value to set.
     */
    public function setRosters($rosters)
    {
        $this->rosters = $rosters;
        return $this;
    }

    public function addRoster(Roster $roster)
    {
        $this->rosters[] = $roster;
    }

    public function removeRoster(Roster $roster)
    {
        return $this->rosters->removeElement($roster);
    }

 
    /**
     * Get statuses.
     *
     * @return statuses.
     */
    public function getStatuses()
    {
        return $this->statuses;
    }
 
    /**
     * Set statuses.
     *
     * @param statuses the value to set.
     */
    public function setStatuses($statuses)
    {
        $this->statuses = $statuses;
        return $this;
    }
 
    /**
     * Get notes.
     *
     * @return notes.
     */
    public function getNotes()
    {
        return $this->notes;
    }
 
    /**
     * Set notes.
     *
     * @param notes the value to set.
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
        return $this;
    }
 
    /**
     * Get attDate.
     *
     * @return attDate.
     */
    public function getAttDate()
    {
        return $this->attDate;
    }
 
    /**
     * Set attDate.
     *
     * @param attDate the value to set.
     */
    public function setAttDate(\DateTime $attDate)
    {
        $this->attDate = $attDate;
        return $this;
    }
}
