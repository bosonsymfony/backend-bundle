<?php

namespace UCI\Boson\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BundleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('format', 'text')
            ->add('namespace', 'text')
            ->add('structure', 'checkbox');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UCI\Boson\BackendBundle\Entity\Bundle',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return 'backend_bundle_form';
    }


}
