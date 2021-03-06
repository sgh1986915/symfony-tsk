<?php
namespace TSK\UserBundle\Admin;
use libphonenumber\PhoneNumberFormat;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use TSK\StudentBundle\Entity\Student;
use Sonata\AdminBundle\Admin\AdminInterface;
use Knp\Menu\ItemInterface;

class ContactAdmin extends Admin
{
    protected $translationDomain = 'TSKUserBundle';
    private $container = null;

    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     */
    public function __construct($code, $class, $baseControllerName, $container=null)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->container = $container;  
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('firstName')
            ->add('lastName')
            ->add('corporations', null, array('required' => false))
            // ->add('schools')
            ->add('imgPath', 'file', array('required' => false, 'data_class' => null, 'mapped' => true))
            ->add('email', 'email', array('required' => false))
            ->add('address1')
            ->add('address2')
            ->add('city')
            ->add('state')
            ->add('postalCode', null, array('required' => false))
            ->add('phone', 'tel', array('required' => false, 'default_region' => 'US', 'format' => PhoneNumberFormat::NATIONAL))
            ->add('mobile', 'tel', array('required' => false, 'default_region' => 'US', 'format' => PhoneNumberFormat::NATIONAL))
            ->add('website')
            ->add('geocode')
            ->add('dateOfBirth', NULL, array('years' => range(1960, 2013)))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('lastName')
            ->add('email')
            ->add('phone')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('firstName')
            ->addIdentifier('lastName')
            ->add('email')
            ->add('phone')
            // ->add('imgPath', NULL, array('template' => 'TSKUserBundle:Contact:list_image.html.twig', 'foo' => 'bar'))
            // ->add('organization.title', null, array('label' => 'Organization'))
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('firstName')
                ->assertMaxLength(array('limit' => 32))
            ->end()
        ;
    }

    public function getCurrentSchool()
    {
        $session = $this->container->get('session');
        $sessionKey = $this->container->getParameter('tsk_user.session.school_key');
        $schoolId = $session->get($sessionKey);
        $em = $this->container->get('doctrine.orm.entity_manager');
        $school = $em->getRepository('TSKSchoolBundle:School')->find($schoolId);
        return $school;
    }

    public function prePersist($object)
    {
        $object->addSchool($this->getCurrentSchool());
        // We get the uploadable manager
        // and add the object to it!
        if ($object->getImgPath()) {
            $uploadableManager = $this->container->get('stof_doctrine_extensions.uploadable.manager');
            $uploadableManager->markEntityToUpload($object, $object->getImgPath());
        }
    }

    public function preUpdate($object)
    {
        $object->addSchool($this->getCurrentSchool());
        // We get the uploadable manager
        // and add the object to it!
        if ($object->getImgPath()) {
            $uploadableManager = $this->container->get('stof_doctrine_extensions.uploadable.manager');
            $uploadableManager->markEntityToUpload($object, $object->getImgPath());
        }
    }
}
