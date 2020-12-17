<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Contao\Module;
use Contao\ContentModel;
use Contao\BackendUser;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;
use Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes\TypeNavigation;
use Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes\TypeRockSolidSlider;

abstract class Base {

  abstract public function run(CustomViewItem $item): CustomViewItem;

  public $module;
  public $element;

  private static function checkModuleAccess($moduletype): bool {
    if (!BackendUser::getInstance()->hasAccess($moduletype, 'modules')) {
      return false;
    }
    return true;
  }

  public static function findClassByElement(ContentModel $element) {

    if ($element->type === 'rocksolid_slider') {
      if (self::checkModuleAccess('rocksolid_slider')) {
        $class = new TypeRockSolidSlider();
        $class->element = $element;
        return $class;
      }
    }

    return null;
  }

  public static function findClassByModule(Module $module) {

    if (class_exists('\Contao\ModuleNavigation') && $module instanceof \Contao\ModuleNavigation) {
      if (self::checkModuleAccess('page')) {
        $class = new TypeNavigation();
        $class->module = $module;
        return $class;
      }
    } else if (class_exists('\Contao\ModuleNewsList') && $module instanceof \Contao\ModuleNewsList) {
      if (self::checkModuleAccess('news')) {
        $class = new TypeNewslist();
        $class->module = $module;
        return $class;
      }
    }

    return null;
  }

}
