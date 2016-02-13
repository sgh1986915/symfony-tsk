<?php
namespace TSK\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\SecurityEvents;
use FOS\UserBundle\Event\UserEvent;
// use FOS\UserBundle\FOSUserEvents;
use TSK\UserBundle\Entity\LoginHistory;
use TSK\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class LoginHistoryListener implements EventSubscriberInterface
{
    private $request;
    private $entityManager;

    public function __construct(Request $request, EntityManager $entityManager)
    {
        $this->request = $request;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            // FOSUserEvents::SECURITY_IMPLICIT_LOGIN => 'recordLoginImplicit',
            SecurityEvents::INTERACTIVE_LOGIN => 'recordLoginInteractive'
        );
    }

    public function recordLoginInteractive(InteractiveLoginEvent $event)
    {
        return $this->recordLogin($event->getAuthenticationToken()->getUser());
    }

    public function recordLoginImplicit(UserEvent $event)
    {
        return $this->recordLogin($event->getUser());
    }

    /**
     * recordLogin 
     * 
     * @param User $user 
     * @access public
     * @return void
     */
    public function recordLogin(User $user)
    {
        $lh = new LoginHistory();
        $lh->setUser($user);
        $lh->setLoginIp($this->request->getClientIp());
        $lh->setUserAgent($this->request->headers->get('user-agent'));
        $this->entityManager->persist($lh);
        $this->entityManager->flush();
    }
}
