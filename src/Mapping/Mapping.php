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

    // If a contentelement is e.g. "text" and ptable is news we have to check if News are enabled as BackendModule
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
    } else if (str_replace('tl_', '', $element->ptable) === 'calendar_events') {
      $item->setHasParentAccess(false);
      if (class_exists('\Contao\CalendarEventsModel')) {
        $objEvent = \Contao\CalendarEventsModel::findById($element->pid);
        if ($objEvent !== null) {
          $objCalendar = $objEvent->getRelated('pid');
          if (BackendUser::getInstance()->hasAccess($objCalendar->id, 'calendars')) {
            $item->setHasParentAccess(true);
          }
        }
      }
    }
  }

  public function checkCustomBackendModule(ContentModel $element, CustomViewItem $item) {

    // Maybe a tl_content element hast a custom BackendModule not equal to ptable
    if (str_replace('tl_', '', $element->ptable) === 'news') {
      $item->setCustomBackendModule('news');
    } else if (str_replace('tl_', '', $element->ptable) === 'calendar_events') {
      $item->setCustomBackendModule('calendar');
    } else if (str_replace('tl_', '', $element->ptable) === 'rocksolid_slide') {
      $item->setCustomBackendModule('rocksolid_slider');
    }
  }

  public function mapContentElement(CustomViewItem $item, ContentModel $element): CustomViewItem {

    $this->checkCustomTypeElementAccess($element, $item);
    $this->checkCustomBackendModule($element, $item);

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
