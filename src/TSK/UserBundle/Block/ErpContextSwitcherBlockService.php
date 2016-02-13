<?php
namespace TSK\UserBundle\Block;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class ErpContextSwitcherBlockService extends BaseBlockService
{
    private $entityManager;
    private $session;
    private $sessionKey;
    private $formFactory;

    public function __construct($name, EngineInterface $templating, EntityManager $entityManager, Session $session, $formFactory, $securityContext)
    {
        parent::__construct($name, $templating);
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->formFactory = $formFactory;
        $this->securityContext = $securityContext;
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $schoolID = $this->session->get('tsk_school_id');
        $orgID = $this->session->get('tsk_organization_id');
        $em = $this->entityManager;
        $sc = $this->securityContext;

        $erpContext = new \TSK\UserBundle\Form\Model\ErpContext();
        if ($orgID) {
            $organization = $em->getRepository('TSKUserBundle:Organization')->find($orgID);
            $erpContext->setOrganization($organization);
        }
        if ($schoolID) {
            $school = $em->getRepository('TSKSchoolBundle:School')->find($schoolID);
            $erpContext->setSchool($school);
        }

        $isSuperAdmin = false;
        foreach ($sc->getToken()->getRoles() as $role) {
            if ($role->getRole() == 'ROLE_SUPER_ADMIN') {
                $isSuperAdmin = true;
            }
        }

        if ($isSuperAdmin) {
            // Create form
            $form = $this->formFactory->create(new \TSK\UserBundle\Form\Type\AdminErpContextType(
                $em,
                $sc
            ), $erpContext
            );
        } else {
            // Determine if user has more than one school ...
            // Create form
            $form = $this->formFactory->create(new \TSK\UserBundle\Form\Type\UserErpContextType(
                $em,
                $sc
            ), $erpContext
            );
        }

        return $this->renderResponse($blockContext->getTemplate(), array( 
            'block'         => $blockContext->getBlock(),
            'form'          => $form->createView(),
            'isSuperAdmin'  => $isSuperAdmin,
            'settings'      => $blockContext->getSettings(),
        ), $response);
    }

    public function setEntityManager($em)
    {
        $this->em = $em;
    }

    public function getName()
    {
        return 'Erp Context Switcher';
    }

    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'isSuperAdmin' => false,
            'template' => 'TSKUserBundle:Permission:switch.html.twig'
            )
        );
    }

    public function getDefaultSettings()
    {
        return array();
    }
    public function buildEditForm(FormMapper $form, BlockInterface $block) { }
    public function buildCreateForm(FormMapper $form, BlockInterface $block) {}

    public function validateBlock(ErrorElement $errorElement, BlockInterface $block) { }
}
?>
