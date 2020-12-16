<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Custom;

use Contao\Module;
use Contao\ContentModel;
use Contao\ModuleNavigation;
use Contao\ModuleCustomnav;
use Contao\BackendUser;
use Alpdesk\AlpdeskFrontendediting\Model\AlpdeskFrontendeditingMappingModel;

class Custom {

  public static function getModDoType(Module $module) {

    $type = null;

    if ($module instanceof ModuleNavigation || $module instanceof ModuleCustomnav) {
      $type = 'page';
    }

    if (!BackendUser::getInstance()->hasAccess($type, 'modules')) {
      $type = null;
    }

    return $type;
  }

  public static function getModDoTypeCe(ContentModel $element) {

    $mappingObject = AlpdeskFrontendeditingMappingModel::findBy(['elements=?'], $element->type);
    if ($mappingObject === null) {
      return null;
    }

    $actEdit = '';
    if ($mappingObject->acteditident !== null && $mappingObject->acteditident != '') {
      $elementRows = $element->row();
      if (\array_key_exists($mappingObject->acteditident, $elementRows)) {
        $actEdit = '&act=edit&id=' . \intval($elementRows[$mappingObject->acteditident]);
      }
    }

    return 'do=' . $mappingObject->modules . $actEdit;
  }

}
