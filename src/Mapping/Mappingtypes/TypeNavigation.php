<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes\Base;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;

class TypeNavigation extends Base {

  public function run(CustomViewItem $item): CustomViewItem {

    $item->setValid(true);
    $item->setPath('do=' . $this->backendmodule . '&pn=0');

    return $item;
  }

}
