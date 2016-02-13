<?php
namespace TSK\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\Exception as AclException;
use Sonata\AdminBundle\Security\Acl\Permission\MaskBuilder;
use TSK\UserBundle\Entity\User;
use TSK\UserBundle\Entity\UserType;
use TSK\UserBundle\Entity\Organization;
use TSK\UserBundle\Entity\Contact;

class LoadAclData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        // select * from contacts
        $em = $this->container->get('doctrine.orm.entity_manager');
        $contacts = $em->getRepository('TSKUserBundle:Contact')->findAll();
        foreach ($contacts as $contact) {
            $this->createTskAcl($contact);
        }
    }

    public function createTskAcl(Contact $contact)
    {
        $aclProvider = $this->container->get('security.acl.provider');
        $objectIdentity = ObjectIdentity::fromDomainObject($contact);
        $orgIdentity = 'ROLE_ORG_' . $contact->getOrganization()->getId();
        $orgSecurityIdentity = new RoleSecurityIdentity($orgIdentity);

        $builder = new MaskBuilder();
        $builder->add('VIEW');
        $builder->add('EDIT');
        $builder->add('CREATE');
        $builder->add('MASTER');
        try {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($orgSecurityIdentity, $builder->get());
            foreach ($contact->getSchools() as $school) {
                $schoolIdentity = 'ROLE_SCHOOL_' . $school->getId();
                $schoolSecurityIdentity = new RoleSecurityIdentity($schoolIdentity);
                $acl->insertObjectAce($schoolSecurityIdentity, $builder->get());
            }
            $aclProvider->updateAcl($acl);
        } catch (AclException $e) {
            throw $e;
        }
    }

    public function getOrder()
    {
        return 20;
    }
}
