<?php
namespace TSK\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\SecurityEvents;
use FOS\UserBundle\Event\UserEvent;
// use FOS\UserBundle\FOSUserEvents;

class OrgSessionListener implements EventSubscriberInterface
{
    private $session;
    private $orgSessionKey;
    private $schoolsSessionKey;
    public function __construct(Session $session, $orgSessionKey, $schoolsSessionKey)
    {
        $this->session = $session;
        $this->orgSessionKey = $orgSessionKey;
        $this->schoolsSessionKey = $schoolsSessionKey;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            // FOSUserEvents::SECURITY_IMPLICIT_LOGIN => 'onImplicitLogin',
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        );
    }

    /**
     * onImplicitLogin 
     * Sets a session var on implicit login
     * 
     * @param UserEvent $event 
     * @access public
     * @return void
     */
    public function onImplicitLogin(UserEvent $event)
    {
        $org = $event->getUser()->getContact()->getOrganization();
        $userSchools = $event->getUser()->getContact()->getSchools();
        if (count($userSchools)) {
            foreach ($userSchools as $school) {
                $schools[] = $school->getId();
            }
            // set school_id in session
            $this->session->set($this->schoolsSessionKey, $schools[0]->getId());
            // $this->session->set($this->schoolsSessionKey, $schools[0]);
        } else {
            throw new \Exception('User is not affiliated with any school');
        }
        // Set org_id in session
        $this->session->set($this->orgSessionKey, $org->getId());
    }

    /**
     * onSecurityInteractiveLogin 
     * Sets a session var on interactive login
     * 
     * @param InteractiveLoginEvent $event 
     * @access public
     * @return void
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        $org = $user->getContact()->getOrganization();
        $this->session->set($this->orgSessionKey, $org->getId());

        $userSchools = $user->getContact()->getSchools();
        if (count($userSchools)) {
            $school = $userSchools->first();
            // $this->session->set($this->schoolsSessionKey, $school->getId());
            $this->session->set($this->schoolsSessionKey, $school->getId());
        }

    }
}
