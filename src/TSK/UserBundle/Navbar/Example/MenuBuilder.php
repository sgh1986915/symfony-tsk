<?php
namespace TSK\UserBundle\Navbar\Example;

use Liip\ThemeBundle\ActiveTheme;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Mopa\Bundle\BootstrapBundle\Navbar\AbstractNavbarMenuBuilder;
use Knp\Menu\FactoryInterface;

/**
 * An example howto inject a default KnpMenu to the Navbar
 * see also Resources/config/example_menu.yml
 * and example_navbar.yml
 * @author phiamo
 *
 */
class MenuBuilder extends AbstractNavbarMenuBuilder
{
    protected $securityContext;

    public function __construct(FactoryInterface $factory, SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
        parent::__construct($factory);
    }

    public function createEmptyMenu(Request $request)
    {
        $menu = $this->createNavbarMenuItem();
        return $menu;
    }
    public function createMainMenu(Request $request)
    {

        $menu = $this->createNavbarMenuItem();
        if ($this->securityContext->getToken()->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
            $orgDropdown = $this->createDropdownMenuItem($menu, "Organization", false, array('icon' => 'caret'));
            $orgDropdown->addChild('Manage Permissions', array('route' => 'tsk_user_permission_index'));
            $orgDropdown->addChild('List Organizations', array('route' => 'admin_tsk_user_organization_list'));
            $orgDropdown->addChild('List Programs', array('route' => 'admin_tsk_program_program_list'));
            $orgDropdown->addChild('List Users', array('route' => 'admin_tsk_user_user_list'));
            $orgDropdown->addChild('List Ranks', array('route' => 'admin_tsk_rank_rank_list'));
            $orgDropdown->addChild('Register New User', array('route' => 'admin_tsk_user_user_create'));
        }
        $classDropdown = $this->createDropdownMenuItem($menu, "Classes", false, array('icon' => 'caret'));
        $classDropdown->addChild('List Classes', array('route' => 'admin_tsk_class_classes_list'));
        $classDropdown->addChild('List Class Types', array('route' => 'admin_tsk_class_classtype_list'));
        $this->addDivider($classDropdown);
        $classDropdown->addChild('Manage Schedules', array('route' => 'admin_tsk_schedule_scheduleentity_list'));
        $classDropdown->addChild('View Class Schedule', array('route' => 'tsk_schedule_default_index'));

        $schoolDropdown = $this->createDropdownMenuItem($menu, "Schools", false, array('icon' => 'caret'));
        $schoolDropdown->addChild('List Schools', array('route' => 'admin_tsk_school_school_list'));
        $schoolDropdown->addChild('List Instructors', array('route' => 'admin_tsk_instructor_instructor_list'));

        $dropdown = $this->createDropdownMenuItem($menu, "Students", false, array('icon' => 'caret'));
        $dropdown->addChild('List Students', array('route' => 'admin_tsk_student_student_list'));
        $dropdown->addChild('Register Student', array('route' => 'tsk_student_default_registerstudent'));
        // $this->addDivider($dropdown);
        // $dropdown->addChild('Create Prospective Student', array('route' => 'admin_tsk_user_prospective_create'));
        // $dropdown->addChild('List Prospective Students', array('route' => 'admin_tsk_user_prospective_list'));
        $this->addDivider($dropdown);
        $dropdown->addChild('Collect Student Payment', array('route' => 'tsk_payment_default_index'));
        $dropdown->addChild('Deferred Revenue Simulator', array('route' => 'deferral_graph'));
        return $menu;
    }

    public function createRightSideDropdownMenu(Request $request, ActiveTheme $activeTheme)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav pull-right');
        $user = $this->securityContext->getToken()->getUser();

        if (is_object($user)) {
            $menu->addChild('Welcome ' . $user, array('route' => 'fos_user_profile_show'));
            $menu->addChild('Logout', array('route' => 'fos_user_security_logout'));
        }
        // ... add theme change
/*

        $dropdown = $this->createDropdownMenuItem($menu, "Change Theme", true, array('icon' => 'caret'));
        foreach ($activeTheme->getThemes() as $theme) {
            $themeDropdown = $dropdown->addChild($theme, array('route' => 'liip_theme_switch', 'routeParameters' => array('theme' => $theme)));
            if ($activeTheme->getName() === $theme) {
                $themeDropdown->setCurrent(true);
            }

        }

        $dropdown = $this->createDropdownMenuItem($menu, "Tools Menu", true, array('icon' => 'caret'));
        $dropdown->addChild('Symfony', array('uri' => 'http://www.symfony.com'));
        $dropdown->addChild('bootstrap', array('uri' => 'http://twitter.github.com/bootstrap/'));
        $dropdown->addChild('node.js', array('uri' => 'http://nodejs.org/'));
        $dropdown->addChild('less', array('uri' => 'http://lesscss.org/'));

        //adding a nice divider
        $this->addDivider($dropdown);
        $dropdown->addChild('google', array('uri' => 'http://www.google.com/'));
        $dropdown->addChild('node.js', array('uri' => 'http://nodejs.org/'));

        //adding a nice divider
        $this->addDivider($dropdown);
        $dropdown->addChild('Mohrenweiser & Partner', array('uri' => 'http://www.mohrenweiserpartner.de'));
*/

        // ... add more children
        return $menu;
    }

    public function createNavbarsSubnavMenu(Request $request)
    {
        $menu = $this->createSubnavbarMenuItem();
        $menu->addChild('Top', array('uri' => '#top'));
        $menu->addChild('Navbars', array('uri' => '#navbars'));
        $menu->addChild('Template', array('uri' => '#template'));
        $menu->addChild('Menus', array('uri' => '#menus'));
        // ... add more children
        return $menu;
    }

    public function createComponentsSubnavMenu(Request $request)
    {
        $menu = $this->createSubnavbarMenuItem();
        $menu->addChild('Top', array('uri' => '#top'));
        $menu->addChild('Flashs', array('uri' => '#flashs'));
        $menu->addChild('Session Flashs', array('uri' => '#session-flashes'));
        $menu->addChild('Labels & Badges', array('uri' => '#labels-badges'));
        // ... add more children
        return $menu;
    }
}
