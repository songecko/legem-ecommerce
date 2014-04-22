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
 * Default product attributes to play with Sylius.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class LoadProductAttributeData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /*$attribute = $this->createAttribute("Diamond's Carat", 'Carat');
        $manager->persist($attribute);

        $attribute = $this->createAttribute("Diamond's Color", 'Color');
        $manager->persist($attribute);

        $attribute = $this->createAttribute("Diamond's Cut", 'Cut');
        $manager->persist($attribute);

        $attribute = $this->createAttribute("Diamond's Clarity", 'Clarity');
        $manager->persist($attribute);

        $manager->flush();*/
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 2;
    }

    /**
     * Create attribute.
     *
     * @param string $name
     * @param string $presentation
     */
    private function createAttribute($name, $presentation)
    {
        $repository = $this->getProductAttributeRepository();

        $attribute = $repository->createNew();
        $attribute->setName($name);
        $attribute->setPresentation($presentation);

        $this->setReference('Sylius.Attribute.'.$name, $attribute);

        return $attribute;
    }
}
