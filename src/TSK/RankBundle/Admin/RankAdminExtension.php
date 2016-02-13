<?php
namespace TSK\RankBundle\Admin;

use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

/**
 * ContactAdminExtension 
 * This extension grabs the org_id and school_id from the session and adds a filter
 * to all list queries for a given admin.  It achieves filtering of all contacts by
 * org_id and school_id.  Note: The query below ONLY works if there is a contact 
 * field listed in the $datagridMapper of configureDatagridFilters.  Remove that 
 * field at your own risk!!
 * 
 * @uses AdminExtension
 * @package 
 * @version $id$
 * @author Malaney J. Hill <malaney@gmail.com> 
 */
class RankAdminExtension extends AdminExtension
{
    private $session;
    private $orgSessionKey;
    private $schoolSessionKey;
    public function __construct($session, $orgSessionKey, $schoolSessionKey)
    {
        $this->session = $session;
        $this->orgSessionKey = $orgSessionKey;
        $this->schoolSessionKey = $schoolSessionKey;
    }

    public function configureQuery(AdminInterface $admin, ProxyQueryInterface $query, $content = 'list')
    {
        $query
            ->andWhere($query->expr()->eq('o.organization', ':org_id'))
            ->setParameter(':org_id', $this->session->get($this->orgSessionKey));
    }
}
