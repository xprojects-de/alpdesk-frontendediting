<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes\Base;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;
use Contao\BackendUser;

class TypeNewslist extends Base {

  private static $DO = 'do=news&table=tl_news';

  public function run(CustomViewItem $item): CustomViewItem {

    if (BackendUser::getInstance()->hasAccess($this->module->pid, 'news')) {
      $item->setValid(true);
      $item->setPath(self::$DO . '&id=' . $this->module->pid);
      $item->setLabel($GLOBALS['TL_LANG']['alpdeskfee_mapping_lables']['news']);
    }

    return $item;
  }

}
