<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes\Base;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomSubviewItem;

class TypeNavigation extends Base {

  private static $DO = 'do=page&pn=0';

  public function run(CustomViewItem $item): CustomViewItem {

    $item->setValid(true);
    $item->setPath(self::$DO);
    $item->setLabel('Navigation');
    
    $subItem = new CustomSubviewItem();
    $subItem->getPath($DO);
    $subItem->setIcon('module');
    $subItem->setIconclass('test');
    
    $item->addSubviewitems($subItem);

    return $item;
  }

}
