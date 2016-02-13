<?php
namespace TSK\userBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use TSK\UserBundle\Form\Type\ContactType;

class OrganizationType extends AbstractType
{
    private $session;
    private $sessionKey;
    public function __construct($session, $sessionKey)
    {
        $this->session = $session;
        $this->sessionKey = $sessionKey;
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
        $org_id = $this->session->get($this->sessionKey);
        if ($org_id) {
            $builder->add('organization', 'hidden', array('data' => $org_id));
        } else {
            $builder->add('organization');
        }
    }

    public function getName()
    {
        return 'tsk_organization_form_type';
    }

    public function getParent()
    {
        return 'hidden';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            // 'data_class' => 'TSK\UserBundle\Entity\User',
        ));
    }
}
