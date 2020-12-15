<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Backend;

use Contao\BackendTemplate;
use Contao\Environment;

class AlpdeskFrontendeditingContainer {

  public function generate(): string {

    $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/alpdeskfrontendediting/js/alpdeskfrontendediting_be.js';
    $GLOBALS['TL_CSS'][] = 'bundles/alpdeskfrontendediting/css/alpdeskfrontendediting_be.css';

    $containerTemplate = new BackendTemplate('be_alpdeskfrontendediting_container');
    $containerTemplate->token = REQUEST_TOKEN;
    $containerTemplate->base = Environment::get('base');

    return $containerTemplate->parse();
  }

}
