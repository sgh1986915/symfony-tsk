<?php
namespace TSK\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager as SonataORMModelManager;

class UserAdmin extends Admin
{
    protected $translationDomain = 'TSKUserBundle';
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('User')
            ->add('contact', 'sonata_type_model_list', array(), array(
                'admin_code' => 'tsk.admin.contact'
            ))
            ->add('username')
            ->add('plainPassword', 'text', array('label' => 'Password', 'required' => preg_match('/create/', $this->request->get('_route'))))
            ->with('Management')
                // ->add('roles', 'sonata_security_roles', array( 'multiple' => true))
                ->add('roles', 'tsk_role_type')
                ->add('locked', null, array('required' => false))
                ->add('expired', null, array('required' => false))
                ->add('enabled', null, array('required' => false))
                ->add('credentialsExpired', null, array('required' => false))
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('username')
            // ->add('lastName')
            // ->add('state')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, array('label' => 'Contact ID'))
            ->add('username')
            ->add('roles')
            ->add('contact.organization.title', null, array('label' => 'Organization'))
            ->add('contact.firstName', null, array('label' => 'First Name'))
            ->add('contact.lastName', null, array('label' => 'Last Name'))
            ->add('contact.email', null, array('label' => 'Email'))
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('firstName')
                ->assertMaxLength(array('limit' => 32))
            ->end()
        ;
    }

    public function prePersist($user)
    {
        $this->getUserManager()->updateCanonicalFields($user);
        $this->getUserManager()->updatePassword($user);
        // $this->getUserManager()->addRole($user, 'ROLE_ADMIN');
    }

    public function preUpdate($user)
    {
        $this->getUserManager()->updateCanonicalFields($user);
        $this->getUserManager()->updatePassword($user);
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    } 
}
