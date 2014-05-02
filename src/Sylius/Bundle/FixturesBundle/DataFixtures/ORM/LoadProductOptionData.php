<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Default product options to play with Sylius.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class LoadProductOptionData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        //$option = $this->createOption('shape', 'Center diamond shape', array('Round', 'Princess'));
        //$manager->persist($option);

        $option = $this->createOption('metal', 'Ring Metal', array('14k white gold', '14k yellow gold', 'Platinum'));
        $manager->persist($option);

        $option = $this->createOption('size', 'Ring Size', array('7','8','9','10'));
        $manager->persist($option);
        
        $option = $this->createOption("carat", "Diamond's Carat", array('0.4', '0.5', '0.6'));
        $manager->persist($option);
        
        $option = $this->createOption("color", "Diamond's Color", array('D', 'E', 'F', 'G'));
        $manager->persist($option);
        
        $option = $this->createOption("cut", "Diamond's Cut", array('EXCELENT', 'GOOD', 'FAIR'));
        $manager->persist($option);
        
        $option = $this->createOption("clarity", "Diamond's Clarity", array('IF', 'VVS1', 'VS2', 'SI1'));
        $manager->persist($option);

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 3;
    }

    /**
     * Create an option.
     *
     * @param string $name
     * @param string $presentation
     * @param array  $values
     */
    protected function createOption($name, $presentation, array $values)
    {
        $option = $this
            ->getProductOptionRepository()
            ->createNew()
        ;

        $option->setName($name);
        $option->setPresentation($presentation);

        foreach ($values as $text) {
            $value = $this->getProductOptionValueRepository()->createNew();
            $value->setValue($text);

            $option->addValue($value);
        }

        $this->setReference('Sylius.Option.'.$name, $option);

        return $option;
    }
}
