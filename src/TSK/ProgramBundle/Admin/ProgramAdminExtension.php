<?php
namespace TSK\ProgramBundle\Admin;

use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

/**
 * ProgramAdminExtension 
 * This extension grabs the org_id and school_id from the session and 
 * to all list queries for this admin.  It achieves filtering of all
 * programs by org_id and school_id.  
 * 
 * @uses AdminExtension
 * @package 
 * @version $id$
 * @author Malaney J. Hill <malaney@gmail.com> 
 */
class ProgramAdminExtension extends AdminExtension
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
            ->andWhere(':school_id MEMBER OF o.schools')
            ->setParameter(':org_id', $this->session->get($this->orgSessionKey))
            ->setParameter(':school_id', $this->session->get($this->schoolSessionKey));
    }
}
