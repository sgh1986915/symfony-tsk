<?php
namespace TSK\UserBundle\Controller;

use TSK\UserBundle\Entity\Organization;
use TSK\UserBundle\Form\Type\PermissionSetType;
use TSK\UserBundle\Form\Model\PermissionSet;
use TSK\UserBundle\Form\Model\Permission;
use TSK\UserBundle\Form\Model\PermBit;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\Exception as AclException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Sonata\AdminBundle\Security\Acl\Permission\MaskBuilder;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

use Symfony\Component\Security\Acl\Domain\PermissionGrantingStrategy;
use Symfony\Component\Security\Acl\Domain\Acl;
use FOS\RestBundle\View\View;

class PermissionController extends Controller
{
    /**
     * @Route("/permtest")
     * @Template()
    public function testAction()
    {
        $router = $this->container->get('router');
        $collection = $router->getRouteCollection();
        $allRoutes = $collection->all();
        foreach ($allRoutes as $route) {
            $defaults = $route->getDefaults();
            if (preg_match('/tsk/i', $defaults['_controller'])) {
                print $defaults['_controller'] . '<br>';
            }
        }
        exit;
    }
     */

    /**
     * switchSaveAction 
     * 
     * @Route("/admin/switchSave", name="tsk_user_erp_context_save")
     * @param Request $request 
     * @access public
     * @return void
     */
    public function switchSaveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sc = $this->get('security.context');
        $isSuperAdmin = false;
        foreach ($sc->getToken()->getRoles() as $role) {
            if ($role->getRole() == 'ROLE_SUPER_ADMIN') {
                $isSuperAdmin = true;
            }
        }
        $erpContext = new \TSK\UserBundle\Form\Model\ErpContext();

        if ($isSuperAdmin) {
            $form = $this->createForm(new \TSK\UserBundle\Form\Type\AdminErpContextType(
                    $em,
                    $sc
                ), $erpContext
            );
        } else {
            $form = $this->createForm(new \TSK\UserBundle\Form\Type\UserErpContextType(
                    $em,
                    $sc
                ), $erpContext
            );
        }
        if ($request->getMethod() == 'POST') {
                $form->bind($request);
                $erpContext = $form->getData();
                // ld($erpContext); exit;
            if ($form->isValid()) {
                $orgSessionKey = $this->container->getParameter('tsk_user.session.org_key');
                $this->get('session')->set($orgSessionKey, $erpContext->getOrganization()->getId());

                $schoolSessionKey = $this->container->getParameter('tsk_user.session.school_key');
                $this->get('session')->set($schoolSessionKey, $erpContext->getSchool()->getId());
                // $this->get('session')->getFlashBag()->add('success', 'School switched!!');
                $this->get('session')->getFlashBag()->add('sonata_flash_success', 'School switched!!');
            } else {
                $errMsgs = array();
                foreach ($form->getErrors() as $err) {
                    $errMsgs[] = $err->getMessage();
                }
                // $this->get('session')->getFlashBag()->add('error', 'invalid form: ' . join(',', $errMsgs) . ' ' . $form->getErrorsAsString());
                $this->get('session')->getFlashBag()->add('sonata_flash_error', 'invalid form: ' . join(',', $errMsgs) . ' ' . $form->getErrorsAsString());
            }
        }
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * getSchoolsAction 
     * 
     * @Route("/get_erp_schools", name="tsk_user_get_erp_schools", options={"expose"=true})
     * @param Request $request 
     * @Template("TSKUserBundle:Permission:getErpSchools.html.twig")
     * @Method("POST")
     * @access public
     * @return void
     */
    public function getSchoolsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sc = $this->get('security.context');

        $session = $this->get('session');
        $schoolID = $session->get($this->container->getParameter('tsk_user.session.school_key'));
        $orgID = $session->get($this->container->getParameter('tsk_user.session.org_key'));


        $erpContext = new \TSK\UserBundle\Form\Model\ErpContext();
        if ($orgID) {
            $organization = $em->getRepository('TSKUserBundle:Organization')->find($orgID);
            $erpContext->setOrganization($organization);
        }
        if ($schoolID) {
            $school = $em->getRepository('TSKSchoolBundle:School')->find($schoolID);
            $erpContext->setSchool($school);
        }

