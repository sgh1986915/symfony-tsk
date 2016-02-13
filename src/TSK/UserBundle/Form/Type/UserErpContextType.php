<?php
namespace TSK\userBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use TSK\UserBundle\Form\Type\ContactType;
use Doctrine\ORM\EntityRepository;

class UserErpContextType extends AbstractType
{
    private $em;
    private $securityContext;

    public function __construct($em, SecurityContext $securityContext)
    {
        $this->em = $em;
        $this->securityContext = $securityContext;
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
        $contact = $this->securityContext->getToken()->getUser()->getContact();
        if ($contact->getSchools()->count() > 1) {
            $builder->add('organization', 'entity_hidden', array('my_class' => 'TSK\UserBundle\Entity\Organization'));
            $builder->add('school', 'entity', array(
                'class' => 'TSKSchoolBundle:School',
                'attr' => array('class' => 'input-medium'),
                'empty_value' => '-- Select a school',
                'query_builder' => function(EntityRepository $er) use ($contact) {
                   $q = $er->createQueryBuilder('s')
                    ->join('s.schoolContacts', 'c', 'WITH', 'c.id=:contact') 
                    ->setParameter(':contact', $contact->getId());
                    return $q;
                }
            ));
        }
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
