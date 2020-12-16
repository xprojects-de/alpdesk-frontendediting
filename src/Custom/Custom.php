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

  public static function getModuleTypeInstanceById($moduleId) {
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

  public static function getModDoType(Module $module): CustomResponse {

    $response = new CustomResponse();
    $response->setType(CustomResponse::$TYPE_MODULE);

    // Show Modules only if mapped in backend
    $mappingObject = AlpdeskFrontendeditingMappingModel::findBy(['fe_modules=?'], $module->id);
    if ($mappingObject === null) {
      return $response;
    }

    // If user has no Access to BackendModule retrun
    if (!BackendUser::getInstance()->hasAccess($mappingObject->modules, 'modules')) {
      return $response;
    }

    $do = 'do=' . $mappingObject->modules;
    $sublevelPath = '';
    if ($mappingObject->fe_modules_sublevel == 1 && $module->pid > 0) {
      $sublevelPath = '&table=tl_' . $mappingObject->modules . '&id=' . $module->pid;
      $response->setSublevelpath($do . $sublevelPath . '&act=edit');
    }

    $response->setValid(true);
    $response->setPath($do . $sublevelPath);
    $response->setLabel($mappingObject->title);

    return $response;
  }

  public static function getModDoTypeCe(ContentModel $element): CustomResponse {

    $response = new CustomResponse();
    $response->setType(CustomResponse::$TYPE_CE);

    $mappingObject = AlpdeskFrontendeditingMappingModel::findBy(['elements=?'], $element->type);
    if ($mappingObject === null) {
      if ($element->type === 'module') {
        $objModule = self::getModuleTypeInstanceById($element->module);
        if ($objModule !== null) {
          return self::getModDoType($objModule);
        }
      }
      return $response;
    }

    // If user has no Access to BackendModule retrun
    if (!BackendUser::getInstance()->hasAccess($mappingObject->modules, 'modules')) {
      return $response;
    }

    $actEdit = '';
    if ($mappingObject->acteditident !== null && $mappingObject->acteditident != '') {
      $elementRows = $element->row();
      if (\array_key_exists($mappingObject->acteditident, $elementRows)) {
        $actEdit = '&act=edit&id=' . \intval($elementRows[$mappingObject->acteditident]);
      }
    }

    $response->setValid(true);
    $response->setPath('do=' . $mappingObject->modules . $actEdit);
    $response->setLabel($mappingObject->title);

    return $response;
  }

}
