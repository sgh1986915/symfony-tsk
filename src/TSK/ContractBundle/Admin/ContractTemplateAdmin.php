<?php
namespace TSK\ContractBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Doctrine\ORM\EntityRepository;
use Knp\Menu\ItemInterface as MenuItemInterface;

class ContractTemplateAdmin extends Admin
{
    protected $translationDomain = 'TSKContractBundle';

    protected function configureFormFields(FormMapper $formMapper)
    {
        // all of this to support version switcher ...
        $objectId = $this->getSubject()->getId();
        $repo = $this->getModelManager()->getEntityManager('TSKContractBundle:ContractTemplateVersion')->getRepository('TSKContractBundle:ContractTemplateVersion');
        $versions = $repo->findBy(array('objectId' => $this->getSubject()->getId(), 'objectClass' => 'TSK\ContractBundle\Entity\ContractTemplate'));

        $choices = $currActiveDate = $preferredChoice = null;
        foreach ($versions as $v) {
            $data = $v->getData();
            if ($v->getActiveDate() > $currActiveDate) {
                $currActiveDate = $v->getActiveDate();
                $preferredChoice = $v;
            }
            $description = (!empty($data['description'])) ? substr($data['description'], 0, 30) : '';
            // $d = json_decode($data);
            $choices[$v->getVersion()] = $v->getVersion() . '|' . $description . '|' . $v->getLoggedAt()->format('Y-m-d H:i:s');
        }

        if (!$preferredChoice) {
            $preferredChoice = $v;
        }

        $formMapper
            ->add('version', 'choice', array(
                'mapped' => false,
                'required' => false,
                'help' => 'Use this to switch between versions of this template',
                'empty_value' => '-- select version',
                'choices' => $choices,
                'preferred_choices' => array($preferredChoice->getVersion()),
                'data' => $preferredChoice->getVersion())
            )
            ->add('name')
            ->add('description')
            ->add('template', null, array('attr' => array('class' => 'input-block-level', 'rows' => 14)))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('description')
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('name')
                ->assertMaxLength(array('limit' => 32))
            ->end()
        ;
    }

    public function preUpdate($contractTemplate)
    {
        $contractTemplate->filterTemplate();
    }

    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!in_array($action, array('edit', 'create'))) {
            return;
        }
        $id = $this->getRequest()->get('id');
        $vars = $menu->addChild(
            'Variables',
            array('route' => 'tsk_contract_default_variables', 'attr' => array('onclick' => 'alert("foo"); return false;'))
        );
        $contract = $vars->addChild('Contract');
        $contract->addChild('getAmount');
        $contract->addChild('getProgram');
        $contract->addChild('getExpireDate');
        $contract->addChild('getRolloverTokens');
        $contract->addChild('getContractNumTokens');
        $contract->addChild('getProgramLegalDescription');
        $student = $vars->addChild('Student');
        $student->addChild('getContact.getFirstName');
        $student->addChild('getContact.getLastName');
        $student->addChild('getContact.getAddress1');
        $student->addChild('getContact.getAddress2');
        $student->addChild('getContact.getCity');
        $student->addChild('getContact.getState');
        $student->addChild('getContact.getPostalcode');
        $student->addChild('getContact.getPhone');
        $school = $vars->addChild('School');
        $school->addChild('getContact.getState');
        $org = $vars->addChild('Org');
        $org->addChild('getName');

    }

}
