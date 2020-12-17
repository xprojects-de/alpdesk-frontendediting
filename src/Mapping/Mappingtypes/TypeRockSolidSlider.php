<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes\Base;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;

class TypeRockSolidSlider extends Base {

  private static $DO = 'do=rocksolid_slider';

  public function run(CustomViewItem $item): CustomViewItem {

    $item->setValid(true);
    $item->setPath(self::$DO . '&act=edit&id=' . $this->element->rsts_id);
    $item->setLabel('News');

    return $item;
  }

}
