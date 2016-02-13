<?php
namespace TSK\RulerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('TSKRulerBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * @Route("/rcdeleterule/{ruleCollectionId}/{ruleGroupIdx}/{ruleIdx}", options={"expose"=true}, name="tsk_ruler_rcdeleterule")
     * @Template()
     * @Method({"GET"})
     */
    public function deleteRuleAction($ruleCollectionId, $ruleGroupIdx, $ruleIdx)
    {
        $em = $this->getDoctrine()->getManager();
        $rc = $em->getRepository('TSKRulerBundle:RuleCollection')->find($ruleCollectionId);
        $ruleGroup = $rc->getRuleGroups()->get($ruleGroupIdx);
        $result = 0;
        if ($ruleGroup) {
            $rule = $ruleGroup->getRules()->get($ruleIdx);
            if ($rule) {
                // delete rule
                $em->remove($rule);
                $em->flush();
                $result = 1;
            }
        }

        return $this->render('TSKRulerBundle:Default:drawItem.html.twig', array('result' => $result));
    }

    /**
     * @Route("/rcdeletereward/{ruleCollectionId}/{ruleGroupIdx}/{rewardIdx}", options={"expose"=true}, name="tsk_ruler_rcdeletereward")
     * @Template()
     * @Method({"GET"})
     */
    public function deleteRewardAction($ruleCollectionId, $ruleGroupIdx, $rewardIdx)
    {
        $em = $this->getDoctrine()->getManager();
        $rc = $em->getRepository('TSKRulerBundle:RuleCollection')->find($ruleCollectionId);
        $ruleGroup = $rc->getRuleGroups()->get($ruleGroupIdx);
        $result = 0;
        if ($ruleGroup) {
            $reward = $ruleGroup->getRewards()->get($rewardIdx);
            if ($reward) {
                // delete reward
                $em->remove($reward);
                $em->flush();
                $result = 1;
            }
        }

        return $this->render('TSKRulerBundle:Default:drawItem.html.twig', array('result' => $result));
    }
}
