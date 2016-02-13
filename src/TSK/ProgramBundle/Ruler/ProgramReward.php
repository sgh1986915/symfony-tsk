<?php
namespace TSK\ProgramBundle\Ruler;

use Ruler\Context;

use TSK\RulerBundle\Model\Reward;
use TSK\RulerBundle\Ruler\RewardsEngine;
use TSK\RulerBundle\Ruler\RewardsEngineInterface;
use TSK\StudentBundle\Entity\TrainingFamilyMembers;
/**
 * ProgramReward 
 * 
 * @package 
 * @version $id$
 * @copyright 2013 Obverse Dev
 * @author Malaney J. Hill <malaney@gmail.com> 
 * @license PHP Version 3.01 {@link http://www.php.net/license/3_01.txt}
 */
class ProgramReward extends RewardsEngine implements RewardsEngineInterface
{
    private $em;
    private $context;
    private $reward;
    public function __construct($em)
    {
        $this->em = $em;
    }

    public function setContext(Context $context)
    {
        $this->context = $context;
    }

    public function setReward(Reward $reward)
    {
        $this->reward = $reward;
    }

    public function rewardInsertTrainingFamilyMember()
    {
        $student = $this->context['studentRegistration']->getStudent();
        if ($this->context['studentRegistration']->getTrainingFamilyMember()) {
            $primaryStudent = $this->context['studentRegistration']->getTrainingFamilyMember()->getPrimaryStudent();
        } else {
            $primaryStudent = $student;
        }
        // Get next slot
        $nextSlot = $this->em->getRepository('TSK\StudentBundle\Entity\TrainingFamilyMembers')->getNextSlotForPrimaryStudent($primaryStudent);
        $nextSlot = min($nextSlot, 3);

        $tfm = new TrainingFamilyMembers();
        $tfm->setPrimaryStudent($primaryStudent);
        $tfm->setStudent($student);
        $tfm->setOrdinalPosition($nextSlot);
        $this->em->persist($tfm);
        $this->em->flush();
    }

    public function rewardAddProgramExclude($programId)
    {
        $this->context['studentRegistration']->addProgramExclude($programId);
    }
}
