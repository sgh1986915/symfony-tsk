<?php
namespace TSK\RulerBundle\Util;

use TSK\RulerBundle\Entity\Rule;
use TSK\RulerBundle\Entity\Reward;
use Doctrine\ORM\EntityManager;

class PropositionParser
{
    private $manager;
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }
    /**
     * parseProposition 
     * 
     * @param mixed $proposition 
     * @access public
     * @return array($rules, $rewards)
     */
    public function parse($array)
    {
        $proposition = $array['clause'];
        $proposition = preg_replace('/^IF /', '', $proposition);
        list($rules, $rewards) = preg_split('/ THEN /i', $proposition);
        $ruleParts = preg_split('/ and /i', $rules); 
        foreach ($ruleParts as $rulePart) {
            list($fact, $comparator, $value) = preg_split('/\s/', $rulePart);
            $rule = new Rule();
            $factRepo = $this->manager->getRepository('TSKRulerBundle:Fact');
            $myFact = $factRepo->findOneBy(array('name' => $fact));
            if (!$myFact) {
                throw new \Exception('No fact [' . $fact . ']');
            }
            $rule->setFact($myFact);
            $rule->setComparator($comparator);
            $rule->setValue($value);
            $myRules[] = $rule;
        }

        $rewardParts = preg_split('/ and /i', $rewards);
        foreach ($rewardParts as $rewardPart) {
            preg_match('/(\w+)\((\w+)?\)/', $rewardPart, $matches);
            if (count($matches) == 2) {
                $method = $matches[1];
                $meta = null;
            } else if (count($matches) == 3) {
                $method = $matches[1];
                $meta = $matches[2];
            } else {
                ld($matches);
                print "nope [$rewardPart]" . count($matches); exit;
            }
            $reward = new Reward();
            $reward->setMethod($method);
            $reward->setMetaData($meta);
            $myRewards[] = $reward;
        }
        return array($myRules, $myRewards);
    }
}
