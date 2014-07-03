<?php

namespace Gecko\LegemdaryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LegemdaryPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title', 'text', array(
        		'required' => true,
        		'label'    => 'Titulo'
        ))
        ->add('text', 'textarea', array(
        		'required' => true,
        		'label'    => 'Texto'
        ))
        ->add('file', 'file', array(
        		'required' => false,
        		'label'    => 'Imagen'
        ));
    }

    public function getName()
    {
        return 'legemdary_post';
    }
}
