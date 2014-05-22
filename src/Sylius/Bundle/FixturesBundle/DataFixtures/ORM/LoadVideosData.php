<?php

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductVariantImage;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LoadVideosData extends DataFixture
{
    public function load(ObjectManager $manager)
    {
        $finder = new Finder();

        $path = __DIR__.'/../../Resources/fixtures/videos';
        foreach ($finder->files()->in($path) as $img) {
            $uploadedFile = new UploadedFile($img->getRealPath(), $img->getFilename());

            $this->setReference('Sylius.Product.Video.'.$img->getBasename('.mp4'), $uploadedFile);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}
