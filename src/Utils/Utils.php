<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Utils;

use Contao\BackendUser;
use Contao\PageModel;
use Contao\Database;
use Contao\Date;
use Contao\StringUtil;

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

  public static function mergeUserGroupPersmissions() {

    $backendUser = BackendUser::getInstance();

    if ($backendUser->inherit == 'group' || $backendUser->inherit == 'extend') {

      $time = Date::floorToMinute();

      foreach ((array) $backendUser->groups as $id) {
        $objGroup = Database::getInstance()->prepare("SELECT alpdesk_fee_enabled,alpdesk_fee_elements FROM tl_user_group WHERE id=? AND disable!='1' AND (start='' OR start<='$time') AND (stop='' OR stop>'$time')")->limit(1)->execute($id);
        if ($objGroup->numRows > 0) {
          $backendUser->alpdesk_fee_enabled = $objGroup->alpdesk_fee_enabled;
          $value = StringUtil::deserialize($objGroup->alpdesk_fee_elements, true);
          if (!empty($value)) {
            if ($backendUser->alpdesk_fee_elements === null) {
              $backendUser->alpdesk_fee_elements = $value;
            } else {
              $backendUser->alpdesk_fee_elements = array_merge($backendUser->alpdesk_fee_elements, $value);
            }
            $backendUser->alpdesk_fee_elements = array_unique($backendUser->alpdesk_fee_elements);
          }
        }
      }
    }
  }

}
