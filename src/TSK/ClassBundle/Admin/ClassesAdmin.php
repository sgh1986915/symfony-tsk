<?php
namespace TSK\ClassBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use TSK\ClassBundle\Entity\ClassTypeCredit;
use TSK\ClassBundle\Entity\ClassToken;
use TSK\ClassBundle\Entity\ClassType;
use TSK\ClassBundle\Form\EventListener\DrawClassTypeCredits;


class ClassesAdmin extends Admin
{
    protected $translationDomain = 'TSKClassBundle';
    private $orgSessionKey;
    public function setOrgSessionKey($orgSessionKey)
    {
        $this->orgSessionKey = $orgSessionKey;
    }
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title')
            ->add('description')
            ->add('tokens', 'text', array('label' => 'Class Tokens', 'required' => false))
            ->add('scheduleColor', 'tsk_class_schedule_color_type')
            // ->with('Class Type Credits')
            // ->add('classTypeCredits', 'collection', array('type' => 'tsk_class_types'))
        ;

        // This subscriber is responsible for dynamically adding 
        // the classTypeCredits interface
        $entityManager = $this->modelManager->getEntityManager('TSK\ClassBundle\Entity\ClassType');

        $builder = $formMapper->getFormBuilder();
        $subscriber = new DrawClassTypeCredits(
                            $builder->getFormFactory(), 
                            $entityManager,
                            $this->request->getSession()->get($this->orgSessionKey));
        $builder->addEventSubscriber($subscriber);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'schedule' => array('template' => 'TSKClassBundle:CRUD:list__action_schedule.html.twig'),
                    'roster' => array('template' => 'TSKClassBundle:CRUD:list__action_roster.html.twig'),
                    'attendance' => array('template' => 'TSKClassBundle:CRUD:list__action_attendance.html.twig')
                )
            ))
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('title')
                ->assertMaxLength(array('limit' => 32))
            ->end()
        ;
    }

    public function prePersist($object)
    {
        // $em = $this->modelManager->getEntityManager('TSK\UserBundle\Entity\Organization');
        // $org = $em->getRepository('TSK\UserBundle\Entity\Organization')->findOneById(4);
        // $object->setOrganization($org);
    }
}
