<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\ContaoManager;

use Alpdesk\AlpdeskFrontendediting\AlpdeskFrontendeditingBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;

class Plugin implements BundlePluginInterface {

  public function getBundles(ParserInterface $parser) {
    return [BundleConfig::create(AlpdeskFrontendeditingBundle::class)->setLoadAfter([ContaoCoreBundle::class])];
  }

}
