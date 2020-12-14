<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Utils;

use Contao\Module;
use Contao\BackendUser;

class Utils {

  public static $mod_DoTypes = [
      'navigation' => 'page',
      'customnav' => 'page',
      'breadcrumb' => 'page',
      'quicknav' => 'page',
      'quicklink' => 'page',
      'booknav' => 'page',
      'sitemap' => 'page',
      'rocksolid_slider' => 'rocksolid_slider',
      'form' => 'form',
  ];
  public static $ce_DoTypes = [
      'rocksolid_slider' => 'rocksolid_slider',
      'form' => 'form',
  ];

  public static function getModDoType(Module $module) {

    $type = null;

    // @TODO
    // Check Fragment Controller
    $moduleType = null;
    foreach ($GLOBALS['FE_MOD'] as $key => $value) {
      if (\is_array($value)) {
        foreach ($value as $subKey => $subValue) {
          if ($module instanceof $subValue) {
            $moduleType = $subKey;
            break 2;
          }
        }
      } else {
        if ($module instanceof $value) {
          $moduleType = $subKey;
          break;
        }
      }
    }

    if ($moduleType !== null && \array_key_exists($moduleType, self::$mod_DoTypes)) {
      $type = self::$mod_DoTypes[$moduleType];
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

    if (\array_key_exists($element, self::$ce_DoTypes)) {
      $type = self::$ce_DoTypes[$element];
    }

    return $type;
  }

}
