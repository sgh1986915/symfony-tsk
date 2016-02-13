<?php
namespace TSK\RankBundle\Ruler;

use Ruler\RuleBuilder;
use Ruler\RuleSet;
use Ruler\Variable;
use Ruler\Rule;
use Ruler\Operator;
use Ruler\Context;
use TSK\RankBundle\Entity\RuleGroup;
use TSK\RankBundle\Entity\RankRule;
use TSK\StudentBundle\Entity\Student;

class RankRuler
{
    private $em;
    private $debug = true;

    public function __construct($em, $debug=false)
    {
        $this->em = $em;
        $this->debug = $debug;
    }

    /**
     * getRuleGroups 
     * 
     * @param Student $student 
     * @access private
     * @return void
     */
    private function getRuleGroups(Student $student)
    {
        // Now retrieve rule groups where groups.rank.rank_order = currentRankOrder + 1 ...
        $query = $this->em->createQuery('SELECT u FROM TSK\RankBundle\Entity\RuleGroup u INNER JOIN u.rank r WHERE r.rankOrder=:rankOrder');
        $query->setParameter(':rankOrder', $student->getRank()->getRankOrder() + 1);
        $ruleGroups = $query->getResult();
        return $ruleGroups;
    }

    /**
     * applyRules 
     * 
     * @param Student $student 
     * @access public
     * @return void
     */
    public function applyRules(Student $student)
    {
        $ruleGroups = $this->getRuleGroups($student);

        $affirmatives = 0;
        if (count($ruleGroups)) {
            $ctx = $this->buildContext($student);
            foreach ($ruleGroups as $ruleGroup) {
                if ($this->evaluateRuleGroup($ruleGroup, $ctx)) {
                    $affirmatives++;
                }
            }
        }
        return ($affirmatives == count($ruleGroups));
    }

    /**
     * evaluateRuleGroup 
     * 
     * @param RuleGroup $ruleGroup 
     * @param Context $ctx 
     * @access private
     * @return void
     */
    private function evaluateRuleGroup(RuleGroup $ruleGroup, Context $ctx)
    {
        $query = $this->em->createQuery('SELECT u FROM TSK\RankBundle\Entity\RankRule u WHERE u.group=:group');
        $query->setParameter(':group', $ruleGroup);
        $rules = $query->getResult();

        $affirmatives = 0;
        foreach ($rules as $rule) {
            if ($this->evaluateRule($rule, $ctx)) {
                $affirmatives++;
            }
        }
        // if we are using the AND conjunction then every rule 
        // should evaluate to true
        // if we are using the OR conjunction then we only need
        // one rule to evalute to true
        if ($ruleGroup->getConjunction() == 'and') {
            return ($affirmatives == count($rules));
        } else if ($ruleGroup->getConjunction() == 'or') {
            return ($affirmatives > 0); 
        }
    }

    /**
     * evaluateRule 
     * 
     * @param RankRule $rule 
     * @param Context $ctx 
     * @access private
     * @return void
     */
    private function evaluateRule(RankRule $rule, Context $ctx)
    {
        $propositionClass = sprintf("Ruler\\Operator\\%s", ucfirst($rule->getComparator()));
        if (!class_exists($propositionClass)) {
            throw new \Exception("Class $propositionClass does not exist");
        }
        
        // If fact is a date, we must convert value to unix timestamp
        // to do comparison
        if (preg_match('/date/i', $rule->getFact()->getName())) {
            $date = new \DateTime($rule->getValue());
            $value = $date->getTimestamp();
        } else {
            $value = $rule->getValue();
        }
        
        $proposition = new $propositionClass(new Variable($rule->getFact()->getName()), new Variable('x' . $rule->getFact()->getName(), $value));
        // add x value to context
        $myRule = new Rule($proposition, function() { });
        if ($myRule->evaluate($ctx)) {
            if ($this->debug) print "<h2>rule passed!</h2>";
            return true;
        } else {
            if ($this->debug) print "<h2>rule failed</h2>";
            return false;
        }
    }

    /**
     * buildContext 
     * 
     * @param mixed $student 
     * @access public
     * @return void
     */
    private function buildContext(Student $student)
    {
        // Build context ...
        $srRepo = $this->em->getRepository('TSKStudentBundle:StudentRank');
        $studentCreditRepo = $this->em->getRepository('TSKStudentBundle:StudentCreditLog');
        
        $ctx = new Context(array(
            'student' => $student,
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
        ));
        return $ctx;
    }
}
