<?php
namespace TSK\RulerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use TSK\RulerBundle\Ruler\RewardsEngineChain;

class RewardType extends AbstractType
{
    private $rewardsEngineChain;

    public function __construct(RewardsEngineChain $rewardsEngineChain)
    {
        $this->rewardsEngineChain = $rewardsEngineChain;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $org = '';
        $builder
            // ->add('name')
            ->add('method', 'choice', array(
                'choices' => $this->getRewardMethods(),
                'attr' => array('class' => 'methodPicker')
            ))
            ->add('metaData')
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TSK\RulerBundle\Entity\Reward',
            'compound' => true,
        ));

        $resolver->setRequired(array(
        ));
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'tsk_reward';
    }

    protected function getRewardMethods()
    {
        $rewardsEngines = $this->rewardsEngineChain->getRewardsEngines();
        $methods = array();
        foreach ($rewardsEngines as $context => $rewardsEngine) {
            $ref = new \ReflectionObject($rewardsEngine);
            foreach ($ref->getMethods() as $method) {
                if (preg_match('/^reward/', $method->getName())) {
                    $methods[$context][$method->getName()] = $method->getName();
                }
            }
        }
        return $methods;
    }


}
