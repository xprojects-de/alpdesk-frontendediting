<?php

use Contao\Input;

if (Input::get('alpdeskmodal') == 1) {
  $GLOBALS['TL_CSS'][] = 'bundles/alpdeskfrontendediting/css/alpdeskfrontendediting_backendutils.css';
  $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/alpdeskfrontendediting/js/alpdeskfrontendediting_backendutils.js';
}