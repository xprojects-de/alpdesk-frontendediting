<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Custom;

use Contao\Module;
use Contao\ContentModel;
use Contao\ModuleModel;
use Contao\BackendUser;
use Alpdesk\AlpdeskFrontendediting\Model\AlpdeskFrontendeditingMappingModel;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomResponse;

class Custom {

  public static function getModDoType(Module $module): CustomResponse {

    $response = new CustomResponse();

    if (!BackendUser::getInstance()->hasAccess($module->type, 'modules')) {
      return $response;
    }

    $mappingObject = AlpdeskFrontendeditingMappingModel::findBy(['fe_modules=?'], $module->id);
    if ($mappingObject === null) {
      return $response;
    }

    $response->setValid(true);

    $actEdit = '';
    if ($module->pid !== 0) {
      //$actEdit = '&table=tl_' . $mappingObject->modules . '&act=edit&id=' . $module->pid;
      $actEdit = '&table=tl_' . $mappingObject->modules . '&id=' . $module->pid;
    }

    $response->setPath('do=' . $mappingObject->modules . $actEdit);
    $response->setLabel($mappingObject->title);

    return $response;
  }

  public static function getModuleType($moduleId) {
    try {
      $moduleObject = ModuleModel::findBy(['id=?'], $moduleId);
      $strClass = Module::findClass($moduleObject->type);
      if (!class_exists($strClass)) {
        return null;
      }
      $moduleObject->typePrefix = 'mod_';
      $objModule = new $strClass($moduleObject);
      if ($objModule !== null && $objModule instanceof Module) {
        return $objModule;
      } else {
        return null;
      }
    } catch (\Exception $e) {
      return null;
    }
  }

  public static function getModDoTypeCe(ContentModel $element): CustomResponse {

    $response = new CustomResponse();

    $mappingObject = AlpdeskFrontendeditingMappingModel::findBy(['elements=?'], $element->type);
    if ($mappingObject === null) {
      if ($element->type === 'module') {
        $objModule = self::getModuleType($element->module);
        if ($objModule !== null) {
          return self::getModDoType($objModule);
        }
      }
      return $response;
    }

    $response->setValid(true);

    $actEdit = '';
    if ($mappingObject->acteditident !== null && $mappingObject->acteditident != '') {
      $elementRows = $element->row();
      if (\array_key_exists($mappingObject->acteditident, $elementRows)) {
        $actEdit = '&act=edit&id=' . \intval($elementRows[$mappingObject->acteditident]);
      }
    }

    $response->setPath('do=' . $mappingObject->modules . $actEdit);
    $response->setLabel($mappingObject->title);

    return $response;
  }

}
