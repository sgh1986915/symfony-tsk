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

class OrgSwitcherBlockService extends BaseBlockService
{
    private $entityManager;
    private $session;
    private $sessionKey;

    public function __construct($name, EngineInterface $templating, EntityManager $entityManager, Session $session, $sessionKey)
    {
        parent::__construct($name, $templating);

        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->sessionKey = $sessionKey;
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $orgs = $this->entityManager->getRepository('TSK\UserBundle\Entity\Organization')->findAll();
        if (count($orgs)) {
            foreach ($orgs as $org) {
                $organizations[$org->getId()] = array(
                                                    'id' => $org->getId(),
                                                    'title' => $org->getTitle(),
                                                    'selected' => ($org->getId() == $this->session->get($this->sessionKey)) ? 'selected' : ''
                );
            }
        }

        return $this->renderResponse($blockContext->getTemplate(), array( 
            'block'         => $blockContext->getBlock(),
            'organizations' => $organizations,
            'settings'      => $blockContext->getSettings(),
        ), $response);
    }

    public function setEntityManager($em)
    {
        $this->em = $em;
    }

    public function getName()
    {
        return 'Org Switcher';
    }

    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'organizations' => NULL,
            'template' => 'TSKUserBundle:Block:org_switcher.html.twig'
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
