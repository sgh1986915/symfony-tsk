<?php
namespace TSK\userBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use TSK\UserBundle\Form\Type\ContactType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class AdminErpContextType extends AbstractType
{
    private $securityContext;

    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * buildForm 
     * Grabs organization-specific class types
     * 
     * @param FormBuilderInterface $builder 
     * @param array $options 
     * @access public
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('organization', 'entity', array(
            'class' => 'TSKUserBundle:Organization',
            'attr' => array('class' => 'input-medium')
            )
        );
        $factory = $builder->getFormFactory();

        $builder->addEventListener(
            FormEvents::PRE_BIND,
            function(FormEvent $event) use ($factory) {
                $form = $event->getForm();
                $data = $event->getData();

                $organization = (!empty($data['organization'])) ? $data['organization'] : '';
                $school = (!empty($data['school'])) ? $data['school'] : '';

                if ($organization) {
                    $form->add($factory->createNamed('school', 'entity', NULL, array(
                        'class' => 'TSKSchoolBundle:School',
                        'data' => $school,
                        'required' => true,
                        'attr' => array('class' => 'input-medium'),
                        'empty_value' => '-- Select School',
                        'query_builder' => function(EntityRepository $er) use ($organization) {
                           return $er->createQueryBuilder('s')
                            ->innerJoin('s.contact', 'c', 'WITH', 'c.organization = :organization') 
                            ->setParameter(':organization', $organization);
                        }
                    )));
                }
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) use ($factory) {
                $form = $event->getForm();
                $data = $event->getData();

                $organization = $data->getOrganization();
                $school = $data->getSchool();

                if ($organization) {
                    $form->add($factory->createNamed('school', 'entity', NULL, array(
                        'class' => 'TSK\SchoolBundle\Entity\School',
                        'data' => $school,
                        'required' => true,
                        'empty_value' => '-- Select School',
                        'attr' => array('class' => 'input-medium'),
                        'query_builder' => function(EntityRepository $er) use ($organization) {
                           return $er->createQueryBuilder('s')
                            ->innerJoin('s.contact', 'c', 'WITH', 'c.organization = :organization') 
                            ->setParameter(':organization', $organization);
                        }
                    )
                    ));
                }
            }
        );
    }

    public function getName()
    {
        return 'tsk_user_erp_context_type';
    }

    public function getParent()
    {
        return 'form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TSK\UserBundle\Form\Model\ErpContext',
            'compound' => 'true'
        ));
    }
}