        // Create form
        $form = $this->createForm(new \TSK\UserBundle\Form\Type\AdminErpContextType(
                $em,
                $sc
            ), $erpContext
        );

        $form->bind($request);

        return array('isSuperAdmin' => true, 'form' => $form->createView());
    }

    /**
     * switchAction 
     * @Route("/switch1")
     * @Template()
     * @Method({"GET","POST"})
     * 
     * @access public
     * @return void
     */
    public function switchAction()
    {
        // Retrieve org and school from session
        $session = $this->get('session');
        $schoolID = $session->get($this->container->getParameter('tsk_user.session.school_key'));
        $orgID = $session->get($this->container->getParameter('tsk_user.session.org_key'));
        $em = $this->getDoctrine()->getManager();
        $sc = $this->get('security.context');

        $erpContext = new \TSK\UserBundle\Form\Model\ErpContext();
        if ($orgID) {
            $organization = $em->getRepository('TSKUserBundle:Organization')->find($orgID);
            $erpContext->setOrganization($organization);
        }
        if ($schoolID) {
            $school = $em->getRepository('TSKSchoolBundle:School')->find($schoolID);
            $erpContext->setSchool($school);
        }

        // Embed into form model

        $isSuperAdmin = false;
        foreach ($sc->getToken()->getRoles() as $role) {
            if ($role->getRole() == 'ROLE_SUPER_ADMIN') {
                $isSuperAdmin = true;
            }
        }

        if ($isSuperAdmin) {
            // Create form
            $form = $this->createForm(new \TSK\UserBundle\Form\Type\AdminErpContextType(
                $em,
                $sc
            ), $erpContext
            );
        } else {
            // Create form
            $form = $this->createForm(new \TSK\UserBundle\Form\Type\UserErpContextType(
                $em,
                $sc
            ), $erpContext
            );
        }
        return array('form' => $form->createView(), 'isSuperAdmin' => $isSuperAdmin);
    }

    /**
     * getPermissionsAction 
     * @Route("/get_permissions", defaults={"_format"="html"}, name="tsk_user_get_permissions", options={"expose"=true})
     * 
     * @param Request $request 
     * @Template()
     * @access public
     * @return void
     */
    public function getPermissionsAction(Request $request)
    {
        // here we need to grab identity, query for perms
        // and populate form
        $postData = $request->request->get('tsk_permission_set');

        if (empty($postData['permissionType'])) {
            $permissionType = 'admin';
        } else {
            $permissionType = $postData['permissionType'];
        }

        switch ($permissionType) {
            case 'admin':
                $serviceName = 'tsk_user.permissions.sonata_permissions_manager';
            break;
            case 'object':
                $serviceName = 'tsk_user.permissions.object_permissions_manager';
            break;
            case 'route':
                $serviceName = 'tsk_user.permissions.route_permissions_manager';
            break;
            default:
                throw new \Exception('Invalid permissionType ' . $permissionType);
            break;
        }

        $pt = new PermissionSetType($this->container->get($serviceName));
        $permissionSet = new PermissionSet();
        $permForm = $this->createForm($pt, $permissionSet);
        $permForm->bind($request);

        $pForm = $this->createForm($pt, $permissionSet);

        return array('pt' => $pt, 'form' => $pForm->createView());
    }


    /**
     * getRoutePermissionsAction 
     * @Route("/get_route_permissions", defaults={"_format"="html"}, name="tsk_user_get_route_permissions", options={"expose"=true})
     * 
     * @param Request $request 
     * @Template()
     * @access public
     * @return void
     */
    public function getRoutePermissionsAction(Request $request)
    {
        // here we need to grab identity, query for perms
        // and populate form
        $pt = new PermissionSetType($this->container->get('tsk_user.permissions.route_permissions_manager'));
        $permissionSet = new PermissionSet();
        $permForm = $this->createForm($pt, $permissionSet);
        $permForm->bind($request);

        $pForm = $this->createForm($pt, $permissionSet);

        return array('pt' => $pt, 'form' => $pForm->createView());
    }

    /**
     * @Route("/permsave", name="perm_save")
     * @Template()
     */
    public function saveAction(Request $request)
    {
        $pt = new PermissionSetType();
        $permissionSet = new PermissionSet($this->container->get('tsk_user.permissions.sonata_permissions_manager'));
        $permForm = $this->createForm($pt, $permissionSet);

        $permForm->bind($request);

        if ($request->getMethod() == 'POST') {
            $pSet = $permForm->getData();
            if ($permForm->isValid()) {
                $this->get('session')->getFlashBag()->add('success', 'Valid form!');
            } else {
                foreach ($permForm->getErrors() as $err) {
                    ld($err); 
                }
                
                $this->get('session')->getFlashBag()->add('error', 'invalid form!');
                exit;
            }
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }



    /**
     * @Route("/admin/perm")
     * @Route("/admin/perm/{permissionType}")
     * @Route("/admin/perm/{permissionType}/{identity}")
     * @PreAuthorize("hasRole('ROLE_SUPER_ADMIN')") 
     * @Template()
     */
    public function indexAction($permissionType='admin', $identity='')
    {
        $request = $this->getRequest();
        $postData = $request->request->get('tsk_permission_set');

        if (!empty($postData['permissionType'])) {
            $permissionType = $postData['permissionType'];
        }
        
        switch ($permissionType) {
            case 'admin':
                $serviceName = 'tsk_user.permissions.sonata_permissions_manager';
            break;
            case 'object':
                $serviceName = 'tsk_user.permissions.object_permissions_manager';
            break;
            case 'route':
                $serviceName = 'tsk_user.permissions.route_permissions_manager';
            break;
            default:
                throw new \Exception('Invalid permissionType ' . $permissionType);
            break;
        }

        $permissionsManager = $this->container->get($serviceName);
        $pt = new PermissionSetType($permissionsManager);
        $permissionSet = new PermissionSet();
        if ($identity) {
            $permissionSet->setIdentity($identity);
            if (preg_match('/^ROLE/', $identity)) {
                $permissionSet->setIdentityType('Roles');
            } else {
                $permissionSet->setIdentityType('Users');
            }
        }
        $permissionSet->setPermissionType($permissionType);
        $permForm = $this->createForm($pt, $permissionSet);

        if ($request->getMethod() == 'POST') {
            $permForm->bind($request);
            $pSet = $permForm->getData();
            if ($permForm->isValid()) {
                // save permissions
                try {
                    $permissionsManager->savePermissionsForIdentity($pSet->getIdentity(), $pSet->getIdentityType(), $pSet->getPermissions());
                    $this->get('session')->getFlashBag()->add('success', 'Valid form!');
                } catch (\Exception $e) {
                    $bt = $e->getTrace();
                    // $this->get('session')->getFlashBag()->add('error', 'Uh oh ... ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' ' . print_r($bt[0], 1));
                    $this->get('session')->getFlashBag()->add('error', 'Uh oh ... ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
                }
            } else {
                foreach ($permForm->getErrors() as $err) {
                    ld($err);
                }
                $this->get('session')->getFlashBag()->add('error', 'invalid form!');
            }
        }
        return array('form' => $permForm->createView());
    }

    /**
     * @Route("/routeperm")
     * @Route("/routeperm/{identity}")
     * @Template()
     */
    public function routeAction($identity="")
    {
        $request = $this->getRequest();
        $permissionsManager = $this->container->get('tsk_user.permissions.route_permissions_manager');
        $pt = new PermissionSetType($permissionsManager);
        $permissionSet = new PermissionSet();
        if ($identity) {
            $permissionSet->setIdentity($identity);
            if (preg_match('/^ROLE/', $identity)) {
                $permissionSet->setIdentityType('Roles');
            } else {
                $permissionSet->setIdentityType('Users');
            }
        }
        $permForm = $this->createForm($pt, $permissionSet);

        if ($request->getMethod() == 'POST') {
            $permForm->bind($request);
            $pSet = $permForm->getData();
            if ($permForm->isValid()) {
                // save permissions
                try {
                    $permissionsManager->savePermissionsForIdentity($pSet->getIdentity(), $pSet->getIdentityType(), $pSet->getPermissions());
                    $this->get('session')->getFlashBag()->add('success', 'Valid form!');
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add('error', 'Uh oh ... ' . $e->getMessage());;
                }
            } else {
                foreach ($permForm->getErrors() as $err) {
                    ld($err);
                }
                $this->get('session')->getFlashBag()->add('error', 'invalid form!');
            }
        }
        return array('form' => $permForm->createView());
    }

    /**
     * @Route("/perm_meta")
     * @Template()
    public function getMetaDataAction()
    {
        $request = $this->container->get('request');
        $entity = $request->query->get('entity');

        try {
            $em = $this->container->get('doctrine')->getEntityManager();
            $fields = $em->getClassMetaData($entity)->getFieldNames();
        } catch (\Exception $e) {
            $fields = array($entity);
        }
        $view = View::create()
            ->setStatusCode(200)
            ->setData($fields)
            ->setTemplate("TSKUserBundle:Permission:metadata.html.twig");
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    public function deleteRoleAces($role)
    {
        $dbh = $this->container->get('doctrine')->getConnection();
        $stmt = $dbh->prepare('select c.class_type, e.field_name, e.mask from acl_classes c inner join acl_entries e on e.class_id=c.id inner join acl_security_identities s on e.security_identity_id=s.id WHERE s.identifier=:identity and object_identity_id IS NULL');
        $stmt->bindValue(':identity', $role);
        $stmt->execute();
        return $stmt->fetchAll();
    }
     */

    /**
     * @Route("/get_aces")
     * @Template()
    public function getRoleAcesAction()
    {
        $request = $this->container->get('request');
        $id = $request->query->get('id');
        $dbh = $this->container->get('doctrine')->getConnection();
        $stmt = $dbh->prepare('select c.class_type, e.field_name, e.mask from acl_classes c inner join acl_entries e on e.class_id=c.id inner join acl_security_identities s on e.security_identity_id=s.id WHERE s.identifier=:identity and object_identity_id IS NULL');
        $stmt->bindValue(':identity', $id);
        $stmt->execute();
        $row = $stmt->fetchAll();
       
        $view = View::create()
            ->setStatusCode(200)
            ->setData($row)
            ->setTemplate("TSKUserBundle:Permission:metadata.html.twig");
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    public function getUserAces($user)
    {
        $identity = 'TSK\UserBundle\Entity\User-' . $user;
        $dbh = $this->container->get('doctrine')->getConnection();
        $stmt = $dbh->prepare('select o.object_identifier, c.class_type, e.field_name, e.mask from acl_classes c inner join acl_entries e on e.class_id=c.id inner join acl_object_identities o on o.class_id=c.id inner join acl_security_identities s on e.security_identity_id=s.id WHERE s.identifier=:identity and object_identity_id IS NULL');
        $stmt->bindValue(':identity', $identity);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRoleAces($role)
    {
        $dbh = $this->container->get('doctrine')->getConnection();
        $stmt = $dbh->prepare('select o.object_identifier, c.class_type, e.field_name, e.mask from acl_classes c inner join acl_entries e on e.class_id=c.id inner join acl_object_identities o on o.class_id=c.id inner join acl_security_identities s on e.security_identity_id=s.id WHERE s.identifier=:identity and object_identity_id IS NULL');
        $stmt->bindValue(':identity', $role);
        $stmt->execute();
        return $stmt->fetchAll();
    }

     */
     /**
     * @Route("/get_user_aces")
     * @Template()
    public function getUserAcesAction()
    {
        $request = $this->container->get('request');
        $id = 'TSK\UserBundle\Entity\User-' . $request->query->get('id');
        $dbh = $this->container->get('doctrine')->getConnection();
        $stmt = $dbh->prepare('select c.class_type, e.field_name, e.mask from acl_classes c inner join acl_entries e on e.class_id=c.id inner join acl_security_identities s on e.security_identity_id=s.id WHERE s.identifier=:identity and object_identity_id IS NULL');
        $stmt->bindValue(':identity', $id);
        $stmt->execute();
        $row = $stmt->fetchAll();
        
        $view = View::create()
            ->setStatusCode(200)
            ->setData(array('data' => $row))
            ->setTemplate("TSKUserBundle:Permission:metadata.html.twig");
        return $this->get('fos_rest.view_handler')->handle($view);
    }
     */
}
