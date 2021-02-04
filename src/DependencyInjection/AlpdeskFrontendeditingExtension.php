<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class AlpdeskFrontendeditingExtension extends Extension {

  public function load(array $mergedConfig, ContainerBuilder $container) {
    $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    $loader->load('services.yml');
    $loader->load('listener.yml');
  }

}
