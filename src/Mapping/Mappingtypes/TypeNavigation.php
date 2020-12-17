<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes\Base;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;

class TypeNavigation extends Base {

  private static $DO = 'do=page&pn=0';

  public function run(CustomViewItem $item): CustomViewItem {

    $item->setValid(true);
    $item->setPath(self::$DO);
    $item->setLabel('Navigation');

    return $item;
  }

}
