<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Utils;

use Contao\BackendUser;
use Contao\PageModel;

class Utils {

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
