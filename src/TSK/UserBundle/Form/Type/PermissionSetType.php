<?php
namespace TSK\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use TSK\UserBundle\Form\Model\Permission;
use TSK\UserBundle\Permissions\PermissionsManagerInterface;

class PermissionSetType extends AbstractType
{
    private $permissionsManager;

    public function __construct(PermissionsManagerInterface $permissionsManager)
    {
        $this->permissionsManager = $permissionsManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // You could tag the permission managers and then get them
        // here instead of hard coding ...
        $builder->add('permissionType', 'choice', array(
            'choices' => array('admin' => 'sonata admins', 'object' => 'objects', 'route' => 'routes'),
            'label' => 'Permission Type'
            )
        );
        $builder->add('identity', 'tsk_identity_dropdown');
        $builder->add('identityType', 'hidden');

        $factory = $builder->getFormFactory();
        // We use a closure to populate permissions and masks for user
        $refreshPerms = function($form, $perms) use ($factory) {
        $form->add($factory->createNamed('permissions', 'collection', $perms, array(
            'type' => 'tsk_permission_type',
            'options' => array('label_attr' => array('class' => 'perm-collection'))
            )));
        };

        //
        // Use FormEvents to add the permissions array dynamically
        //
        $builder->addEventListener(
            FormEvents::PRE_BIND,
            function(FormEvent $event) use ($factory, $refreshPerms) {
                $form = $event->getForm();
                $data = $event->getData();

                if (!empty($data['identity'])) {
                    $perms = $this->permissionsManager->getPermissionsForIdentity($data['identity'], $data['identityType']);
                    $refreshPerms($form, $perms);
                }
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) use ($factory, $refreshPerms) {
                $form = $event->getForm();
                $data = $event->getData();

                if ($data->getIdentity()) {
                    $perms = $this->permissionsManager->getPermissionsForIdentity($data->getIdentity(), $data->getIdentityType());
                    $refreshPerms($form, $perms);
                }
            }
        );
    }

    public function getName()
    {
        return 'tsk_permission_set';
    }

    public function getParent()
    {
        return 'form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TSK\UserBundle\Form\Model\PermissionSet',
            'compound' => true
        ));
    }
}
