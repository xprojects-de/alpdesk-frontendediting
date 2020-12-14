<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Utils;

use Contao\Module;
use Contao\ModuleNavigation;
use Contao\ModuleCustomnav;
use Contao\BackendUser;

/**
 * @todo Better Algo for loading Mods and and Ce-Elements!
 * 
 */
class Utils {

  public static $ce_DoTypes = [
      'rocksolid_slider' => 'rocksolid_slider'
  ];

  public static function getModDoType(Module $module) {

    $type = null;

    if ($module instanceof ModuleNavigation || $module instanceof ModuleCustomnav) {
      $type = 'page';
    }

    $backendUser = BackendUser::getInstance();
    if (!$backendUser->isAdmin) {
      if (\count($backendUser->modules) <= 0 || !\in_array($type, $backendUser->modules)) {
        $type = null;
      }
    }

    return $type;
  }

  public static function getModDoTypeCe(string $element) {

    $type = null;

    switch ($element) {
      case 'rocksolid_slider':
        $type = 'rocksolid_slider';
        break;
      default:
        break;
    }

    return $type;
  }

}
