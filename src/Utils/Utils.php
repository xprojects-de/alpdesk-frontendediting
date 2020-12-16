<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Utils;

use Contao\Module;
use Contao\ModuleNavigation;
use Contao\ModuleCustomnav;
use Contao\BackendUser;
use Contao\PageModel;

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

    if (!BackendUser::getInstance()->hasAccess($type, 'modules')) {
      $type = null;
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

  public static function hasPagemountAccess(PageModel $objPage): bool {

    $backendUser = BackendUser::getInstance();

    if ($backendUser->isAdmin || $backendUser->hasAccess($objPage->id, 'pagemounts')) {
      return true;
    }

    $check = false;

    // Bad but fo not want to override PageModel-Reference from Hook
    $objParentPage = PageModel::findById($objPage->id);
    $pid = $objPage->pid;
    while ($objParentPage !== null && $check === false && $pid > 0) {
      $pid = $objParentPage->pid;
      $check = $backendUser->hasAccess($objParentPage->id, 'pagemounts');
      if ($check === false) {
        $objParentPage = PageModel::findById($pid);
      }
    }

    return $check;
  }

}
