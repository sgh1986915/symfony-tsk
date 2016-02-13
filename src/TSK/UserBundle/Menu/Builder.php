<?php
namespace TSK\UserBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        // This will add the proper classes to your UL
        // Use push_right if you want your menu on the right
        $menu = $factory->createItem('root', array(
            'navbar' => true,
            'pull-right' => true,
        ));

        // Regular menu item, no change
        //$menu->addChild('TSK ERP System', array('route' => 'sonata_admin_dashboard'));
		$menu->addChild('TSK ERP System', array('route' => 'tsk_user_default_index'));

        // Create a dropdown
        $orgDropdown = $menu->addChild('Organization', array(
            'dropdown' => true,
            'caret' => true,
        ));

        $orgDropdown->addChild('Manage Permissions', array('route' => 'tsk_user_permission_index'));
        $orgDropdown->addChild('List Organizations', array('route' => 'admin_tsk_user_organization_list'));
        $orgDropdown->addChild('List Programs', array('route' => 'admin_tsk_program_program_list'));
        $orgDropdown->addChild('List Users', array('route' => 'admin_tsk_user_user_list'));
        $orgDropdown->addChild('List Ranks', array('route' => 'admin_tsk_rank_rank_list'));
        $orgDropdown->addChild('Register New User', array('route' => 'admin_tsk_user_user_create'));

        // Create class dropdown
        $classDropdown = $menu->addChild('Classes', array(
            'dropdown' => true,
            'caret' => true,
        ));
        $classDropdown->addChild('List Classes', array('route' => 'admin_tsk_class_classes_list'));
        $classDropdown->addChild('List Class Types', array('route' => 'admin_tsk_class_classtype_list'));
        // $this->addDivider($classDropdown);
        $classDropdown->addChild('Manage Schedules', array('route' => 'admin_tsk_schedule_scheduleentity_list'));
        $classDropdown->addChild('View Class Schedule', array('route' => 'tsk_schedule_default_index'));

        // Create school dropdown
        $schoolDropdown = $menu->addChild('Schools', array(
            'dropdown' => true,
            'caret' => true,
        ));
        $schoolDropdown->addChild('List Schools', array('route' => 'admin_tsk_school_school_list'));
        $schoolDropdown->addChild('List Instructors', array('route' => 'admin_tsk_instructor_instructor_list'));

        // Create student dropdown
        $studentDropdown = $menu->addChild('Students', array(
            'dropdown' => true,
            'caret' => true,
        ));
        $studentDropdown->addChild('List Students', array('route' => 'admin_tsk_student_student_list'));
        $studentDropdown->addChild('Register Student', array('route' => 'tsk_student_default_registerstudent'));
        // $this->addDivider($studentDropdown);
        $studentDropdown->addChild('Receive Payment', array('route' => 'tsk_payment_default_index'));
        $studentDropdown->addChild('Deferred Revenue Simulator', array('route' => 'deferral_graph'));

        return $menu;
    }

    public function rightSideMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', array(
            'navbar' => true,
            'push_right' => true,
        ));
        $sc = $this->container->get('security.context');
        $user = $sc->getToken()->getUser();

        if (is_object($user)) {
            $menu->addChild('Welcome ' . $user, array('route' => 'fos_user_profile_show'));
            $menu->addChild('Logout', array('route' => 'fos_user_security_logout'));
        }

        return $menu;
    }
}
