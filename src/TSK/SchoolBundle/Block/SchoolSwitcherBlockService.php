<?php
namespace TSK\SchoolBundle\Block;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class SchoolSwitcherBlockService extends BaseBlockService
{
    private $entityManager;
    private $session;
    private $sessionKey;
    private $orgSessionKey;
    private $securityContext;

    public function __construct($name, EngineInterface $templating, EntityManager $entityManager, Session $session, $schoolSessionKey, $orgSessionKey, $securityContext)
    {
        parent::__construct($name, $templating);

        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->orgSessionKey = $orgSessionKey;
        $this->schoolSessionKey = $schoolSessionKey;
        $this->securityContext = $securityContext;
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $user = $this->securityContext->getToken()->getUser();
        if ($user->hasRole('ROLE_SUPER_ADMIN')) {
            $query = $this->entityManager->createQuery('SELECT p from TSK\SchoolBundle\Entity\School p JOIN p.contact c WHERE c.organization=:org');
            $query->setParameter(':org', $this->session->get($this->orgSessionKey));
            $schools = $query->getResult();
        } else {
            $schools = $user->getContact()->getSchools()->toArray();
            // if (count($userSchools)) {
            //     foreach ($userSchools as $us) {
            //         $schools[] = $us->getId();
            //     }
            //     // Filter schools by org and according to your contact_schools list
            //     $query = $this->entityManager->createQuery('SELECT s from TSK\SchoolBundle\Entity\School s WHERE s.id IN (:schools)');
            //     $query->setParameter(':schools', join($schools));
            // }
        }
        
        $scs = array();
        if (count($schools)) {
            foreach ($schools as $school) {
                $scs[$school->getId()] = array(
                    'id' => $school->getId(),
                    'title' => $school->getContact()->getFirstName().' ' . $school->getContact()->getLastName(),
                    'selected' => ($school->getId() == $this->session->get($this->schoolSessionKey)) ? 'selected' : ''
                );
            }
        }

        return $this->renderResponse($blockContext->getTemplate(), array( 
            'block'     => $blockContext->getBlock(),
            'schools'   => $scs,
            'settings'  => $blockContext->getSettings(),
        ), $response);
    }

    public function setEntityManager($em)
    {
        $this->em = $em;
    }

    public function getName()
    {
        return 'School Switcher';
    }

    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'organizations' => NULL,
            'template' => 'TSKSchoolBundle:Block:school_switcher.html.twig'
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
