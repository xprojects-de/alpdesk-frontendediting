<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Backend;

use Contao\BackendTemplate;

class AlpdeskFrontendeditingContainer {

  public function generate(): string {

    $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/alpdeskfrontendediting/js/alpdeskfrontendediting.js|async';
    $GLOBALS['TL_CSS'][] = 'bundles/alpdeskfrontendediting/css/alpdeskfrontendediting.css';

    $containerTemplate = new BackendTemplate('be_alpdeskfrontendediting_container');

    return $containerTemplate->parse();
  }

}
