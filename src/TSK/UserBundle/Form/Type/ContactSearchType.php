<?php
namespace TSK\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

use TSK\UserBundle\Form\DataTransformer\ContactToNumberTransformer;
use TSK\UserBundle\Form\DataTransformer\EntityToIdTransformer;

class ContactSearchType extends AbstractType
{
    private $em;
    
    public function __construct($em)
    {
        $this->em = $em;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->resetViewTransformers()
            // ->addModelTransformer(new ContactToNumberTransformer($this->em));
            ->addModelTransformer(new EntityToIdTransformer($this->em, $options['entity']));
    }

    public function getName()
    {
        return 'tsk_contact_list';
    }

    public function getParent()
    {
        return 'text';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $value = $form->getViewData();
        $view->vars['uniqid'] = uniqid();
        $view->vars['show_add'] = $options['show_add'];
        $view->vars['show_edit'] = $options['show_edit'];
        $view->vars['list_label'] = $options['list_label'];
        $view->vars['add_label'] = $options['add_label'];
        $view->vars['edit_label'] = $options['edit_label'];
        $view->vars['admin'] = $options['admin'];
        $view->vars['admin_name'] = $options['admin_name'];
        
        if (is_numeric($value)) {
            $contact = $this->em
                    ->getRepository($options['entity'])
                    ->find($value)
                ;

            if (is_object($contact)) {
                $view->vars['object'] = $contact;
            } else {
                throw new \Exception('Unknown entity');
            }
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'compound' => false,
            'show_add' => true,
            'entity' => 'TSKUserBundle:Contact',
            'list_label' => 'List',
            'add_label' => 'Add New Contact',
            'edit_label' => 'Edit',
            'show_edit' => false,
            'admin' => null,
            'admin_name' => 'tsk.admin.contact'
        ));

        $resolver->setRequired(array(
            'admin'
        ));

        $resolver->setAllowedTypes(array(
        ));
    }
}
