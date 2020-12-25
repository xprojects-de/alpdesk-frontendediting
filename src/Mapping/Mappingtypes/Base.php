<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Contao\Module;
use Contao\ContentModel;
use Contao\BackendUser;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;

abstract class Base {

  abstract public function run(CustomViewItem $item): CustomViewItem;

  public $module;
  public $element;
  public $icon;
  public $iconclass;
  public $label;
  public $backendmodule;
  public $additional_static_params = [];

  private static function checkModuleAccess($moduletype): bool {
    if (!BackendUser::getInstance()->hasAccess($moduletype, 'modules')) {
      return false;
    }
    return true;
  }

  public static function findClassByElement(ContentModel $element, array $mappingconfig) {

    if ($mappingconfig !== null && \is_array($mappingconfig)) {
      if (\array_key_exists($element->type, $mappingconfig['alpdesk_frontendediting_mapping']['type_mapping'])) {
        $backendmodule = $mappingconfig['alpdesk_frontendediting_mapping']['type_mapping'][$element->type]['backend_module'];
        $mappingObject = $mappingconfig['alpdesk_frontendediting_mapping']['type_mapping'][$element->type]['mapping_object'];
        $icon = $mappingconfig['alpdesk_frontendediting_mapping']['type_mapping'][$element->type]['icon'];
        $iconclass = $mappingconfig['alpdesk_frontendediting_mapping']['type_mapping'][$element->type]['iconclass'];
        $labelkey = $mappingconfig['alpdesk_frontendediting_mapping']['type_mapping'][$element->type]['labelkey'];
        $additional_static_params = $mappingconfig['alpdesk_frontendediting_mapping']['type_mapping'][$element->type]['additional_static_params'];
        if (self::checkModuleAccess($backendmodule)) {
          $class = new $mappingObject();
          $class->element = $element;
          $class->icon = $icon;
          $class->iconclass = $iconclass;
          $class->label = $GLOBALS['TL_LANG']['alpdeskfee_mapping_lables'][$labelkey];
          $class->backendmodule = $backendmodule;
          $class->additional_static_params = $additional_static_params;
          return $class;
        }
      }
    }

    return null;
  }

  public static function findClassByModule(Module $module, array $mappingconfig) {

    if ($mappingconfig !== null && \is_array($mappingconfig)) {
      foreach ($mappingconfig['alpdesk_frontendediting_mapping']['type_mapping'] as $key => $value) {
        if ($value['is_module'] == true) {
          $mappingObject = $value['mapping_object'];
          $backendmodule = $value['backend_module'];
          $moduleobject = $value['module_object'];
          $icon = $value['icon'];
          $iconclass = $value['iconclass'];
          $labelkey = $value['labelkey'];
          $additional_static_params = $value['additional_static_params'];
          if (class_exists($moduleobject) && $module instanceof $moduleobject) {
            if (self::checkModuleAccess($backendmodule)) {
              $class = new $mappingObject();
              $class->module = $module;
              $class->icon = $icon;
              $class->iconclass = $iconclass;
              $class->label = $GLOBALS['TL_LANG']['alpdeskfee_mapping_lables'][$labelkey];
              $class->backendmodule = $backendmodule;
              $class->additional_static_params = $additional_static_params;
              return $class;
            }
            break;
          }
        }
      }
    }

    return null;
  }

}
