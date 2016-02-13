<?php
namespace TSK\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\Exception as AclException;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;
use Sonata\AdminBundle\Security\Acl\Permission\MaskBuilder;
use TSK\UserBundle\Entity\Contact;
use TSK\StudentBundle\Entity\Student;

class LoadAclsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('tsk:user:load_acls')
        ->setDefinition(array(
        ))
        ->setDescription('Load ACLs')
        ->setHelp(<<<EOT
The <info>tsk:user:load_acls</info> command loads acls for all contacts

EOT
);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'TSK User Load ACLs');   
        $output->writeln(array(
            '', 
            'This script will load acls for all contacts',
            '' 
        )); 
    }
    
    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // select * from contacts
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $contacts = $em->getRepository('TSKUserBundle:Contact')->findAll();
        foreach ($contacts as $contact) {
            $this->createTskAcl($contact);
        }

    }

    public function createTskAcl(Contact $contact)
    {
        $aclProvider = $this->getContainer()->get('security.acl.provider');
        $objectIdentity = ObjectIdentity::fromDomainObject($contact);
        $orgIdentity = 'ROLE_ORG_' . $contact->getOrganization()->getId();
        $orgSecurityIdentity = new RoleSecurityIdentity($orgIdentity);

        $builder = new MaskBuilder();
        $builder->add('VIEW');
        $builder->add('EDIT');
        $builder->add('CREATE');
        $builder->add('MASTER');
        try {
            try {
                $acl = $aclProvider->createAcl($objectIdentity);
                $acl->insertObjectAce($orgSecurityIdentity, $builder->get());
                foreach ($contact->getSchools() as $school) {
                    $schoolIdentity = 'ROLE_SCHOOL_' . $school->getId();
                    $schoolSecurityIdentity = new RoleSecurityIdentity($schoolIdentity);
                    $acl->insertObjectAce($schoolSecurityIdentity, $builder->get());
                }
                $aclProvider->updateAcl($acl);

            } catch (AclAlreadyExistsException $e) {
                // keep going ...
            }
        } catch (AclException $e) {
            throw $e;
        }
    }
}
