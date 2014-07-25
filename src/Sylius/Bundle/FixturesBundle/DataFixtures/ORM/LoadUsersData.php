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
 * User fixtures.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class LoadUsersData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = $this->getUserRepository()->createNew();

        $user->setFirstname($this->faker->firstName);
        $user->setLastname($this->faker->lastName);
        $user->setEmail('legemdary1@gmail.com');
        $user->setPlainPassword('123456');
        $user->setEnabled(true);
        $user->setRoles(array('ROLE_SYLIUS_ADMIN'));
        $user->setCurrency('USD');

        $manager->persist($user);

        $this->setReference('User-Administrator', $user);
        
        //Vendor
        $vendor = $this->getUserRepository()->createNew();
        $vendor->setFirstname($this->faker->firstName);
        $vendor->setLastname($this->faker->lastName);
        $vendor->setEmail('vendor@legemdary.com');
        $vendor->setPlainPassword('123456');
        $vendor->setEnabled(true);
        $vendor->setRoles(array('ROLE_SYLIUS_VENDOR'));
        $vendor->setCurrency('USD');
        
        $manager->persist($user);
        $manager->persist($vendor);
        
        $manager->flush();
        
        for ($i = 1; $i <= 15; $i++) {
            $user = $this->getUserRepository()->createNew();

            $username = $this->faker->username;

            $user->setFirstname($this->faker->firstName);
            $user->setLastname($this->faker->lastName);
            $user->setEmail($username.'@example.com');
            $user->setPlainPassword($username);
            $user->setEnabled($this->faker->boolean());

            $manager->persist($user);

            $this->setReference('Sylius.User-'.$i, $user);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
