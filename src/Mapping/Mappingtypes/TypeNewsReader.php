<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes\Base;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;

class TypeNewsReader extends Base {

  private static $DO = 'do=news&table=tl_content';

  public function run(CustomViewItem $item): CustomViewItem {

    $item->setValid(true);
    $item->setPath(self::$DO . '&id=' . $this->module->pid);
    $item->setLabel('News');

    return $item;
  }

}
