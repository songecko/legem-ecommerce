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
 * Default assortment product options to play with sandbox.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class LoadOptionsData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        // T-Shirt size option.
        $option = $this->createOption('Center diamond shape', 'shape', array('Round', 'Princess'));
        $manager->persist($option);

        // T-Shirt color option.
        $option = $this->createOption('Ring metal', 'metal', array('14k white gold', '14k yellow gold', 'Platinum'));
        $manager->persist($option);

        // Sticker size option.
        $option = $this->createOption('Ring Size', 'Size', array('5','5.5','6','6.5','7','7.5','8','8.5','9','9.5','10','10.5','11','11.5','12','12.5','13'));
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
            ->getOptionRepository()
            ->createNew()
        ;

        $option->setName($name);
        $option->setPresentation($presentation);

        foreach ($values as $text) {
            $value = $this->getOptionValueRepository()->createNew();
            $value->setValue($text);

            $option->addValue($value);
        }

        $this->setReference('Sylius.Option.'.$name, $option);

        return $option;
    }
}
