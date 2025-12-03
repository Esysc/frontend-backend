<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(\Symfony\Component\DependencyInjection\ContainerBuilder $container, \Symfony\Component\Config\Loader\LoaderInterface $loader): void
    {
        $container->setParameter('container.dumper.inline_class_loader', !ini_get('opcache.enable'));
        $container->setParameter('container.dumper.inline_factories', true);
        $confDir = $this->getProjectDir().'/config';

        $loader->load($confDir.'/{packages}/*.{php,yaml,yml}', 'glob');
        $loader->load($confDir.'/{packages}/'.$this->environment.'/*.{php,yaml,yml}', 'glob');
        $loader->load($confDir.'/{services}.{php,yaml,yml}', 'glob');
        $loader->load($confDir.'/{services}_'.$this->environment.'.{php,yaml,yml}', 'glob');
    }

    protected function configureRoutes(\Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator $routes): void
    {
        $confDir = $this->getProjectDir().'/config';

        $routes->import($confDir.'/{routes}/'.$this->environment.'/*.{php,yaml,yml}', 'glob');
        $routes->import($confDir.'/{routes}/*.{php,yaml,yml}', 'glob');
        $routes->import($confDir.'/{routes}.{php,yaml,yml}', 'glob');
    }
}
