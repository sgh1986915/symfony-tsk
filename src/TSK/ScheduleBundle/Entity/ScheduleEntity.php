<?php

namespace TSK\ScheduleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use TSK\ClassBundle\Entity\Classes;
use TSK\InstructorBundle\Entity\Instructor;

/**
 * ScheduleEntity
 *
 * @ORM\Table(name="tsk_schedule_entity")
 * @ORM\Entity(repositoryClass="TSK\ScheduleBundle\Entity\ScheduleEntityRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ScheduleEntity
{
    static public $TIME_UNITS = array(
        'week'    => 604800,
        'day'     => 86400,
        'hour'    => 3600,
        'minute'  => 60,
        'second'  => 1,
    );

    /**
     * @var integer
     *
     * @ORM\Column(name="schedule_entity_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_user_id", referencedColumnName="user_id", nullable=false)
     * @Assert\Type(type="TSK\UserBundle\Entity\User")
     */
    protected $user;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\SchoolBundle\Entity\School", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_school_id", referencedColumnName="school_id", nullable=false)
     * @Assert\Type(type="TSK\SchoolBundle\Entity\School")
     */
    protected $school;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\ScheduleBundle\Entity\ScheduleCategory", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_schedule_category_id", referencedColumnName="schedule_category_id", nullable=false)
     * @Assert\Type(type="TSK\ScheduleBundle\Entity\ScheduleCategory")
     */
    protected $category;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\ScheduleBundle\Entity\ScheduleLocation", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_schedule_location_id", referencedColumnName="schedule_location_id", nullable=false)
     * @Assert\Type(type="TSK\ScheduleBundle\Entity\ScheduleLocation")
     */
    protected $location;

    /**
     * @ORM\OneToMany(targetEntity="TSK\ScheduleBundle\Entity\ScheduleInstance", mappedBy="scheduleEntity", cascade={"persist","remove"})
     **/
    protected $instances;

    /**
     * @ORM\ManyToMany(targetEntity="TSK\ClassBundle\Entity\Classes", inversedBy="schedules")
     * @ORM\JoinTable(name="tsk_class_schedule", joinColumns={@ORM\JoinColumn(name="fk_schedule_entity_id", referencedColumnName="schedule_entity_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="fk_class_id", referencedColumnName="class_id")})
     */
    protected $classes;

    /**
     * @ORM\ManyToMany(targetEntity="TSK\InstructorBundle\Entity\Instructor", inversedBy="schedules")
     * @ORM\JoinTable(name="tsk_schedule_instructor", joinColumns={@ORM\JoinColumn(name="fk_schedule_entity_id", referencedColumnName="schedule_entity_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="fk_instructor_id", referencedColumnName="instructor_id")})
     */
    protected $instructors;


    /**
     * @var integer
     *
     * @ORM\Column(name="capacity", type="integer", nullable=true)
     */
    protected $capacity;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_at", type="datetime")
     */
    private $startAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_at", type="datetime", nullable=true)
     */
    private $endAt;

    /**
     * Recurrence rule string see RFC2445, RFC5545
     * @var string
     *
     * @ORM\Column(name="rrule", type="string", nullable=true)
     */
    private $rrule;

    /**
     * Specifies whether entity should take up all day
     * @var boolean
     *
     * @ORM\Column(name="options", type="array", nullable=true)
     */
    private $options;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision", type="integer")
     */
    private $revision = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="guests", type="text", nullable=true)
     */
    private $guests;

    /**
     * @var string
     *
     * @ORM\Column(name="duration", type="string", length=32, nullable=true)
     */
    private $duration;

    /**
     * @var integer
     *
     * @ORM\Column(name="priority", type="integer", nullable=true)
     */
    private $priority;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_transparent", type="boolean", nullable=true)
     */
    private $isTransparent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * last-mod
     *
     * The property specifies the date and time that the
     * information associated with the calendar component was last revised
     * in the calendar store.
     *
     * @ORM\Column(type="datetime", name="last_modified_at")
     */
    private $lastModifiedAt;

    /**
     * last-processed
     *
     * The property specifies the date and time that the
     * information associated with the calendar component was last 
     * processed into schedule instances.  Mainly associated with
     * recurring events.  If last-processed < last-mod then we must
     * re-process to take into account any new changes.
     *
     * @ORM\Column(type="datetime", name="last_processed_at", nullable=true)
     */
    private $lastProcessedAt;

    /**
     * expires
     *
     * The property specifies the date and time that the
     * information is considered no longer valid and may need
     * re-processing.  This mainly involves recurring events which
     * are set to recur indefinitely, but have been processed only until
     * $expiresAt date.  When $expiresAt relapses it will be time to process
     * an additional batch of instances for this event.
     *
     * @ORM\Column(type="datetime", name="expires_at", nullable=true)
     */
    private $expiresAt;

    /**
     * max-date
     *
     * The property specifies the max date and time for which we have to
     * process data for this calendar.  If an calendar event runs until 
     * 10/2/2013, then the maxDate will be 10/2/2013 and there will be no
     * need to process instances for this calendar beyond that date.
     *
     * @ORM\Column(type="datetime", name="max_date_at", nullable=true)
     */
    private $maxDateAt;

    public function __construct()
    {
        $this->instances = new ArrayCollection();
        $this->classes = new ArrayCollection();
    }

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
     * Set title
     *
     * @param string $title
     * @return ScheduleEntity
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ScheduleEntity
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set startAt
     *
     * @param \DateTime $startAt
     * @return ScheduleEntity
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * Get startAt
     *
     * @return \DateTime 
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     * @return ScheduleEntity
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime 
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * Set revision
     *
     * @param integer $revision
     * @return ScheduleEntity
     */
    public function setRevision($revision)
    {
        $this->revision = $revision;

        return $this;
    }

    /**
     * Get revision
     *
     * @return integer 
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * Set guests
     *
     * @param string $guests
     * @return ScheduleEntity
     */
    public function setGuests($guests)
    {
        $this->guests = $guests;

        return $this;
    }

    /**
     * Get guests
     *
     * @return string 
     */
    public function getGuests()
    {
        return $this->guests;
    }

    /**
     * Set duration
     *
     * @param string $duration
     * @return ScheduleEntity
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return string 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return ScheduleEntity
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set isTransparent
     *
     * @param boolean $isTransparent
     * @return ScheduleEntity
     */
    public function setIsTransparent($isTransparent)
    {
        $this->isTransparent = $isTransparent;

        return $this;
    }

    /**
     * Get isTransparent
     *
     * @return boolean 
     */
    public function getIsTransparent()
    {
        return $this->isTransparent;
    }
 
    /**
     * Get user.
     *
     * @return user.
     */
    public function getUser()
    {
        return $this->user;
    }
 
    /**
     * Set user.
     *
     * @param user the value to set.
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }
 
    /**
     * Get school.
     *
     * @return school.
     */
    public function getSchool()
    {
        return $this->school;
    }
 
    /**
     * Set school.
     *
     * @param school the value to set.
     */
    public function setSchool($school)
    {
        $this->school = $school;
        return $this;
    }
 
    /**
     * Get rrule.
     *
     * @return rrule.
     */
    public function getRrule()
    {
        return $this->rrule;
    }
 
    /**
     * Set rrule.
     *
     * @param rrule the value to set.
     */
    public function setRrule($rrule)
    {
        $this->rrule = $rrule;
        return $this;
    }

    public function __toString()
    {
        if (empty($this->title)) {
            return '<new schedule entity>';
        }
        return $this->title;
    }
 
    /**
     * Get category.
     *
     * @return category.
     */
    public function getCategory()
    {
        return $this->category;
    }
 
    /**
     * Set category.
     *
     * @param category the value to set.
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }
 
    /**
     * Get location.
     *
     * @return location.
     */
    public function getLocation()
    {
        return $this->location;
    }
 
    /**
     * Set location.
     *
     * @param location the value to set.
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return CalendarEntity
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }


    /**
     * Set lastModifiedAt
     *
     * @param \DateTime $lastModifiedAt
     * @return CalendarEntity
     */
    public function setLastModifiedAt($lastModifiedAt)
    {
        $this->lastModifiedAt = $lastModifiedAt;
    
        return $this;
    }

    /**
     * Get lastModifiedAt
     *
     * @return \DateTime 
     */
    public function getLastModifiedAt()
    {
        return $this->lastModifiedAt;
    }

    /**
     * upRevisionSequence
     */
    public function upRevisionSequence()
    {
        $this->setRevision($this->getRevision() + 1);
    }


    /**
     * onCreation
     *
     * @ORM\PrePersist()
     */
    public function onCreation()
    {
        $now = new \DateTime('now');

        $this->setCreatedAt($now);
        $this->setLastModifiedAt($now);
    }

    /**
     * onUpdate
     *
     * @ORM\PreUpdate()
     */
    public function onUpdate()
    {
        $now = new \DateTime('now');

        $this->setLastModifiedAt($now);
        $this->upRevisionSequence();
    }

    /**
     * computeDuration
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function computeDuration()
    {
        if ($this->getStartAt()) {
            $start = $this->getStartAt()->getTimestamp();
            $end = $this->getEndAt()->getTimestamp();

            $duration = $this->time_to_iso8601_duration($end - $start); 
            $this->setDuration($duration);
        }
    }

    /**
     * durationInTime
     * Convert a duration into a countable given time unit
     *
     * @param string duration
     * @param string time unit [week, day, hour, minute, second]
     * @return float
     */
    static public function durationInTime($duration, $time_unit = 'second')
    {
        if(!$duration) {
            return false;
        }

        if(!in_array($time_unit, array_keys(self::$TIME_UNITS))) {
            throw new Exception(sprintf('Wrong given time unit: %s', $time_unit));
        }

        $durationArray = self::durationToArray($duration);
        $durationSeconds = 0;
        foreach($durationArray as $unit => $value) {
            $durationSeconds += $value * self::$TIME_UNITS[$unit];
        }

        return $durationSeconds / self::$TIME_UNITS[$time_unit];
    }

    public static function durationToArray($duration)
    {
        $matches = array();

        $pattern = "#^P(?:([0-9]+)W)?(?:([0-9]+)D)?T?(?:([0-9]+)H)?(?:([0-9]+)M)?(?:([0-9]+)S)?$#";
        if(!preg_match($pattern, $duration, $matches)) {
            return null;
        }

        $result = array(
            'week'   => isset($matches[1]) ? $matches[1] : 0,
            'day'    => isset($matches[2]) ? $matches[2] : 0,
            'hour'   => isset($matches[3]) ? $matches[3] : 0,
            'minute' => isset($matches[4]) ? $matches[4] : 0,
            'second' => isset($matches[5]) ? $matches[5] : 0
        );

        return $result;
    }

        
    public function time_to_iso8601_duration($time) 
    {
        $units = array(
            "Y" => 365*24*3600,
            "D" =>     24*3600,
            "H" =>        3600,
            "M" =>          60,
            "S" =>           1,
        );
        
        $str = "P";
        $istime = false;
        
        foreach ($units as $unitName => &$unit) {
            $quot  = intval($time / $unit);
            $time -= $quot * $unit;
            $unit  = $quot;
            if ($unit > 0) {
                if (!$istime && in_array($unitName, array("H", "M", "S"))) { // There may be a better way to do this
                    $str .= "T";
                    $istime = true;
                }
                $str .= strval($unit) . $unitName;
            }
        }
        
        return $str;
    }
 
    /**
     * Get options
     *
     * @return options
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * Set options
     *
     * @param options the value to set.
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }
 
    /**
     * Get lastProcessedAt.
     *
     * @return lastProcessedAt.
     */
    public function getLastProcessedAt()
    {
        return $this->lastProcessedAt;
    }
 
    /**
     * Set lastProcessedAt.
     *
     * @param lastProcessedAt the value to set.
     */
    public function setLastProcessedAt($lastProcessedAt)
    {
        $this->lastProcessedAt = $lastProcessedAt;
        return $this;
    }
 
    /**
     * Get expiresAt.
     *
     * @return expiresAt.
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
 
    /**
     * Set expiresAt.
     *
     * @param expiresAt the value to set.
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }
 
    /**
     * Get maxDateAt.
     *
     * @return maxDateAt.
     */
    public function getMaxDateAt()
    {
        return $this->maxDateAt;
    }
 
    /**
     * Set maxDateAt.
     *
     * @param maxDateAt the value to set.
     */
    public function setMaxDateAt($maxDateAt)
    {
        $this->maxDateAt = $maxDateAt;
        return $this;
    }

    public function getInstances()
    {
        return $this->instances;
    }

    public function setInstances($instances)
    {
        foreach ($instances as $idx => $instanc) {
            $this->addInstance($instanc);
        }
    }

    public function addInstance(ScheduleInstance $instance)
    {
        $this->instances[] = $instance;
    }

    public function removeInstance(ScheduleInstance $instance)
    {
        return $this->instances->removeElement($instance);
    }

    public function getClasses()
    {
        return $this->classes;
    }

    public function setClasses($classes)
    {
        foreach ($classes as $idx => $class) {
            $this->addClass($class);
        }
    }

    public function addClass(Classes $class)
    {
        $this->classes[] = $class;
    }

    public function removeClass(Classes $class)
    {
        return $this->classes->removeElement($class);
    }

    public function getInstructors()
    {
        return $this->instructors;
    }

    public function setInstructors($instructors)
    {
        foreach ($instructors as $idx => $instructor) {
            $this->addInstructor($instructor);
        }
    }

    public function addInstructor(Instructor $instructor)
    {
        $this->instructors[] = $instructor;
    }

    public function removeInstructor(Instructor $instructor)
    {
        return $this->instructors->removeElement($instructor);
    }
 
    /**
     * Get capacity.
     *
     * @return capacity.
     */
    public function getCapacity()
    {
        return $this->capacity;
    }
 
    /**
     * Set capacity.
     *
     * @param capacity the value to set.
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
        return $this;
    }
}
