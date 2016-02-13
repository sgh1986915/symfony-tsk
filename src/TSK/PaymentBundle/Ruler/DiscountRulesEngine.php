<?php
namespace TSK\PaymentBundle\Ruler;

use Ruler\Context;
use Doctrine\ORM\EntityManager;
use TSK\RulerBundle\Ruler\RulesEngine;
use TSK\RulerBundle\Model\RuleGroup;
use TSK\RulerBundle\Ruler\RulesEngineInterface;
use TSK\RulerBundle\Ruler\RewardsEngineInterface;
use TSK\StudentBundle\Form\Model\StudentRegistration;

class DiscountRulesEngine extends RulesEngine implements RulesEngineInterface
{
    public function __construct(EntityManager $em, RewardsEngineInterface $rewardsEngine, $debug=false)
    {
        parent::__construct($em, $rewardsEngine, $debug);
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function buildContext($studentRegistration)
    {
        // Need to build proper context and set up proper rules
        // What programs is this user eligible for?
        // do they have any training family members?
        // if (num_familymembers == 1) then allow for 2nd family member programs
        // if (num_familymembers > 1) then allow for 3rd family member programs

        // how many active contracts does this user have?
        // if (num_contracts > 1) then allow sign up for 3 year program
        // Build context ...

        $tfmRepo = $this->em->getRepository('TSK\StudentBundle\Entity\TrainingFamilyMembers');
        if ($studentRegistration->getTrainingFamilyMember()) {
            $nextSlot = $tfmRepo->getNextSlotForPrimaryStudent($studentRegistration->getTrainingFamilyMember()->getPrimaryStudent());
            $this->logger->debug('tfm=' . $studentRegistration->getTrainingFamilyMember()->getId());
        } else {
            $nextSlot = 1;
        }
        $this->logger->debug("nextSlot = $nextSlot");
        if ($studentRegistration->getStudent()) {
            $numContracts = $studentRegistration->getStudent()->getContracts()->count();
        } else {
            $numContracts = 0;
        }
        $ctx = new Context(array(
            'studentRegistration' => $studentRegistration,
            'numContracts' => $numContracts,
            'student.nextAvailableTFMSlot' => $nextSlot
        ));
        return $ctx;
    }

    /**
     * getNextAvailableTFMSlot 
     * This function determines the next training family slot is available
     * for a given primary student.  Slots can either be 1, 2 or 3.
     * 
     * @param mixed $studentRegistration 
     * @param int $maxSlot 
     * @access public
     * @return void
    public function getNextAvailableTFMSlot($studentRegistration, $maxSlot=3)
    {
        if ($studentRegistration->getTrainingFamilyMember()) {
            $tfmRepo = $this->em->getRepository('TSK\StudentBundle\Entity\TrainingFamilyMembers');
            $results = $tfmRepo->findBy(array('primaryStudent' => $studentRegistration->getTrainingFamilyMember()));
            $nextSlot = 1;
            foreach ($results as $result) {
                if ($result->getOrdinalPosition() == $nextSlot) {
                    $nextSlot++;
                }
            }
            return min($nextSlot, $maxSlot);
        }
    }
     */
}
