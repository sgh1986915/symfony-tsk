<?php
namespace TSK\ClassBundle\Admin;

use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

/**
 * ClassAdminExtension 
 * This extension grabs the org_id from the session and adds a filter
 * to all list queries for this admin.  It achieves filtering of all
 * schools by org_id.  Note: The query below ONLY works if there is 
 * a contact field listed in the $datagridMapper of configureDatagridFilters.
 * Remove that field at your own risk!!
 * 
 * @uses AdminExtension
 * @package 
 * @version $id$
 * @author Malaney J. Hill <malaney@gmail.com> 
 */
class ClassAdminExtension extends AdminExtension
{
    private $session;
    private $sessionKey;
    public function __construct($session, $sessionKey)
    {
        $this->session = $session;
        $this->sessionKey = $sessionKey;
    }

    public function configureQuery(AdminInterface $admin, ProxyQueryInterface $query, $content = 'list')
    {
        $query
            ->andWhere($query->expr()->eq('o.organization', ':org_id'))
            ->setParameter(':org_id', $this->session->get($this->sessionKey));
    }
}
