<?php

use Symfony\Component\ClassLoader\DebugUniversalClassLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Debug\ErrorHandler;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;
use Sylius\Bundle\CoreBundle\Kernel\SyliusKernel;

class AppKernel extends SyliusKernel
{
    public function registerBundles()
    {
        $bundles = array(
        	new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle(),
            new Gecko\LegemdaryBundle\LegemdaryBundle(),
        );

        return array_merge($bundles, parent::registerBundles());
    }
}
