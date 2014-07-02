<?php

namespace Gecko\LegemdaryBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sylius\Bundle\FixturesBundle\DataFixtures\ORM\DataFixture;
use Gecko\LegemdaryBundle\Entity\LegemdaryPost;

class LoadPostsData extends DataFixture
{	
    public function load(ObjectManager $manager)
    {
    	$finder = new Finder();

        $path = __DIR__.'/../../Resources/fixtures/posts';
        foreach ($finder->files()->in($path) as $img) {
            $post = new LegemdaryPost();
            $post->setTitle($this->faker->word);
            $post->setText($this->faker->text);
            $post->setFile(new UploadedFile($img->getRealPath(), $img->getFilename()));
            
            $manager->persist($post);

            $this->setReference('Gecko.Post.'.$img->getBasename('.jpg'), $post);
        }

        $manager->flush();
    }
    
    public function getOrder()
    {
    	return 8;
    }
}
