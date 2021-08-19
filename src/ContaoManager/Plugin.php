<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\ContaoManager;

use Alpdesk\AlpdeskFrontendediting\AlpdeskFrontendeditingBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;

class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [BundleConfig::create(AlpdeskFrontendeditingBundle::class)->setLoadAfter([ContaoCoreBundle::class])];
    }

    /**
     * @throws \Exception
     */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
    {
        $file = __DIR__ . '/../Resources/config/routes.yml';
        return $resolver->resolve($file)->load($file);
    }
}
