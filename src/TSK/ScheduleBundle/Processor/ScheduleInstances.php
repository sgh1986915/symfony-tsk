<?php
namespace TSK\ScheduleBundle\Processor;

use Doctrine\ORM\EntityManager;
use TSK\ScheduleBundle\Entity\ScheduleEntity;
use TSK\ScheduleBundle\Entity\ScheduleInstance;
use Recurr\RecurrenceRule;
use Recurr\RecurrenceRuleTransformer;

class ScheduleInstances
{
    private $em;
    private $maxInstances = 100;
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * processEntity 
     * This function is responsible for taking a ScheduleEntity object
     * and generating up to $maxInstances ScheduleInstance objects from it.
     * 
     * @param ScheduleEntity $scheduleEntity 
     * @param mixed $maxInstances 
     * @access public
     * @return void
     */
    public function processEntity(ScheduleEntity $scheduleEntity, $maxInstances=null)
    {
        if (is_null($maxInstances)) {
            $maxInstances = $this->maxInstances;
        }
        if ($maxInstances > $this->maxInstances) {
            $maxInstances = $this->maxInstances;
        }

        // get rrule
        $timezone = 'America/New_York';
        $today = new \DateTime();
        $dateTimeZone = new \DateTimeZone($timezone);
        if ($scheduleEntity->getRrule()) {
            // Handling a recurring event
            $rrule = new RecurrenceRule($scheduleEntity->getRrule(), $scheduleEntity->getStartAt(), $timezone);
            $transformer = new RecurrenceRuleTransformer($rrule);

            $instances = $transformer->getComputedArray(
                    $scheduleEntity->getStartAt(), 
                    $dateTimeZone,
                    $today->add(new \DateInterval('P6M')), // compute dates 6 months out
                    $dateTimeZone
                    );
            $options = $scheduleEntity->getOptions();

            // generate instances
            if (count($instances)) {
                $count = 0;
                foreach ($instances as $startDateTime) {
                    if ($count < $maxInstances) {
                        // compute endDateTime based on duration
                        $endDateTime = clone $startDateTime;
                        $endDateTime->add(new \DateInterval($scheduleEntity->getDuration()));
                        $si = new ScheduleInstance();
                        $si->setScheduleEntity($scheduleEntity);
                        $si->setUser($scheduleEntity->getUser());
                        $si->setSchool($scheduleEntity->getSchool());
                        $si->setTitle($scheduleEntity->getTitle());
                        $si->setStart($startDateTime);
                        $si->setEnd($endDateTime);
                        $si->setIsAllDay(in_array('all_day', $scheduleEntity->getOptions()));
                        $lastEnd = $endDateTime;
                        $this->em->persist($si);
                        $count++;
                    }
                }
            } else {
                $instances = array(null);
            }
            $lastInstance = array_pop($instances);
        } else {
            // Handling a non-recurring event
            $si = new ScheduleInstance();
            $si->setScheduleEntity($scheduleEntity);
            $si->setUser($scheduleEntity->getUser());
            $si->setSchool($scheduleEntity->getSchool());
            $si->setTitle($scheduleEntity->getTitle());
            $si->setStart($scheduleEntity->getStartAt());
            $si->setEnd($scheduleEntity->getEndAt());
            $si->setIsAllDay(in_array('all_day', $scheduleEntity->getOptions()));
            $this->em->persist($si);

            // $lastEnd = $lastInstance = $scheduleEntity->getStartAt()->add(new \DateInterval($scheduleEntity->getDuration()));
        }

        // update last_processed
        // $scheduleEntity->setLastProcessedAt(new \DateTime());
        
        // update expireAt
        // $scheduleEntity->setExpiresAt($lastEnd);

        // update maxDateAt
        // $scheduleEntity->setMaxDateAt($lastInstance);
        // $this->em->persist($scheduleEntity);

        $this->em->flush();
    }
}
