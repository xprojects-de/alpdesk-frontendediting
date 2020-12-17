<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping;

use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;
use Contao\Module;
use Contao\ContentModel;
use Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes\Base;
use Alpdesk\AlpdeskFrontendediting\Events\AlpdeskFrontendeditingEventService;
use Alpdesk\AlpdeskFrontendediting\Events\AlpdeskFrontendeditingEventElement;
use Alpdesk\AlpdeskFrontendediting\Events\AlpdeskFrontendeditingEventModule;

class Mapping {

  private $alpdeskfeeEventDispatcher = null;

  public function __construct(AlpdeskFrontendeditingEventService $alpdeskfeeEventDispatcher) {
    $this->alpdeskfeeEventDispatcher = $alpdeskfeeEventDispatcher;
  }

  public function mapContentElement(CustomViewItem $item, ContentModel $element): CustomViewItem {

    $instance = Base::findClassByElement($element);
    if ($instance !== null) {
      $modifiedItem = $instance->run($item);
      $eventElement = new AlpdeskFrontendeditingEventElement($modifiedItem, $element);
      $this->alpdeskfeeEventDispatcher->getDispatcher()->dispatch($eventElement, AlpdeskFrontendeditingEventElement::NAME);
      return $eventElement->getItem();
    }

    return $item;
  }

  public function mapModule(CustomViewItem $item, Module $module): CustomViewItem {

    $instance = Base::findClassByModule($module);
    if ($instance !== null) {
      $modifiedItem = $instance->run($item);
      $eventModule = new AlpdeskFrontendeditingEventModule($modifiedItem, $module);
      $this->alpdeskfeeEventDispatcher->getDispatcher()->dispatch($eventModule, AlpdeskFrontendeditingEventModule::NAME);
      return $eventModule->getItem();
    }

    return $item;
  }

}
