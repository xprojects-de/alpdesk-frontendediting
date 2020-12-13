<?php

use Contao\Input;
use Alpdesk\AlpdeskFrontendediting\Backend\AlpdeskFrontendeditingContainer;

if (Input::get('alpdeskmodal') == 1) {
  $GLOBALS['TL_CSS'][] = 'bundles/alpdeskfrontendediting/css/alpdeskfrontendediting_backendutils.css';
}

$GLOBALS['BE_MOD']['alpdeskfrontendediting_legend']['alpdeskfrontendediting'] = [
    'callback' => AlpdeskFrontendeditingContainer::class
];

