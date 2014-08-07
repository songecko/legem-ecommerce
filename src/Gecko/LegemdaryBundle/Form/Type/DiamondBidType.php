<?php

namespace Gecko\LegemdaryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DiamondBidType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('carat', 'text', array(
        		'required' => true,
        		'label'    => 'Diamond Carat'
        ))
        ->add('color', 'text', array(
        		'required' => true,
        		'label'    => 'Diamond Color'
        ))
        ->add('clarity', 'text', array(
        		'required' => true,
        		'label'    => 'Diamond Clarity'
        ))
        ->add('cut', 'text', array(
        		'required' => true,
        		'label'    => 'Diamond Cut'
        ))
        ->add('price', 'sylius_money', array(
        		'required' => true,
				'label' => 'Price'
        ))
        ->add('pdfFile', 'file', array(
        		'required' => true,
        		'label' => 'Certificate'
        ));
    }

    public function getName()
    {
        return 'legemdary_diamond_bid';
    }
}
