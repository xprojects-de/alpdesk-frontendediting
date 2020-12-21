<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes\Base;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;

class TypeNavigation extends Base {

  private static $DO = 'do=page&pn=0';
  private static $icon = '../../../system/themes/flexible/icons/root.svg';
  private static $iconclass = 'tl_navigation_baritem';

  public function run(CustomViewItem $item): CustomViewItem {

    $item->setValid(true);
    $item->setIcon(self::$icon);
    $item->setIconclass(self::$iconclass);
    $item->setPath(self::$DO);
    $item->setLabel($GLOBALS['TL_LANG']['alpdeskfee_mapping_lables']['navigation']);

    return $item;
  }

}
