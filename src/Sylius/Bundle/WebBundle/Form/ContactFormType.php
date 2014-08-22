<?php

namespace Sylius\Bundle\WebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
            	'label' => 'Your name'
            ))
            ->add('email', 'email', array(
                'label' => 'Email address'
            ))
            ->add('phone', 'text', array(
            	'label' => 'Phone'
            ))
            ->add('message', 'textarea', array(
                'label' => 'Your message'
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $collectionConstraint = new Collection(array(
            'name' => array(
                new NotBlank(array('message' => 'Your name should be valid')),
                new Length(array('min' => 2))
            ),
            'email' => array(
                new NotBlank(array('message' => 'Your email address should be valid')),
                new Email(array('message' => 'Your email address should be valid'))
            ),
        	'phone' => array(
        	),
            'message' => array(
                new NotBlank(array('message' => 'Your message should be valid')),
                new Length(array('min' => 5))
            )
        ));

        $resolver->setDefaults(array(
            'constraints' => $collectionConstraint
        ));
    }

    public function getName()
    {
        return 'contact';
    }
}