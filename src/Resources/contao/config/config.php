<?php

use Contao\Input;
use Alpdesk\AlpdeskFrontendediting\Backend\AlpdeskFrontendeditingContainer;
use Alpdesk\AlpdeskFrontendediting\Model\AlpdeskFrontendeditingMappingModel;

if (Input::get('alpdeskmodal') == 1) {
  $GLOBALS['TL_CSS'][] = 'bundles/alpdeskfrontendediting/css/alpdeskfrontendediting_backendutils.css';
  $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/alpdeskfrontendediting/js/alpdeskfrontendediting_backendutils.js';
}

$GLOBALS['TL_MODELS']['tl_alpdeskfrontendediting_mapping'] = AlpdeskFrontendeditingMappingModel::class;

$GLOBALS['BE_MOD']['content']['alpdeskfrontendediting'] = [
    'callback' => AlpdeskFrontendeditingContainer::class
];

$GLOBALS['BE_MOD']['system']['alpdeskfrontendediting_mapping'] = [
    'tables' => ['tl_alpdeskfrontendediting_mapping']
];

