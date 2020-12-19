<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping;

use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;
use Contao\Module;
use Contao\ContentModel;
use Contao\BackendUser;
use Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes\Base;
use Alpdesk\AlpdeskFrontendediting\Events\AlpdeskFrontendeditingEventService;
use Alpdesk\AlpdeskFrontendediting\Events\AlpdeskFrontendeditingEventElement;
use Alpdesk\AlpdeskFrontendediting\Events\AlpdeskFrontendeditingEventModule;

class Mapping {

  private $alpdeskfeeEventDispatcher = null;

  public function __construct(AlpdeskFrontendeditingEventService $alpdeskfeeEventDispatcher) {
    $this->alpdeskfeeEventDispatcher = $alpdeskfeeEventDispatcher;
  }

  public function checkCustomTypeElementAccess(ContentModel $element, CustomViewItem $item) {

    // If a contentelement comse e.g. "text" and ptable is news we have to check if News are enabled at BackendUser
    if (str_replace('tl_', '', $element->ptable) === 'news') {
      $item->setHasParentAccess(false);
      if (class_exists('\Contao\NewsModel')) {
        $objNews = \Contao\NewsModel::findById($element->pid);
        if ($objNews !== null) {
          $objArchive = $objNews->getRelated('pid');
          if (BackendUser::getInstance()->hasAccess($objArchive->id, 'news')) {
            $item->setHasParentAccess(true);
          }
        }
      }
    }
  }

  public function mapContentElement(CustomViewItem $item, ContentModel $element): CustomViewItem {

    $this->checkCustomTypeElementAccess($element, $item);

    $instance = Base::findClassByElement($element);
    if ($instance !== null) {
      $modifiedItem = $instance->run($item);
      $eventElement = new AlpdeskFrontendeditingEventElement($modifiedItem, $element);
      $this->alpdeskfeeEventDispatcher->getDispatcher()->dispatch($eventElement, AlpdeskFrontendeditingEventElement::NAME);
      return $eventElement->getItem();
    }

    $eventElement = new AlpdeskFrontendeditingEventElement($item, $element);
    $this->alpdeskfeeEventDispatcher->getDispatcher()->dispatch($eventElement, AlpdeskFrontendeditingEventElement::NAME);
    return $eventElement->getItem();
  }

  public function mapModule(CustomViewItem $item, Module $module): CustomViewItem {

    $instance = Base::findClassByModule($module);
    if ($instance !== null) {
      $modifiedItem = $instance->run($item);
      $eventModule = new AlpdeskFrontendeditingEventModule($modifiedItem, $module);
      $this->alpdeskfeeEventDispatcher->getDispatcher()->dispatch($eventModule, AlpdeskFrontendeditingEventModule::NAME);
      return $eventModule->getItem();
    }

    $eventModule = new AlpdeskFrontendeditingEventModule($item, $module);
    $this->alpdeskfeeEventDispatcher->getDispatcher()->dispatch($eventModule, AlpdeskFrontendeditingEventModule::NAME);
    return $eventModule->getItem();
  }

}
