<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Option form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OptionType extends AbstractType
{
    /**
     * Variable object name.
     *
     * @var string
     */
    protected $variableName;

    /**
     * Data class.
     *
     * @var string
     */
    protected $dataClass;

    /**
     * Validation groups.
     *
     * @var array
     */
    protected $validationGroups;

    /**
     * Constructor.
     *
     * @param string $variableName
     * @param string $dataClass
     * @param array  $validationGroups
     */
    public function __construct($variableName, $dataClass, array $validationGroups)
    {
        $this->variableName = $variableName;
        $this->dataClass = $dataClass;
        $this->validationGroups = $validationGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.option.name'
            ))
            ->add('presentation', 'text', array(
                'label' => 'sylius.form.option.presentation'
            ))
            ->add('values', 'collection', array(
                'type'         => sprintf('sylius_%s_option_value', $this->variableName),
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label'        => 'sylius.form.option.values'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'        => $this->dataClass,
                'validation_groups' => $this->validationGroups,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_option', $this->variableName);
    }
}
