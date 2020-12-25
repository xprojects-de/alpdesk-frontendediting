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
  private $mappingconfig = null;

  public function __construct(AlpdeskFrontendeditingEventService $alpdeskfeeEventDispatcher, array $mappingconfig) {
    $this->alpdeskfeeEventDispatcher = $alpdeskfeeEventDispatcher;
    $this->mappingconfig = $mappingconfig;
  }

  public function checkCustomTypeElementAccess(ContentModel $element, CustomViewItem $item) {

    if ($this->mappingconfig !== null && \is_array($this->mappingconfig)) {
      $pTable = str_replace('tl_', '', $element->ptable);
      if (\array_key_exists($pTable, $this->mappingconfig['alpdesk_frontendediting_mapping']['element_access_check'])) {
        $model = $this->mappingconfig['alpdesk_frontendediting_mapping']['element_access_check'][$pTable]['model'];
        $item->setHasParentAccess(false);
        $accesskey = $this->mappingconfig['alpdesk_frontendediting_mapping']['element_access_check'][$pTable]['accesskey'];
        if (class_exists($model)) {
          $objModel = $model::findById($element->pid);
          if ($objModel !== null) {
            $objModelParent = $objModel->getRelated('pid');
            if (BackendUser::getInstance()->hasAccess($objModelParent->id, $accesskey)) {
              $item->setHasParentAccess(true);
            }
          }
        }
      }
    }
  }

  public function checkCustomBackendModule(ContentModel $element, CustomViewItem $item) {

    // Maybe a tl_content element hast a custom BackendModule not equal to ptable
    if ($this->mappingconfig !== null && \is_array($this->mappingconfig)) {
      $pTable = str_replace('tl_', '', $element->ptable);
      if (\array_key_exists($pTable, $this->mappingconfig['alpdesk_frontendediting_mapping']['element_backendmodule_mapping'])) {
        $backendmodule = $this->mappingconfig['alpdesk_frontendediting_mapping']['element_backendmodule_mapping'][$pTable]['backend_module'];
        $item->setCustomBackendModule($backendmodule);
      }
    }
  }

  public function mapContentElement(CustomViewItem $item, ContentModel $element): CustomViewItem {

    $this->checkCustomTypeElementAccess($element, $item);
    $this->checkCustomBackendModule($element, $item);

    $instance = Base::findClassByElement($element, $this->mappingconfig);
    if ($instance !== null) {
      $item->setIcon($instance->icon);
      $item->setIconclass($instance->iconclass);
      $item->setLabel($instance->label);
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

    $instance = Base::findClassByModule($module, $this->mappingconfig);
    if ($instance !== null) {
      $item->setIcon($instance->icon);
      $item->setIconclass($instance->iconclass);
      $item->setLabel($instance->label);
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
