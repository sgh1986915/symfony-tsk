<?php
namespace TSK\UserBundle\EventListener;

use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * OrgUpdateListener 
 * This listener is called before an entity is created/updated and
 * checks to see if the entity doesn't already have an org set.  If 
 * not, it gets the appropriate organization by taking
 * its value from the session and calls the entities setOrganization()
 * method.  
 * Only those entities set in tsk_user.org_update_listener.allowed_classes
 * will be acted upon.
 * 
 * @package 
 * @version $id$
 * @copyright 2012 TSK
 * @author Malaney J. Hill <malaney@gmail.com> 
 * @license PHP Version 3.01 {@link http://www.php.net/license/3_01.txt}
 */
class OrgUpdateListener 
{
    private $session;
    private $sessionKey;
    private $allowedClasses = array();

    public function __construct(Session $session, $sessionKey, $allowedClasses)
    {
        $this->session = $session;
        $this->sessionKey = $sessionKey;
        $this->allowedClasses = $allowedClasses;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();
        
        if (in_array(get_class($entity), $this->allowedClasses)) {
            if (!$entity->getOrganization()) {
                $userOrganization = $entityManager->getRepository('TSK\UserBundle\Entity\Organization')->findOneById($this->session->get($this->sessionKey));
                $entity->setOrganization($userOrganization);
            }
        }
    }

    public function preUpdate(LifecycleEventArgs $event)
    {
        return $this->prePersist($event);
    }
}
