<?php

namespace TSK\RankBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use TSK\StudentBundle\Entity\Student;
use TSK\StudentBundle\Entity\StudentRank;
use TSK\StudentBundle\Form\Type\StudentPromotionFormType;
use Doctrine\ORM\EntityRepository;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('TSKRankBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * @Route("/dropdown/{rankId}", options={"expose"=true})
     * @Template()
     * @Method({"GET"})
     */
    public function dropdownAction($rankId=null)
    {
        $org = $this->getRequest()->getSession()->get('tsk_organization_id');
        $em = $this->getDoctrine()->getManager();
        if ($rankId) {
            $data = $em->getRepository('TSKRankBundle:Rank')->find($rankId);
        } else {
            $data = null;
        }
        $form = $this->createFormBuilder()
            ->add('value', 'entity', array(
                        'class' => 'TSKRankBundle:Rank',
                        'data' => $data,
                        'empty_value' => '-- Select rank',
                        'query_builder' => function(EntityRepository $er) use ($org) {
                        return $er->createQueryBuilder('u')
                        ->where('u.organization=:org')
                        ->setParameter(':org', $org);
                        }
                        ))
            ->getForm();

        return $this->render('TSKRankBundle:Default:drawRankRuleForm.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/test/{id}")
     * @Template()
     * @Method({"GET"})
     */
    public function testAction(Student $student)
    {
        $em = $this->getDoctrine()->getManager();
        $forms = array();
        $eligibleRanks = $student->getEligibleRanks();
        foreach ($eligibleRanks as $rank) {
            $studentRank = new StudentRank();
            $studentRank->setStudent($student);
            $studentRank->setRank($rank);
            $studentRank->setRankType($rank->getRankType());
            $form = $this->createForm(new StudentPromotionFormType(), $studentRank);
            $forms[] = $form->createView();
        }

        $srRepo = $em->getRepository('TSKStudentBundle:StudentRank');
        $studentCreditRepo = $em->getRepository('TSKStudentBundle:StudentCreditLog');
        return array(
            'student' => $student,
            'forms' => $forms,
            'currentRank' => $student->getRank()->getId(),
            'numberStripesEarnedAtCurrentBelt' => $srRepo->getNumberStripesEarnedAtCurrentBelt($student),
            'numberKbStripesEarnedAtCurrentBelt' => $srRepo->getNumberKbStripesEarnedAtCurrentBelt($student),
            'numberGrStripesEarnedAtCurrentBelt' => $srRepo->getNumberGrStripesEarnedAtCurrentBelt($student),
            'lastPromotionDate' => $srRepo->getLastPromotionDate($student),
            'lastKbPromotionDate' => $srRepo->getLastKbPromotionDate($student),
            'lastGrPromotionDate' => $srRepo->getLastGrPromotionDate($student),
            'creditsEarnedSinceLastPromotion' => $studentCreditRepo->getCreditsEarnedSinceLastPromotion($student),
            'kbCreditsEarnedSinceLastKbPromotion' => $studentCreditRepo->getKbCreditsEarnedSinceLastKbPromotion($student),
            'grCreditsEarnedSinceLastGrPromotion' => $studentCreditRepo->getGrCreditsEarnedSinceLastGrPromotion($student),
        );
 
    }
}
