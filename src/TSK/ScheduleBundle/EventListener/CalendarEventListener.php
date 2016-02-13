<?php
namespace TSK\ScheduleBundle\EventListener;

use ADesigns\CalendarBundle\Event\CalendarEvent;
use ADesigns\CalendarBundle\Entity\EventEntity;
use Doctrine\ORM\EntityManager;

class CalendarEventListener
{
    private $entityManager;
    private $securityContext;

    public function __construct(EntityManager $entityManager, $securityContext)
    {
        $this->entityManager = $entityManager;
        $this->securityContext = $securityContext;
    }

    public function loadEvents(CalendarEvent $calendarEvent)
    {
        $startDate = $calendarEvent->getStartDatetime();
        $endDate = $calendarEvent->getEndDatetime();

        $user = $this->securityContext->getToken()->getUser();
        $instanceRepository = $this->entityManager->getRepository('TSKScheduleBundle:ScheduleInstance');
        // TODO:  Filter instances based on userId, schoolId, etc ...
        $scheduleInstances = $instanceRepository
            ->createQueryBuilder('si')
            ->where('si.start BETWEEN :startDate and :endDate')
            ->setParameter(':startDate', $startDate->format('Y-m-d H:i:s'))
            ->setParameter(':endDate', $endDate->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult();

        foreach($scheduleInstances as $si) {
            $eventEntity = new EventEntity(
                $si->getTitle(),
                $si->getStart(),
                $si->getEnd(),
                $si->getIsAllDay()
            );
            $eventEntity->setId($si->getId());
            $color = '';
            $classes = $si->getScheduleEntity()->getClasses();
            if ($classes) {
                $class = $classes->first();
                if (is_object($class)) {
                    $color = $class->getScheduleColor();
                }
            }
            if (!$color) {
                $color = $si->getScheduleEntity()->getCategory()->getColor();
            }
            if ($color) {
                $eventEntity->setBgColor($color);
            }
            $calendarEvent->addEvent($eventEntity);
        }
    }
}
