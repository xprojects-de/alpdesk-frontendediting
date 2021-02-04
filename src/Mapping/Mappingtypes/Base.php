<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Contao\Module;
use Contao\Form;
use Contao\ContentModel;
use Contao\BackendUser;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;

abstract class Base {

  abstract public function run(CustomViewItem $item): CustomViewItem;

  public $module;
  public $element;
  public $form;
  public $icon;
  public $iconclass;
  public $label;
  public $backendmodule;
  public $additional_static_params = [];
  public $table;

  private static function checkModuleAccess($moduletype): bool {
    if (!BackendUser::getInstance()->hasAccess($moduletype, 'modules')) {
      return false;
    }
    return true;
  }

  public static function findClassByElement(ContentModel $element, array $mappingconfig) {

    if ($mappingconfig !== null && \is_array($mappingconfig)) {
      foreach ($mappingconfig['alpdesk_frontendediting_mapping']['type_mapping'] as $value) {
        if ($value['element'] !== null) {
          if ($element->type == $value['element']) {
            $backendmodule = $value['backend_module'];
            $mappingObject = $value['mapping_object'];
            $icon = $value['icon'];
            $iconclass = $value['iconclass'];
            $labelkey = $value['labelkey'];
            $additional_static_params = $value['additional_static_params'];
            $table = $value['table'];
            if (self::checkModuleAccess($backendmodule)) {
              $class = new $mappingObject();
              $class->element = $element;
              $class->icon = $icon;
              $class->iconclass = $iconclass;
              $class->label = $GLOBALS['TL_LANG']['alpdeskfee_mapping_lables'][$labelkey];
              $class->backendmodule = $backendmodule;
              $class->additional_static_params = $additional_static_params;
              $class->table = $table;
              return $class;
            }
            break;
          }
        }
      }
    }

    return null;
  }

  public static function findClassByModule(Module $module, array $mappingconfig) {

    if ($mappingconfig !== null && \is_array($mappingconfig)) {
      foreach ($mappingconfig['alpdesk_frontendediting_mapping']['type_mapping'] as $value) {
        if ($value['module'] !== null) {
          $mappingObject = $value['mapping_object'];
          $backendmodule = $value['backend_module'];
          $moduleobject = $value['module'];
          $icon = $value['icon'];
          $iconclass = $value['iconclass'];
          $labelkey = $value['labelkey'];
          $additional_static_params = $value['additional_static_params'];
          $table = $value['table'];
          if (class_exists($moduleobject) && $module instanceof $moduleobject) {
            if (self::checkModuleAccess($backendmodule)) {
              $class = new $mappingObject();
              $class->module = $module;
              $class->icon = $icon;
              $class->iconclass = $iconclass;
              $class->label = $GLOBALS['TL_LANG']['alpdeskfee_mapping_lables'][$labelkey];
              $class->backendmodule = $backendmodule;
              $class->additional_static_params = $additional_static_params;
              $class->table = $table;
              return $class;
            }
            break;
          }
        }
      }
    }

    return null;
  }

  public static function findClassByForm(Form $form, array $mappingconfig) {

    if ($mappingconfig !== null && \is_array($mappingconfig)) {
      foreach ($mappingconfig['alpdesk_frontendediting_mapping']['type_mapping'] as $value) {
        if ($value['module'] !== null) {
          $mappingObject = $value['mapping_object'];
          $backendmodule = $value['backend_module'];
          $moduleobject = $value['module'];
          $icon = $value['icon'];
          $iconclass = $value['iconclass'];
          $labelkey = $value['labelkey'];
          $additional_static_params = $value['additional_static_params'];
          $table = $value['table'];
          if (class_exists($moduleobject) && $form instanceof $moduleobject) {
            if (self::checkModuleAccess($backendmodule)) {
              $class = new $mappingObject();
              $class->form = $form;
              $class->icon = $icon;
              $class->iconclass = $iconclass;
              $class->label = $GLOBALS['TL_LANG']['alpdeskfee_mapping_lables'][$labelkey];
              $class->backendmodule = $backendmodule;
              $class->additional_static_params = $additional_static_params;
              $class->table = $table;
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
